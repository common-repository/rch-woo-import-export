<?php

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

class RCH_Existing_File_Upload_Extension {

        public function __construct() {

                add_filter( 'rch_import_upload_sections', array( $this, 'get_existing_file_view' ), 10, 1 );

                add_action( 'wp_ajax_rch_import_set_existing_file', array( $this, 'prepare_existing_file' ) );
        }

        public function get_existing_file_view( $rch_sections = array() ) {

                $rch_sections[ "rch_import_existing_file_upload" ] = array(
                        "label" => __( "Use existing file", 'rch-woo-import-export' ),
                        "icon"  => 'fas fa-paperclip',
                        "view"  => RCH_IMPORT_CLASSES_DIR . "/extensions/existing-file/rch-existing-file-view.php",
                );

                return $rch_sections;
        }

        public function prepare_existing_file() {

                $require_file = RCH_IMPORT_CLASSES_DIR . '/extensions/existing-file/class-existing-file.php';

                if ( file_exists( $require_file ) ) {

                        require_once($require_file);
                }

                $upload = new \rch\import\upload\existingfile\RCH_Existing_File();

                $fileName = isset( $_GET[ 'file_name' ] ) ? rch_sanitize_field( $_GET[ 'file_name' ] ) : "";
                $rch_import_id = isset( $_GET[ 'rch_import_id' ] ) ? absint( rch_sanitize_field( $_GET[ 'rch_import_id' ] ) ) : 0;

                $file = $upload->rch_upload_file( $fileName, $rch_import_id );

                unset( $fileName );

                $return_value = array( 'status' => 'error' );

                if ( is_wp_error( $file ) ) {
                        $return_value[ 'message' ] = $file->get_error_message();
                } elseif ( empty( $file ) ) {
                        $return_value[ 'erorr_message' ] = __( 'Failed to upload files', 'rch-woo-import-export' );
                } elseif ( $file == "processing" ) {
                        $return_value[ 'status' ] = 'success';
                        $return_value[ 'message' ] = 'processing';
                } else {

                        $return_value[ 'file_list' ] = isset( $file[ 'file_list' ] ) ? $file[ 'file_list' ] : array();

                        $return_value[ 'file_count' ] = count( $return_value[ 'file_list' ] );

                        $return_value[ 'rch_import_id' ] = isset( $file[ 'rch_import_id' ] ) ? $file[ 'rch_import_id' ] : 0;

                        $return_value[ 'file_name' ] = isset( $file[ 'file_name' ] ) ? $file[ 'file_name' ] : "";

                        $return_value[ 'file_size' ] = isset( $file[ 'file_size' ] ) ? $file[ 'file_size' ] : "";

                        $return_value[ 'status' ] = 'success';
                }
                unset( $file );

                echo json_encode( $return_value );

                unset( $return_value );

                die();
        }

}

new RCH_Existing_File_Upload_Extension();
