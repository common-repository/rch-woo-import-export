<?php

namespace rch\import\engine;

use rch\import\backup\RCH_Import_Backup;
use rch\import\log\RCH_Import_Log;
use rch\import\images;
use rch\import\record;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php');
}

abstract class RCH_Import_Engine extends \rch\import\base\RCH_Import_Base {

        abstract function process_import_data();

        abstract protected function search_duplicate_item();

        public function rch_import_data( $template_data = null ) {

                $this->rch_import_id = isset( $template_data->id ) ? $template_data->id : 0;

                $this->rch_import_option = isset( $template_data->options ) && trim( $template_data->options ) != "" ? maybe_unserialize( $template_data->options ) : array();

                $this->import_username = isset( $template_data->username ) && trim( $template_data->username ) != "" ? $template_data->username : "";

                $this->init_services();

                $import_data = $this->get_records( $template_data );

                if ( is_wp_error( $import_data ) ) {
                        return $import_data;
                }

                $process_data = isset( $template_data->process_log ) ? maybe_unserialize( $template_data->process_log ) : array();

                unset( $template_data );

                $this->process_log = array(
                        'total'               => (isset( $process_data[ 'total' ] ) && $process_data[ 'total' ] != "") ? absint( $process_data[ 'total' ] ) : 0,
                        'imported'            => (isset( $process_data[ 'imported' ] ) && $process_data[ 'imported' ] != "") ? absint( $process_data[ 'imported' ] ) : 0,
                        'created'             => (isset( $process_data[ 'created' ] ) && $process_data[ 'created' ] != "") ? absint( $process_data[ 'created' ] ) : 0,
                        'updated'             => (isset( $process_data[ 'updated' ] ) && $process_data[ 'updated' ] != "") ? absint( $process_data[ 'updated' ] ) : 0,
                        'skipped'             => (isset( $process_data[ 'skipped' ] ) && $process_data[ 'skipped' ] != "") ? absint( $process_data[ 'skipped' ] ) : 0,
                        'last_records_id'     => (isset( $process_data[ 'last_records_id' ] ) && $process_data[ 'last_records_id' ] != "") ? absint( $process_data[ 'last_records_id' ] ) : 0,
                        'last_records_status' => (isset( $process_data[ 'last_records_status' ] ) && $process_data[ 'last_records_status' ] != "") ? $process_data[ 'last_records_status' ] : ''
                );

                unset( $process_data );

                $addon_class = apply_filters( 'rch_import_addon', array(), rch_sanitize_field( $this->get_field_value( 'rch_import_type', true ) ) );

                if ( ! empty( $addon_class ) ) {

                        foreach ( $addon_class as $key => $addon ) {

                                if ( class_exists( $addon ) ) {

                                        $this->addons[ $key ] = new $addon( $this->rch_import_option, $this->import_type, $this->addon_error, $this->addon_log );
                                }
                        }
                }

                unset( $addon_class );

                $this->import_log = array();

                global $wpdb;

                if ( ! empty( $import_data ) ) {

                        foreach ( $import_data as $data ) {

                                $this->reset_iteration_data();

                                $this->rch_import_record = $data;

                                $this->init_import_process();

                                $wpdb->update( $wpdb->prefix . "rch_template", array( 'last_update_date' => current_time( 'mysql' ), 'process_log' => maybe_serialize( $this->process_log ) ), array( 'id' => $this->rch_import_id ) );

                                $this->set_log( "" );
                        }
                }

                unset( $import_data );

                if ( ! empty( $this->addons ) ) {

                        foreach ( $this->addons as $addon ) {

                                if ( method_exists( $addon, "task_completed" ) ) {

                                        $addon->task_completed();

                                        if ( ! empty( $this->addon_log ) ) {

                                                $this->set_log( $this->addon_log );

                                                $this->addon_log = array();
                                        }

                                        if ( $this->addon_error === true ) {

                                                break;
                                        }
                                }
                        }
                }
                if ( $this->addon_error === true ) {

                        $this->remove_current_item();

                        return true;
                }

                $this->finalyze_process();

                if ( $this->process_log[ "total" ] !== 0 && $this->process_log[ "imported" ] >= $this->process_log[ "total" ] ) {

                        $wpdb->update( $wpdb->prefix . "rch_template", array( 'last_update_date' => current_time( 'mysql' ), 'status' => "completed" ), array( 'id' => $this->rch_import_id ) );
                }

                return array( 'process_log' => $this->process_log, 'import_log' => $this->import_log );
        }

