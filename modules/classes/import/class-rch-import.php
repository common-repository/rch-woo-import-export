<?php

namespace rch\import;

use rch\import\upload\validate\RCH_Upload_Validate;
use rch\import\chunk\RCH_Chunk;
use rch\import\upload\RCH_Upload;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

class RCH_Import {

        public function __construct() {
                
        }

        public function rch_get_import_type() {

                global $wp_version;

                $custom_export_type = get_post_types( array( '_builtin' => true ), 'objects' ) + get_post_types( array( '_builtin' => false, 'show_ui' => true ), 'objects' ) + get_post_types( array( '_builtin' => false, 'show_ui' => false ), 'objects' );

                foreach ( $custom_export_type as $key => $ct ) {
                        if ( ! in_array( $key, array( 'shop_order', 'shop_coupon', 'shop_customer', 'product' ) ) ) {
                                unset( $custom_export_type[ $key ] );
                        }
                }

                $custom_export_type = $this->rch_manage_woo_data( $custom_export_type );

                $custom_export_type = apply_filters( 'rch_custom_import_types', $custom_export_type );

                $export_type_result = array();

                foreach ( $custom_export_type as $key => $data ) {

                        $export_type_result[ $key ] = $data;

                        if ( ! empty( $custom_export_type[ 'product' ] ) && $key == 'product' ) {

                                $export_type_result[ 'taxonomies' ] = new \stdClass();
                                $export_type_result[ 'taxonomies' ]->labels = new \stdClass();
                                $export_type_result[ 'taxonomies' ]->labels->name = __( 'Taxonomies', 'rch-woo-import-export' );

                                $export_type_result[ 'comments' ] = new \stdClass();
                                $export_type_result[ 'comments' ]->labels = new \stdClass();
                                $export_type_result[ 'comments' ]->labels->name = __( 'Comments', 'rch-woo-import-export' );
                                ;
                        }
                }

                $order = array( 'shop_order', 'shop_coupon', 'shop_customer', 'product' );

                foreach ( $order as $data ) {

                        if ( ! empty( $custom_export_type[ $data ] ) ) {
                                $export_type_result[ $data ] = $custom_export_type[ $data ];
                        }
                }

                uasort( $custom_export_type, array( $this, "rch_set_import_custom_types" ) );

                foreach ( $custom_export_type as $key => $data ) {

                        if ( empty( $export_type_result[ $key ] ) ) {

                                $export_type_result[ $key ] = $data;
                        }
                }

                unset( $custom_export_type, $order );

                return apply_filters( 'rch_import_type', $export_type_result );
        }

        public function rch_set_import_custom_types( $key = null, $data = null ) {
                return strcmp( $key->labels->name, $data->labels->name );
        }

        private function rch_manage_woo_data( $custom_data_types = array() ) {

                if ( class_exists( 'WooCommerce' ) ) {

                        if ( ! empty( $custom_data_types[ 'product' ] ) ) {
                                $custom_data_types[ 'product' ]->labels->name = __( 'WooCommerce Products', 'rch-woo-import-export' );
                        }
                        if ( ! empty( $custom_data_types[ 'shop_order' ] ) ) {
                                $custom_data_types[ 'shop_order' ]->labels->name = __( 'WooCommerce Orders', 'rch-woo-import-export' );
                        }
                        if ( ! empty( $custom_data_types[ 'shop_coupon' ] ) ) {
                                $custom_data_types[ 'shop_coupon' ]->labels->name = __( 'WooCommerce Coupons', 'rch-woo-import-export' );
                        }
                        if ( ! empty( $custom_data_types[ 'product_variation' ] ) ) {
                                unset( $custom_data_types[ 'product_variation' ] );
                        }
                        if ( ! empty( $custom_data_types[ 'shop_order_refund' ] ) ) {
                                unset( $custom_data_types[ 'shop_order_refund' ] );
                        }

                        $order = array( 'shop_order', 'shop_coupon', 'shop_customer', 'product' );

                        $ordered_custom_types = array();

                        foreach ( $order as $type ) {

                                if ( isset( $ordered_custom_types[ $type ] ) )
                                        continue;

                                if ( $type == 'shop_customer' ) {
                                        $ordered_custom_types[ 'shop_customer' ] = new \stdClass();
                                        $ordered_custom_types[ 'shop_customer' ]->labels = new \stdClass();
                                        $ordered_custom_types[ 'shop_customer' ]->labels->name = __( 'WooCommerce Customers', 'rch-woo-import-export' );
                                } else {

                                        foreach ( $custom_data_types as $key => $custom_type ) {

                                                if ( isset( $ordered_custom_types[ $key ] ) ) {
                                                        continue;
                                                }
                                                if ( in_array( $key, $order ) ) {
                                                        if ( $key == $type ) {
                                                                $ordered_custom_types[ $key ] = $custom_type;
                                                        }
                                                } else {
                                                        $ordered_custom_types[ $key ] = $custom_type;
                                                }
                                        }
                                }
                        }

                        unset( $order, $custom_data_types );

                        return $ordered_custom_types;
                }
                return $custom_data_types;
        }

