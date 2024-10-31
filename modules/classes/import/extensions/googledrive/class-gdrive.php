<?php

namespace rch\import\upload\googledrive;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php' ) ) {
        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php');
}

class RCH_GDrive extends \rch\import\upload\RCH_Upload {

        public function __construct() {
                
        }

        public function download_gdrive_file() {

                if ( ! is_dir( RCH_UPLOAD_IMPORT_DIR ) || ! wp_is_writable( RCH_UPLOAD_IMPORT_DIR ) ) {

                        return new \WP_Error( 'rch_import_error', __( 'Uploads folder is not writable', 'rch-woo-import-export' ) );
                }

                $rch_import_id = isset( $_POST[ 'rch_import_id' ] ) ? intval( rch_sanitize_field( $_POST[ 'rch_import_id' ] ) ) : 0;

                $fileId = isset( $_POST[ "fileId" ] ) ? rch_sanitize_field( $_POST[ "fileId" ] ) : '';

                $oAuthToken = isset( $_POST[ "oAuthToken" ] ) ? rch_sanitize_field( $_POST[ "oAuthToken" ] ) : "";

                $fileName = isset( $_POST[ "name" ] ) ? rch_sanitize_field( $_POST[ "name" ] ) : '';

                $newfiledir = parent::rch_create_safe_dir_name( $fileName );

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir );

                $newFilePath = RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir;

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original" );

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse" );

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse/chunks" );

                $filePath = RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName;

                chmod( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/", 0755 );

                $file_url = 'https://www.googleapis.com/drive/v2/files/' . $fileId . '?alt=media';

                $response = wp_safe_remote_get( $file_url, array( 'timeout' => 3000, 'stream' => true, 'filename' => $filePath, "headers" => array( 'Authorization' => 'Bearer ' . $oAuthToken ) ) );

                unset( $file_url, $oAuthToken, $fileId );

                if ( is_wp_error( $response ) ) {
                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }
                        unset( $rch_import_id, $fileName, $newfiledir, $newFilePath, $filePath );

                        return $response;
                }

                if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }
                        unset( $rch_import_id, $fileName, $newfiledir, $newFilePath, $filePath );
                        return new \WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
                }

                $content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );

                unset( $response );

                if ( $content_md5 ) {
                        $md5_check = verify_file_md5( $filePath, $content_md5 );
                        if ( is_wp_error( $md5_check ) ) {

                                if ( file_exists( $filePath ) ) {
                                        unlink( $filePath );
                                }

                                unset( $rch_import_id, $fileName, $newfiledir, $newFilePath, $filePath, $content_md5 );

                                return $md5_check;
                        }

                        unset( $md5_check );
                }

                unset( $newFilePath, $filePath, $content_md5 );

                return parent::rch_manage_import_file( $fileName, $newfiledir, $rch_import_id );
        }

}