        private function get_records( &$template_data ) {

                $xpath = isset( $this->rch_import_option[ "xpath" ] ) ? "/" . wp_unslash( $this->rch_import_option[ "xpath" ] ) : "";

                $process_data = isset( $template_data->process_log ) ? maybe_unserialize( $template_data->process_log ) : array();

                $start = (isset( $process_data[ 'imported' ] ) && $process_data[ 'imported' ] != "") ? absint( $process_data[ 'imported' ] ) : 0;

                $last_records_status = isset( $process_data[ 'last_records_status' ] ) ? $process_data[ 'last_records_status' ] : "";

                if ( $last_records_status == "pending" ) {

                        if ( $start > 0 ) {
                                $start --;
                        }
                }

                $rch_file_processing_type = isset( $this->rch_import_option[ "rch_import_file_processing" ] ) ? rch_sanitize_field( $this->rch_import_option[ "rch_import_file_processing" ] ) : "chunk";

                $split_file = "";

                $length = false;

                if ( $rch_file_processing_type == "chunk" ) {
                        $length = isset( $this->rch_import_option[ "rch_records_per_request" ] ) ? absint( rch_sanitize_field( $this->rch_import_option[ "rch_records_per_request" ] ) ) : 20;
                        $split_file = isset( $this->rch_import_option[ "rch_import_split_file" ] ) ? rch_sanitize_field( $this->rch_import_option[ "rch_import_split_file" ] ) : "";
                }

                $activeFile = isset( $this->rch_import_option[ 'activeFile' ] ) ? $this->rch_import_option[ 'activeFile' ] : "";

                $importFile = isset( $this->rch_import_option[ 'importFile' ] ) ? $this->rch_import_option[ 'importFile' ] : array();

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                $chunks = 1000;

                if ( $start !== false && $start >= $chunks ) {
                        $start_file = floor( $start / $chunks ) + 1;
                        $start = $start % $chunks;
                } else {
                        $start_file = 1;
                        $start = $start;
                }

                $newFile = RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" . $this->rch_fileName . $start_file . '.xml';

                if ( ! file_exists( $newFile ) ) {
                        return new \WP_Error( 'rch_import_error', __( 'File not exist', 'rch-woo-import-export' ) );
                }
                if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-record.php' ) ) {

                        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-record.php');
                }

                $records = new \rch\import\record\RCH_Record();

                $results = $records->get_records( $newFile, $xpath, $start, $length );

                unset( $xpath, $process_data, $start, $rch_file_processing_type, $activeFile, $fileData, $baseDir, $chunks, $start_file, $newFile, $records );

