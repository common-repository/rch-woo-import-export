<?php

namespace rch\import\upload\url;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}
if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php' ) ) {
        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php');
}

class RCH_URL_Upload extends \rch\import\upload\RCH_Upload {

        public function __construct() {
                
        }

        public function rch_download_file_from_url( $rch_import_id = 0, $file_url = "" ) {

                if ( empty( $file_url ) ) {

                        return new \WP_Error( 'rch_import_error', __( 'File URL is empty', 'rch-woo-import-export' ) );
                } elseif ( ! is_dir( RCH_UPLOAD_IMPORT_DIR ) || ! wp_is_writable( RCH_UPLOAD_IMPORT_DIR ) ) {

                        return new \WP_Error( 'rch_import_error', __( 'Uploads folder is not writable', 'rch-woo-import-export' ) );
                }

                $file_url = $this->process_url( $file_url );

                $file_data = $this->download_file( $file_url );

                if ( is_wp_error( $file_data ) ) {

                        return $file_data;
                }

                $fileName = $file_data[ 'name' ] ? $file_data[ 'name' ] : "";

                $tempName = $file_data[ 'tmp_name' ] ? $file_data[ 'tmp_name' ] : "";

                if ( ! preg_match( '%\W(xml|zip|csv|xls|xlsx|xml|ods|txt|json)$%i', trim( $fileName ) ) ) {
                        return new \WP_Error( 'invalid_image', __( 'Invalid File Extension', 'rch-woo-import-export' ) );
                }

                $newfiledir = parent::rch_create_safe_dir_name( $fileName );

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir );

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original" );

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse" );

                wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse/chunks" );

                $filePath = RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName;

                chmod( RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/", 0755 );

                copy( $tempName, RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName );

                unlink( $tempName );

                unset( $file_url, $filePath );

                return parent::rch_manage_import_file( $fileName, $newfiledir, $rch_import_id );
        }

        private function process_url( $link = "", $format = 'csv' ) {

                if ( empty( $link ) ) {
                        return $link;
                }

                $link = str_replace( " ", "%20", $link );

                preg_match( '/(?<=.com\/).*?(?=\/d)/', $link, $match );

                if ( isset( $match[ 0 ] ) && ! empty( $match[ 0 ] ) ) {
                        $type = $match[ 0 ];
                } else {
                        $type = null;
                }

                $parse = parse_url( $link );
                $domain = isset( $parse[ 'host' ] ) ? $parse[ 'host' ] : '';
                unset( $match, $parse );
                
                if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $match ) ) {
                        $domain = isset( $match[ 'domain' ] ) ? $match[ 'domain' ] : "";
                }
                unset( $match );

                if ( ! empty( $domain ) ) {
                        switch ( $domain ) {
                                  case 'google.com':
                                        if ( ! empty( $type ) ) {
                                                switch ( $type ) {
                                                        case 'file':
                                                                $pattern = '/(?<=\/file\/d\/).*?(?=\/edit)/';
                                                                preg_match( $pattern, $link, $match );
                                                                $file_id = isset( $match[ 0 ] ) ? $match[ 0 ] : null;
                                                                if ( ! empty( $file_id ) ) {
                                                                        $link = 'https://drive.google.com/uc?export=download&id=' . $file_id;
                                                                }
                                                                break;
                                                        case 'spreadsheets':
                                                                $pattern = '/(?<=\/spreadsheets\/d\/).*?(?=\/edit)/';
                                                                preg_match( $pattern, $link, $match );
                                                                $file_id = isset( $match[ 0 ] ) ? $match[ 0 ] : null;
                                                                if ( ! empty( $file_id ) ) {
                                                                        $link = 'https://docs.google.com/spreadsheets/d/' . $file_id . '/export?format=' . $format;
                                                                }
                                                                break;
                                                }
                                        }
                                        break;
                        }
                }
                return $link;
        }

        private function download_file( $file_url = "" ) {

                if ( empty( $file_url ) ) {
                        return new \WP_Error( 'http_404', __( 'Empty Image URL', 'rch-woo-import-export' ) );
                }

                $fileName = time() . rand() . ".tmp";

                $filePath = RCH_UPLOAD_TEMP_DIR . "/" . $fileName;

                $response = wp_safe_remote_get( $file_url, array( 'timeout' => 3000, 'stream' => true, 'filename' => $filePath ) );

                if ( is_wp_error( $response ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        return $response;
                }

                if ( 200 != wp_remote_retrieve_response_code( $response ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        return new \WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
                }

                $content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );

                if ( $content_md5 ) {

                        $md5_check = verify_file_md5( $filePath, $content_md5 );

                        if ( is_wp_error( $md5_check ) ) {

                                if ( file_exists( $filePath ) ) {
                                        unlink( $filePath );
                                }

                                return $md5_check;
                        }

                        unset( $md5_check );
                }

                $original_name = $this->get_filename_from_headers( $response, $file_url );

                if ( is_wp_error( $original_name ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        return $original_name;
                }

                preg_match( '/[^\?]+\.(xml|zip|csv|xls|xlsx|xml|ods|txt|json)\b/i', strtolower( trim( $original_name ) ), $matches );

                if ( ! $matches ) {
                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }
                        return new \WP_Error( 'invalid_image', __( 'Invalid File Extension', 'rch-woo-import-export' ) );
                }

                return [ "name" => $original_name, "tmp_name" => $filePath ];
        }

        private function get_filename_from_headers( $response = "", $file_url = "" ) {

                $header_content_disposition = wp_remote_retrieve_header( $response, 'content-disposition' );

                $default_filename = basename( parse_url( $file_url, PHP_URL_PATH ) );

                if ( empty( $header_content_disposition ) ) {
                        return $default_filename;
                }

                $regex = '/.*?filename=(?<fn>[^\s]+|\x22[^\x22]+\x22)\x3B?.*$/m';

                $new_file_data = null;

                $original_name = "";

                if ( preg_match( $regex, $header_content_disposition, $new_file_data ) ) {

                        if ( isset( $new_file_data[ 'fn' ] ) && ! empty( $new_file_data[ 'fn' ] ) ) {
                                $wp_filetype = wp_check_filetype( $new_file_data[ 'fn' ] );
                                if ( isset( $wp_filetype[ 'ext' ] ) && ( ! empty( $wp_filetype[ 'ext' ] )) && isset( $wp_filetype[ 'type' ] ) && ( ! empty( $wp_filetype[ 'type' ] )) ) {
                                        $original_name = $new_file_data[ 'fn' ];
                                }
                        }
                }

                if ( empty( $original_name ) ) {

                        $regex = '/.*filename=([\'\"]?)([^\"]+)\1/';

                        if ( preg_match( $regex, $header_content_disposition, $new_file_data ) ) {

                                if ( isset( $new_file_data[ '2' ] ) && ! empty( $new_file_data[ '2' ] ) ) {
                                        $wp_filetype = wp_check_filetype( $new_file_data[ '2' ] );
                                        if ( isset( $wp_filetype[ 'ext' ] ) && ( ! empty( $wp_filetype[ 'ext' ] )) && isset( $wp_filetype[ 'type' ] ) && ( ! empty( $wp_filetype[ 'type' ] )) ) {
                                                $original_name = $new_file_data[ '2' ];
                                        }
                                }
                        }
                }
                if ( empty( $original_name ) ) {
                        $original_name = $default_filename;
                }

                return preg_replace( "/[^a-z0-9\_\-\.]/i", '', preg_replace( '#[ -]+#', '-', $original_name ) );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
