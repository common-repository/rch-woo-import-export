<?php

namespace rch\import\bg;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import.php');
}

class RCH_BG_Import extends \rch\import\RCH_Import {

        public function __construct() {

                add_action( 'init', array( $this, 'rch_bg_import' ), 100 );

                add_action( 'init', array( $this, 'rch_bg_unlock_import' ), 200 );

                add_filter( 'rch_add_import_extension_process_btn_files', array( $this, 'rch_add_bg_process_btn' ), 10, 1 );
        }

        public function rch_add_bg_process_btn( $files = array() ) {

                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/bg/rch_bg_btn.php';

                if ( ! in_array( $fileName, $files ) ) {

                        $files[] = $fileName;
                }

                return $files;
        }

        public function rch_bg_unlock_import() {

                global $wpdb;

                $current_time = date( 'Y-m-d H:i:s', strtotime( '-1 hour', strtotime( current_time( "mysql" ) ) ) );

                $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}rch_template SET process_lock = 0 WHERE process_lock = 1 and last_update_date < %s", $current_time ) );

                unset( $current_time );
        }

        public function rch_bg_import() {

                global $wpdb;

                $id = $wpdb->get_var( "SELECT `id` FROM " . $wpdb->prefix . "rch_template where `opration` in ('import','schedule_import') and status LIKE '%background%' and process_lock = 0 ORDER BY `id` ASC limit 0,1" );

                if ( $id && absint( $id ) > 0 ) {

                        parent::rch_import_process_data( $id );
                }
                unset( $id );
        }

}
