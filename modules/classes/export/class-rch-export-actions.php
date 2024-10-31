<?php

namespace rch\export\actions;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_EXPORT_CLASSES_DIR . '/class-rch-export.php' ) ) {
        require_once(RCH_EXPORT_CLASSES_DIR . '/class-rch-export.php');
}

class RCH_Export_Actions extends \rch\export\RCH_Export {

        public function __construct() {

                add_action( 'wp_ajax_rch_export_get_template_list', array( $this, 'rch_export_get_template_list' ) );

                add_action( 'wp_ajax_rch_export_save_template', array( $this, 'rch_export_save_template' ) );

                add_action( 'wp_ajax_rch_export_get_template_data', array( $this, 'rch_export_get_template_data' ) );

                add_action( 'wp_ajax_rch_export_records_count', array( $this, 'rch_export_records_count' ) );

                add_action( 'wp_ajax_rch_export_field_list', array( $this, 'rch_export_field_list' ) );

                add_action( 'wp_ajax_rch_export_get_rule_list', array( $this, 'rch_export_get_rule_list' ) );

                add_action( 'wp_ajax_rch_export_create_data', array( $this, 'rch_export_create_data' ) );

                add_action( 'wp_ajax_rch_export_update_data', array( $this, 'rch_export_update_data' ) );

                add_action( 'wp_ajax_rch_export_prepare_file', array( $this, 'rch_export_prepare_file' ) );

                add_action( 'wp_ajax_rch_export_get_preview_data', array( $this, 'rch_export_get_preview_data' ) );

                add_action( 'wp_ajax_rch_export_update_status', array( $this, 'rch_export_update_status' ) );
        }

        public function rch_export_get_template_list() {

                parent::get_template_list();
        }

        public function rch_export_records_count() {

                parent::get_item_count();
        }

        public function rch_export_field_list() {
                parent::get_field_list();
        }

        public function rch_export_get_rule_list() {

                parent::get_export_rule();
        }

        public function rch_export_save_template() {

                parent::save_template_data();
        }

        public function rch_export_get_template_data() {

                parent::get_template();
        }

        public function rch_export_create_data() {

                $is_package = isset( $_POST[ 'is_package' ] ) ? intval( sanitize_text_field($_POST[ 'is_package' ]) ) === 1 : false;

                if ( $is_package && ! class_exists( '\ZipArchive' ) ) {
                        $error_data = [
                                'status'  => "error",
                                'message' => __( 'Please enable PHP ZIP extension', 'rch-woo-import-export' )
                        ];

                        echo json_encode( $error_data );

                        die();
                }
                parent::init_new_export();
        }

        public function rch_export_update_data() {

                parent::init_export_process();
        }

        public function rch_export_prepare_file() {

                parent::prepare_file();
        }

        public function rch_export_get_preview_data() {

                parent::get_preview();
        }

        public function rch_export_update_status() {

                parent::update_process_status();
        }

}
