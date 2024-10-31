<?php

namespace rch\import\acf\attachment;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php');
}

if ( file_exists( ABSPATH . 'wp-admin/modules/image.php' ) ) {
        require_once(ABSPATH . 'wp-admin/modules/image.php');
}

class RCH_ACF_Attachment extends \rch\import\base\RCH_Import_Base {

        private $target_dir;

        public function __construct( $item_id = 0, $is_new_item = true, $rch_import_option = array(), $rch_import_record = array() ) {


                $this->item_id = $item_id;

                $this->is_new_item = $is_new_item;

                $this->rch_import_option = $rch_import_option;

                $this->rch_import_record = $rch_import_record;

                $wp_uploads = wp_upload_dir();

                $this->target_dir = isset( $wp_uploads[ 'path' ] ) ? $wp_uploads[ 'path' ] : "";

                unset( $wp_uploads );
        }

        public function rch_get_file_from_url( $file_url = "", $type = "image", $is_search_through_media = false, $upload_dir_data = false ) {

                if ( empty( $file_url ) ) {
                        return false;
                }

                if ( ! is_array( $file_url ) ) {
                        $file_url = array( $file_url );
                }

                $data = array();

                foreach ( $file_url as $file ) {

                        $filename = basename( $file );

                        $attach_id = false;

                        if ( $is_search_through_media ) {

                                $attch_image = $this->rch_get_file_from_gallery( $filename );

                                if ( ! is_wp_error( $attch_image ) && isset( $attch_image->ID ) ) {

                                        $attach_id = $attch_image->ID;
                                }
                                unset( $attch_image );
                        }
                        if ( $attach_id === false && $upload_dir_data ) {

                                $attch_data = $this->rch_get_file_from_local( $filename, RCH_UPLOAD_TEMP_DIR . "/" . $filename );

                                if ( ! ($attch_data === false || is_wp_error( $attch_data )) ) {
                                        $attach_id = $attch_data;
                                }
                                unset( $attch_data );
                        }

                        if ( $attach_id === false || empty( $attach_id ) ) {

                                $attch_data = false;

                                if ( file_exists( ABSPATH . 'wp-admin/modules/media.php' ) ) {
                                        require_once(ABSPATH . 'wp-admin/modules/media.php');
                                }
                                if ( file_exists( ABSPATH . 'wp-admin/modules/file.php' ) ) {
                                        require_once(ABSPATH . 'wp-admin/modules/file.php');
                                }
                                if ( $type === "file" ) {
                                        $attch_data = $this->download_atatchment( $file );
                                } else {

                                        $attch_data = media_sideload_image( $file, $this->item_id, '', 'id' );
                                }

                                if ( ! ($attch_data === false || empty( $attch_data ) || is_wp_error( $attch_data )) ) {
                                        $attach_id = $attch_data;
                                }
                                unset( $attch_data );
                        }
                        if ( $attach_id !== false ) {
                                $data[] = absint( $attach_id );
                        }

                        unset( $attach_id, $filename );
                }

                return $data;
        }

        private function rch_get_file_from_local( $file = "" ) {

                if ( ( ! wp_is_writable( $this->target_dir )) || empty( $file ) || ( ! file_exists( $file )) ) {
                        return false;
                }

                return $this->download_atatchment( $file );
        }

        private function download_atatchment( $file = "" ) {

                if ( empty( $file ) ) {
                        return false;
                }
                $filename = basename( $file );

                $upload_file = wp_upload_bits( $filename, null, file_get_contents( $file ) );

                if ( ! $upload_file[ 'error' ] ) {

                        $wp_filetype = wp_check_filetype( $filename, null );

                        $attachment = array(
                                'post_mime_type' => $wp_filetype[ 'type' ],
                                'post_parent'    => $this->item_id,
                                'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
                                'post_content'   => '',
                                'post_status'    => 'inherit',
                                'post_author'    => $this->get_post_user()
                        );

                        $attachment_id = wp_insert_attachment( $attachment, $upload_file[ 'file' ], $this->item_id );

                        if ( ! is_wp_error( $attachment_id ) ) {

                                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file[ 'file' ] );

                                wp_update_attachment_metadata( $attachment_id, $attachment_data );

                                unset( $attachment_data );
                        }

                        unset( $attachment, $wp_filetype, $upload_file, $filename );

                        return $attachment_id;
                }

