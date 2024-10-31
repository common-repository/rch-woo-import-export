<?php

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

class RCH_Schedule_Import_Extension {

        public function __construct() {

                add_action( 'wp_ajax_rch_save_import_scheduled', array( $this, 'save_schedule' ) );

                add_filter( 'rch_add_import_extension_file', array( $this, 'get_schedule_view' ), 10, 1 );

                add_action( 'rch_cron_schedule_import', array( $this, 'prepare_import_cron' ), 10, 1 );

                add_action( 'rch_manage_import_tab_files', array( $this, 'get_manage_schedule_tab' ), 10, 1 );
        }

        public function prepare_import_cron( $template_id = 0 ) {

                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-import.php';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);

                        $schedule = new \rch\import\schedule\Schedule_Import();

                        $schedule->process_schedule( $template_id );

                        unset( $schedule );
                }
                unset( $fileName );
        }

        public function save_schedule() {

                $data = [
                        'status'  => 'error',
                        'message' => __( 'Problem in saving Schedule', 'rch-woo-import-export' )
                ];

                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-new-schedule.php';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);

                        $schedule = new \rch\import\schedule\New_Schedule( $_POST );

                        if ( $schedule->save_schedule() ) {

                                $data = [
                                        'status'  => 'success',
                                        'message' => __( 'Scheduled Saved Successfully', 'rch-woo-import-export' )
                                ];
                        }
                }

                echo json_encode( $data );

                die();
        }

        public function get_schedule_view( $files = array() ) {

                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/rch-schedule-view.php';

                if ( ! in_array( $fileName, $files ) ) {

                        $files[] = $fileName;
                }

                return $files;
        }

        public function get_manage_schedule_tab( $files = array() ) {

                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/rch-manage-schedule.php';

                if ( ! in_array( $fileName, $files ) ) {

                        $files[] = $fileName;
                }

                return $files;
        }

}

new RCH_Schedule_Import_Extension();
