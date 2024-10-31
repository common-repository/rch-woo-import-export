<?php

namespace rch\import\comment;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-engine.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-engine.php');
}

class RCH_Comment extends \rch\import\engine\RCH_Import_Engine {

        protected $import_type = "comment";
        protected $post_id = 0;

        public function process_import_data() {

                global $wpdb;

                $this->search_post_item();

                if ( $this->post_id == 0 ) {

                        $this->set_log( "<strong>" . __( 'ERROR', 'rch-woo-import-export' ) . '</strong> : ' . __( 'Post not found', 'rch-woo-import-export' ) );

                        $this->process_log[ 'skipped' ] ++;

                        $this->process_log[ 'imported' ] ++;

                        return true;
                }

                if ( $this->is_update_field( "post_id" ) ) {

                        $this->rch_final_data[ 'comment_post_ID' ] = $this->post_id;
                }

                if ( $this->is_update_field( "author" ) ) {
                        $this->rch_final_data[ 'comment_author' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_comment_author' ) );
                }
                if ( $this->is_update_field( "author_email" ) ) {
                        $this->rch_final_data[ 'comment_author_email' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_comment_author_email' ) );
                }
                if ( $this->is_update_field( "author_url" ) ) {
                        $this->rch_final_data[ 'comment_author_url' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_comment_author_url' ) );
                }
                if ( $this->is_update_field( "author_ip" ) ) {
                        $this->rch_final_data[ 'comment_author_IP' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_comment_author_ip' ) );
                }
                if ( $this->is_update_field( "date" ) ) {
                        $comment_date = rch_sanitize_field( $this->get_field_value( 'rch_item_comment_date' ) );

                        if ( empty( trim( $comment_date ) ) || strtotime( $comment_date ) === false ) {
                                $comment_date = current_time( 'mysql' );
                        }

                        $this->rch_final_data[ 'comment_date' ] = date( 'Y-m-d H:i:s', strtotime( $comment_date ) );
                }
                if ( $this->is_update_field( "content" ) ) {
                        $this->rch_final_data[ 'comment_content' ] = rch_sanitize_textarea( $this->get_field_value( 'rch_item_comment_content' ) );
                }
                if ( $this->is_update_field( "karma" ) ) {
                        $this->rch_final_data[ 'comment_karma' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_comment_karma' ) );
                }
                if ( $this->is_update_field( "approved" ) ) {
                        $this->rch_final_data[ 'comment_approved' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_comment_approved' ) );
                }
                if ( $this->is_update_field( "agent" ) ) {
                        $this->rch_final_data[ 'comment_agent' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_comment_agent' ) );
                }
                if ( $this->is_update_field( "type" ) ) {
                        $this->rch_final_data[ 'comment_type' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_comment_type' ) );
                }
                if ( $this->is_update_field( "parent" ) ) {
                        $this->rch_final_data[ 'comment_parent' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_comment_parent' ) );
                }

                $this->rch_final_data = apply_filters( 'rch_before_comment_import', $this->rch_final_data, $this->rch_import_option, $this->rch_import_record );

                if ( $this->is_new_item ) {

                        $this->item_id = wp_insert_comment( $this->rch_final_data );

                        $this->process_log[ 'imported' ] ++;

                        if ( $this->item_id === false ) {

                                $this->set_log( "<strong>" . __( 'ERROR', 'rch-woo-import-export' ) . '</strong> : ' . __( 'Fail to insert comment', 'rch-woo-import-export' ) );

                                $this->process_log[ 'skipped' ] ++;

                                return true;
                        }

                        $this->process_log[ 'created' ] ++;
                } else {

                        $this->rch_final_data[ 'comment_ID' ] = $this->existing_item_id;

                        $is_success = wp_update_comment( $this->rch_final_data );

                        $this->item_id = $this->existing_item_id;

                        $this->process_log[ 'imported' ] ++;

                        if ( $is_success == 0 ) {

                                $this->set_log( "<strong>" . __( 'ERROR', 'rch-woo-import-export' ) . '</strong> : ' . __( 'Fail to Update comment', 'rch-woo-import-export' ) );

                                $this->process_log[ 'skipped' ] ++;

                                return true;
                        }
                        unset( $is_success );

                        $this->process_log[ 'updated' ] ++;
                }

                if ( $this->backup_service !== false && $this->is_new_item ) {
                        $this->backup_service->create_backup( $this->item_id, true );
                }

                $wpdb->update( $wpdb->prefix . "rch_template", array( 'last_update_date' => current_time( 'mysql' ),
                        'process_log'      => maybe_serialize( $this->process_log ) ), array(
                        'id' => $this->rch_import_id ) );

                do_action( 'rch_after_comment_import', $this->item_id, $this->rch_final_data, $this->rch_import_option );

                if ( $this->is_update_field( "cf" ) ) {

                        $this->rch_import_cf();
                }

                return $this->item_id;
        }

        protected function search_duplicate_item() {

                global $wpdb;

                $rch_duplicate_indicator = strtolower( trim( rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic', true ) ) ) );

                if ( $rch_duplicate_indicator === "id" ) {

                        $duplicate_id = absint( rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_id' ) ) );

                        if ( $duplicate_id > 0 ) {

                                $comment = get_comment( $duplicate_id );

                                if ( ! empty( $comment ) ) {
                                        $this->existing_item_id = $duplicate_id;
                                }

                                unset( $comment );
                        }
                        unset( $duplicate_id );
                } elseif ( $rch_duplicate_indicator === "content" ) {

                        $content = rch_sanitize_textarea( $this->get_field_value( 'rch_item_comment_content' ) );

                        if ( ! empty( $content ) ) {

                                $comment_id = $wpdb->get_var( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_content IN (%s,%s) ORDER BY `comment_ID` ASC limit 0,1", $content, preg_replace( '%[ \\t\\n]%', '', $content ) ) );

                                if ( $comment_id && $comment_id > 0 ) {
                                        $this->existing_item_id = absint( $comment_id );
                                }
                                unset( $comment_id );
                        }
                        unset( $content );
                } elseif ( $rch_duplicate_indicator === "cf" ) {

                        $meta_key = rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_cf_key' ) );

                        $meta_val = rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_cf_value' ) );

                        if ( ! empty( $meta_key ) ) {

                                $args = array(
                                        'number'     => 1,
                                        'offset'     => 0,
                                        'fields'     => "ids",
                                        'meta_key'   => $meta_key,
                                        'meta_value' => $meta_val,
                                        'orderby'    => 'comment_ID',
                                        'order'      => 'ASC '
                                );

                                $comments = get_comments( $args );

                                if ( ! empty( $comments ) && ! is_wp_error( $comments ) ) {
                                        foreach ( $comments as $comment ) {
                                                $this->existing_item_id = $comment->comment_ID;
                                                break;
                                        }
                                }
                                unset( $comments, $args );
                        }

                        unset( $meta_key, $meta_val );
                }

                unset( $rch_duplicate_indicator );
        }

        protected function search_post_item() {

                global $wpdb;

                $post_types = $this->get_field_value( 'rch_comment_parent_include_post_types' );

                if ( empty( $post_types ) ) {

                        unset( $post_types );

                        return;
                }

                $post_indicator = strtolower( trim( $this->get_field_value( 'rch_item_search_post_based_on', true ) ) ) === "id" ? "id" : "title";

                if ( $post_indicator === "id" ) {

                        $post_id = absint( rch_sanitize_field( $this->get_field_value( 'rch_item_comment_post_id' ) ) );

                        if ( $post_id > 0 ) {
                                $_post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 0,1", $post_id ) );

                                if ( $_post && absint( $_post ) > 0 ) {
                                        $this->post_id = absint( $_post );
                                }
                                unset( $_post );
                        }
                        unset( $post_id );
                } else {


                        $title = $this->get_field_value( "rch_item_comment_post_title" );

                        if ( ! empty( $title ) ) {
                                $_post = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "SELECT ID FROM " . $wpdb->posts . "
                                WHERE
                                    post_type IN ('" . implode( "','", $post_types ) . "')
                                    AND ID != 0
                                    AND post_title = %s
                                LIMIT 1
                                ", $title
                                        )
                                );


                                if ( $_post && absint( $_post ) > 0 ) {
                                        $this->post_id = absint( $_post );
                                }
                                unset( $_post );
                        }

                        unset( $title );
                }
                unset( $post_indicator );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
