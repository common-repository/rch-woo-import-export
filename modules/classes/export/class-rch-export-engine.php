<?php

namespace rch\export\engine;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_EXPORT_CLASSES_DIR . '/class-rch-export-base.php' ) ) {

        require_once(RCH_EXPORT_CLASSES_DIR . '/class-rch-export-base.php');
}

abstract class RCH_Export_Engine extends \rch\export\base\RCH_Export_Base {

        private $fp;

        abstract protected function get_fields();

        abstract protected function parse_rule( $filter );

        abstract protected function process_export();

        public function init_engine( $export_type = "post", $opration = "export", $template = null ) {

                if ( $export_type == "product" ) {
                        $this->export_type = array( "product", "product_variation" );
                } else {

                        $this->export_type = array( $export_type );
                }

                $this->opration = strtolower( trim( $opration ) );

                if ( $opration === "fields" ) {
                        $this->template_options = $template;
                        return $this->get_fields();
                } elseif ( $opration === "count" ) {
                        return $this->get_item_data( $template );
                } elseif ( $opration === "ids" ) {
                        return $this->get_item_data( $template );
                } elseif ( $opration === "preview" ) {
                        $this->is_preview = true;
                        return $this->get_item_data( $template );
                } elseif ( $opration === "import_backup" ) {
                        return $this->get_backup_data( $template );
                } else {
                        return $this->init_export( $template );
                }
        }

        private function get_backup_data( $template ) {

                $this->template_options = $template;

                $this->process_log = array(
                        'exported' => isset( $this->template_options[ 'count' ] ) ? $this->template_options[ 'count' ] : 0,
                        'total'    => 0,
                );

                $backup_dir = isset( $this->template_options[ 'backup_dir' ] ) ? $this->template_options[ 'backup_dir' ] : "";

                $this->open_export_file( $backup_dir . "/backup.csv" );

                unset( $backup_dir );

                $this->init_export_addons();

                $id = isset( $this->template_options[ 'id' ] ) && absint( $this->template_options[ 'id' ] ) > 0 ? absint( $this->template_options[ 'id' ] ) : 0;

                $this->process_items( array( $id ) );

                unset( $id );

                $this->remove_addons();

                $this->close_export_file();
        }

        private function get_item_data( $template = null ) {

                $this->template_options = $template;

                $this->process_log = array(
                        'exported' => 0,
                        'total'    => 0,
                );

                $this->manage_rules();

                $this->init_export_addons();

                $export = $this->process_export();

                $this->remove_addons();

                if ( isset( $this->is_preview ) && $this->is_preview == true ) {

                        unset( $export );

                        return $this->preview_data;
                } else {
                        return $export;
                }
        }

        private function init_export_addons() {

                $addon_class = apply_filters( 'rch_prepare_export_addons', array(), $this->export_type );

                if ( ! empty( $addon_class ) ) {

                        foreach ( $addon_class as $key => $addon ) {

                                if ( class_exists( $addon ) ) {

                                        $this->addons[ $key ] = new $addon();

                                        if ( method_exists( $this->addons[ $key ], "init_process" ) ) {

                                                $this->addons[ $key ]->init_process( $this->template_options );
                                        }
                                }
                        }
                }
        }

        private function remove_addons() {
                unset( $this->addons );
        }

        private function init_export( $template = null ) {

                $this->export_id = isset( $template->id ) ? $template->id : 0;

                $this->template_options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                global $wpdb;

                $wpdb->update( $wpdb->prefix . "rch_template", array( 'process_lock' => 1 ), array( 'id' => absint( $this->export_id ) ) );

                $process_data = isset( $template->process_log ) ? maybe_unserialize( $template->process_log ) : array();

                $this->process_log = array(
                        'exported' => (isset( $process_data[ 'exported' ] ) && $process_data[ 'exported' ] != "") ? absint( $process_data[ 'exported' ] ) : 0,
                        'total'    => (isset( $process_data[ 'total' ] ) && $process_data[ 'total' ] != "") ? absint( $process_data[ 'total' ] ) : 0,
                );

                $filename = isset( $this->template_options[ 'fileName' ] ) ? $this->template_options[ 'fileName' ] : "";

                $filedir = isset( $this->template_options[ 'fileDir' ] ) ? $this->template_options[ 'fileDir' ] : "";

                $filepath = RCH_UPLOAD_EXPORT_DIR . '/' . $filedir . '/' . $filename;

                unset( $process_data, $filename, $filedir );

                $this->manage_rules();

                $this->open_export_file( $filepath );

                $this->init_export_addons();

                $this->process_export();

                $this->remove_addons();

                $this->close_export_file();

                $final_data = array(
                        'last_update_date' => current_time( 'mysql' ),
                        'process_lock'     => 0,
                );

                $wpdb->update( $wpdb->prefix . "rch_template", $final_data, array( 'id' => $this->export_id ) );

                unset( $final_data, $filepath );

                return $this->process_log;
        }

        private function open_export_file( $filepath = "" ) {

                $this->fp = @fopen( $filepath, 'a+' );

                unset( $filename, $filedir, $filepath );
        }

        private function close_export_file() {

                fclose( $this->fp );
        }

