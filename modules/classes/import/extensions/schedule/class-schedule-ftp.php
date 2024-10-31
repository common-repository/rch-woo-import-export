<?php

namespace rch\import\schedule;

use \rch\import\upload\ftp\RCH_FTP_SFTP;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php');
}

class Schedule_Ftp extends Schedule_Base {

        public function __construct( $options = [] ) {

                $this->options = $options;

                parent::generate_template();

                $this->process_upload_files();
        }

        private function process_upload_files() {

                if ( $this->downlod_file() === false || $this->validate_upload() === false ) {

                        $this->delete_template();

                        return false;
                }
                return true;
        }

        private function downlod_file() {

                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/ftp-sftp/class-ftp-sftp.php';

                if ( ! file_exists( $fileName ) ) {

                        return false;
                }

                require_once($fileName);

                $upload = new RCH_FTP_SFTP();

                $ftp_details = isset( $this->options [ "rch_ftp_details" ] ) ? rch_sanitize_field( $this->options [ "rch_ftp_details" ] ) : '';

                if ( ! empty( $ftp_details ) ) {

                        $ftp_details = json_decode( wp_unslash( $ftp_details ), true );

                        if ( is_array( $ftp_details ) && ! empty( $ftp_details ) ) {

                                $hostname = isset( $ftp_details[ "host" ] ) ? rch_sanitize_field( $ftp_details[ "host" ] ) : '';

                                $host_port = isset( $ftp_details[ "post" ] ) && absint( $ftp_details[ "post" ] ) > 0 ? absint( rch_sanitize_field( $ftp_details[ "post" ] ) ) : 21;

                                $host_username = isset( $ftp_details[ "username" ] ) ? rch_sanitize_field( $ftp_details[ "username" ] ) : '';

                                $host_password = isset( $ftp_details[ "password" ] ) ? rch_sanitize_field( $ftp_details[ "password" ] ) : '';

                                $host_path = isset( $ftp_details[ "path" ] ) ? rch_sanitize_field( $ftp_details[ "path" ] ) : '';

                                $connection_arguments = array(
                                        'port'     => $host_port,
                                        'hostname' => $hostname,
                                        'username' => $host_username,
                                        'password' => $host_password,
                                );

                                $file_list = $upload->rch_download_file_from_ftp( $connection_arguments, $host_path, $this->id );

                                if ( ! is_wp_error( $file_list ) ) {
                                        unset( $upload, $file );
                                        return $file_list;
                                }
                        }
                }
                unset( $upload, $ftp_details );

                return false;
        }

}
