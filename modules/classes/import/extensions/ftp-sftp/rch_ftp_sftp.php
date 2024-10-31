<?php

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

class RCH_FTP_SFTP_Extension {

        public function __construct() {
                add_filter( 'rch_import_upload_sections', array( $this, 'get_ftp_upload_views' ), 10, 1 );

                add_action( 'wp_ajax_rch_import_upload_file_from_ftp', array( $this, 'upload_file_from_ftp' ) );
        }

        public function get_ftp_upload_views( $rch_sections = array() ) {

                $rch_sections[ "rch_import_ftp_file_upload" ] = array(
                        "label" => __( "Upload From FTP/SFTP", 'rch-woo-import-export' ),
                        "icon"  => 'fas fa-cloud-upload-alt',
                        "view"  => RCH_IMPORT_CLASSES_DIR . "/extensions/ftp-sftp/rch-ftp-sftp-view.php",
                );

                return $rch_sections;
        }

        public function upload_file_from_ftp() {

                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/ftp-sftp/class-ftp-sftp.php';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);
                }

                $rch_import_id = isset( $_POST[ 'rch_import_id' ] ) ? intval( rch_sanitize_field( $_POST[ 'rch_import_id' ] ) ) : 0;

                $hostname = isset( $_POST[ "hostname" ] ) ? rch_sanitize_field( $_POST[ "hostname" ] ) : '';

                $host_port = isset( $_POST[ "host_port" ] ) && absint( sanitize_text_field($_POST[ "host_port" ]) ) > 0 ? absint( rch_sanitize_field( $_POST[ "host_port" ] ) ) : 21;

                $host_username = isset( $_POST[ "username" ] ) ? rch_sanitize_field( $_POST[ "username" ] ) : '';

                $host_password = isset( $_POST[ "password" ] ) ? rch_sanitize_field( $_POST[ "password" ] ) : '';

                $host_path = isset( $_POST[ "host_path" ] ) ? rch_sanitize_field( $_POST[ "host_path" ] ) : '';

                $connection_arguments = array(
                        'port'     => $host_port,
                        'hostname' => $hostname,
                        'username' => $host_username,
                        'password' => $host_password,
                );

                $upload = new \rch\import\upload\ftp\RCH_FTP_SFTP();

                $file = $upload->rch_download_file_from_ftp( $connection_arguments,$host_path, $rch_import_id );

                unset( $fileName, $upload );

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

                echo json_encode( $return_value );

                die();
        }

}

new RCH_FTP_SFTP_Extension();