        protected function process_data() {

                if ( ! empty( $this->export_data ) ) {

                        if ( $this->is_preview ) {

                                $this->process_log[ 'exported' ] ++;

                                $this->preview_data[] = array_values( $this->export_data );
                        } else {
                                $file_type = (isset( $this->template_options[ 'rch_export_file_type' ] ) && trim( $this->template_options[ 'rch_export_file_type' ] ) != "") ? rch_sanitize_field( $this->template_options[ 'rch_export_file_type' ] ) : "csv";

                                if ( $file_type == "csv" ) {
                                        $separator = (isset( $this->template_options[ 'rch_csv_field_separator' ] ) && trim( $this->template_options[ 'rch_csv_field_separator' ] ) != "") ? rch_sanitize_field( $this->template_options[ 'rch_csv_field_separator' ] ) : ",";
                                } else {
                                        $separator = ",";
                                }
                                if ( $this->process_log[ 'exported' ] == 0 && ! empty( $this->export_labels ) ) {
                                        fputcsv( $this->fp, array_values( $this->export_labels ), $separator );
                                        unset( $this->export_labels );
                                }
                                if ( $this->has_multiple_rows ) {
                                        foreach ( $this->export_data as $data ) {
                                                fputcsv( $this->fp, array_values( $data ), $separator );
                                        }
                                } else {
                                        fputcsv( $this->fp, array_values( $this->export_data ), $separator );
                                }

                                unset( $separator );

                                $this->process_log[ 'exported' ] ++;

                                $final_data = array(
                                        'last_update_date' => current_time( 'mysql' ),
                                        'process_log'      => maybe_serialize( $this->process_log ),
                                );

                                if ( $this->process_log[ 'exported' ] >= $this->process_log[ 'total' ] ) {

                                        $final_data[ 'status' ] = "completed";

                                        $extra_copy_path = isset( $this->template_options[ 'extra_copy_path' ] ) && ! empty( $this->template_options[ 'extra_copy_path' ] ) ? ltrim( trailingslashit( sanitize_text_field( $this->template_options[ 'extra_copy_path' ] ) ), '/\\' ) : "";

                                        $is_package = isset( $this->template_options[ 'is_package' ] ) ? intval( $this->template_options[ 'is_package' ] ) : 0;


                                        if ( $this->opration === "schedule_export" ) {
                                                $is_package = isset( $this->template_options[ 'is_migrate_package' ] ) ? intval( $this->template_options[ 'is_migrate_package' ] ) : 0;
                                        }

                                        if ( $is_package === 0 && strtolower( $file_type ) === "csv" && ! empty( $extra_copy_path ) && is_dir( RCH_SITE_UPLOAD_DIR . "/" . $extra_copy_path ) ) {

                                                $filename = isset( $this->template_options[ 'fileName' ] ) ? $this->template_options[ 'fileName' ] : "";

                                                $filedir = isset( $this->template_options[ 'fileDir' ] ) ? $this->template_options[ 'fileDir' ] : "";

                                                $filepath = RCH_UPLOAD_EXPORT_DIR . '/' . $filedir . '/' . $filename;

                                                copy( $filepath, RCH_SITE_UPLOAD_DIR . '/' . $extra_copy_path . $filename );

                                                unset( $filename, $filedir, $filepath );
                                        }

                                        do_action( 'rch_export_task_complete', $this->export_id, $this->opration, $this->template_options );
                                }

                                global $wpdb;

                                $wpdb->update( $wpdb->prefix . "rch_template", $final_data, array( 'id' => $this->export_id ) );

                                do_action( 'rch_export_complete', $this->export_id, $this->opration, $this->template_options );

                                unset( $final_data );
                        }
                }

                $this->export_data = array();
        }

        protected function manage_rules() {

                $rch_export_condition = isset( $this->template_options[ 'rch_filter_rule' ] ) ? rch_sanitize_field( stripslashes_deep( $this->template_options[ 'rch_filter_rule' ] ) ) : "";

                if ( ! empty( $rch_export_condition ) ) {

                        $rch_filter_rule = explode( "~`|`~", $rch_export_condition );

                        if ( is_array( $rch_filter_rule ) && ! empty( $rch_filter_rule ) ) {

                                foreach ( $rch_filter_rule as $data ) {

                                        if ( empty( $data ) ) {
                                                continue;
                                        }

                                        $options = explode( "`|~`", $data );

                                        $filter = array();

                                        $rule = isset( $options[ 0 ] ) ? rch_sanitize_field( wp_unslash( $options[ 0 ] ) ) : "";

                                        if ( $rule != "" ) {

                                                $filter_data = json_decode( $rule, true );

                                                if ( isset( $filter_data[ 'type' ] ) && ! empty( $filter_data[ 'type' ] ) ) {
                                                        $filter = $filter_data;
                                                        $filter[ 'element' ] = $filter_data[ 'type' ];
                                                }
                                                unset( $rule, $filter_data );
                                        } else {
                                                unset( $options, $filter, $rule );
                                                continue;
                                        }

                                        $filter[ 'condition' ] = isset( $options[ 1 ] ) ? rch_sanitize_field( wp_unslash( $options[ 1 ] ) ) : "";

                                        $filter[ 'value' ] = isset( $options[ 2 ] ) ? esc_sql( rch_sanitize_field( wp_unslash( $options[ 2 ] ) ) ) : "";

                                        $filter[ 'clause' ] = isset( $options[ 3 ] ) ? rch_sanitize_field( wp_unslash( $options[ 3 ] ) ) : "";

                                        if ( ! empty( $filter[ 'element' ] ) && ! empty( $filter[ 'condition' ] ) ) {

                                                $this->parse_rule( $filter );
                                        }

                                        unset( $filter );
                                }
                        }

                        unset( $rch_filter_rule );
                }

                unset( $rch_export_condition );
        }

}
