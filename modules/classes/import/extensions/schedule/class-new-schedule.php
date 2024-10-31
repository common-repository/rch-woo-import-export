<?php

namespace rch\import\schedule;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php');
}

class New_Schedule extends Schedule_Base {

        public function __construct( $options = [] ) {

                $this->options = $options;
        }

        public function save_schedule() {

                $interval = isset( $this->options[ 'rch_import_interval' ] ) ? rch_sanitize_field( $this->options[ 'rch_import_interval' ] ) : "";

                $start_time = isset( $this->options[ 'rch_interval_start_time' ] ) ? rch_sanitize_field( $this->options[ 'rch_interval_start_time' ] ) : "";

                if ( ! empty( $start_time ) ) {
                        $import_time = strtotime( $start_time );
                } else {
                        $import_time = time();
                }

                if ( ! empty( $interval ) ) {

                        $import_id = isset( $this->options[ "rch_import_id" ] ) ? absint( rch_sanitize_field( $this->options[ "rch_import_id" ] ) ) : 0;

                        $template_options = [];

                        $process_log = "";

                        if ( $import_id > 0 ) {

                                $template_data = $this->get_template_by_id( $import_id );

                                if ( $template_data ) {

                                        $template_options = maybe_unserialize( $template_data->options );

                                        $process_log = isset( $template_data->process_log ) ? $template_data->process_log : "";
                                }
                        }

                        if ( ! empty( $template_options ) ) {
                                $new_options = array_merge( $this->options, $template_options );
                        } else {
                                $new_options = $this->options;
                        }

                        $new_options = $this->copy_template( $new_options );

                        $scheduled_id = parent::rch_generate_template( $new_options, 'schedule_import_template', 'completed' );

                        if ( ! empty( $process_log ) ) {

                                global $wpdb;

                                $wpdb->update( $wpdb->prefix . "rch_template", [ 'process_log' => $process_log ], [ 'id' => $scheduled_id ] );
                        }

                        wp_schedule_event( $import_time, $interval, 'rch_cron_schedule_import', [ $scheduled_id ] );

                        unset( $scheduled_id, $import_id, $template_options, $new_options );

                        return true;
                }
                return false;
        }

        private function copy_template( $options = [] ) {

                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-template-copy.php';

                if ( ! file_exists( $fileName ) ) {

                        return false;
                }
                require_once($fileName);

                $tempalte = new Template_Copy( $options );

                $new_options = $tempalte->options;

                unset( $tempalte, $fileName );

                return $new_options;
        }

}
