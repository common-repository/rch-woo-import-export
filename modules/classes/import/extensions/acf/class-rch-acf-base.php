<?php

namespace rch\import\acf\base;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php');
}

abstract class RCH_ACF_Base extends \rch\import\base\RCH_Import_Base {

        private $attch_class;
        private $skip_empty = true;

        public function __construct( $rch_import_option = array (), $import_type = "" ) {

                $this->rch_import_option = $rch_import_option;

                $this->import_type = $import_type;
        }

        public function before_item_import( $rch_import_record = array (), $existing_item_id = 0, $is_new_item = true, $is_search_duplicates ) {

                $this->rch_import_record = $rch_import_record;
        }

        public function get_acf_fields_view( $acf_fields ) {

                if ( ! empty( $acf_fields ) ) {

                        foreach ( $acf_fields as $field ) {

                                $field_data = $field;

                                if ( is_numeric( $field ) ) {
                                        $field_data = $this->acf_get_field( $field );
                                }

                                if ( isset( $field_data[ 'type' ] ) && in_array( $field_data[ 'type' ], array ( "message", "accordion", "tab" ) ) ) {
                                        continue;
                                }
                                ?>
                                <div class="rch_acf_field_data_wrapper rch_acf_item_field_<?php echo esc_attr( $field_data[ 'type' ] ); ?>">
                                    <?php $this->get_acf_field_views( $field_data ); ?>
                                </div>
                                <?php
                                unset( $field_data );
                        }
                }
        }

        public function get_acf_field_views( $acf_field = array (), $parent_field = array (), $is_sub_fields = false ) {

                if ( empty( $acf_field ) ) {
                        return;
                }

                if ( ! (is_array( $acf_field ) && isset( $acf_field[ 'type' ] ) && ! in_array( $acf_field[ 'type' ], array ( "message", "accordion", "tab" ) )) ) {
                        return;
                }

                $this->render_acf_field( $acf_field, $parent_field, $is_sub_fields );
        }

