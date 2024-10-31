<?php

namespace rch\import\schedule;

use rch\import\upload\url\RCH_URL_Upload;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php');
}

class Schedule_Url extends Schedule_Base {

        public function __construct( $options = [] ) {

                $this->options = $options;

                parent::generate_template();

                return $this->process_upload_files();
        }

        private function process_upload_files() {

                if ( $this->downlod_file() === false || $this->validate_upload() === false ) {

                        $this->delete_template();

                        return false;
                }
                return true;
        }

        private function downlod_file() {

                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/url-upload/class-rch-url-upload.php';

                if ( ! file_exists( $fileName ) ) {

                        return false;
                }

                require_once($fileName);

                $upload = new RCH_URL_Upload();

                $file_url = isset( $this->options[ "rch_upload_final_file_url" ] ) ? esc_url( urldecode( $this->options[ "rch_upload_final_file_url" ] ) ) : '';

                if ( ! empty( $file_url ) ) {

                        $file = $upload->rch_download_file_from_url( $this->id, $file_url );

                        if ( ! is_wp_error( $file ) ) {
                                unset( $upload, $file_url );
                                return $file;
                        }
                }
                unset( $upload, $file_url );

                return false;
        }

}