                unset( $upload_file, $filename );

                return false;
        }

        private function get_post_user() {

                $user_id = 0;
                if ( ! empty( $this->item_id ) ) {
                        $post = get_post( $this->item_id );
                        if ( $post && isset( $post->post_author ) && ! empty( $post->post_author ) ) {
                                $user_id = $post->post_author;
                        }
                        unset( $post );
                }
                if ( $user_id === 0 ) {
                        if ( ! empty( $this->import_username ) ) {

                                $user = get_user_by( "login", $this->import_username );

                                if ( $user && isset( $user->ID ) ) {
                                        $user_id = $user->ID;
                                }
                                unset( $user );
                        }
                }
                if ( $user_id === 0 ) {

                        $current_user = wp_get_current_user();

                        if ( $current_user && isset( $current_user->ID ) ) {
                                $user_id = $current_user->ID;
                        }
                        unset( $current_user );
                }
                return $user_id;
        }

        private function rch_get_file_from_gallery( $attach_name = "", $attch_type = "image" ) {

                if ( empty( $attach_name ) ) {
                        return false;
                }
                global $wpdb;

                $original_image_name = $attach_name;

                $attch = false;

                $attachment_metas = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key = %s AND (meta_value = %s OR meta_value LIKE %s);", '_wp_attached_file', $attach_name, "%/" . $attach_name ) );

                if ( ! empty( $attachment_metas ) ) {

                        foreach ( $attachment_metas as $attachment_meta ) {
                                $attch = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->posts . " WHERE ID = %d", $attachment_meta->post_id ) );
                                if ( ! empty( $attch ) ) {
                                        break;
                                }
                        }
                }

                if ( empty( $attch ) ) {
                        $attachment_metas = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key = %s AND (meta_value = %s OR meta_value LIKE %s);", '_wp_attached_file', $attach_name, "%/" . $attach_name ) );

                        if ( ! empty( $attachment_metas ) ) {
                                foreach ( $attachment_metas as $attachment_meta ) {
                                        $attch = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->posts . " WHERE ID = %d", $attachment_meta->post_id ) );
                                        if ( ! empty( $attch ) ) {
                                                break;
                                        }
                                }
                        }
                }
                unset( $attachment_metas );

                if ( empty( $attch ) ) {

                        $wp_filetype = wp_check_filetype( basename( $attach_name ), NULL );

                        if ( ! empty( $wp_filetype[ 'type' ] ) ) {
                                $attch = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->posts . " WHERE (post_title = %s OR post_title = %s OR post_name = %s) AND post_type = %s AND post_mime_type = %s;", $attach_name, preg_replace( '/\\.[^.\\s]{3,4}$/', '', $attach_name ), $attach_name, "attachment", $wp_filetype[ 'type' ] ) );
                        }

                        if ( empty( $attch ) && ! empty( $wp_filetype[ 'type' ] ) ) {

                                $attachment_title = explode( ".", $attach_name );

                                if ( is_array( $attachment_title ) && count( $attachment_title ) > 1 ) {
                                        array_pop( $attachment_title );
                                }

                                $attach_name = implode( ".", $attachment_title );

                                unset( $attachment_title );

                                $attch = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->posts . " WHERE (post_title = %s OR post_title = %s OR post_name = %s) AND post_type = %s AND post_mime_type LIKE %s;", $attach_name, preg_replace( '/\\.[^.\\s]{3,4}$/', '', $attach_name ), $attach_name, "attachment", $wp_filetype[ 'type' ] ) );
                        }
                        unset( $wp_filetype );
                }

                if ( empty( $attch ) && file_exists( $this->target_dir . "/" . $original_image_name ) ) {

                        if ( empty( $attch ) && file_exists( $this->target_dir . "/" . $original_image_name ) ) {
                                unlink( $this->target_dir . "/" . $original_image_name );
                        }
                }

                return apply_filters( 'rch_get_file_from_gallery', $attch, $original_image_name, $this->target_dir );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