        public function render_acf_field( $field = array (), $parent_field = array (), $is_sub_fields = false ) {

                $field_type = isset( $field[ 'type' ] ) ? $field[ 'type' ] : "text";

                $title = isset( $field[ 'label' ] ) ? $field[ 'label' ] : $field_type;

                $field_key = isset( $field[ 'key' ] ) ? $field[ 'key' ] : "";

                $parent_field_key = "";

                if ( ! empty( $parent_field ) ) {

                        $parent_field_key = isset( $parent_field[ 'key' ] ) ? "[" . $parent_field[ 'key' ] . "]" : "";

                        $parent_field_type = isset( $parent_field[ 'type' ] ) ? $parent_field[ 'type' ] : "text";

                        if ( in_array( $parent_field_type, array ( "flexible_content", "repeater" ) ) ) {
                                $parent_field_key .= "[rch_row_number]";
                        }
                }

                if ( $is_sub_fields && ! empty( $parent_field_key ) ) {
                        $parent_field_key .= "[sub_fields]";
                }

                if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/extensions/acf/views/field_type.php' ) ) {
                        include RCH_IMPORT_CLASSES_DIR . '/extensions/acf/views/field_type.php';
                }
                if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/extensions/acf/views/' . $field_type . '.php' ) ) {
                        include RCH_IMPORT_CLASSES_DIR . '/extensions/acf/views/' . $field_type . '.php';
                }
                unset( $field_type, $title, $field_key );
        }

        protected function search_post_duplicate_item( $post_data = "" ) {

                if ( empty( $post_data ) ) {
                        return false;
                }
                global $wpdb;

                $post_id = false;

                if ( is_numeric( $post_data ) && absint( $post_data ) > 0 ) {

                        $_post = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 1", absint( $post_data ) ) );

                        if ( $_post ) {
                                $post_id = absint( $post_data );
                        }

                        unset( $_post );
                }

                if ( $post_id === false ) {

                        $post_id = $wpdb->get_var(
                                $wpdb->prepare(
                                        "SELECT ID FROM " . $wpdb->posts . "
                                WHERE post_title = %s
                                ", $post_data
                                )
                        );
                }

                return $post_id;
        }

        protected function search_taxonomy_duplicate_item( $tax_data = "" ) {

                if ( empty( $tax_data ) ) {
                        return false;
                }

                global $wpdb, $wp_version;

                $tax_id = false;

                if ( is_numeric( $tax_data ) && absint( $tax_data ) > 0 ) {

                        $term = get_term_by( 'id', $tax_data, "category" );

                        if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
                                $tax_id = absint( $tax_data );
                        }

                        unset( $term );
                }

                if ( $tax_id === false ) {

                        $args = array (
                                'get'                    => 'all',
                                'number'                 => 1,
                                'taxonomy'               => "category",
                                'update_term_meta_cache' => false,
                                'orderby'                => 'none',
                                'fields'                 => 'ids',
                                'suppress_filter'        => true,
                                'slug'                   => $tax_data
                        );

                        if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                $taxonomy_items = get_terms( "category", $args );
                        } else {
                                $taxonomy_items = get_terms( $args );
                        }

                        if ( ! empty( $taxonomy_items ) && ! is_wp_error( $taxonomy_items ) ) {
                                $tax_id = $taxonomy_items;
                        }
                }

                return $tax_id;
        }

        protected function search_user_duplicate_item( $user_data = "" ) {

                if ( empty( $user_data ) ) {
                        return false;
                }

                global $wpdb;

                $user_id = false;

                if ( is_numeric( $user_data ) && absint( $user_data ) > 0 ) {

                        $user = get_user_by( 'id', absint( $user_data ) );

                        if ( $user ) {
                                $user_id = absint( $user_data );
                        }

                        unset( $user );
                }

                if ( $user_id === false ) {

                        $user = get_user_by( 'email', $user_data );

                        if ( $user && isset( $user->ID ) ) {
                                $user_id = $user->ID;
                        }

                        unset( $user );
                }

                if ( $user_id === false ) {

                        $user = get_user_by( 'username', $user_data );

                        if ( $user && isset( $user->ID ) ) {
                                $user_id = $user->ID;
                        }

                        unset( $user );
                }

                return $user_id;
        }

        public function after_item_import( $item_id = 0, $item = null, $is_new_item = true ) {

                $this->item_id = $item_id;

                $this->item = $item;

                $this->is_new_item = $is_new_item;

                $acf_data = $this->get_field_value( 'acf' );

                $this->skip_empty = intval( $this->get_field_value( 'skip_empty', true ) ) === 1;

                if ( ! empty( $acf_data ) ) {

                        if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/extensions/acf/class-rch-acf-attachment.php' ) ) {
                                require_once( RCH_IMPORT_CLASSES_DIR . '/extensions/acf/class-rch-acf-attachment.php');
                        }

                        $this->attch_class = new \rch\import\acf\attachment\RCH_ACF_Attachment( $this->item_id, $this->is_new_item, $this->rch_import_option, $this->rch_import_record );

                        $acf = $this->process_acf( $acf_data );

                        if ( ! empty( $acf ) ) {

                                if ( function_exists( "\acf_update_values" ) ) {
                                        \acf_update_values( $acf, $this->item_id );
                                } else {
                                        foreach ( $acf as $k => $v ) {

                                                $f = apply_filters( 'acf/load_field', false, $k );

                                                do_action( 'acf/update_value', $v, $this->item_id, $f );
                                        }
                                }
                        }
                }

                unset( $acf );
        }

        private function process_acf( $acf_data = array () ) {

                $acf = array ();

                if ( ! empty( $acf_data ) && is_array( $acf_data ) ) {

                        foreach ( $acf_data as $key => $data ) {

                                if ( ( ! is_array( $data )) || empty( $data ) ) {
                                        continue;
                                }
                                $type = isset( $data[ 'type' ] ) ? trim( strtolower( sanitize_text_field( $data[ 'type' ] ) ) ) : "";

                                $value = null;

                                $is_bool = false;

                                switch ( $type ) {
                                        case "select":
                                        case "checkbox":
                                                $value = $this->process_choice_fields( $key, $data );

                                                break;
                                        case "radio":
                                        case "button_group":
                                                $value = $this->process_choice_fields( $key, $data );

                                                break;
                                        case "true_false":
                                                $value = $this->process_choice_fields( $key, $data );
                                                if ( is_array( $value ) ) {
                                                        $value = isset( $value[ 0 ] ) ? $value[ 0 ] : "";
                                                }
                                                $is_bool = true;

                                                break;
                                        case "image":
                                        case "file":
                                        case "gallery":
                                                $value = $this->process_file_fields( $key, $data );
                                                break;
                                        case "date_picker":
                                                $value = isset( $data[ 'value' ] ) && ! empty( $data[ 'value' ] ) ? date( "Ymd", strtotime( $data[ 'value' ] ) ) : "";
                                                break;
                                        case "post_object":
                                        case "relationship":
                                        case "page_link":
                                        case "user":
                                                $value = $this->prepare_post_fields( $key, $data );
                                                break;
                                        case "taxonomy":
                                                $value = $this->prepare_taxonomy_fields( $key, $data );
                                                break;
                                        case "group":
                                        case "clone":

                                                $value_option = isset( $data[ 'value_option' ] ) ? strtolower( trim( $data[ 'value_option' ] ) ) : "";

                                                if ( $value_option === "custom" ) {

                                                        $value = isset( $data[ 'custom_value' ] ) && ! empty( $data[ 'custom_value' ] ) ? json_decode( stripslashes_deep( $data[ 'custom_value' ] ), true ) : "";
                                                } else {
                                                        $sub_fields = isset( $data[ 'sub_fields' ] ) ? $data[ 'sub_fields' ] : [];

                                                        if ( ! empty( $sub_fields ) ) {
                                                                $value = $this->process_acf( $sub_fields );
                                                        }
                                                }

                                                unset( $value_option );

                                                break;

                                        case "repeater":
                                        case "flexible_content":

                                                $value_option = isset( $data[ 'value_option' ] ) ? strtolower( trim( $data[ 'value_option' ] ) ) : "";

                                                if ( $value_option === "custom" ) {

                                                        $value = isset( $data[ 'custom_value' ] ) && ! empty( $data[ 'custom_value' ] ) ? json_decode( stripslashes_deep( $data[ 'custom_value' ] ), true ) : "";
                                                } else {

                                                        foreach ( $data as $sub_key => $sub_data ) {

                                                                if ( trim( strtolower( $sub_key ) ) === "type" ) {
                                                                        continue;
                                                                }

                                                                $sub_fields = isset( $sub_data[ 'sub_fields' ] ) ? $sub_data[ 'sub_fields' ] : [];

                                                                if ( ! empty( $sub_fields ) ) {
                                                                        $value[] = $this->process_acf( $sub_fields );
                                                                }
                                                        }
                                                }

                                                break;
                                        case "wysiwyg":
                                                $value = isset( $data[ 'value' ] ) ? wp_kses_post( html_entity_decode( $data[ 'value' ] ) ) : "";
                                                break;

                                        default:
                                                $value = isset( $data[ 'value' ] ) ? $data[ 'value' ] : "";
                                                break;
                                }

                                if ( ( ! $this->skip_empty) || ($this->skip_empty && ! empty( $value )) ) {

                                        if ( $is_bool ) {

                                                if ( strtolower( trim( $value ) ) === "yes" || intval( $value ) === 1 ) {
                                                        $value = 1;
                                                } else {
                                                        $value = "";
                                                }
                                        }
                                        $acf[ $key ] = $value;
                                }

                                unset( $value, $type );
                        }
                }

                return $acf;
        }

        private function prepare_post_fields( $key = "", $data = [] ) {

                $value = null;

                $temp_value = isset( $data[ 'value' ] ) ? $data[ 'value' ] : "";

                if ( ! empty( $temp_value ) ) {

                        $_value_data = array ();

                        $delim = isset( $data[ 'delim' ] ) && ! empty( $data[ 'delim' ] ) ? sanitize_text_field( $data[ 'delim' ] ) : ",";

                        $_temp_data = explode( $delim, $temp_value );

                        $type = isset( $data[ 'type' ] ) ? trim( strtolower( sanitize_text_field( $data[ 'type' ] ) ) ) : "";

                        foreach ( $_temp_data as $_post_value ) {

                                if ( $type === "user" ) {
                                        $post_id = $this->search_user_duplicate_item( $_post_value );
                                } else {
                                        $post_id = $this->search_post_duplicate_item( $_post_value );
                                }
                                if ( $post_id && absint( $post_id ) > 0 ) {
                                        $_value_data[] = $post_id;
                                }
                        }

                        if ( ! empty( $_value_data ) ) {
                                $value = $_value_data;
                        }
                }

                return $value;
        }

        private function process_choice_fields( $key = "", $data = [] ) {

                $value = null;

                $value_option = isset( $data[ 'value_option' ] ) ? strtolower( trim( $data[ 'value_option' ] ) ) : "";

                if ( $value_option === "custom" ) {

                        $_value = isset( $data[ 'custom_value' ] ) ? $data[ 'custom_value' ] : "";

                        if ( ! empty( $_value ) ) {
                                $_value = explode( ",", $_value );

                                foreach ( $_value as $_data ) {

                                        $_new_data = explode( ":", $_data );

                                        if ( isset( $_new_data[ 1 ] ) ) {
                                                $value[ $_new_data[ 0 ] ] = $_new_data[ 1 ];
                                        } else {
                                                $value[] = $_data;
                                        }
                                }
                        }
                } else {
                        $value = isset( $data[ $key ] ) ? $data[ $key ] : "";
                }

                return $value;
        }

        private function process_file_fields( $key = "", $data = [] ) {

                $value = null;

                $value_data = isset( $data[ 'value' ] ) ? $data[ 'value' ] : "";

                if ( ! empty( $value_data ) ) {

                        $type = isset( $data[ 'type' ] ) ? trim( strtolower( sanitize_text_field( $data[ 'type' ] ) ) ) : "";

                        $is_search_through_media = isset( $data[ 'search_through_media' ] ) && ! empty( $data[ 'search_through_media' ] ) ? intval( strtolower( trim( $data[ 'search_through_media' ] ) ) ) === 1 : false;

                        $upload_dir_data = isset( $data[ 'use_upload_dir' ] ) ? strtolower( trim( $data[ 'use_upload_dir' ] ) ) === 1 : false;

                        if ( $type === "gallery" ) {

                                $_temp_value = explode( "\n", $value_data );

                                if ( ! isset( $_temp_value[ 1 ] ) ) {

                                        $delim = isset( $data[ 'delim' ] ) ? sanitize_text_field( $data[ 'delim' ] ) : "|";

                                        $value_data = explode( $delim, $value_data );

                                        unset( $delim );
                                } else {
                                        $value_data = $_temp_value;
                                }
                        }

                        $attach_ids = $this->attch_class->rch_get_file_from_url( $value_data, $type, $is_search_through_media, $upload_dir_data );

                        if ( $type != "gallery" && is_array( $attach_ids ) && ! empty( $attach_ids ) ) {
                                $attach_ids = implode( ",", $attach_ids );
                        } elseif ( empty( $attach_ids ) ) {
                                $attach_ids = "";
                        }

                        $value = $attach_ids;
                }
                return $value;
        }

        private function prepare_taxonomy_fields( $key = "", $data = [] ) {

                $value = null;

                $value_option = isset( $data[ 'value_option' ] ) ? strtolower( trim( $data[ 'value_option' ] ) ) : "";

                if ( $value_option === "custom" ) {

                        $_value = isset( $data[ 'custom_value' ] ) ? $data[ 'custom_value' ] : "";

                        if ( ! empty( $_value ) ) {
                                $_value = explode( ",", $_value );

                                foreach ( $_value as $_data ) {

                                        $tax_data = $this->search_taxonomy_duplicate_item( $_data );

                                        if ( $tax_data && absint( $tax_data ) > 0 ) {

                                                $value[] = $tax_data;
                                        }
                                }
                        }
                } else {
                        $value = isset( $data[ $key ] ) ? $data[ $key ] : "";
                }

                return $value;
        }

        private function acf_get_field( $key ) {

                if ( function_exists( "acf_get_field" ) ) {
                        return acf_get_field( $key );
                } else {
                        return apply_filters( 'acf/load_field', false, $key );
                }
        }

}
