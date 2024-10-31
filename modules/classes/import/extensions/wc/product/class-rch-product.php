<?php

namespace rch\import\wc\product;

use WC_Product_Factory;
use rch\import\wc\product\external\RCH_External_Product;
use rch\import\wc\product\grouped\RCH_Grouped_Product;
use rch\import\wc\product\variable\RCH_Variable_Product;
use rch\import\wc\product\simple\RCH_Simple_Product;
use rch\import\wc\product\variation\RCH_Variation_Product;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}
if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php');
}

class RCH_Product_Import extends \rch\import\base\RCH_Import_Base {

        private $unique_keys = array();
        private $parent_id;
        private $variation_id;

        public function __construct( $rch_import_option = array(), $import_type = "" ) {

                add_filter( 'rch_before_post_import', array( $this, "rch_before_post_import" ), 10, 3 );

                $this->rch_import_option = $rch_import_option;

                $this->import_type = $import_type;

                $activeFile = isset( $this->rch_import_option[ 'activeFile' ] ) ? $this->rch_import_option[ 'activeFile' ] : "";

                $importFile = isset( $this->rch_import_option[ 'importFile' ] ) ? $this->rch_import_option[ 'importFile' ] : array();

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                $this->base_dir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                unset( $activeFile, $importFile, $fileData );

                $this->get_product_unique_id();
        }

        public function before_item_import( $rch_import_record = array(), &$existing_item_id = 0, &$is_new_item = true, &$is_search_duplicates = true ) {

                $this->rch_import_record = $rch_import_record;

                if ( absint( $existing_item_id ) > 0 ) {
                        return;
                }

                $this->parent_id = 0;

                $this->variation_id = 0;

                $product_type = rch_sanitize_field( strtolower( trim( $this->get_field_value( 'rch_item_product_type', false, true ) ) ) );

                if ( $product_type == "variable" || $product_type == "variation" ) {

                        $variation_method = rch_sanitize_field( $this->get_field_value( 'rch_item_variation_import_method', true ) );

                        $parent_key = "";

                        switch ( $variation_method ) {
                                case "match_unique_field";
                                        $parent_key = rch_sanitize_field( $this->get_field_value( 'rch_item_product_variation_match_unique_field_parent' ) );
                                        break;
                                case "match_group_field";
                                        $parent_key = rch_sanitize_field( $this->get_field_value( 'rch_item_product_variation_match_group_field' ) );
                                        break;
                                case "match_title_field";
                                        $parent_key = rch_sanitize_field( $this->get_field_value( 'rch_item_variation_import_method_title_field' ) );
                                        break;
                                case "match_title_field_no_parent";
                                        $parent_key = rch_sanitize_field( $this->get_field_value( 'rch_item_variation_import_method_title_field_no_parent' ) );
                                        break;
                        }

                        if ( ! empty( $this->unique_keys ) && in_array( $parent_key, $this->unique_keys ) ) {

                                $this->parent_id = array_search( $parent_key, $this->unique_keys );

                                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-variation-product.php';

                                if ( file_exists( $fileName ) ) {

                                        require_once($fileName);
                                }

                                $variation = new \rch\import\wc\product\variation\RCH_Variation_Product( $this->rch_import_option, $this->rch_import_record, 0, null, false, $this->parent_id );

                                $variation_id = $variation->is_variation_exist();

                                if ( $variation_id !== false && intval( $variation_id ) > 0 ) {

                                        $this->variation_id = $existing_item_id = $variation_id;
                                }

                                $is_search_duplicates = false;

                                unset( $parent, $fileName );
                        }

                        unset( $variation_method, $parent_key );
                }
        }

        public function rch_before_post_import( $rch_final_data = array(), $rch_import_option = array(), $rch_import_record = array() ) {

                if ( intval( $this->parent_id > 0 ) ) {

                        $rch_final_data[ 'post_type' ] = "product_variation";

                        $rch_final_data[ 'post_parent' ] = intval( $this->parent_id );

                        if ( intval( $this->variation_id ) == 0 && isset( $rch_final_data[ 'ID' ] ) ) {
                                unset( $rch_final_data[ 'ID' ] );
                        }
                }

                return $rch_final_data;
        }

