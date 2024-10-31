<?php

namespace rch\import\upload\existingfile;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php' ) ) {
        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php');
}

class RCH_Existing_File extends \rch\import\upload\RCH_Upload {

        public function __construct() {
                
        }

        public function rch_upload_file( $fileName = "", $rch_import_id = "" ) {

                if ( empty( $fileName ) ) {

                        unset( $fileName );

                        return new \WP_Error( 'rch_import_error', __( 'File Name is empty', 'rch-woo-import-export' ) );
                }

                $filePath = RCH_UPLOAD_MAIN_DIR . "/" . $fileName;

                if ( ! file_exists( $filePath ) ) {

                        unset( $fileName, $filePath );

                        return new \WP_Error( 'rch_import_error', __( 'File not exist', 'rch-woo-import-export' ) );
                }


                chmod( $filePath, 0755 );

                $newfiledir = parent::rch_create_safe_dir_name( $fileName );

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir );

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original" );

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse" );

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse/chunks" );

                copy( $filePath, RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName );

                unset( $filePath );

                return parent::rch_manage_import_file( $fileName, $newfiledir, $rch_import_id );
        }

}
