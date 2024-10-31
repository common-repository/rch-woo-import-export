<?php

namespace rch\import\record;

use rch\lib\xml\xml2array;
use XMLReader;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

class RCH_Record {

        private $rch_fileName = "rch-import-data-";
        public $record_length = 0;
        public $tag_list = array();

        public function __construct() {
                
        }

        public function get_records( $fileName = "", $xpath = "", $start = false, $length = false, $total = false, $tags = false, $xmlView = "single_array" ) {

                if ( file_exists( RCH_DEPENCY_DIR . '/xml/class-rch-xml2array.php' ) ) {
                        require_once(RCH_DEPENCY_DIR . '/xml/class-rch-xml2array.php');
                }

                $converter = new \rch\lib\xml\xml2array\XmlToArray( $fileName );

                $converter->set_xpath( $xpath );

                $records = $converter->get_records( $start, $length, $xmlView );

                if ( $total === true ) {
                        $this->record_length = $converter->get_record_length();
                } else {
                        $this->record_length = 0;
                }
                if ( $tags === true ) {
                        $this->tag_list = $converter->get_tags();
                } else {
                        $this->tag_list = array();
                }

                unset( $converter );

                return $records;
        }

        public function auto_fetch_records_by_template( $template_options = array() ) {

                $xpath = isset( $template_options[ "xpath" ] ) ? "/" . wp_unslash( $template_options[ "xpath" ] ) : "";

                $root = isset( $template_options[ "root" ] ) ? rch_sanitize_field( wp_unslash( $template_options[ "root" ] ) ) : "";

                $start = isset( $template_options[ "start" ] ) ? intval( rch_sanitize_field( $template_options[ "start" ] ) ) : 0;

                $length = isset( $template_options[ "length" ] ) ? intval( rch_sanitize_field( $template_options[ "length" ] ) ) : 1;

                $activeFile = isset( $template_options[ 'activeFile' ] ) ? $template_options[ 'activeFile' ] : "";

                $importFile = isset( $template_options[ 'importFile' ] ) ? $template_options[ 'importFile' ] : array();

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                $file_name = $fileData[ 'fileName' ] ? $fileData[ 'fileName' ] : "";

                $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                $type = explode( '.', $file_name );

                $fileType = end( $type );

                $file_count = 1;

                $newFile = RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/" . $this->rch_fileName . $file_count . '.xml';

                $node_list = array();

                if ( $root == "" ) {

                        $node_list = $this->rch_get_node_list( $newFile );

                        $root = $this->rch_get_root_node( $node_list );

                        $xpath = "//" . $root;
                }

                $data = array();

                $data[ "root" ] = $root;

                $data[ "xpath" ] = $xpath;

                $data[ "node_list" ] = $node_list;

                $data[ "file_type" ] = $fileType;

                $data[ "content" ] = $this->get_records( $newFile, $xpath, $start, $length, true, true, "xml" );

                $data[ "count" ] = $this->record_length;

                $data[ "filter_element" ] = $this->tag_list;

                unset( $xpath, $root, $start, $length, $activeFile, $importFile, $fileData, $file_name, $baseDir, $type, $fileType, $file_count, $newFile, $node_list );

                return $data;
        }

        private function rch_get_root_node( $nodeList = array() ) {

                $rch_xpath = "";

                if ( ! empty( $nodeList ) ) {

                        $preset_elements = array( 'item', 'property', 'listing', 'hotel', 'record', 'article', 'node', 'post', 'book', 'item_0', 'job', 'deal', 'product', 'entry' );

                        foreach ( $nodeList as $element_name => $value ) {
                                if ( in_array( strtolower( $element_name ), $preset_elements ) ) {
                                        $rch_xpath = $element_name;
                                        break;
                                }
                        }
                        unset( $preset_elements );

                        if ( empty( $rch_xpath ) ) {
                                foreach ( $nodeList as $element => $count ) {
                                        $rch_xpath = $element;
                                        break;
                                }
                        }
                }

                return $rch_xpath;
        }

        private function rch_get_node_list( $filePath = "" ) {

                if ( ! file_exists( $filePath ) ) {
                        return new \WP_Error( 'rch_import_error', __( 'File not exist', 'rch-woo-import-export' ) );
                }
                $nodeList = array();

                $reader = new \XMLReader();

                $reader->open( $filePath );

                $reader->setParserProperty( XMLReader::VALIDATE, false );

                while ( $reader->read() ) {

                        switch ( $reader->nodeType ) {

                                case (XMLREADER::ELEMENT):

                                        $localName = str_replace( "_colon_", ":", $reader->localName );

                                        if ( array_key_exists( str_replace( ":", "_", $localName ), $nodeList ) ) {
                                                $nodeList[ str_replace( ":", "_", $localName ) ] ++;
                                        } else {
                                                $nodeList[ str_replace( ":", "_", $localName ) ] = 1;
                                        }
                                        unset( $localName );

                                        break;
                                default:

                                        break;
                        }
                }

                unset( $reader );

                return $nodeList;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