        public function after_item_import( $item_id = 0, $item = null, $is_new_item = false ) {

                $this->item_id = $item_id;

                $this->item = $item;

                $this->is_new_item = $is_new_item;

                if ( $this->parent_id > 0 ) {

                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-variation-product.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);
                        }

                        $variation = new \rch\import\wc\product\variation\RCH_Variation_Product( $this->rch_import_option, $this->rch_import_record, $this->item_id, $this->item, $this->is_new_item, $this->parent_id );

                        $variation->set_product( new \WC_Product_Variation( $this->item_id ) );

                        $variation->import_data();
                } else {

                        $product_type = "";

                        if ( $this->is_update_field( "product_type" ) ) {

                                $product_type = rch_sanitize_field( strtolower( trim( $this->get_field_value( 'rch_item_product_type', false, true ) ) ) );
                        }

                        if ( empty( $product_type ) ) {
                                $product_type = \WC_Product_Factory::get_product_type( $this->item_id );
                        }

                        if ( $product_type ) {
                                $className = \WC_Product_Factory::get_product_classname( $this->item_id, $product_type ? $product_type : 'simple' );
                        }

                        $product = new $className( $this->item_id );

                        unset( $className );

                        $productClass = "";

                        $fileName = "";

                        switch ( $product_type ) {

                                case 'external':

                                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-external-product.php';

                                        $productClass = '\rch\import\wc\product\external\RCH_External_Product';

                                        break;
                                case 'grouped':
                                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-grouped-product.php';

                                        $productClass = '\rch\import\wc\product\grouped\RCH_Grouped_Product';

                                        break;
                                case 'variation':
                                case 'variable':

                                        $this->set_unique_key( $product_type );

                                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-variable-product.php';

                                        $productClass = '\rch\import\wc\product\variable\RCH_Variable_Product';

                                        break;
                                default:
                                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-simple-product.php';

                                        $productClass = '\rch\import\wc\product\simple\RCH_Simple_Product';

                                        break;
                        }


                        if ( ! empty( $fileName ) && file_exists( $fileName ) ) {

                                require_once($fileName);
                        }

                        unset( $fileName, $product_type );

                        if ( class_exists( $productClass ) ) {

                                $product_data = new $productClass( $this->rch_import_option, $this->rch_import_record, $item_id, $item, $is_new_item );

                                $product_data->set_product( $product );

                                $product_data->import_data();
                        }
                        unset( $productClass );
                }
        }

        private function set_unique_key( $product_type = "" ) {

                if ( $product_type == "variable" || $product_type == "variation" ) {

                        $unique_key = "";

                        $variation_method = rch_sanitize_field( $this->get_field_value( 'rch_item_variation_import_method', true ) );

                        switch ( $variation_method ) {
                                case "match_unique_field";
                                        $unique_key = rch_sanitize_field( $this->get_field_value( 'rch_item_product_variation_field_parent' ) );
                                        break;
                                case "match_group_field";
                                        $unique_key = rch_sanitize_field( $this->get_field_value( 'rch_item_product_variation_match_group_field' ) );
                                        break;
                                case "match_title_field";
                                        $unique_key = rch_sanitize_field( $this->get_field_value( 'rch_item_variation_import_method_title_field' ) );
                                        break;
                                case "match_title_field_no_parent";
                                        $unique_key = rch_sanitize_field( $this->get_field_value( 'rch_item_variation_import_method_title_field_no_parent' ) );
                                        break;
                        }

                        if ( ! empty( $unique_key ) ) {
                                $this->set_product_unique_id( $unique_key );
                        }
                        unset( $unique_key );
                }
        }

        private function get_product_unique_id() {

                if ( file_exists( $this->get_product_group_log_dir() . "/log.json" ) ) {

                        $this->unique_keys = json_decode( file_get_contents( $this->get_product_group_log_dir() . "/log.json" ), true );
                }
        }

        private function set_product_unique_id( $unique_key = "" ) {

                $this->unique_keys[ $this->item_id ] = $unique_key;

                $base_dir = $this->get_product_group_log_dir();

                wp_mkdir_p( $base_dir );

                file_put_contents( $base_dir . "/log.json", json_encode( array_unique( $this->unique_keys ) ) );
        }

        private function get_product_group_log_dir() {
                return RCH_UPLOAD_IMPORT_DIR . "/" . $this->base_dir . "/group";
        }

        public function __destruct() {

                parent::__destruct();

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
