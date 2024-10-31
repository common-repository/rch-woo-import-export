<?php

namespace rch\import\schedule;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

class Schedule_Import {

        public function process_schedule( $template_id = 0 ) {

                if ( absint( $template_id ) < 1 ) {
                        return false;
                }
                global $wpdb;

                $options = $wpdb->get_var( $wpdb->prepare( "SELECT options FROM " . $wpdb->prefix . "rch_template where `id` = %d", absint( $template_id ) ) );

                if ( ( ! $options) && empty( $options ) ) {
                        return false;
                }

                $options = maybe_unserialize( $options );

                $upload_method = ( isset( $options[ 'rch_file_upload_method' ] ) && ! empty( $options[ 'rch_file_upload_method' ] )) ? strtolower( trim( rch_sanitize_field( $options[ 'rch_file_upload_method' ] ) ) ) : "";

                if ( $upload_method === "rch_import_url_file_upload" ) {

                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-url.php';
                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                new Schedule_Url( $options );
                        }
                } elseif ( $upload_method === "rch_import_existing_file_upload" ) {
                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-existing-file.php';
                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                new Schedule_Existing_File( $options );
                        }
                } elseif ( $upload_method === "rch_import_ftp_file_upload" ) {
                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-ftp.php';
                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                new Schedule_Ftp( $options, $template_id );
                        }
                } else {
                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-local.php';
                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                new Schedule_Local( $options, $template_id );
                        }
                }
        }

}
