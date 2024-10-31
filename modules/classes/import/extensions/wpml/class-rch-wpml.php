<?php

namespace rch\import\wpml;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php');
}

class RCH_WPML_Import extends \rch\import\base\RCH_Import_Base {

        private $default_language;
        private $active_languages;

        public function __construct( $rch_import_option = array(), $import_type = "" ) {

                global $sitepress;

                $this->default_language = $sitepress->get_default_language();

                $this->active_languages = $sitepress->get_active_languages();

                $this->rch_import_option = $rch_import_option;

                $this->import_type = $import_type;
        }

        public function before_item_import( $rch_import_record = array(), &$existing_item_id = 0, &$is_new_item = true, &$is_search_duplicates ) {

                $this->rch_import_record = $rch_import_record;

                $source_item = false;

                if ( $this->import_type == "post" ) {
                        $source_item = $this->search_post_duplicate_item();
                } elseif ( $this->import_type == "taxonomy" ) {
                        $source_item = $this->search_taxonomy_duplicate_item();
                }

                if ( ! empty( $source_item ) && $source_item !== 0 && is_numeric( $source_item ) ) {
                        $source_item = array( $source_item );
                }


                if ( is_array( $source_item ) && ! empty( $source_item ) ) {

                        $item_lang = rch_sanitize_field( $this->get_field_value( 'rch_wpml_lang_code' ) );

                        if ( $item_lang == "as_specified" ) {
                                $item_lang = rch_sanitize_field( $this->get_field_value( 'rch_item_wpml_lang' ) );
                        }

                        if ( ! empty( $item_lang ) && isset( $this->active_languages[ $item_lang ] ) ) {

                                $element_type = "post_post";

                                if ( $this->import_type == "post" ) {
                                        $element_type = 'post_' . rch_sanitize_field( $this->get_field_value( 'rch_import_type' ) );
                                } elseif ( $this->import_type == "taxonomy" ) {
                                        $element_type = 'tax_' . rch_sanitize_field( $this->get_field_value( 'rch_taxonomy_type', true ) );
                                }
                                global $wpdb;

                                $element_id = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "SELECT element_id FROM {$wpdb->prefix}icl_translations 
                                                WHERE element_id IN ('" . implode( "','", $source_item ) . "') AND 
                                                language_code = %s AND
                                                element_type = %s",
                                                $item_lang,
                                                $element_type
                                        ) );

                                if ( ! empty( $element_id ) && $element_id > 0 ) {
                                        $is_new_item = false;
                                        $existing_item_id = $element_id;
                                }
                        }
                }

                $is_search_duplicates = false;

                if ( $this->import_type == "taxonomy" ) {
                        $this->remove_wpml_term_filters();
                }

                unset( $source_item );
        }

        public function after_item_import( $item_id = 0, $item = null, $is_new_item = true ) {

                $this->item_id = $item_id;

                $this->item = $item;

                $this->is_new_item = $is_new_item;

                $this->update_tanslation();
        }

        private function update_tanslation() {

                global $sitepress, $wpdb;

                $element_type = "post_post";

                if ( $this->import_type == "post" ) {
                        $element_type = 'post_' . $this->item->post_type;
                } elseif ( $this->import_type == "taxonomy" ) {

                        $element_type = 'tax_' . $this->item->taxonomy;
                }

                $item_lang = rch_sanitize_field( $this->get_field_value( 'rch_wpml_lang_code' ) );

                if ( $item_lang == "as_specified" ) {
                        $item_lang = rch_sanitize_field( $this->get_field_value( 'rch_item_wpml_lang' ) );
                }

                if ( empty( $item_lang ) || ! isset( $this->active_languages[ $item_lang ] ) ) {
                        return true;
                }

                if ( $this->import_type == "post" ) {
                        $source_item = $this->search_source_post_item( true );
                } elseif ( $this->import_type == "taxonomy" ) {
                        $source_item = $this->search_source_taxonomy_item( true );
                }

                $old_trid = 0;
                if ( ! $this->is_new_item ) {

                        $old_trid = $wpdb->get_var(
                                $wpdb->prepare( "SELECT trid FROM {$wpdb->prefix}icl_translations 
                                                WHERE element_id = %d AND 
                                                language_code = %s AND
                                                element_type = %s",
                                        $this->item_id,
                                        $item_lang,
                                        $element_type
                                ) );
                }

                $trid = 0;

                if ( ! empty( $source_item ) ) {

                        $default_lang = $this->default_language;

                        if ( is_array( $source_item ) ) {

                                if ( ($item_key = array_search( $this->item_id, $source_item )) !== false ) {
                                        unset( $source_item[ $item_key ] );
                                }

                                $trid = $wpdb->get_var(
                                        $wpdb->prepare( "SELECT trid FROM {$wpdb->prefix}icl_translations 
                                                        WHERE element_id IN ('" . implode( "','", $source_item ) . "') AND 
                                                        language_code = %s AND
                                                        element_type = %s Limit 0,1",
                                                $this->default_language,
                                                $element_type
                                        ) );


                                if ( empty( $trid ) ) {
                                        $translation = $wpdb->get_row(
                                                $wpdb->prepare( "SELECT trid,language_code,source_language_code FROM {$wpdb->prefix}icl_translations 
                                                                WHERE element_id IN ('" . implode( "','", $source_item ) . "') AND 
                                                                element_type = %s Limit 0,1",
                                                        $element_type
                                                ) );

                                        if ( $translation && isset( $translation->trid ) ) {
                                                $trid = $translation->trid;
                                                $default_lang = empty( $translation->source_language_code ) ? $translation->language_code : $translation->source_language_code;
                                        }
                                }
                        } elseif ( is_numeric( $source_item ) && absint( $source_item ) > 0 ) {
                                $trid = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "SELECT trid FROM {$wpdb->prefix}icl_translations
                                                WHERE element_id = %d AND element_type = %s Limit 0,1",
                                                absint( $source_item ),
                                                $element_type
                                        ) );
                        }

                        if ( ! empty( $trid ) && absint( $trid ) > 0 && $old_trid !== $trid ) {
                                $sitepress->set_element_language_details( $this->item_id, $element_type, $trid, $item_lang, $default_lang );
                        }
                }

                if ( empty( $trid ) || absint( $trid ) === 0 ) {
                        $sitepress->set_element_language_details( $this->item_id, $element_type, false, $item_lang );
                }

                unset( $item_lang, $element_type, $trid, $source_item );
        }

        private function remove_wpml_term_filters() {

                global $sitepress;

                remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ) );

                remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );

                remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
        }

        private function search_source_taxonomy_item( $is_multiple = false ) {

                global $wpdb, $wp_version;

                $this->remove_wpml_term_filters();

                $logic = rch_sanitize_field( $this->get_field_value( 'rch_item_wpml_default_item', true ) );

                $taxonomy_type = rch_sanitize_field( $this->get_field_value( 'rch_taxonomy_type' ) );

                $taxonomy_items = false;

                if ( $logic == "id" ) {

                        $item_id = absint( rch_sanitize_field( $this->get_field_value( 'rch_item_wpml_trid' ) ) );

                        if ( $item_id > 0 ) {

                                $term = get_term_by( 'id', $item_id, $taxonomy_type );

                                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                                        $taxonomy_items = $item_id;
                                }
                                unset( $term );
                        }

                        unset( $item_id );
                } elseif ( $logic == "slug" ) {

                        $slug = rch_sanitize_field( $this->get_field_value( 'rch_item_wpml_translation_slug', false, true ) );

                        if ( ! empty( $slug ) ) {

                                $args = array(
                                        'get'                    => 'all',
                                        'number'                 => 0,
                                        'taxonomy'               => $taxonomy_type,
                                        'update_term_meta_cache' => false,
                                        'orderby'                => 'none',
                                        'fields'                 => 'ids',
                                        'suppress_filter'        => true,
                                        'slug'                   => $slug
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $taxonomy_items = get_terms( $taxonomy_type, $args );
                                } else {
                                        $taxonomy_items = get_terms( $args );
                                }
                        }
                        unset( $slug );
                } elseif ( $logic == "name" ) {

                        $name = rch_sanitize_field( $this->get_field_value( 'rch_item_wpml_default_item_name', false, true ) );

                        if ( ! empty( $name ) ) {

                                $args = array(
                                        'get'                    => 'all',
                                        'number'                 => 0,
                                        'taxonomy'               => $taxonomy_type,
                                        'update_term_meta_cache' => false,
                                        'orderby'                => 'none',
                                        'fields'                 => 'ids',
                                        'suppress_filter'        => true,
                                        'name'                   => $name
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $taxonomy_items = get_terms( $taxonomy_type, $args );
                                } else {
                                        $taxonomy_items = get_terms( $args );
                                }
                        }
                        unset( $name );
                }
                unset( $logic, $taxonomy_type );

                return $taxonomy_items;
        }

        private function search_taxonomy_duplicate_item() {

                global $wpdb, $wp_version;

                $this->remove_wpml_term_filters();

                $taxonomy_item = false;

                $rch_duplicate_indicator = rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic', true ) );

                $taxonomy_type = rch_sanitize_field( $this->get_field_value( 'rch_taxonomy_type' ) );

                if ( $rch_duplicate_indicator == "id" ) {

                        $duplicate_id = absint( rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_id' ) ) );

                        if ( $duplicate_id > 0 ) {

                                $term = get_term_by( 'id', $duplicate_id, $taxonomy_type );

                                if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
                                        $taxonomy_item = $duplicate_id;
                                }
                                unset( $term );
                        }
                        unset( $duplicate_id );
                } elseif ( $rch_duplicate_indicator == "slug" ) {

                        $slug = rch_sanitize_field( $this->get_field_value( 'rch_item_term_slug', false, true ) );

                        if ( ! empty( $slug ) ) {

                                $args = array(
                                        'get'                    => 'all',
                                        'number'                 => 0,
                                        'taxonomy'               => "category",
                                        'update_term_meta_cache' => false,
                                        'orderby'                => 'id',
                                        'fields'                 => 'ids',
                                        'suppress_filter'        => true,
                                        'slug'                   => $slug
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $taxonomy_item = get_terms( $taxonomy_type, $args );
                                } else {
                                        $taxonomy_item = get_terms( $args );
                                }
                        }
                        unset( $slug );
                } elseif ( $rch_duplicate_indicator == "name" ) {

                        $name = rch_sanitize_field( $this->get_field_value( 'rch_item_term_name' ) );

                        if ( ! empty( $name ) ) {

                                $args = array(
                                        'get'                    => 'all',
                                        'number'                 => 0,
                                        'taxonomy'               => $taxonomy_type,
                                        'update_term_meta_cache' => false,
                                        'orderby'                => 'none',
                                        'fields'                 => 'ids',
                                        'suppress_filter'        => true,
                                        'name'                   => $name
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $taxonomy_item = get_terms( $taxonomy_type, $args );
                                } else {
                                        $taxonomy_item = get_terms( $args );
                                }
                        }

                        unset( $name );
                } elseif ( $rch_duplicate_indicator == "cf" ) {

                        $meta_key = rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_cf_key' ) );

                        $meta_val = rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_cf_value' ) );

                        if ( ! empty( $meta_key ) ) {

                                $args = array(
                                        'taxonomy'   => $taxonomy_type,
                                        'number'     => 0,
                                        'fields'     => 'ids',
                                        'hide_empty' => false,
                                        'meta_query' => array(
                                                array(
                                                        'key'     => $meta_key,
                                                        'value'   => $meta_val,
                                                        'compare' => '='
                                                )
                                        )
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $taxonomy_item = get_terms( $taxonomy_type, $args );
                                } else {
                                        $taxonomy_item = get_terms( $args );
                                }

                                unset( $terms, $args );
                        }

                        unset( $meta_key, $meta_val );
                }

                unset( $taxonomy_type, $rch_duplicate_indicator );

                if ( is_wp_error( $taxonomy_item ) ) {
                        return false;
                }
                return $taxonomy_item;
        }

        private function search_post_duplicate_item() {

                global $wpdb;

                $post_id = 0;

                $rch_duplicate_indicator = empty( $this->get_field_value( 'rch_existing_item_search_logic', true ) ) ? 'title' : rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic', true ) );

                if ( $rch_duplicate_indicator == "id" ) {

                        $duplicate_id = absint( rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_id' ) ) );

                        if ( $duplicate_id > 0 ) {
                                $_post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 1", $duplicate_id ) );

                                if ( $_post && absint( $_post ) > 0 ) {
                                        $post_id = absint( $duplicate_id );
                                }
                                unset( $_post );
                        }
                        unset( $duplicate_id );
                } elseif ( $rch_duplicate_indicator == "title" || $rch_duplicate_indicator == "content" ) {

                        $rch_field = 'post_' . $rch_duplicate_indicator;

                        $temp_field = 'rch_item_' . $rch_duplicate_indicator;

                        $rch_field_data = $this->get_field_value( $temp_field );

                        if ( ! empty( $rch_field_data ) ) {

                                $_post = $wpdb->get_col(
                                        $wpdb->prepare(
                                                "SELECT ID FROM " . $wpdb->posts . "
                                                WHERE
                                                    post_type = %s
                                                    AND ID != 0
                                                    AND " . $rch_field . " = %s
                                                ", rch_sanitize_field( $this->get_field_value( 'rch_import_type', true ) ), rch_sanitize_field( $rch_field_data )
                                        )
                                );

                                if ( $_post && ! empty( $_post ) ) {
                                        $post_id = $_post;
                                }

                                unset( $_post );
                        }
                        unset( $rch_field, $rch_field_data, $temp_field );
                } elseif ( $rch_duplicate_indicator == "cf" ) {

                        $meta_key = rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_cf_key' ) );

                        $meta_val = rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_cf_value' ) );

                        $post_types = rch_sanitize_field( $this->get_field_value( 'rch_import_type', true ) );

                        if ( $post_types == "product" ) {
                                $post_types = array( "product", "product_variation" );
                        } else {
                                $post_types = array( $post_types );
                        }

                        $sql_post_type = implode( "','", $post_types );

                        $id = $wpdb->get_var(
                                $wpdb->prepare(
                                        "
                                                SELECT posts.ID
                                                FROM {$wpdb->posts} as posts
                                                INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                                                WHERE posts.post_type IN ( '{$sql_post_type}' )
                                                AND posts.post_status NOT IN ('trash','auto-draft' )
                                                AND postmeta.meta_key = %s                                               
                                                AND postmeta.meta_value = %s
                                                ORDER BY posts.ID ASC
                                                LIMIT 0, 1
                                        ",
                                        $meta_key,
                                        $meta_val
                                )
                        );

                        if ( absint( $id ) > 0 ) {
                                $post_id = $id;
                        }

                        if ( $post_id === 0 ) {

                                $id = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "
                                                        SELECT posts.ID
                                                        FROM {$wpdb->posts} as posts
                                                        INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                                                        WHERE posts.post_type IN ( '{$sql_post_type}' )
                                                        AND postmeta.meta_key = %s                                               
                                                        AND postmeta.meta_value = %s
                                                        ORDER BY posts.ID ASC
                                                        LIMIT 0, 1
                                                ",
                                                $meta_key,
                                                $meta_val
                                        )
                                );

                                if ( absint( $id ) > 0 ) {
                                        $post_id = $id;
                                }
                        }
                        if ( $post_id === 0 ) {

                                $id = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "
                                                        SELECT posts.ID
                                                        FROM {$wpdb->posts} as posts
                                                        INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                                                        WHERE posts.post_type IN ( '{$sql_post_type}' )
                                                        AND postmeta.meta_key IN ( %s,%s )                                               
                                                        AND postmeta.meta_value IN( %s,%s,%s )
                                                        ORDER BY posts.ID ASC
                                                        LIMIT 0, 1
                                                ",
                                                $meta_key,
                                                trim( $meta_key ),
                                                rch_sanitize_field( $meta_key ),
                                                $meta_val,
                                                trim( $meta_val ),
                                                rch_sanitize_field( $meta_val ),
                                                preg_replace( '%[ \\t\\n]%', '', $meta_val )
                                        )
                                );

                                if ( absint( $id ) > 0 ) {
                                        $post_id = $id;
                                }
                        }

                        unset( $meta_key, $meta_val, $post_types, $sql_post_type, $id );
                }
                unset( $rch_duplicate_indicator );

                return $post_id;
        }

        private function search_source_post_item( $is_multiple = false ) {

                global $wpdb;

                $logic = rch_sanitize_field( $this->get_field_value( 'rch_item_wpml_default_item', true ) );

                $post_id = 0;

                if ( $logic == "id" ) {

                        $item_id = absint( rch_sanitize_field( $this->get_field_value( 'rch_item_wpml_trid' ) ) );

                        if ( $item_id > 0 ) {

                                $_post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 0,1", $item_id ) );

                                if ( $_post && absint( $_post ) > 0 ) {
                                        $post_id = absint( $item_id );
                                }
                                unset( $_post );
                        }
                        unset( $item_id );
                } elseif ( $logic == "title" ) {

                        $rch_field_data = $this->get_field_value( "rch_item_wpml_translation_title" );

                        if ( ! empty( $rch_field_data ) ) {

                                if ( $is_multiple ) {

                                        $_post = $wpdb->get_col(
                                                $wpdb->prepare(
                                                        "SELECT ID FROM " . $wpdb->posts . "
                                                        WHERE
                                                            post_type = %s
                                                            AND ID != 0
                                                            AND post_title = %s
                                                ", rch_sanitize_field( $this->get_field_value( 'rch_import_type', true ) ), rch_sanitize_field( $rch_field_data )
                                                )
                                        );

                                        if ( $_post && ! empty( $_post ) ) {
                                                return $_post;
                                        }

                                        unset( $_post );
                                } else {

                                        $_post = $wpdb->get_var(
                                                $wpdb->prepare(
                                                        "SELECT ID FROM " . $wpdb->posts . "
                                                        WHERE
                                                            post_type = %s
                                                            AND ID != 0
                                                            AND post_title = %s
                                                        LIMIT 1
                                                ", rch_sanitize_field( $this->get_field_value( 'rch_import_type', true ) ), rch_sanitize_field( $rch_field_data )
                                                )
                                        );

                                        if ( $_post && absint( $_post ) > 0 ) {
                                                $post_id = absint( $_post );
                                        }

                                        unset( $_post );
                                }
                        }
                        unset( $rch_field_data );
                }

                unset( $logic );

                if ( $post_id == 0 ) {
                        return false;
                }
                return $post_id;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
