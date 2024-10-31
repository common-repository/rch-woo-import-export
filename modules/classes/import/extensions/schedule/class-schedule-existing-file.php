<?php

namespace rch\import\schedule;

use \rch\import\upload\existingfile\RCH_Existing_File;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php');
}

class Schedule_Existing_File extends Schedule_Base {

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

                $fileName = RCH_IMPORT_CLASSES_DIR . "/extensions/existing-file/class-existing-file.php";

                if ( ! file_exists( $fileName ) ) {

                        return false;
                }

                require_once($fileName);

                $upload = new RCH_Existing_File();

                $file = isset( $this->options [ "final_existing_file" ] ) ? rch_sanitize_field( $this->options [ "final_existing_file" ] ) : '';

                if ( ! empty( $file ) ) {

                        $file_list = $upload->rch_upload_file( $file, $this->id );

                        if ( ! is_wp_error( $file_list ) ) {
                                unset( $upload, $file );
                                return $file_list;
                        }
                }
                unset( $upload, $file );

                return false;
        }

}