                return $results;
        }

        private function reset_iteration_data() {

                $this->is_new_item = true;

                $this->item_id = 0;

                $this->existing_item_id = 0;

                $this->rch_final_data = array();

                $this->as_draft = false;

                $this->item = false;
        }

        private function init_import_process() {

                global $wpdb;

                $is_search_duplicates = true;

                $this->set_log( "<strong>" . __( 'Record', 'rch-woo-import-export' ) . "</strong>" . " #" . ( $this->process_log[ 'imported' ] + 1) );

                if ( isset( $this->process_log[ 'last_records_status' ] ) && $this->process_log[ 'last_records_status' ] == 'pending' && isset( $this->process_log[ 'last_records_id' ] ) ) {

                        $_post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 1", intval( $this->process_log[ 'last_records_id' ] ) ) );

                        if ( $_post ) {

                                $this->is_new_item = false;

                                $is_search_duplicates = false;

                                $this->existing_item_id = intval( $this->process_log[ 'last_records_id' ] );

                                $this->set_log( __( 'Complete Pending Last Records', 'rch-woo-import-export' ) . " #" . $this->existing_item_id );
                        }

                        unset( $_post );
                }

                if ( ! empty( $this->addons ) ) {

                        foreach ( $this->addons as $addon ) {

                                if ( method_exists( $addon, "before_item_import" ) ) {

                                        $addon->before_item_import( $this->rch_import_record, $this->existing_item_id, $this->is_new_item, $is_search_duplicates );

                                        if ( ! empty( $this->addon_log ) ) {

                                                $this->set_log( $this->addon_log );

                                                $this->addon_log = array();
                                        }

                                        if ( $this->addon_error === true ) {

                                                $this->remove_current_item();

                                                break;
                                        }
                                }
                        }
                }

                if ( $is_search_duplicates ) {
                        $this->search_duplicate_item();
                }

                if ( absint( $this->existing_item_id ) > 0 ) {

                        $this->is_new_item = false;

                        $this->set_log( __( 'Existing item found', 'rch-woo-import-export' ) . " #" . $this->existing_item_id );
                }

                $handle_items = $this->get_field_value( 'handle_items', true );

                if ( ! $this->is_new_item && $handle_items == "new" ) {

                        $this->set_log( "<strong>" . __( 'SKIPPED', 'rch-woo-import-export' ) . '</strong> : ' . __( 'Skip Existing Items', 'rch-woo-import-export' ) );

                        $this->process_log[ 'skipped' ] ++;

                        $this->process_log[ 'imported' ] ++;

                        unset( $handle_items );

                        return $this->existing_item_id;
                } elseif ( $this->is_new_item && $handle_items == "existing" ) {

                        $this->set_log( "<strong>" . __( 'SKIPPED', 'rch-woo-import-export' ) . '</strong> : ' . __( 'Skip New Items', 'rch-woo-import-export' ) );

                        $this->process_log[ 'skipped' ] ++;

                        $this->process_log[ 'imported' ] ++;

                        unset( $handle_items );

                        return true;
                }

                if ( $this->backup_service !== false && $process_last_records === false && absint( $this->existing_item_id ) > 0 ) {

                        $is_success = $this->backup_service->create_backup( $this->existing_item_id, false );

                        if ( is_wp_error( $is_success ) ) {
                                $this->set_log( "<strong>" . __( 'Warning', 'rch-woo-import-export' ) . '</strong> : ' . $is_success->get_error_message() );
                        }
                        unset( $is_success );
                }

                unset( $handle_items, $process_last_records );

                $item_id = $this->process_import_data();

                if ( $item_id === true ) {
                        unset( $item_id );
                        return true;
                }

                unset( $item_id );

                if ( ! empty( $this->addons ) ) {

                        foreach ( $this->addons as $addon ) {

                                if ( method_exists( $addon, "after_item_import" ) ) {

                                        $addon->after_item_import( $this->item_id, $this->item, $this->is_new_item );

                                        if ( ! empty( $this->addon_log ) ) {

                                                $this->set_log( $this->addon_log );

                                                $this->addon_log = array();
                                        }

                                        if ( $this->addon_error === true ) {

                                                break;
                                        }
                                }
                        }
                }
                if ( $this->addon_error === true ) {

                        $this->remove_current_item();

                        return true;
                }

                if ( $this->as_draft ) {
                        $wpdb->update( $wpdb->posts, array( 'post_status' => "draft" ), array( 'ID' => $this->item_id ) );
                }

                do_action( 'rch_after_completed_item_import', $this->item_id, $this->rch_import_record, $this->rch_final_data, $this->rch_import_option );

                $this->set_log( $this->import_type . ' #' . $this->item_id . ' ' . __( 'Successfully Imported', 'rch-woo-import-export' ) );

                $this->process_log[ 'last_records_status' ] = 'completed';

                $this->process_log[ 'last_activity' ] = date( 'Y-m-d H:i:s' );
        }

        protected function rch_import_images() {

                if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-images.php' ) ) {
                        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-images.php');
                }

                $rch_images = new \rch\import\images\RCH_Images( $this->item_id, $this->is_new_item, $this->rch_import_option, $this->rch_import_record );

                $image_data = $rch_images->prepare_images();

                if ( ! empty( $image_data ) ) {
                        if ( isset( $image_data[ 'as_draft' ] ) && $image_data[ 'as_draft' ] === true ) {
                                $this->as_draft = true;
                        }
                        if ( isset( $image_data[ 'import_log' ] ) && is_array( $image_data[ 'import_log' ] ) && ! empty( $image_data[ 'import_log' ] ) ) {

                                array_map( array( $this, 'set_log' ), $image_data[ 'import_log' ] );
                        }
                }

                unset( $image_data, $rch_images );
        }

        protected function rch_import_cf() {

                $item_cf_option = rch_sanitize_field( $this->get_field_value( 'rch_item_update_cf', true ) );

                $existing_metas = array();

                $exclude_metas = array();

                $includes_metas = array();

                if ( $item_cf_option == "all" ) {
                        $existing_metas = $this->get_meta();
                } elseif ( $item_cf_option == "excludes" ) {

                        $exclude_metas_input = rch_sanitize_field( $this->get_field_value( 'rch_item_update_cf_excludes_data' ) );

                        if ( ! empty( $exclude_metas_input ) ) {
                                $exclude_metas = explode( ",", $exclude_metas_input );
                        }
                        unset( $exclude_metas_input );
                } elseif ( $item_cf_option == "includes" ) {

                        $includes_metas_input = rch_sanitize_field( $this->get_field_value( 'rch_item_update_cf_includes_data' ) );

                        if ( ! empty( $includes_metas_input ) ) {
                                $includes_metas = explode( ",", $includes_metas_input );
                        }
                        unset( $includes_metas_input );
                }

                unset( $item_cf_option );

                $rch_item_cf = rch_sanitize_field( $this->get_field_value( 'rch_item_cf' ) );

                $not_add_empty = intval( rch_sanitize_field( $this->get_field_value( 'rch_item_not_add_empty', true ) ) );

                $cf = $this->get_cf_list( $rch_item_cf );

                if ( ! empty( $cf ) ) {

                        foreach ( $cf as $meta_key => $meta_value ) {

                                if ( isset( $existing_metas[ $meta_key ] ) ) {
                                        unset( $existing_metas[ $meta_key ] );
                                }
                                if ( ! empty( $includes_metas ) && ! in_array( $meta_key, $includes_metas ) ) {
                                        continue;
                                }
                                if ( ! empty( $exclude_metas ) && in_array( $meta_key, $exclude_metas ) ) {
                                        continue;
                                }

                                if ( in_array( $meta_key, array( '_thumbnail_id', '_product_image_gallery' ) ) ) {
                                        continue;
                                }
                                if ( ($not_add_empty === 1 && ! empty( $meta_value )) || $not_add_empty !== 1 ) {

                                        $this->update_meta( $meta_key, $meta_value );
                                }
                        }
                }

                if ( ! empty( $existing_metas ) ) {
                        foreach ( $existing_metas as $meta ) {
                                $this->remove_meta( $meta );
                        }
                }
                unset( $existing_metas, $exclude_metas, $includes_metas, $cf );
        }

        private function get_cf_list( $rch_item_cf ) {

                $cf = array();

                if ( ! empty( $rch_item_cf ) && is_array( $rch_item_cf ) ) {

                        foreach ( $rch_item_cf as $key => $value ) {

                                $option = isset( $value[ 'option' ] ) ? strtolower( trim( $value[ 'option' ] ) ) : "";

                                if ( $option === "serialized" ) {
                                        if ( isset( $value[ 'values' ] ) && ! empty( $value[ 'values' ] ) ) {
                                                $_value = $this->get_cf_list( $value[ 'values' ] );
                                        } else {
                                                $_value = "";
                                        }
                                } else {
                                        $_value = (isset( $value[ 'value' ] ) && ! empty( $value[ 'value' ] )) ? $value[ 'value' ] : "";
                                }

                                if ( isset( $value[ 'name' ] ) && ! empty( $value[ 'name' ] ) ) {
                                        $meta_key = $value[ 'name' ];
                                        $cf[ $meta_key ] = $_value;
                                } else {
                                        $cf[] = $_value;
                                }
                        }
                }

                return $cf;
        }

        private function init_services() {

                $activeFile = isset( $this->rch_import_option[ 'activeFile' ] ) ? $this->rch_import_option[ 'activeFile' ] : "";

                $importFile = isset( $this->rch_import_option[ 'importFile' ] ) ? $this->rch_import_option[ 'importFile' ] : array();

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                $this->base_dir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                unset( $activeFile, $importFile, $fileData );

                $this->init_log_services();

                /* $is_import_reversable = isset($this->rch_import_option['is_import_reversable']) ? $this->rch_import_option['is_import_reversable'] : 0;

                  if ($is_import_reversable == 1) {
                  // $this->init_backup_services();
                  }

                 */
        }

        private function init_backup_services() {

                if ( $this->backup_service === false ) {

                        if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-backup.php' ) ) {

                                require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-backup.php');
                        }
                        $import_type = rch_sanitize_field( $this->get_field_value( 'rch_import_type', true ) );

                        $rch_taxonomy_type = rch_sanitize_field( $this->get_field_value( 'rch_taxonomy_type', true ) );

                        $this->backup_service = new \rch\import\backup\RCH_Import_Backup();

                        wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $this->base_dir . "/backup" );

                        $data = $this->backup_service->init_backup_services( $import_type, $rch_taxonomy_type, RCH_UPLOAD_IMPORT_DIR . "/" . $this->base_dir . "/backup" );

                        if ( is_wp_error( $data ) ) {
                                $this->set_log( "<strong>" . __( 'Warning', 'rch-woo-import-export' ) . '</strong> : ' . $data->get_error_message() );
                        }
                        unset( $data );
                }
        }

        private function init_log_services() {

                if ( $this->log_service === false ) {

                        if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-log.php' ) ) {

                                require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-log.php');
                        }

                        $this->log_service = new \rch\import\log\RCH_Import_Log();


                        wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $this->base_dir . "/log" );


                        $data = $this->log_service->init_log_services( RCH_UPLOAD_IMPORT_DIR . "/" . $this->base_dir . "/log" );

                        if ( is_wp_error( $data ) ) {
                                $this->set_log( "<strong>" . __( 'Warning', 'rch-woo-import-export' ) . '</strong> : ' . $data->get_error_message() );
                        }

                        unset( $data );
                }
        }

        protected function finalyze_process() {

                if ( method_exists( $this->log_service, "finalyze_process" ) ) {
                        $this->log_service->finalyze_process();

                        unset( $this->log_service, $this->backup_service );
                }
        }

        public function set_log( $log = "" ) {

                if ( ! empty( $log ) ) {

                        if ( is_array( $log ) ) {

                                foreach ( $log as $_log_text ) {

                                        $this->prepare_log( $_log_text );
                                }
                        } else {
                                $this->prepare_log( $log );
                        }
                }
        }

        private function prepare_log( $log = "" ) {

                $data = "[" . date( 'h:i:s' ) . "] " . $log;

                $this->import_log[] = "<p>" . $data . "</p>";

                $this->log_service->add_log( $data );

                unset( $log, $data );
        }

        private function remove_current_item() {
                wp_delete_post( $this->item_id, true );
        }

        public function __destruct() {
                parent::__destruct();
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
