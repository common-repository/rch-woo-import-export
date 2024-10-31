<?php

namespace rch\import\upload\validate;

use rch\import\chunk\csv;
use rch\lib\xml\array2xml;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Writer;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

class RCH_Upload_Validate {

        private $rch_fileName = "rch-import-data-";

        public function __construct() {
                
        }

        public function rch_parse_upload_data( $template_data = null, $rch_csv_delimiter = ",", $activeFile = false, $rch_import_id = false ) {

                if ( empty( $template_data ) ) {
                        return false;
                }
                
                global $wpdb;

                if ( is_array( $template_data ) ) {
                        $template_options = $template_data;
                } else {
                        $template_options = isset( $template_data->options ) ? maybe_unserialize( $template_data->options ) : array();
                }

                $importFile = isset( $template_options[ 'importFile' ] ) ? $template_options[ 'importFile' ] : array();

                if ( $activeFile === false ) {
                        $activeFile = isset( $_GET[ 'activeFile' ] ) ? rch_sanitize_field( $_GET[ 'activeFile' ] ) : "";
                }
                if ( $rch_import_id === false ) {
                        $rch_import_id = isset( $_GET[ "rch_import_id" ] ) ? intval( rch_sanitize_field( $_GET[ "rch_import_id" ] ) ) : 0;
                }

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : array();

                $file_path = isset( $fileData[ 'fileDir' ] ) ? rch_sanitize_field( $fileData[ 'fileDir' ] ) : "";

                $file_name = isset( $fileData[ 'fileName' ] ) ? rch_sanitize_field( $fileData[ 'fileName' ] ) : "";

                $baseDir = isset( $fileData[ 'baseDir' ] ) ? rch_sanitize_field( $fileData[ 'baseDir' ] ) : "";

                $template_options[ 'activeFile' ] = $activeFile;

                if ( is_dir( RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/" ) ) {
                        $this->rch_remove_old_files( RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/" );
                }

                $wpdb->update( $wpdb->prefix . "rch_template", array( "options" => maybe_serialize( $template_options ) ), array( 'id' => $rch_import_id ) );

                $file = RCH_UPLOAD_IMPORT_DIR . "/" . $file_path . "/" . $file_name;

                if ( ! file_exists( $file ) ) {

                        unset( $template_options, $importFile, $activeFile, $file, $rch_import_id, $fileData, $file_path, $file_name, $baseDir );

                        return new \WP_Error( 'rch_import_error', __( 'File not found', 'rch-woo-import-export' ) );
                } elseif ( preg_match( '%\W(xls|xlsx|ods)$%i', trim( $file_name ) ) ) {

                        unset( $template_options, $importFile, $activeFile, $file, $rch_import_id, $fileData );

                        return $this->rch_convert_excel_2_csv( $file_path, $file_name, $baseDir );
                } elseif ( preg_match( '%\W(csv)$%i', trim( $file_name ) ) ) {

                        unset( $template_options, $importFile, $activeFile, $file, $rch_import_id, $fileData );

                        return $this->rch_convert_csv_2_xml( $file_path, $file_name, $baseDir, $rch_csv_delimiter );
                } elseif ( preg_match( '%\W(txt|json)$%i', trim( $file_name ) ) ) {

                        unset( $template_options, $importFile, $activeFile, $file, $rch_import_id, $fileData );

                        return $this->rch_convert_json_2_xml( $file_path, $file_name, $baseDir );
                } elseif ( preg_match( '%\W(xml)$%i', trim( $file_name ) ) ) {

                        copy( $file, RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/" . $this->rch_fileName . "1.xml" );

                        unset( $template_options, $importFile, $activeFile, $file, $rch_import_id, $fileData, $file_path, $file_name, $baseDir );

                        return true;
                }

                unset( $template_options, $importFile, $activeFile, $file, $rch_import_id, $fileData, $file_path, $file_name, $baseDir );

                return new \WP_Error( 'rch_import_error', __( 'Invalid File to parse. Please Choose other FIle', 'rch-woo-import-export' ) );
        }

        private function rch_convert_excel_2_csv( $fileDir = "", $file_name = "", $baseDir = "" ) {

                $file = RCH_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . $file_name;

                if ( ! file_exists( $file ) ) {
                        return new \WP_Error( 'rch_import_error', __( 'File not found', 'rch-woo-import-export' ) );
                }

                $newFileName = wp_unique_filename( RCH_UPLOAD_IMPORT_DIR . "/" . $fileDir, preg_replace( '%\W(xls|xlsx|ods)$%i', ".csv", $file_name ) );

                if ( file_exists( RCH_DEPENCY_DIR . '/composer/vendor/autoload.php' ) ) {
                        require_once( RCH_DEPENCY_DIR . '/composer/vendor/autoload.php' );
                }

                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $file );

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv( $spreadsheet );

                $writer->save( RCH_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . preg_replace( '%\W(xls|xlsx|ods)$%i', ".csv", $file_name ) );

                $spreadsheet->disconnectWorksheets();

                $return_data = $this->rch_convert_csv_2_xml( $fileDir, $newFileName, $baseDir );

                unset( $file, $newFileName, $reader, $spreadsheet, $writer );

                return $return_data;
        }

        private function rch_convert_csv_2_xml( $fileDir = "", $file_name = "", $baseDir = "", $rch_csv_delimiter = "," ) {

                if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-csv-chunk.php' ) ) {
                        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-csv-chunk.php');
                }

                $csv_chunk = new \rch\import\chunk\csv\RCH_CSV_Chunk();

                $return_data = $csv_chunk->process_csv( $fileDir, $file_name, $baseDir, $rch_csv_delimiter, $this->rch_fileName );

                unset( $csv_chunk );

                return $return_data;
        }

        private function rch_convert_json_2_xml( $fileDir = "", $file_name = "", $baseDir = "" ) {

                $file = RCH_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . $file_name;

                if ( ! file_exists( $file ) ) {
                        return false;
                }

                $json = file_get_contents( $file );

                $file_data = json_decode( $json, true );

                $fileName = $this->rch_fileName . '1.xml';

                $xmlFilePath = RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/";

                $xmlfileName = wp_unique_filename( $xmlFilePath, $fileName );

                if ( file_exists( RCH_DEPENCY_DIR . '/xml/class-rch-array2xml.php' ) ) {
                        require_once(RCH_DEPENCY_DIR . '/xml/class-rch-array2xml.php');
                }

                $converter = new \rch\lib\xml\array2xml\ArrayToXml();

                $converter->create_root( "rchdata" );

                $converter->convertElement( $converter->root, $file_data, 0 );

                $converter->saveFile( $xmlFilePath . "/" . $xmlfileName );

                unset( $file, $json, $file_data, $fileName, $converter );

                return $xmlFilePath . "/" . $xmlfileName;
        }

        private function rch_remove_old_files( $targetDir = "" ) {

                $cdir = scandir( $targetDir );

                if ( is_array( $cdir ) && ! empty( $cdir ) ) {
                        foreach ( $cdir as $key => $value ) {
                                if ( ! in_array( $value, array( ".", ".." ) ) ) {
                                        if ( is_dir( $targetDir . '/' . $value ) ) {
                                                $this->rch_remove_old_files( $targetDir . '/' . $value );
                                        } else {
                                                unlink( $targetDir . '/' . $value );
                                        }
                                }
                        }
                }
                unset( $cdir );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
