<?php

namespace rch\import\schedule;

use \rch\import\RCH_Import;
use \rch\import\upload\validate\RCH_Upload_Validate;
use \rch\import\chunk\RCH_Chunk;
use rch\import\record\RCH_Record;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import.php');
}

class Schedule_Base extends RCH_Import {

        protected $id = 0;
        protected $options = [];

        protected function generate_template( $type = "schedule_import", $status = "draft" ) {

                $this->id = parent::rch_generate_template( $this->options, $type, $status );
        }

        protected function delete_template() {

                global $wpdb;

                $wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "rch_template WHERE id = %d", $this->id ) );
        }

        protected function get_template_options() {

                global $wpdb;

                return $wpdb->get_var( $wpdb->prepare( "SELECT options FROM " . $wpdb->prefix . "rch_template where `id` = %d", $this->id ) );
        }

        protected function update_template_options() {

                $options = $this->get_template_options();

                if ( ( ! $options) && empty( $options ) ) {
                        return false;
                }

                $this->options = maybe_unserialize( $options );
        }

        protected function validate_upload() {

                if ( $this->update_template_options() === false ) {
                        return false;
                } elseif ( $this->validate_file() === false ) {
                        return false;
                } elseif ( $this->generate_chunks() === false ) {
                        return false;
                } elseif ( $this->reset_template() === false ) {
                        return false;
                }

                return true;
        }

        protected function validate_file() {

                if ( ! file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-upload-validate.php' ) ) {
                        return false;
                }

                require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload-validate.php');

                $data_parser = new RCH_Upload_Validate();

                $delim = isset( $this->options[ "rch_csv_delimiter" ] ) ? rch_sanitize_field( $this->options[ "rch_csv_delimiter" ] ) : ",";

                $file = isset( $this->options[ "activeFile" ] ) ? rch_sanitize_field( $this->options[ "activeFile" ] ) : false;

                $data = $data_parser->rch_parse_upload_data( $this->options, $delim, $file, $this->id );

                unset( $data_parser, $delim, $file );

                if ( is_wp_error( $data ) ) {
                        return false;
                }
                return true;
        }

        protected function generate_chunks() {

                if ( ! file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-csv-chunk.php' ) ) {
                        return false;
                }
                require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-csv-chunk.php');

                $chunk = new RCH_Chunk();

                $chunk->process_data( $this->options );

                unset( $chunk );

                return true;
        }

        protected function reset_template() {

                if ( ! file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-record.php' ) ) {
                        return false;
                }

                require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-record.php');

                $records = new RCH_Record();

                $parse_data = $records->auto_fetch_records_by_template( $this->options );

                if ( is_wp_error( $parse_data ) ) {
                        unset( $records );
                        return false;
                } else {

                        if ( isset( $parse_data[ 'count' ] ) && absint( $parse_data[ 'count' ] ) > 0 ) {

                                global $wpdb;

                                $wpdb->update(
                                        $wpdb->prefix . "rch_template",
                                        [
                                                "status"           => "background",
                                                'last_update_date' => current_time( 'mysql' ),
                                                'process_log'      => maybe_serialize( [ "total" => absint( $parse_data[ 'count' ] ) ] )
                                        ],
                                        [ 'id' => $this->id ]
                                );
                        }
                }
                unset( $records, $parse_data );
                return true;
        }

        protected function finalyze_data( $type = "import-draft" ) {
                return parent::rch_finalyze_template_data( $type );
        }

}