        public function rch_get_all_taxonomies( $exclude_taxonomies = array(), $object_type = array(), $field = 'name' ) {

                $taxonomies = get_taxonomies( FALSE, 'objects' );

                $ignore_taxonomies = array( 'nav_menu', 'link_category' );

                if ( ! empty( $exclude_taxonomies ) ) {
                        $ignore_taxonomies = array_merge( $ignore_taxonomies, $exclude_taxonomies );
                }

                $result = array();

                if ( ! empty( $taxonomies ) ) {

                        foreach ( $taxonomies as $_key => $taxonomy ) {

                                if ( in_array( $_key, $ignore_taxonomies ) || $taxonomy->show_in_nav_menus === false ) {
                                        continue;
                                }
                                 if ( isset( $taxonomy->object_type ) && ! in_array( "product", $taxonomy->object_type ) ) {
                                        continue;
                                }

                                if ( ! empty( $object_type ) ) {

                                        $temp = 0;

                                        if ( is_array( $taxonomy->object_type ) ) {
                                                foreach ( $taxonomy->object_type as $value ) {
                                                        if ( in_array( $value, $object_type ) ) {
                                                                $temp ++;
                                                                break;
                                                        }
                                                }
                                        }
                                        if ( $temp === 0 ) {
                                                continue;
                                        }
                                        unset( $temp );
                                }

                                if ( $field == 'name' ) {
                                        if ( ! empty( $taxonomy->labels->name ) && strpos( $taxonomy->labels->name, "_" ) === false ) {
                                                $result[ $_key ] = $taxonomy->labels->name;
                                        } else {
                                                $result[ $_key ] = empty( $taxonomy->labels->singular_name ) ? $taxonomy->name : $taxonomy->labels->singular_name;
                                        }
                                } elseif ( $field == 'keytitle' ) {
                                        if ( $_key === "product_cat" ) {
                                                $_label = "Product Category";
                                        } else {
                                                $_label = ucwords( str_replace( '_', ' ', $_key ) );
                                        }
                                        $result[ $_key ] = $_label;
                                } elseif ( $field == 'all' ) {
                                        $result[ $_key ] = $taxonomy;
                                }
                        }
                }

                if ( $field != 'all' ) {
                        asort( $result, SORT_FLAG_CASE | SORT_STRING );
                } else {
                        asort( $result );
                }

                unset( $exclude_taxonomies, $taxonomies, $ignore_taxonomies, $object_type, $field );

                return $result;
        }

        public function rch_generate_template( $options = array(), $opration = 'import', $status = 'processing', $unique_id = "", $import_id = 0 ) {

                global $wpdb;

                $rch_import_type = (isset( $options[ 'rch_import_type' ] ) && trim( $options[ 'rch_import_type' ] ) != "") ? $options[ 'rch_import_type' ] : "post";

                $current_time = current_time( 'mysql' );

                $new_values = array();

                $new_values[ 'opration' ] = $opration;

                $new_values[ 'opration_type' ] = $rch_import_type;

                $new_values[ 'process_lock' ] = 0;

                $new_values[ 'process_log' ] = "";

                $new_values[ 'status' ] = $status;

                $new_values[ 'options' ] = maybe_serialize( wp_unslash( $options ) );

                $new_values[ 'last_update_date' ] = $current_time;

                $is_update = false;

                if ( absint( $import_id ) > 0 ) {
                        $is_update = $wpdb->update( $wpdb->prefix . "rch_template", $new_values, [ "id" => absint( $import_id ) ] );
                }

                if ( $is_update === false || absint( $is_update ) === 0 ) {

                        if ( empty( $unique_id ) ) {
                                $unique_id = uniqid();
                        }

                        $new_values[ 'create_date' ] = $current_time;

                        $new_values[ 'unique_id' ] = $unique_id;

                        $current_user = wp_get_current_user();

                        if ( $current_user && isset( $current_user->user_login ) ) {
                                $new_values[ 'username' ] = $current_user->user_login;
                        }

                        $wpdb->insert( $wpdb->prefix . "rch_template", $new_values );

                        $import_id = $wpdb->insert_id;
                }


                unset( $current_time, $rch_import_type, $new_values );

                return $import_id;
        }

