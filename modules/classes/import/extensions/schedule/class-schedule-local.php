<?php

namespace rch\import\schedule;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php');
}

class Schedule_Local extends Schedule_Base {

        private $parent_id = 0;

        public function __construct( $options = [], $parent_id = 0 ) {

                $new_options = $this->copy_template( $options );

                if ( $new_options === false ) {
                        return false;
                }
                $this->options = $new_options;

                $this->parent_id = $parent_id;

                parent::generate_template();

                $this->reset_template_process();

                parent::generate_chunks();
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

        private function reset_template_process() {

                global $wpdb;

                $process_log = $wpdb->get_var( $wpdb->prepare( "SELECT process_log FROM " . $wpdb->prefix . "rch_template where `id` = %d", $this->parent_id ) );

                if ( empty( $process_log ) ) {
                        return false;
                }
                $process_log = \maybe_unserialize( $process_log );

                $total = isset( $process_log[ "total" ] ) ? absint( $process_log[ "total" ] ) : 0;

                if ( $total === 0 ) {

                        $this->delete_template();

                        return false;
                }

                $wpdb->update(
                        $wpdb->prefix . "rch_template",
                        [
                                "status"      => "background",
                                'process_log' => maybe_serialize( [ "total" => $total ] )
                        ],
                        [ 'id' => $this->id ]
                );

                unset( $process_log );
        }

}