        public function get_template_by_id( $rch_import_id = 0 ) {

                if ( intval( $rch_import_id ) > 0 ) {

                        global $wpdb;

                        $results = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "rch_template where `id` = %d limit 0,1", intval( $rch_import_id ) ) );

                        if ( ! empty( $results ) ) {
                                return $results;
                        }
                }

                return false;
        }

        public function get_template_by_ref( $ref = "" ) {

                if ( ! empty( $ref ) ) {

                        global $wpdb;

                        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "rch_template where `unique_id` = %s ORDER BY `id` ASC limit 0,1", $ref ) );
                }

                return false;
        }

        protected function rch_parse_upload_file() {

                $return_value = array( 'status' => 'error' );

                $rch_import_id = isset( $_GET[ "rch_import_id" ] ) ? intval( rch_sanitize_field( $_GET[ "rch_import_id" ] ) ) : 0;

                if ( $rch_import_id != 0 ) {

                        $template_data = $this->get_template_by_id( $rch_import_id );

                        if ( $template_data ) {

                                if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-upload-validate.php' ) ) {
                                        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload-validate.php');
                                }

                                $data_parser = new RCH_Upload_Validate();

                                $rch_csv_delimiter = isset( $_GET[ "rch_csv_delimiter" ] ) ? rch_sanitize_field( $_GET[ "rch_csv_delimiter" ] ) : ",";

                                $data = $data_parser->rch_parse_upload_data( $template_data, $rch_csv_delimiter );

                                if ( is_wp_error( $data ) ) {
                                        $return_value[ 'message' ] = $data->get_error_message();
                                } else {
                                        $return_value[ 'status' ] = "success";
                                        $return_value[ 'message' ] = __( 'File is Valid', 'rch-woo-import-export' );
                                }
                                unset( $data_parser, $rch_csv_delimiter, $data );
                        } else {
                                $return_value[ 'message' ] = __( 'Template Not Found', 'rch-woo-import-export' );
                        }
                        unset( $template_data );
                } else {
                        $return_value[ 'message' ] = __( 'Data Not Found', 'rch-woo-import-export' );
                }

                unset( $rch_import_id );

                echo json_encode( $return_value );

                die();
        }

        protected function rch_import_get_filtered_records() {

                $return_value = array( 'status' => 'error' );

                $rch_import_id = isset( $_POST[ "rch_import_id" ] ) ? intval( rch_sanitize_field( $_POST[ "rch_import_id" ] ) ) : 0;

                $template_data = $this->get_template_by_id( $rch_import_id );

                if ( $template_data ) {

                        $template_options = maybe_unserialize( $template_data->options );

                        $new_template_data = array_merge( $template_options, $_POST );

                        if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-record.php' ) ) {
                                require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-record.php');
                        }

                        $records = new \rch\import\record\RCH_Record();

                        $parse_data = $records->auto_fetch_records_by_template( $new_template_data );

                        if ( is_wp_error( $parse_data ) ) {
                                $return_value[ 'message' ] = $parse_data->get_error_message();
                        } else {

                                $return_value = $parse_data;

                                if ( isset( $parse_data[ 'count' ] ) && absint( $parse_data[ 'count' ] ) > 0 ) {

                                        global $wpdb;

                                        $wpdb->update( $wpdb->prefix . "rch_template", array( 'last_update_date' => current_time( 'mysql' ), 'process_log' => maybe_serialize( array( "total" => absint( $parse_data[ 'count' ] ) ) ) ), array( 'id' => $rch_import_id ) );
                                }

                                $return_value[ 'status' ] = 'success';
                        }

                        unset( $records, $parse_data );
                } else {

                        $return_value[ 'message' ] = __( 'Template Not Found', 'rch-woo-import-export' );
                }
                unset( $rch_import_id, $template_data );

                echo json_encode( $return_value );

                die();
        }

        protected function rch_get_import_fields() {

                $return_value = array( 'status' => 'error' );

                $type = isset( $_GET[ "type" ] ) ? rch_sanitize_field( $_GET[ "type" ] ) : "";

                if ( ! empty( $type ) ) {

                        $fileName = "";

                        if ( $type == "taxonomies" ) {
                                $fileName = RCH_IMPORT_CLASSES_DIR . '/fields/rch-taxonomy.php';
                        } elseif ( $type == "comments" ) {
                                $fileName = RCH_IMPORT_CLASSES_DIR . '/fields/rch-comments.php';
                        } else {
                                $fileName = RCH_IMPORT_CLASSES_DIR . '/fields/rch-post.php';
                        }

                        $fileName = apply_filters( 'rch_import_mapping_fields_file', $fileName, $type );

                        if ( file_exists( $fileName ) ) {
                                require_once($fileName);
                        }

                        $fields = apply_filters( 'rch_import_mapping_fields', array(), $type );

                        ksort( $fields );

                        $field_data = "";

                        if ( ! empty( $fields ) ) {
                                foreach ( $fields as $section ) {
                                        $field_data .= balanceTags( $section );
                                }
                        }

                        $return_value[ 'update_fields' ] = apply_filters( 'rch_import_update_existing_item_fields', "", $type );

                        $return_value[ 'search_fields' ] = apply_filters( 'rch_import_search_existing_item', "", $type );

                        $return_value[ 'fields' ] = $field_data;

                        unset( $fileName, $fields, $field_data );

                        $return_value[ 'status' ] = 'success';
                } else {

                        $return_value[ 'message' ] = __( 'Import Type is undefind', 'rch-woo-import-export' );
                }

                unset( $type );

                echo json_encode( $return_value );

                die();
        }

        public function rch_finalyze_template_data( $opration = "import" ) {

                $rch_import_id = isset( $_POST[ "rch_import_id" ] ) ? absint( rch_sanitize_field( $_POST[ "rch_import_id" ] ) ) : 0;

                if ( $rch_import_id > 0 ) {

                        global $wpdb;

                        $new_values = array();

                        $template_data = $this->get_template_by_id( $rch_import_id );

                        if ( $template_data ) {

                                $template_options = maybe_unserialize( $template_data->options );
                        } else {
                                $template_options = array();
                        }

                        $new_template_data = array_merge( $template_options, $_POST );

                        $new_values[ 'options' ] = maybe_serialize( $new_template_data );

                        $new_values[ 'opration' ] = $opration;

                        $new_values[ 'opration_type' ] = isset( $_POST[ 'rch_import_type' ] ) ? rch_sanitize_field( $_POST[ 'rch_import_type' ] ) : "post";

                        $bg = isset( $_POST[ 'bg' ] ) ? absint( rch_sanitize_field( $_POST[ 'bg' ] ) ) : 0;

                        if ( $bg == 1 ) {
                                $new_values[ 'status' ] = "background";
                        } else {
                                $new_values[ 'status' ] = "processing";
                        }

                        $wpdb->update( $wpdb->prefix . "rch_template", $new_values, array( 'id' => $rch_import_id ) );

                        if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-csv-chunk.php' ) ) {
                                require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-csv-chunk.php');
                        }

                        $chunk = new RCH_Chunk();

                        $result = $chunk->process_data( $new_template_data );

                        unset( $template_data, $template_options, $bg, $new_values, $chunk, $new_template_data, $rch_import_id );

                        return $result;
                }

                unset( $rch_import_id );
        }

        protected function rch_import_save_data() {

                $return_value = array( 'status' => 'error' );

                $result = $this->rch_finalyze_template_data();

                if ( is_wp_error( $result ) ) {
                        $return_value[ 'message' ] = $result->get_error_message();
                } else {

                        $return_value[ 'status' ] = 'success';
                }

                echo json_encode( $return_value );

                die();
        }

        protected function rch_import_data() {

                $return_value = array( 'status' => 'error' );

                $rch_import_id = isset( $_GET[ "rch_import_id" ] ) ? intval( rch_sanitize_field( $_GET[ "rch_import_id" ] ) ) : 0;

                if ( $rch_import_id != 0 ) {

                        $import_process = $this->rch_import_process_data( $rch_import_id );

                        if ( is_wp_error( $import_process ) ) {

                                $return_value[ 'message' ] = $import_process->get_error_message();
                        } else {

                                $return_value[ 'status' ] = 'success';

                                $process_log = isset( $import_process[ 'process_log' ] ) ? $import_process[ 'process_log' ] : array();

                                $import_log = isset( $import_process[ 'import_log' ] ) ? $import_process[ 'import_log' ] : "";

                                $return_value[ 'imported' ] = isset( $process_log[ 'imported' ] ) ? intval( $process_log[ 'imported' ] ) : 0;

                                $return_value[ 'created' ] = isset( $process_log[ 'created' ] ) ? intval( $process_log[ 'created' ] ) : 0;

                                $return_value[ 'updated' ] = isset( $process_log[ 'updated' ] ) ? intval( $process_log[ 'updated' ] ) : 0;

                                $return_value[ 'skipped' ] = isset( $process_log[ 'skipped' ] ) ? intval( $process_log[ 'skipped' ] ) : 0;

                                $return_value[ 'import_log' ] = $import_log;

                                unset( $import_log, $process_log );
                        }

                        unset( $import_process );
                }
                unset( $rch_import_id );

                echo json_encode( $return_value );

                die();
        }

        protected function rch_import_process_data( $rch_import_id = 0 ) {

                $template_data = $this->get_template_by_id( $rch_import_id );

                if ( ! $template_data ) {
                        return new \WP_Error( 'rch_import_error', __( 'Template Not Found', 'rch-woo-import-export' ) );
                }

                global $wpdb;

                $wpdb->update( $wpdb->prefix . "rch_template", array( 'process_lock' => 1 ), array( 'id' => $rch_import_id ) );

                $rch_import_type = (isset( $template_data->opration_type ) && trim( $template_data->opration_type ) != "") ? $template_data->opration_type : "post";

                $import_engine = "";

                if ( $rch_import_type == "taxonomies" ) {

                        if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-taxonomy.php' ) ) {

                                require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-taxonomy.php');

                                $import_engine = 'rch\import\taxonomy\RCH_Taxonomy';
                        }
                } elseif ( $rch_import_type == "comments" ) {

                        if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-comment.php' ) ) {

                                require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-comment.php');
                        }

                        $import_engine = 'rch\import\comment\RCH_Comment';
                } else {

                        if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-post.php' ) ) {

                                require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-post.php');

                                $import_engine = 'rch\import\post\RCH_Post';
                        }
                }

                $import_engine = apply_filters( 'rch_import_engine_init', $import_engine, $rch_import_type, $template_data );

                $import_process = array();

                if ( class_exists( $import_engine ) ) {

                        $import_data = new $import_engine();

                        if ( method_exists( $import_data, "rch_import_data" ) ) {

                                $import_process = $import_data->rch_import_data( $template_data );
                        }

                        unset( $import_data );
                }

                $final_data = array(
                        'last_update_date' => current_time( 'mysql' ),
                        'process_lock'     => 0
                );

                $wpdb->update( $wpdb->prefix . "rch_template", $final_data, array( 'id' => $rch_import_id ) );

                unset( $template_data, $rch_import_type, $import_engine, $final_data );

                return $import_process;
        }

        protected function rch_get_template_list() {

                global $wpdb;

                $content_type = isset( $_GET[ 'rch_import_type' ] ) ? rch_sanitize_field( $_GET[ 'rch_import_type' ] ) : "post";

                $results = $wpdb->get_results( $wpdb->prepare( "SELECT `id`,`options` FROM " . $wpdb->prefix . "rch_template where `opration_type` = %s AND `opration`='import_template'", $content_type ) );

                $data = array();

                if ( ! empty( $results ) ) {

                        $count = 0;

                        foreach ( $results as $template ) {

                                $data[ $count ][ 'id' ] = isset( $template->id ) ? $template->id : 0;

                                $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                $data[ $count ][ 'name' ] = isset( $options[ 'rch_template_name' ] ) ? $options[ 'rch_template_name' ] : "";

                                $count ++;

                                unset( $options );
                        }

                        unset( $count );
                }
                unset( $content_type, $results );

                $return_value = array();

                $return_value[ 'status' ] = 'success';

                $return_value[ 'templates' ] = $data;

                unset( $data );

                echo json_encode( $return_value );

                die();
        }

        protected function rch_import_save_template_data() {

                $return_value = array();

                global $wpdb;

                $template_id = isset( $_POST[ 'template_id' ] ) ? absint( rch_sanitize_field( $_POST[ 'template_id' ] ) ) : 0;

                if ( $template_id > 0 ) {

                        $options = $wpdb->get_var( $wpdb->prepare( "SELECT `options` FROM " . $wpdb->prefix . "rch_template where `id`=%d", $template_id ) );

                        if ( ! is_null( $options ) ) {

                                $options = maybe_unserialize( $options );

                                $new_options = $_POST;

                                $new_options[ 'rch_template_name' ] = isset( $options[ 'rch_template_name' ] ) ? $options[ 'rch_template_name' ] : "";

                                $new_values = array();

                                $new_values[ 'options' ] = maybe_serialize( wp_unslash( $new_options ) );

                                $wpdb->update( $wpdb->prefix . "rch_template", $new_values, array( 'id' => $template_id ) );

                                $return_value[ 'status' ] = 'success';

                                $return_value[ 'message' ] = __( 'Template has been successfully updated', 'rch-woo-import-export' );

                                echo json_encode( $return_value );

                                die();
                        }
                }

                $template_name = isset( $_POST[ 'rch_template_name' ] ) ? rch_sanitize_field( $_POST[ 'rch_template_name' ] ) : "";

                $is_exist = false;

                if ( ! empty( $template_name ) ) {

                        $results = $wpdb->get_results( "SELECT `id`,`options` FROM " . $wpdb->prefix . "rch_template where `opration`='import_template'" );

                        if ( ! empty( $results ) ) {

                                foreach ( $results as $template ) {

                                        $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                        $temp_name = isset( $options[ 'rch_template_name' ] ) ? $options[ 'rch_template_name' ] : "";

                                        if ( ! empty( $temp_name ) && $temp_name == $template_name ) {
                                                $is_exist = true;
                                                break;
                                        }
                                        unset( $options, $temp_name );
                                }
                        }

                        unset( $results );
                }

                if ( $is_exist === false ) {

                        $template_id = $this->rch_generate_template( $_POST, "import_template", "completed" );

                        $return_value[ 'status' ] = 'success';

                        $return_value[ 'template_id' ] = $template_id;

                        unset( $template_id );

                        $return_value[ 'message' ] = __( 'Template has been successfully saved', 'rch-woo-import-export' );
                } else {
                        $return_value[ 'status' ] = 'error';

                        $return_value[ 'message' ] = __( 'Template already exists', 'rch-woo-import-export' );
                }

                echo json_encode( $return_value );

                die();
        }

        protected function rch_import_get_template_info() {

                $return_value = array( 'status' => 'error' );

                $template_id = isset( $_GET[ "rch_template_id" ] ) ? absint( rch_sanitize_field( $_GET[ "rch_template_id" ] ) ) : 0;

                if ( $template_id > 0 ) {

                        $template_data = $this->get_template_by_id( $template_id );

                        $return_value[ 'status' ] = 'success';

                        $return_value[ 'template_data' ] = isset( $template_data->options ) ? maybe_unserialize( $template_data->options ) : array();

                        $return_value[ 'message' ] = __( 'Template has been successfully saved', 'rch-woo-import-export' );

                        unset( $template_data );
                }

                unset( $template_id );

                echo json_encode( $return_value );

                die();
        }

        protected function rch_import_update_csv_delimiter() {

                $return_value = array( 'status' => 'error' );

                $rch_import_id = isset( $_GET[ "rch_import_id" ] ) ? intval( rch_sanitize_field( $_GET[ "rch_import_id" ] ) ) : 0;

                if ( $rch_import_id != 0 ) {

                        $template_data = $this->get_template_by_id( $rch_import_id );

                        if ( $template_data ) {

                                if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-upload-validate.php' ) ) {
                                        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload-validate.php');
                                }

                                $data_validate = new RCH_Upload_Validate();

                                $data = $data_validate->rch_parse_upload_data( $template_data );

                                if ( is_wp_error( $data ) ) {
                                        $return_value[ 'message' ] = $data->get_error_message();
                                } else {
                                        $return_value[ 'status' ] = "success";
                                        $return_value[ 'message' ] = __( 'File is Valid', 'rch-woo-import-export' );
                                }
                                unset( $data_validate, $data );
                        } else {
                                $return_value[ 'message' ] = __( 'Template Not Found', 'rch-woo-import-export' );
                        }

                        unset( $template_data );
                } else {
                        $return_value[ 'message' ] = __( 'Data Not Found', 'rch-woo-import-export' );
                }

                unset( $rch_import_id );

                echo json_encode( $return_value );

                die();
        }

        protected function rch_import_update_process_status() {

                $return_value = array( 'status' => 'error' );

                $rch_import_id = isset( $_GET[ "rch_import_id" ] ) ? absint( rch_sanitize_field( $_GET[ "rch_import_id" ] ) ) : 0;

                if ( $rch_import_id != 0 ) {

                        $rch_status = isset( $_GET[ "status" ] ) ? rch_sanitize_field( $_GET[ "status" ] ) : "";

                        $status = "";

                        if ( $rch_status == "bg" ) {
                                $status = "background";
                        }

                        if ( $status != "" ) {

                                $final_data = array(
                                        'last_update_date' => current_time( 'mysql' ),
                                        'process_lock'     => 0,
                                        'status'           => $status
                                );

                                global $wpdb;

                                $wpdb->update( $wpdb->prefix . "rch_template", $final_data, array( 'id' => $rch_import_id ) );

                                unset( $final_data );
                        }

                        unset( $rch_status, $status );

                        $return_value[ 'status' ] = "success";
                } else {
                        $return_value[ 'message' ] = __( 'Template Not Found', 'rch-woo-import-export' );
                }

                unset( $rch_import_id );

                echo json_encode( $return_value );

                die();
        }

        protected function get_config_file() {

                $return_value = array( 'status' => 'error' );

                $rch_import_id = isset( $_GET[ "import_id" ] ) ? absint( rch_sanitize_field( $_GET[ "import_id" ] ) ) : 0;

                if ( $rch_import_id != 0 ) {

                        $template = $this->get_template_by_id( $rch_import_id );

                        if ( $template !== false ) {

                                $option = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                $activeFile = isset( $option[ 'activeFile' ] ) ? $option[ 'activeFile' ] : "";

                                $importFile = isset( $option[ 'importFile' ] ) ? $option[ 'importFile' ] : array();

                                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                                $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                                $configFile = RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/config/config.json";

                                if ( file_exists( $configFile ) ) {

                                        $return_value[ 'config' ] = json_decode( file_get_contents( $configFile ) );
                                }

                                unset( $option, $activeFile, $importFile, $fileData, $baseDir, $configFile );
                        }

                        unset( $template );

                        $return_value[ 'status' ] = "success";
                } else {
                        $return_value[ 'message' ] = __( 'Template Not Found', 'rch-woo-import-export' );
                }

                unset( $rch_import_id );

                echo json_encode( $return_value );

                die();
        }

        protected function process_reimport_data() {

                $return_value = array( "status" => "error" );

                $import_id = isset( $_GET[ 'import_id' ] ) ? absint( sanitize_text_field($_GET[ 'import_id' ]) ) : 0;

                if ( $import_id > 0 ) {

                        $ref_id = isset( $_GET[ 'ref_id' ] ) ? sanitize_text_field($_GET[ 'ref_id' ]) : "";

                        $nonce = isset( $_GET[ 'nonce' ] ) ? sanitize_text_field($_GET[ 'nonce' ]) : "";

                        $validate_nonce = wp_verify_nonce( $nonce, $import_id . $ref_id );

                        if ( $validate_nonce === 1 || $validate_nonce === 2 ) {

                                $ref_template = $this->get_template_by_ref( $ref_id );

                                $new_import_id = 0;

                                $ref_base_dir = "";

                                $is_completed = false;

                                if ( ! empty( $ref_template ) ) {

                                        $ref_status = isset( $ref_template->status ) ? $ref_template->status : "";

                                        if ( $ref_status !== "completed" ) {
                                                $new_import_id = isset( $ref_template->id ) ? $ref_template->id : 0;

                                                $ref_option = isset( $ref_template->options ) ? maybe_unserialize( $ref_template->options ) : array();

                                                $ref_activeFile = isset( $ref_option[ 'activeFile' ] ) ? $ref_option[ 'activeFile' ] : "";

                                                $ref_importFile = isset( $ref_option[ 'importFile' ] ) ? $ref_option[ 'importFile' ] : array();

                                                $ref_fileData = isset( $ref_importFile[ $ref_activeFile ] ) ? $ref_importFile[ $ref_activeFile ] : "";

                                                $ref_base_dir = $ref_fileData[ 'baseDir' ] ? $ref_fileData[ 'baseDir' ] : "";

                                                $this->remove_dir( RCH_UPLOAD_IMPORT_DIR . "/" . $ref_base_dir . "/" );

                                                unset( $ref_option, $ref_activeFile, $ref_importFile, $ref_fileData );
                                        } else {
                                                $is_completed = true;
                                        }
                                }

                                if ( $is_completed === false ) {
                                        $template = $this->get_template_by_id( $import_id );

                                        if ( $template !== false ) {

                                                $option = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                                $activeFile = isset( $option[ 'activeFile' ] ) ? $option[ 'activeFile' ] : "";

                                                $importFile = isset( $option[ 'importFile' ] ) ? $option[ 'importFile' ] : array();

                                                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                                                $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                                                if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php' ) ) {
                                                        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php');
                                                }

                                                $data_uploader = new RCH_Upload();

                                                $new_dir_name = $data_uploader->rch_create_safe_dir_name( $import_id );

                                                $this->custom_copy( RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir, RCH_UPLOAD_IMPORT_DIR . "/" . $new_dir_name );

                                                $file_data = array();

                                                $fileList = [];

                                                if ( ! empty( $importFile ) ) {

                                                        foreach ( $importFile as $key => $value ) {

                                                                if ( is_array( $value ) ) {

                                                                        $base_dir = isset( $value[ 'baseDir' ] ) ? $value[ 'baseDir' ] : "";

                                                                        $fileDir = isset( $value[ 'fileDir' ] ) && ! empty( $value[ 'fileDir' ] ) ? str_replace( $base_dir, $new_dir_name, $value[ 'fileDir' ] ) : "";

                                                                        $value[ 'baseDir' ] = $new_dir_name;

                                                                        $value[ 'fileDir' ] = $fileDir;

                                                                        $file_data[ $key ] = $value;

                                                                        $fileList[] = array(
                                                                                'fileKey'  => $key,
                                                                                'fileName' => isset( $value[ 'fileName' ] ) ? $value[ 'fileName' ] : ""
                                                                        );
                                                                }
                                                        }
                                                }
                                                $option[ 'importFile' ] = $file_data;

                                                $importFile = isset( $option[ 'importFile' ] ) ? $option[ 'importFile' ] : array();

                                                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                                                $fileDir = $fileData[ 'fileDir' ] ? $fileData[ 'fileDir' ] : "";

                                                $originalName = $fileData[ 'originalName' ] ? $fileData[ 'originalName' ] : "";

                                                if ( is_readable( RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/log" ) ) {
                                                        $this->remove_dir( RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/log/" );
                                                        wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/log" );
                                                }
                                                if ( is_readable( RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunk" ) ) {
                                                        $this->remove_dir( RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" );
                                                        wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks" );
                                                }

                                                $return_value[ 'rch_import_id' ] = $this->rch_generate_template( $option, 'import', 'processing', $ref_id, $new_import_id );

                                                $return_value[ 'file_list' ] = $fileList;

                                                $return_value[ 'file_size' ] = filesize( RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/original/" . $originalName );

                                                $return_value[ 'file_name' ] = $originalName;

                                                $return_value[ 'file_count' ] = count( $fileList );

                                                $return_value[ 'status' ] = "success";
                                        } else {
                                                $return_value[ 'message' ] = __( 'Template Not Found', 'rch-woo-import-export' );
                                        }
                                } else {
                                        $return_value[ "message" ] = esc_html__( 'Reimport for given link is completed. please generate new links for reimport from manage import', "rch-woo-import-export" );
                                }
                        } else {
                                $return_value[ "message" ] = esc_html__( 'Invalid Nonce. Go to Import List for new valid Reimport links', "rch-woo-import-export" );
                        }
                } else {
                        $return_value[ 'message' ] = __( 'Template not found', 'rch-woo-import-export' );
                }

                echo json_encode( $return_value );

                die();
        }

        private function custom_copy( $src = "", $dst = "" ) {

                if ( is_dir( $src ) ) {
                        // open the source directory 
                        $dir = opendir( $src );

                        // Make the destination directory if not exist 
                        if ( ! is_dir( $dst ) ) {
                                wp_mkdir_p( $dst );
                        }

                        // Loop through the files in source directory 
                        while ( $file = readdir( $dir ) ) {

                                if ( ( $file != '.' ) && ( $file != '..' ) ) {
                                        if ( is_dir( $src . '/' . $file ) ) {

                                                // Recursively calling custom copy function 
                                                // for sub directory  
                                                $this->custom_copy( $src . '/' . $file, $dst . '/' . $file );
                                        } else {
                                                copy( $src . '/' . $file, $dst . '/' . $file );
                                        }
                                }
                        }

                        closedir( $dir );
                }
        }

        private function remove_dir( $targetDir = "" ) {

                if ( is_dir( $targetDir ) ) {

                        $cdir = scandir( $targetDir );

                        if ( is_array( $cdir ) && ! empty( $cdir ) ) {
                                foreach ( $cdir as $key => $value ) {
                                        if ( ! in_array( $value, array( ".", ".." ) ) ) {
                                                if ( is_dir( $targetDir . '/' . $value ) ) {
                                                        $this->remove_dir( $targetDir . '/' . $value );
                                                } else {
                                                        unlink( $targetDir . '/' . $value );
                                                }
                                        }
                                }
                        }

                        rmdir( $targetDir );

                        unset( $cdir );
                }
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
