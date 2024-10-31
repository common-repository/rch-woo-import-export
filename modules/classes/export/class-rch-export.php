<?php

namespace rch\export;

use rch\export\post;
use rch\export\taxonomy;
use rch\lib\xml\array2xml;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Writer;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

class RCH_Export {

        protected function get_template_list() {

                global $wpdb;

                $content_type = isset( $_POST[ 'content_type' ] ) ? rch_sanitize_field( $_POST[ 'content_type' ] ) : "post";

                $results = $wpdb->get_results( $wpdb->prepare( "SELECT `id`,`options` FROM " . $wpdb->prefix . "rch_template where `opration_type` = %s AND `opration`='export_template'", $content_type ) );

                $data = array ();

                if ( ! empty( $results ) ) {

                        $count = 0;

                        foreach ( $results as $template ) {

                                $data[ $count ][ 'id' ] = isset( $template->id ) ? $template->id : 0;

                                $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array ();

                                $data[ $count ][ 'name' ] = isset( $options[ 'template_name' ] ) ? $options[ 'template_name' ] : "";

                                unset( $options );

                                $count ++;
                        }

                        unset( $count );
                }

                unset( $content_type, $results );

                $return_value = array ();

                $return_value[ 'status' ] = 'success';

                $return_value[ 'data' ] = $data;

                unset( $data );

                echo json_encode( $return_value );

                die();
        }

        public function prepare_fields( $export_type = "", $taxonomy_type = "" ) {
                return $this->init_export( $export_type, "fields", array ( "rch_taxonomy_type" => $taxonomy_type ) );
        }

        protected function get_field_list() {

                $export_type = isset( $_GET[ 'export_type' ] ) ? rch_sanitize_field( $_GET[ 'export_type' ] ) : "post";

                $taxonomy_type = isset( $_GET[ 'taxonomy_type' ] ) ? rch_sanitize_field( $_GET[ 'taxonomy_type' ] ) : "";

                $fields = $this->prepare_fields( $export_type, $taxonomy_type );

                if ( is_wp_error( $fields ) ) {

                        $return_value[ 'status' ] = 'error';

                        $return_value[ 'message' ] = $fields->get_error_message();
                } else {
                        $return_value[ 'status' ] = 'success';

                        $return_value[ 'fields' ] = $fields;
                }

                unset( $export_type, $taxonomy_type, $fields );

                echo json_encode( $return_value );

                die();
        }

        public function init_export( $export_type = "post", $opration = "export", $template = null ) {

                $export_engine = "";

                if ( $export_type == "taxonomies" ) {

                        if ( file_exists( RCH_EXPORT_CLASSES_DIR . '/class-rch-taxonomy.php' ) ) {

                                require_once(RCH_EXPORT_CLASSES_DIR . '/class-rch-taxonomy.php');
                        }

                        $export_engine = '\rch\export\taxonomy\RCH_Taxonomy';
                } elseif ( $export_type == "comments" ) {

                        if ( file_exists( RCH_EXPORT_CLASSES_DIR . '/class-rch-comment.php' ) ) {

                                require_once(RCH_EXPORT_CLASSES_DIR . '/class-rch-comment.php');
                        }
                        $export_engine = '\rch\export\comment\RCH_Comment';
                } else {

                        if ( file_exists( RCH_EXPORT_CLASSES_DIR . '/class-rch-post.php' ) ) {

                                require_once(RCH_EXPORT_CLASSES_DIR . '/class-rch-post.php');
                        }
                        $export_engine = '\rch\export\post\RCH_Post';
                }

                $export_engine = apply_filters( 'rch_export_engine_init', $export_engine, $export_type, $template );

                $export_process = array ();

                if ( class_exists( $export_engine ) ) {

                        $export_data = new $export_engine();

                        if ( method_exists( $export_data, "init_engine" ) ) {
                                $export_process = $export_data->init_engine( $export_type, $opration, $template );
                        }

                        unset( $export_data );
                } else {
                        return new \WP_Error( 'rch_import_error', sprintf( __( 'Class %s Not Exist', 'rch-woo-import-export' ), $export_engine ) );
                }

                unset( $export_engine, $export_type );

                return $export_process;
        }

        public function get_export_type() {

                global $wp_version;

                $custom_export_type = get_post_types( array ( '_builtin' => true ), 'objects' ) + get_post_types( array ( '_builtin' => false, 'show_ui' => true ), 'objects' ) + get_post_types( array ( '_builtin' => false, 'show_ui' => false ), 'objects' );

                if ( ! empty( $custom_export_type ) ) {

                        foreach ( $custom_export_type as $key => $ct ) {
                                if ( ! in_array( $key, array( 'shop_order', 'shop_coupon', 'shop_customer', 'product' ) ) ) {
                                        unset( $custom_export_type[ $key ] );
                                }
                        }
                }

                $custom_export_type = apply_filters( 'rch_custom_export_types', $this->rch_manage_woo_data( $custom_export_type ) );

                $export_type = array ();

                if ( ! empty( $custom_export_type ) ) {

                        foreach ( $custom_export_type as $key => $data ) {

                                $export_type[ $key ] = $data;

                                if ( ! empty( $custom_export_type[ 'product' ] ) && $key == 'product' ) {

                                        $export_type[ 'taxonomies' ] = new \stdClass();
                                        $export_type[ 'taxonomies' ]->labels = new \stdClass();
                                        $export_type[ 'taxonomies' ]->labels->name = __( 'Taxonomies', 'rch-woo-import-export' );

                                        $export_type[ 'comments' ] = new \stdClass();
                                        $export_type[ 'comments' ]->labels = new \stdClass();
                                        $export_type[ 'comments' ]->labels->name = __( 'Comments', 'rch-woo-import-export' );
                                }
                        }
                }

                $wc_types = array ( 'shop_order', 'shop_coupon', 'shop_customer', 'product' );

                foreach ( $wc_types as $data ) {

                        if ( ! empty( $custom_export_type[ $data ] ) ) {
                                $export_type[ $data ] = $custom_export_type[ $data ];
                        }
                }

                uasort( $custom_export_type, array ( $this, "set_export_custom_types" ) );

                foreach ( $custom_export_type as $key => $data ) {
                        if ( empty( $export_type[ $key ] ) ) {
                                $export_type[ $key ] = $data;
                        }
                }
                unset( $wc_types, $custom_export_type );

                return $export_type;
        }

        public function set_export_custom_types( $key = null, $data = null ) {
                return strcmp( $key->labels->name, $data->labels->name );
        }

        private function rch_manage_woo_data( $custom_data_types = array () ) {

                if ( class_exists( 'WooCommerce' ) ) {

                        if ( isset( $custom_data_types[ 'product' ] ) && ! empty( $custom_data_types[ 'product' ] ) ) {
                                $custom_data_types[ 'product' ]->labels->name = __( 'WooCommerce Products', 'rch-woo-import-export' );
                        }
                        if ( isset( $custom_data_types[ 'shop_order' ] ) && ! empty( $custom_data_types[ 'shop_order' ] ) ) {
                                $custom_data_types[ 'shop_order' ]->labels->name = __( 'WooCommerce Orders', 'rch-woo-import-export' );
                        }
                        if ( isset( $custom_data_types[ 'shop_coupon' ] ) && ! empty( $custom_data_types[ 'shop_coupon' ] ) ) {
                                $custom_data_types[ 'shop_coupon' ]->labels->name = __( 'WooCommerce Coupons', 'rch-woo-import-export' );
                        }
                        if ( isset( $custom_data_types[ 'product_variation' ] ) && ! empty( $custom_data_types[ 'product_variation' ] ) ) {
                                unset( $custom_data_types[ 'product_variation' ] );
                        }
                        if ( isset( $custom_data_types[ 'shop_order_refund' ] ) && ! empty( $custom_data_types[ 'shop_order_refund' ] ) ) {
                                unset( $custom_data_types[ 'shop_order_refund' ] );
                        }

                        $wc_types = array ( 'shop_order', 'shop_coupon', 'shop_customer', 'product' );

                        $wc_custom_types = array ();

                        foreach ( $wc_types as $type ) {

                                if ( isset( $wc_custom_types[ $type ] ) ) {
                                        continue;
                                }

                                if ( $type == 'shop_customer' ) {
                                        $wc_custom_types[ 'shop_customer' ] = new \stdClass();
                                        $wc_custom_types[ 'shop_customer' ]->labels = new \stdClass();
                                        $wc_custom_types[ 'shop_customer' ]->labels->name = __( 'WooCommerce Customers', 'rch-woo-import-export' );
                                } else {
                                        if ( ! empty( $custom_data_types ) ) {
                                                foreach ( $custom_data_types as $key => $custom_type ) {
                                                        if ( isset( $wc_custom_types[ $key ] ) ) {
                                                                continue;
                                                        }

                                                        if ( in_array( $key, $wc_types ) ) {
                                                                if ( $key == $type ) {
                                                                        $wc_custom_types[ $key ] = $custom_type;
                                                                }
                                                        } else {
                                                                $wc_custom_types[ $key ] = $custom_type;
                                                        }
                                                }
                                        }
                                }
                        }

                        unset( $custom_data_types, $wc_types );

                        return $wc_custom_types;
                }
                return $custom_data_types;
        }

        public function rch_get_taxonomies() {

                $taxonomies = get_taxonomies( false, 'objects' );

                $ignore_taxonomies = array ( 'nav_menu', 'link_category' );

                $result = array ();

                if ( ! empty( $taxonomies ) ) {

                        foreach ( $taxonomies as $_key => $taxonomy ) {

                                if ( isset( $taxonomy->object_type ) && ! in_array( "product", $taxonomy->object_type ) ) {
                                        continue;
                                }
                                if ( in_array( $_key, $ignore_taxonomies ) || (isset( $taxonomy->show_in_nav_menus ) && $taxonomy->show_in_nav_menus === false) ) {
                                        continue;
                                }

                                if ( $_key === "product_cat" ) {
                                        $_label = "Product Category";
                                } else {
                                        $_label = ucwords( str_replace( '_', ' ', $_key ) );
                                }

                                $result[ $_key ] = $_label;
                        }
                }

                unset( $taxonomies, $ignore_taxonomies );

                asort( $result, SORT_FLAG_CASE | SORT_STRING );

                return $result;
        }

        protected function get_export_rule() {

                $rch_export_rules = array (
                        'rch_tax'              => array (
                                'in'     => __( 'In', 'rch-woo-import-export' ),
                                'not_in' => __( 'Not In', 'rch-woo-import-export' )
                        ),
                        'rch_date'             => array (
                                'equals'            => __( 'equals', 'rch-woo-import-export' ),
                                'not_equals'        => __( "doesn't equal", 'rch-woo-import-export' ),
                                'greater'           => __( 'newer than', 'rch-woo-import-export' ),
                                'equals_or_greater' => __( 'equal to or newer than', 'rch-woo-import-export' ),
                                'less'              => __( 'older than', 'rch-woo-import-export' ),
                                'equals_or_less'    => __( 'equal to or older than', 'rch-woo-import-export' ),
                                'contains'          => __( 'contains', 'rch-woo-import-export' ),
                                'not_contains'      => __( "doesn't contain", 'rch-woo-import-export' ),
                                'is_empty'          => __( 'is empty', 'rch-woo-import-export' ),
                                'is_not_empty'      => __( 'is not empty', 'rch-woo-import-export' ),
                        ),
                        'rch_capabilities'     => array (
                                'contains'     => __( 'contains', 'rch-woo-import-export' ),
                                'not_contains' => __( "doesn't contain", 'rch-woo-import-export' ),
                        ),
                        'rch_user'             => array (
                                'equals'       => __( 'equals', 'rch-woo-import-export' ),
                                'not_equals'   => __( "doesn't equal", 'rch-woo-import-export' ),
                                'contains'     => __( 'contains', 'rch-woo-import-export' ),
                                'not_contains' => __( "doesn't contain", 'rch-woo-import-export' ),
                                'is_empty'     => __( 'is empty', 'rch-woo-import-export' ),
                                'is_not_empty' => __( 'is not empty', 'rch-woo-import-export' ),
                        ),
                        'rch_term_parent_slug' => array (
                                'equals'            => __( 'equals', 'rch-woo-import-export' ),
                                'not_equals'        => __( "doesn't equal", 'rch-woo-import-export' ),
                                'greater'           => __( 'greater than', 'rch-woo-import-export' ),
                                'equals_or_greater' => __( 'equal to or greater than', 'rch-woo-import-export' ),
                                'less'              => __( 'less than', 'rch-woo-import-export' ),
                                'equals_or_less'    => __( 'equal to or less than', 'rch-woo-import-export' ),
                                'is_empty'          => __( 'is empty', 'rch-woo-import-export' ),
                                'is_not_empty'      => __( 'is not empty', 'rch-woo-import-export' ),
                        ),
                        'default'               => array (
                                'equals'            => __( 'equals', 'rch-woo-import-export' ),
                                'not_equals'        => __( "doesn't equal", 'rch-woo-import-export' ),
                                'greater'           => __( 'greater than', 'rch-woo-import-export' ),
                                'equals_or_greater' => __( 'equal to or greater than', 'rch-woo-import-export' ),
                                'less'              => __( 'less than', 'rch-woo-import-export' ),
                                'equals_or_less'    => __( 'equal to or less than', 'rch-woo-import-export' ),
                                'contains'          => __( 'contains', 'rch-woo-import-export' ),
                                'not_contains'      => __( "doesn't contain", 'rch-woo-import-export' ),
                                'is_empty'          => __( 'is empty', 'rch-woo-import-export' ),
                                'is_not_empty'      => __( 'is not empty', 'rch-woo-import-export' ),
                                'in'                => __( 'In', 'rch-woo-import-export' ),
                                'not_in'            => __( 'Not In', 'rch-woo-import-export' )
                        )
                );


                $return_value = array ();

                $return_value[ 'status' ] = 'success';

                $return_value[ 'rch_export_rule' ] = apply_filters( "rch_export_ruels", $rch_export_rules );

                echo json_encode( $return_value );

                die();
        }

        protected function save_template_data() {

                global $wpdb;

                $template_name = isset( $_POST[ 'template_name' ] ) ? rch_sanitize_field( $_POST[ 'template_name' ] ) : "";

                $template_id = isset( $_POST[ 'template_id' ] ) ? absint( rch_sanitize_field( $_POST[ 'template_id' ] ) ) : 0;

                if ( $template_id > 0 ) {

                        $options = $wpdb->get_var( $wpdb->prepare( "SELECT `options` FROM " . $wpdb->prefix . "rch_template where `id`=%d", $template_id ) );

                        if ( ! is_null( $options ) ) {

                                $options = maybe_unserialize( $options );

                                $new_options = $_POST;

                                $new_options[ 'template_name' ] = isset( $options[ 'template_name' ] ) ? $options[ 'template_name' ] : "";

                                $new_values = array ();

                                $new_values[ 'options' ] = maybe_serialize( $new_options );

                                $wpdb->update( $wpdb->prefix . "rch_template", $new_values, array ( 'id' => $template_id ) );

                                $return_value[ 'status' ] = 'success';

                                $return_value[ 'message' ] = __( 'Template has been successfully updated', 'rch-woo-import-export' );

                                echo json_encode( $return_value );

                                die();
                        }
                }
                $is_exist = false;

                if ( ! empty( $template_name ) ) {

                        $results = $wpdb->get_results( "SELECT `id`,`options` FROM " . $wpdb->prefix . "rch_template where `opration`='export_template'" );

                        if ( ! empty( $results ) ) {

                                foreach ( $results as $template ) {

                                        $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array ();

                                        $temp_name = isset( $options[ 'template_name' ] ) ? $options[ 'template_name' ] : "";

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

                        $new_values = array ();

                        $new_values[ 'opration' ] = "export_template";

                        $new_values[ 'opration_type' ] = isset( $_POST[ 'rch_export_type' ] ) ? rch_sanitize_field( $_POST[ 'rch_export_type' ] ) : "post";

                        $new_values[ 'options' ] = maybe_serialize( $_POST );

                        $new_values[ 'create_date' ] = current_time( 'mysql' );

                        $new_values[ 'unique_id' ] = uniqid();

                        $current_user = wp_get_current_user();

                        if ( $current_user && isset( $current_user->user_login ) ) {
                                $new_values[ 'username' ] = $current_user->user_login;
                        }

                        $wpdb->insert( $wpdb->prefix . "rch_template", $new_values );

                        unset( $new_values, $current_user );

                        $template_id = $wpdb->insert_id;

                        $return_value = array ();

                        if ( $template_id && absint( $template_id ) > 0 ) {

                                $return_value[ 'status' ] = 'success';

                                $return_value[ 'template_id' ] = $template_id;

                                $return_value[ 'message' ] = __( 'Template has been successfully saved', 'rch-woo-import-export' );
                        } else {

                                $return_value[ 'status' ] = 'error';

                                $return_value[ 'message' ] = __( 'Fail to save template in database', 'rch-woo-import-export' );
                        }
                        unset( $template_id );
                } else {
                        $return_value[ 'status' ] = 'error';

                        $return_value[ 'message' ] = __( 'Template already exists', 'rch-woo-import-export' );
                }

                echo json_encode( $return_value );

                die();
        }

        protected function get_template() {

                $return_value = array ();

                $template_id = isset( $_GET[ 'template_id' ] ) ? absint( rch_sanitize_field( $_GET[ 'template_id' ] ) ) : 0;

                if ( $template_id > 0 ) {

                        $template_data = $this->get_template_by_id( $template_id );

                        if ( $template_data !== false && isset( $template_data->options ) ) {

                                $options = isset( $template_data->options ) ? wp_unslash( maybe_unserialize( $template_data->options ) ) : array ();

                                $template_data->fields_data = isset( $options[ 'fields_data' ] ) ? wp_unslash( $options[ 'fields_data' ] ) : array ();

                                $return_value[ 'message' ] = 'success';

                                $return_value[ 'data' ] = $options;
                        } else {
                                $return_value[ 'status' ] = 'error';

                                $return_value[ 'message' ] = __( 'Template Not Found', 'rch-woo-import-export' );
                        }

                        unset( $template_data );
                } else {
                        $return_value[ 'status' ] = 'error';

                        $return_value[ 'message' ] = __( 'Template Not Found', 'rch-woo-import-export' );
                }
                unset( $template_id );

                echo json_encode( $return_value );

                die();
        }

        protected function get_template_by_id( $export_id = 0 ) {

                if ( ! empty( $export_id ) && absint( $export_id ) > 0 ) {

                        global $wpdb;

                        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "rch_template where `id` = %d", $export_id ) );

                        if ( ! empty( $results ) && isset( $results[ 0 ] ) ) {
                                return $results[ 0 ];
                        }
                }
                return false;
        }

        protected function update_process_status() {

                global $wpdb;

                $return_value = array ( "status" => "error" );

                $rch_import_id = isset( $_GET[ 'rch_export_id' ] ) ? absint( rch_sanitize_field( $_GET[ 'rch_export_id' ] ) ) : 0;

                if ( $rch_import_id > 0 ) {

                        $process_status = isset( $_GET[ 'process_status' ] ) ? rch_sanitize_field( $_GET[ 'process_status' ] ) : "";

                        $new_satus = "";

                        if ( $process_status == "bg" ) {

                                $new_satus = "background";

                                $return_value[ 'message' ] = __( 'Background process has been successfully set', 'rch-woo-import-export' );
                        } elseif ( $process_status == "stop" ) {

                                $new_satus = "stopped";

                                $return_value[ 'message' ] = __( 'Process has been stopped successfully', 'rch-woo-import-export' );
                        }

                        unset( $process_status );

                        if ( $new_satus != "" ) {

                                $final_data = array (
                                        'last_update_date' => current_time( 'mysql' ),
                                        'status'           => $new_satus,
                                );

                                $wpdb->update( $wpdb->prefix . "rch_template", $final_data, array ( 'id' => $rch_import_id ) );

                                unset( $final_data );

                                $return_value[ 'status' ] = 'success';
                        } else {
                                $return_value[ 'message' ] = __( 'Empty Status', 'rch-woo-import-export' );
                        }

                        unset( $new_satus );
                } else {
                        $return_value[ 'message' ] = __( 'Template id not found', 'rch-woo-import-export' );
                }

                unset( $rch_import_id );

                echo json_encode( $return_value );

                die();
        }

        protected function get_safe_dir_name( $str = "", $separator = 'dash', $lowercase = true ) {

                if ( $separator == 'dash' ) {
                        $search = '_';
                        $replace = '-';
                } else {
                        $search = '-';
                        $replace = '_';
                }

                $trans = array (
                        '&\#\d+?;'       => '',
                        '&\S+?;'         => '',
                        '\s+'            => $replace,
                        '[^a-z0-9\-\._]' => '',
                        $search . '+'    => $replace,
                        $search . '$'    => $replace,
                        '^' . $search    => $replace,
                        '\.+$'           => ''
                );

                $str = strip_tags( $str );

                foreach ( $trans as $key => $val ) {
                        $str = preg_replace( "#" . $key . "#i", $val, $str );
                }

                if ( $lowercase === true ) {
                        $str = strtolower( $str );
                }
                unset( $search, $replace, $trans );

                return md5( trim( wp_unslash( $str ) ) . time() );
        }

        protected function init_new_export() {

                $export_id = $this->generate_template( $_POST, 'export' );

                $return_value = array ();

                $return_value[ 'status' ] = 'success';

                $return_value[ 'export_id' ] = $export_id;

                unset( $export_id );

                echo json_encode( $return_value );

                die();
        }

        protected function generate_template( $options = array (), $template_type = 'export', $status = 'processing' ) {

                $options[ 'max_item_count' ] = apply_filters( 'rch_export_max_item_count', 1, $options );

                $file_data = $this->set_file_headers( $options );

                $options[ 'fileName' ] = isset( $file_data[ 'filename' ] ) ? $file_data[ 'filename' ] : "";

                $options[ 'fileDir' ] = isset( $file_data[ 'filedir' ] ) ? $file_data[ 'filedir' ] : "";

                $total = 0;

                if ( isset( $options[ "total" ] ) ) {

                        $total = absint( $options[ "total" ] );

                        unset( $options[ "total" ] );
                }

                $rch_export_type = (isset( $options[ 'rch_export_type' ] ) && trim( $options[ 'rch_export_type' ] ) != "") ? rch_sanitize_field( $options[ 'rch_export_type' ] ) : "post";

                $current_time = current_time( 'mysql' );

                $new_values = array ();

                $new_values[ 'opration' ] = $template_type;

                $new_values[ 'opration_type' ] = $rch_export_type;

                $new_values[ 'process_lock' ] = 0;

                $new_values[ 'process_log' ] = maybe_serialize( array ( "total" => $total ) );

                $new_values[ 'status' ] = $status;

                $new_values[ 'options' ] = maybe_serialize( $options );

                $new_values[ 'create_date' ] = $current_time;

                $new_values[ 'last_update_date' ] = $current_time;

                $new_values[ 'unique_id' ] = uniqid();

                $current_user = wp_get_current_user();

                if ( $current_user && isset( $current_user->user_login ) ) {
                        $new_values[ 'username' ] = $current_user->user_login;
                }

                global $wpdb;

                $wpdb->insert( $wpdb->prefix . "rch_template", $new_values );

                unset( $options, $file_data, $total, $rch_export_type, $current_time, $new_values );

                return $wpdb->insert_id;
        }

        private function generate_config_file( $options = array () ) {

                $rch_export_type = isset( $options[ 'rch_export_type' ] ) ? $options[ 'rch_export_type' ] : "post";

                $config = array ();

                $config[ "import_type" ] = $rch_export_type;

                $config[ "site_url" ] = site_url();

                $config[ "import_sub_type" ] = isset( $options[ 'rch_taxonomy_type' ] ) ? $options[ 'rch_taxonomy_type' ] : "";

                $fields_data = (isset( $options[ 'fields_data' ] ) && trim( $options[ 'fields_data' ] ) != "") ? explode( "~||~", rch_sanitize_field( wp_unslash( $options[ 'fields_data' ] ) ) ) : array ();

                $export_fields = array ( "is_exported" => 1 );

                if ( $rch_export_type == "taxonomies" ) {
                        $export_fields[ "rch_existing_item_search_logic" ] = "slug";
                } elseif ( $rch_export_type == "product" ) {
                        $export_fields[ "rch_item_variation_import_method" ] = "match_unique_field";
                        $export_fields[ "rch_item_product_variation_field_parent" ] = "{id[1]}";
                        $export_fields[ "rch_item_product_variation_match_unique_field_parent" ] = "{parent[1]}";
                } elseif ( $rch_export_type == "shop_order" ) {
                        /* billing fields */
                        $export_fields[ "rch_item_order_billing_source" ] = "existing";
                        $export_fields[ "rch_item_order_billing_match_by" ] = "email";
                        $export_fields[ "rch_item_order_billing_match_by_email" ] = "{_customer_user_email[1]}";
                        $export_fields[ "rch_item_order_billing_no_match_guest" ] = "1";
                        $export_fields[ "rch_item_guest_billing_first_name" ] = "{_billing_first_name[1]}";
                        $export_fields[ "rch_item_guest_billing_last_name" ] = "{_billing_last_name[1]}";
                        $export_fields[ "rch_item_guest_billing_address_1" ] = "{_billing_address_1[1]}";
                        $export_fields[ "rch_item_guest_billing_address_2" ] = "{_billing_address_2[1]}";
                        $export_fields[ "rch_item_guest_billing_city" ] = "{_billing_city[1]}";
                        $export_fields[ "rch_item_guest_billing_postcode" ] = "{_billing_postcode[1]}";
                        $export_fields[ "rch_item_guest_billing_country" ] = "{_billing_country[1]}";
                        $export_fields[ "rch_item_guest_billing_state" ] = "{_billing_state[1]}";
                        $export_fields[ "rch_item_guest_billing_email" ] = "{_billing_email[1]}";
                        $export_fields[ "rch_item_guest_billing_phone" ] = "{_billing_phone[1]}";
                        $export_fields[ "rch_item_guest_billing_company" ] = "{_billing_company[1]}";

                        /* shipping fields */
                        $export_fields[ "rch_item_order_shipping_source" ] = "guest";
                        $export_fields[ "rch_item_order_shipping_no_match_billing" ] = "1";
                        $export_fields[ "rch_item_shipping_first_name" ] = "{_shipping_first_name[1]}";
                        $export_fields[ "rch_item_shipping_last_name" ] = "{_shipping_last_name[1]}";
                        $export_fields[ "rch_item_shipping_address_1" ] = "{_shipping_address_1[1]}";
                        $export_fields[ "rch_item_shipping_address_2" ] = "{_shipping_address_2[1]}";
                        $export_fields[ "rch_item_shipping_city" ] = "{_shipping_city[1]}";
                        $export_fields[ "rch_item_shipping_postcode" ] = "{_shipping_postcode[1]}";
                        $export_fields[ "rch_item_shipping_country" ] = "{_shipping_country[1]}";
                        $export_fields[ "rch_item_shipping_state" ] = "{_shipping_state[1]}";
                        $export_fields[ "rch_item_shipping_email" ] = "";
                        $export_fields[ "rch_item_shipping_phone" ] = "";
                        $export_fields[ "rch_item_shipping_company" ] = "{_shipping_company[1]}";
                        $export_fields[ "rch_item_order_customer_provided_note" ] = "{customernote[1]}";

                        /* payment fields */
                        $export_fields[ "rch_item_order_payment_method" ] = "as_specified";
                        $export_fields[ "rch_item_order_payment_method_as_specified_data" ] = "{paymentmethodtitle[1]}";
                        $export_fields[ "rch_item_order_transaction_id" ] = "{transactionid[1]}";

                        /* Order Items List Start */

                        /* Product Item */
                        $export_fields[ "rch_item_order_item_product_name" ] = "{productname1[1]}";
                        $export_fields[ "rch_item_order_item_product_price" ] = "{itemcost1[1]}";
                        $export_fields[ "rch_item_order_item_product_quantity" ] = "{quantity1[1]}";
                        $export_fields[ "rch_item_order_item_product_sku" ] = "{sku1[1]}";
                        $export_fields[ "rch_item_order_item_product_delim" ] = "|";

                        $rch_order_item_count = isset( $options[ 'rch_order_item_count' ] ) ? intval( $options[ 'rch_order_item_count' ] ) : 0;

                        if ( $rch_order_item_count > 1 ) {

                                for ( $i = 2; $i <= $rch_order_item_count; $i ++ ) {

                                        $export_fields[ "rch_item_order_item_product_name" ] .= "|{productname" . $i . "[1]}";
                                        $export_fields[ "rch_item_order_item_product_price" ] .= "|{itemcost" . $i . "[1]}";
                                        $export_fields[ "rch_item_order_item_product_quantity" ] .= "|{quantity" . $i . "[1]}";
                                        $export_fields[ "rch_item_order_item_product_sku" ] .= "|{sku" . $i . "[1]}";
                                }
                        }

                        /* Fee Item */
                        $export_fields[ "rch_item_order_item_fee" ] = "{feename[1]}";
                        $export_fields[ "rch_item_order_item_fee_amount" ] = "{feeamountpersurcharge[1]}";
                        $export_fields[ "rch_item_order_item_fees_delim" ] = "|";

                        /* Coupons Item */
                        $export_fields[ "rch_item_order_item_coupon" ] = "{couponsused[1]}";
                        $export_fields[ "rch_item_order_item_coupon_amount" ] = "{discountamountpercoupon[1]}";
                        $export_fields[ "rch_item_order_item_coupon_amount_tax" ] = "";
                        $export_fields[ "rch_item_order_item_coupon_delim" ] = "|";

                        /* Shipping Item */
                        $export_fields[ "rch_item_order_item_shipping_name" ] = "{shippingmethod[1]}";
                        $export_fields[ "rch_item_order_item_shipping_amount" ] = "{shippingcost[1]}";
                        $export_fields[ "rch_item_order_item_shipping_method" ] = "{shippingmethod[1]}";
                        $export_fields[ "rch_item_order_item_shipping_costs_delim" ] = "|";

                        /* Taxes Item */
                        $export_fields[ "rch_item_order_item_tax_rate_amount" ] = "{amountpertax[1]}";
                        $export_fields[ "rch_item_order_item_tax_shipping_tax_amount" ] = "{shippingtaxes[1]}";
                        $export_fields[ "rch_item_order_item_tax_rate" ] = "{ratecodepertax[1]}";
                        $export_fields[ "rch_item_order_item_shipping_costs_delim" ] = "|";

                        /* Order Items List End */

                        /* Refunds */
                        $export_fields[ "rch_item_order_item_refund_amount" ] = "{refundamounts[1]}";
                        $export_fields[ "rch_item_order_item_refund_reason" ] = "{refundreason[1]}";
                        $export_fields[ "rch_item_order_item_refund_date" ] = "{refunddate[1]}";
                        $export_fields[ "rch_item_order_item_refund_issued_match_by" ] = "existing";
                        $export_fields[ "rch_item_order_item_refund_issued_by" ] = "email";
                        $export_fields[ "rch_item_refund_customer_email" ] = "{refundauthoremail[1]}";

                        /* Order Total */
                        $export_fields[ "rch_item_order_total" ] = "manually";
                        $export_fields[ "rch_item_order_total_as_specified" ] = "{ordertotal[1]}";

                        /* Order Notes */
                        $export_fields[ "rch_item_import_order_note_content" ] = "{notecontent[1]}";
                        $export_fields[ "rch_item_import_order_note_date" ] = "{notedate[1]}";
                        $export_fields[ "rch_item_import_order_note_visibility" ] = "{notevisibility[1]}";
                        $export_fields[ "rch_item_import_order_note_username" ] = "{noteusername[1]}";
                        $export_fields[ "rch_item_import_order_note_email" ] = "{noteuseremail[1]}";
                        $export_fields[ "rch_item_import_order_note_delim" ] = "|";

                        /* Handle Existing Items */
                        $export_fields[ "rch_existing_item_search_logic" ] = "cf";
                        $export_fields[ "rch_existing_item_search_logic_cf_key" ] = "_order_key";
                        $export_fields[ "rch_existing_item_search_logic_cf_value" ] = "{orderkey[1]}";
                }

                if ( ! empty( $fields_data ) ) {

                        $configData = array ();
                        foreach ( $fields_data as $field ) {

                                if ( empty( $field ) ) {
                                        continue;
                                }
                                $new_field = explode( "|~|", $field );

                                $field_label = isset( $new_field[ 0 ] ) ? rch_sanitize_field( $new_field[ 0 ] ) : "";

                                $field_option = isset( $new_field[ 1 ] ) ? json_decode( rch_sanitize_field( $new_field[ 1 ] ), true ) : "";

                                unset( $new_field );

                                $field_type = isset( $field_option[ 'type' ] ) ? rch_sanitize_field( $field_option[ 'type' ] ) : "";

                                $fielData = "{" . strtolower( preg_replace( '/[^a-z0-9_]/i', '', $field_label ) ) . "[1]}";

                                if ( in_array( $fielData, $configData ) ) {

                                        $tempField = $fielData;

                                        $count = 1;

                                        while ( in_array( $tempField, $configData ) ) {
                                                $tempField = "{" . strtolower( preg_replace( '/[^a-z0-9_]/i', '', $field_label ) ) . "_" . $count . "[1]}";
                                                $count ++;
                                        }

                                        $fielData = $tempField;

                                        unset( $tempField );

                                        unset( $count );
                                }

                                $new_key = $field_type;

                                if ( $field_type == "wc-product" ) {
                                        $field_type = "rch_cf";
                                }

                                if ( $field_type == "rch_cf" ) {
                                        $is_acf = isset( $field_option[ 'is_acf' ] ) ? intval( rch_sanitize_field( $field_option[ 'is_acf' ] ) ) : 0;

                                        if ( $is_acf === 1 ) {
                                                continue;
                                        }

                                        $new_key = isset( $field_option[ 'metaKey' ] ) ? rch_sanitize_field( $field_option[ 'metaKey' ] ) : "";
                                } elseif ( $field_type == "rch_tax" ) {

                                        $new_key = isset( $field_option[ 'taxName' ] ) ? rch_sanitize_field( $field_option[ 'taxName' ] ) : "";
                                }

                                if ( $field_type == "rch-acf" ) {

                                        $acf_key = isset( $field_option[ 'acfKey' ] ) && ! empty( $field_option[ 'acfKey' ] ) ? $field_option[ 'acfKey' ] : "";

                                        if ( ! empty( $acf_key ) ) {

                                                $acf_field_id = isset( $field_option[ 'id' ] ) && ! empty( $field_option[ 'id' ] ) ? $field_option[ 'id' ] : "";

                                                $acf = [ $acf_key => $this->get_acf_field_data( $acf_field_id ) ];

                                                if ( ! empty( $acf ) ) {
                                                        if ( ! isset( $export_fields[ 'acf' ] ) ) {
                                                                $export_fields[ 'acf' ] = [];
                                                        }
                                                        $export_fields[ 'acf' ] = array_replace( $export_fields[ 'acf' ], $acf );
                                                }
                                                unset( $acf, $acf_field_id );
                                        }
                                        unset( $acf_key );
                                        continue;
                                }
                                if ( $rch_export_type == "shop_order" ) {

                                        if ( $field_type == "rch_cf" &&
                                                in_array( $new_key, array ( '_billing_first_name', '_billing_last_name', '_billing_company',
                                                        '_billing_address_1', '_billing_address_2', '_billing_city',
                                                        '_billing_postcode', '_billing_country', '_billing_state',
                                                        '_billing_email', '_customer_user_email', '_billing_phone',
                                                        '_shipping_first_name', '_shipping_last_name', '_shipping_company',
                                                        '_shipping_address_1', '_shipping_address_2', '_shipping_city',
                                                        '_shipping_postcode', '_shipping_country', '_shipping_state',
                                                        "_payment_method", "_transaction_id", "_payment_method_title", "_order_total",
                                                        '_customer_user'
                                                        )
                                                )
                                        ) {
                                                continue;
                                        } elseif ( $field_type == "wc-order" ) {

                                                $order_field_type = isset( $field_option[ 'field_type' ] ) ? rch_sanitize_field( $field_option[ 'field_type' ] ) : "";

                                                $order_field_key = isset( $field_option[ 'field_key' ] ) ? rch_sanitize_field( $field_option[ 'field_key' ] ) : "";

                                                if ( $order_field_type == "coupons" && in_array( $new_key, array ( "_cart_discount" ) ) ) {
                                                        continue;
                                                }
                                        }
                                }

                                if ( isset( $configData[ $new_key ] ) ) {

                                        $tempField = $new_key;

                                        $count = 0;

                                        while ( isset( $configData[ $tempField ] ) ) {
                                                $tempField = $new_key . "_" . $count;
                                                $count ++;
                                        }

                                        $new_key = $tempField;

                                        unset( $tempField );

                                        unset( $count );
                                }

                                if ( $field_type == "rch_cf" ) {

                                        if ( $rch_export_type == "product" && in_array( $new_key, array ( "_sku", "_regular_price", "_sale_price", "_sale_price_dates_from", "_sale_price_dates_to", "_virtual", "_downloadable", "_tax_status", "_tax_class", "_downloadable_files", "_downloadable_file_name", "_download_limit", "_download_expiry", "_manage_stock", "_stock", "_stock_status", "_backorders", "_sold_individually", "_weight", "_length", "_width", "_height", "_upsell_ids", "_crosssell_ids", "_purchase_note", "_featured", "_visibility" ) ) ) {

                                                $export_fields[ "rch_item_meta" . $new_key ] = $fielData;

                                                if ( $rch_export_type == "_downloadable_files" ) {
                                                        $export_fields[ "rch_item_downloadable_files_delim" ] = ",";
                                                        $export_fields[ "rch_item_downloadable_file_name_delim" ] = ",";
                                                }
                                        } else {
                                                $_uniqueid = uniqid();
                                                $export_fields[ "rch_item_cf" ][ $_uniqueid ][ "name" ] = $new_key;
                                                $export_fields[ "rch_item_cf" ][ $_uniqueid ][ "value" ] = $fielData;
                                        }
                                } elseif ( $field_type == "rch_tax" ) {

                                        if ( in_array( $new_key, array ( "product_type", "product_shipping_class" ) ) ) {

                                                $export_fields[ "rch_item_" . $new_key ] = $fielData;

                                                if ( $new_key == "product_shipping_class" ) {
                                                        $export_fields[ "rch_item_product_shipping_class_logic" ] = "as_specified";
                                                }
                                        } else {
                                                $export_fields[ "rch_item_set_taxonomy" ][ $new_key ] = 1;
                                                $export_fields[ "rch_item_taxonomy" ][ $new_key ] = $fielData;
                                                if ( isset( $field_option[ 'hierarchical' ] ) && $field_option[ 'hierarchical' ] == 1 ) {
                                                        $export_fields[ "rch_item_taxonomy_hierarchical_delim" ][ $new_key ] = ">";
                                                }
                                        }
                                } elseif ( $field_type == "wc-product-attr" ) {

                                        $attr_name = isset( $field_option[ 'name' ] ) ? strtolower( preg_replace( '/[^a-z0-9_]/i', '', $field_option[ 'name' ] ) ) : "";

                                        $export_fields[ "rch_product_attr_name" ][] = "{attributename" . $attr_name . "[1]}";
                                        $export_fields[ "rch_product_attr_value" ][] = "{attributevalue" . $attr_name . "[1]}";
                                        $export_fields[ "rch_attr_in_variations" ][] = "{attributeinvariations" . $attr_name . "[1]}";
                                        $export_fields[ "rch_attr_is_visible" ][] = "{attributeisvisible" . $attr_name . "[1]}";
                                        $export_fields[ "rch_attr_is_taxonomy" ][] = "{attributeistaxonomy" . $attr_name . "[1]}";
                                        $export_fields[ "rch_attr_is_auto_create_term" ][] = "yes";

                                        unset( $attr_name );
                                        continue;
                                } else {

                                        switch ( $new_key ) {
                                                case "author_email":
                                                        $export_fields[ "rch_item_author" ] = $fielData;
                                                        break;
                                                case "term_parent_slug":
                                                        $export_fields[ "rch_item_term_parent" ] = $fielData;
                                                        break;
                                                case "user_pass":
                                                        $export_fields[ "rch_item_set_hashed_password" ] = 1;
                                                        break;

                                                case "image_title":
                                                case "image_caption":
                                                case "image_description":
                                                case "image_alt":
                                                        $export_fields[ "rch_item_set_" . $new_key ] = 1;
                                                        break;
                                        }
                                        $export_fields[ "rch_item_" . $new_key ] = $fielData;

                                        unset( $updated_key );
                                }

                                $configData[ $new_key ] = $fielData;

                                unset( $fielData, $field_option, $field_label, $field_type );
                        }
                        unset( $configData );
                }


                $config[ "fields" ] = $export_fields;

                $type = isset( $options[ 'rch_export_file_type' ] ) && ! empty( $options[ 'rch_export_file_type' ] ) ? $options[ 'rch_export_file_type' ] : "csv";

                $fileName = isset( $options[ 'fileName' ] ) ? $options[ 'fileName' ] : "";

                if ( $type != "csv" && $fileName != "" ) {
                        $fileName = str_replace( ".csv", "." . $type, $fileName );
                }

                $config[ "fileName" ] = $fileName;

                $fileDir = isset( $options[ 'fileDir' ] ) ? $options[ 'fileDir' ] : "";

                $filePath = RCH_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . "config.json";

                file_put_contents( $filePath, json_encode( $config ) );

                unset( $config, $fields_data, $export_fields, $type, $fileName, $fileDir, $filePath );
        }

        private function get_acf_field_data( $field_id = "" ) {

                if ( empty( $field_id ) ) {
                        return;
                }

                $field = acf_get_field( $field_id );

                if ( ! is_array( $field ) || empty( $field ) ) {
                        return;
                }

                $data = array ();

                $type = isset( $field[ 'type' ] ) ? $field[ 'type' ] : "";

                $new_name = isset( $field[ 'label' ] ) && ! empty( $field[ 'label' ] ) ? strtolower( str_replace( ' ', '_', preg_replace( '/[^a-z0-9_]/i', '', $field[ 'label' ] ) ) ) : "";

                switch ( $type ) {
                        case "select":
                        case "checkbox":
                        case "radio":
                        case "button_group":
                        case "true_false":
                        case "taxonomy":
                        case 'repeater':
                        case 'flexible_content':
                        case 'clone':
                        case 'group':
                                $data = array (
                                        "value_option" => "custom",
                                        "custom_value" => "{" . $new_name . "[1]}",
                                        "type"         => $type
                                );
                                break;
                        case "image":
                        case "file":
                        case "gallery":
                                $data = array (
                                        "value"                => "{" . $new_name . "[1]}",
                                        "search_through_media" => "1",
                                        "use_upload_dir"       => "",
                                        "delim"                => ",",
                                        "type"                 => $type
                                );
                                break;
                        case "link":
                                $data = array (
                                        "value" => [
                                                "url"    => "{" . $new_name . "url[1]}",
                                                "title"  => "{" . $new_name . "title[1]}",
                                                "target" => "{" . $new_name . "target[1]}",
                                        ],
                                        "type"  => $type
                                );
                                break;
                        case "google_map":
                                $data = array (
                                        "value" => [
                                                "address" => "{" . $new_name . "address[1]}",
                                                "lat"     => "{" . $new_name . "lat[1]}",
                                                "lng"     => "{" . $new_name . "lng[1]}",
                                        ],
                                        "type"  => $type
                                );
                                break;
                        case "post_object":
                        case "page_link":
                        case "relationship":
                        case "user":
                                $data = array (
                                        "value" => "{" . $new_name . "[1]}",
                                        "delim" => ",",
                                        "type"  => $type
                                );
                                break;
                        default :
                                $data = array (
                                        "value" => "{" . $new_name . "[1]}",
                                        "type"  => $type
                                );

                                break;
                }

                return $data;
        }

        private function set_file_headers( $template_data = array () ) {

                $rch_export_type = (isset( $template_data[ 'rch_export_type' ] ) && trim( $template_data[ 'rch_export_type' ] ) != "") ? array ( rch_sanitize_field( $template_data[ 'rch_export_type' ] ) ) : array ( "post" );

                $export_type = $this->get_export_type();

                $temp_rch_export_type = $rch_export_type[ 0 ];

                unset( $rch_export_type );

                $export_type[ $temp_rch_export_type ] = isset( $export_type[ $temp_rch_export_type ] ) ? $export_type[ $temp_rch_export_type ] : "";

                if ( ! empty( $export_type[ $temp_rch_export_type ] ) && isset( $export_type[ $temp_rch_export_type ]->labels ) && isset( $export_type[ $temp_rch_export_type ]->labels->name ) ) {
                        $exported_data = $export_type[ $temp_rch_export_type ]->labels->name;
                } else {
                        $exported_data = 'post';
                }

                unset( $export_type );

                if ( $temp_rch_export_type == "taxonomies" ) {

                        $taxonomy_data = $this->rch_get_taxonomies();

                        $tax_temp_data = (isset( $template_data[ 'rch_taxonomy_type' ] ) && trim( $template_data[ 'rch_taxonomy_type' ] ) != "") ? rch_sanitize_field( $template_data[ 'rch_taxonomy_type' ] ) : "";

                        if ( ! empty( $tax_temp_data ) && isset( $taxonomy_data[ $tax_temp_data ] ) && ! empty( $taxonomy_data[ $tax_temp_data ] ) ) {
                                $exported_data = $taxonomy_data[ $tax_temp_data ];
                        }
                        unset( $tax_temp_data, $taxonomy_data );
                }
                unset( $temp_rch_export_type );

                $filename = sanitize_file_name( (isset( $template_data[ 'rch_export_file_name' ] ) && trim( $template_data[ 'rch_export_file_name' ] ) != "") ? $template_data[ 'rch_export_file_name' ] : $exported_data . ' Export ' . date( 'Y M d His' ) );

                $filename = apply_filters( 'rch_export_file_name', $filename );

                $filename = pathinfo( $filename, PATHINFO_FILENAME ) . '.csv';

                $export_dir = $this->get_safe_dir_name( $filename );

                wp_mkdir_p( RCH_UPLOAD_EXPORT_DIR . "/" . $export_dir );

                $filepath = RCH_UPLOAD_EXPORT_DIR . '/' . $export_dir . '/' . $filename;

                $fh = @fopen( $filepath, 'w+' );

                $rch_export_include_bom = (isset( $template_data[ 'rch_export_include_bom' ] ) && trim( $template_data[ 'rch_export_include_bom' ] ) != "") ? rch_sanitize_field( $template_data[ 'rch_export_include_bom' ] ) : "";

                if ( $rch_export_include_bom == 1 ) {
                        fwrite( $fh, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
                }

                fclose( $fh );

                unset( $exported_data, $filepath, $fh, $template_data, $rch_export_include_bom );

                return array ( "filename" => $filename, "filedir" => $export_dir );
        }

        protected function init_export_process() {

                $return_value = array ( "status" => "error" );

                $export_id = isset( $_GET[ 'export_id' ] ) ? absint( rch_sanitize_field( $_GET[ 'export_id' ] ) ) : 0;

                if ( $export_id > 0 ) {

                        $template = $this->get_template_by_id( $export_id );

                        if ( $template !== false ) {

                                $export_type = isset( $template->opration_type ) ? $template->opration_type : "post";

                                $process_log = $this->init_export( $export_type, "export", $template );

                                $return_value[ 'exported_records' ] = isset( $process_log[ 'exported' ] ) ? $process_log[ 'exported' ] : 0;

                                $total = isset( $process_log[ 'total' ] ) ? $process_log[ 'total' ] : 0;

                                unset( $export_type, $process_log );

                                if ( $return_value[ 'exported_records' ] >= $total ) {

                                        $return_value[ 'export_status' ] = 'completed';
                                } else {
                                        $return_value[ 'export_status' ] = 'processing';
                                }

                                unset( $total );

                                $return_value[ 'status' ] = 'success';
                        } else {
                                $return_value[ 'message' ] = __( 'Template not found', 'rch-woo-import-export' );
                        }
                        unset( $template );
                } else {
                        $return_value[ 'message' ] = __( 'Template not found', 'rch-woo-import-export' );
                }

                unset( $export_id );

                echo json_encode( $return_value );

                die();
        }

        protected function prepare_file() {

                $return_value = array ( "status" => "error" );

                $export_id = isset( $_GET[ 'export_id' ] ) ? absint( rch_sanitize_field( $_GET[ 'export_id' ] ) ) : 0;

                $process = $this->process_export_file( $export_id );

                if ( is_wp_error( $process ) ) {
                        $return_value[ 'message' ] = $process->get_error_message();
                } else {
                        $return_value[ 'status' ] = 'success';
                }

                echo json_encode( $return_value );

                die();
        }

        protected function process_export_file( $export_id = "" ) {

                if ( $export_id > 0 ) {

                        $template = $this->get_template_by_id( $export_id );

                        if ( $template !== false ) {

                                $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array ();

                                $filename = isset( $options[ 'fileName' ] ) ? $options[ 'fileName' ] : "";

                                $fileDir = isset( $options[ 'fileDir' ] ) ? $options[ 'fileDir' ] : "";

                                $is_package = isset( $options[ 'is_package' ] ) ? intval( $options[ 'is_package' ] ) : 0;

                                if ( $template->opration === "schedule_export" ) {
                                        $is_package = isset( $options[ 'is_migrate_package' ] ) ? intval( $options[ 'is_migrate_package' ] ) : 0;
                                }

                                $delim = isset( $options[ 'rch_csv_field_separator' ] ) ? $options[ 'rch_csv_field_separator' ] : ",";

                                $type = isset( $options[ 'rch_export_file_type' ] ) && ! empty( $options[ 'rch_export_file_type' ] ) ? $options[ 'rch_export_file_type' ] : "csv";

                                $new_type = "";

                                if ( $is_package === 0 ) {

                                        if ( $type != "" || $type != "csv" ) {

                                                switch ( $type ) {

                                                        case "xml" :
                                                                $data = $this->csv2xml( $filename, $fileDir );

                                                                break;
                                                        case "json" :
                                                                $data = $this->csv2json( $filename, $fileDir );
                                                                break;
                                                        case "xls" :
                                                        case "xlsx" :
                                                        case "ods" :
                                                                $data = $this->csv2excel( $filename, $fileDir, $type );
                                                                break;
                                                }

                                                if ( isset( $data ) && is_wp_error( $data ) ) {
                                                        return $data;
                                                }

                                                $new_type = $type;
                                        }
                                } else {

                                        $is_success = $this->create_zip( $options );

                                        if ( is_wp_error( $is_success ) ) {

                                                return $data;
                                        }

                                        unset( $is_success );

                                        $new_type = "zip";
                                }

                                if ( $new_type != "" ) {

                                        $options[ 'fileName' ] = str_replace( ".csv", "." . $new_type, $filename );

                                        global $wpdb;

                                        $wpdb->update( $wpdb->prefix . "rch_template", array ( 'options' => maybe_serialize( $options ) ), array ( 'id' => $export_id ) );
                                }

                                $extra_copy_path = isset( $options[ 'extra_copy_path' ] ) && ! empty( $options[ 'extra_copy_path' ] ) ? ltrim( trailingslashit( sanitize_text_field( $options[ 'extra_copy_path' ] ) ), '/\\' ) : "";

                                if ( ! empty( $extra_copy_path ) && is_dir( RCH_SITE_UPLOAD_DIR . "/" . $extra_copy_path ) ) {

                                        @copy( RCH_UPLOAD_EXPORT_DIR . '/' . $fileDir . '/' . $options[ 'fileName' ], RCH_SITE_UPLOAD_DIR . "/" . $extra_copy_path . $options[ 'fileName' ] );
                                }

                                unset( $options, $filename, $fileDir, $is_package, $delim, $type, $new_type );
                        } else {
                                return new \WP_Error( 'woo_import_export_error', __( 'Template not found', 'rch-woo-import-export' ) );
                        }
                        unset( $template );
                } else {
                        return new \WP_Error( 'woo_import_export_error', __( 'Template not found', 'rch-woo-import-export' ) );
                }

                return true;
        }

        protected function create_zip( $options = array () ) {

                $this->generate_config_file( $options );

                $zip = new \ZipArchive();

                $filename = isset( $options[ 'fileName' ] ) ? $options[ 'fileName' ] : "";

                $fileDir = isset( $options[ 'fileDir' ] ) ? $options[ 'fileDir' ] : "";

                $zipfile = RCH_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . str_replace( ".csv", ".zip", $filename );

                if ( $zip->open( $zipfile, \ZIPARCHIVE::CREATE ) != TRUE ) {

                        return new \WP_Error( 'woo_import_export_error', __( 'Could not open archive', 'rch-woo-import-export' ) );
                }

                unset( $zipfile );

                $zip->addFile( RCH_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . $filename, $filename );

                $zip->addFile( RCH_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/config.json", "config.json" );

                $zip->close();

                unset( $zip );

                return true;
        }

        private function csv2excel( $filename = "", $fileDir = "", $type = "xlsx" ) {

                $file = RCH_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . $filename;

                if ( ! file_exists( $file ) ) {
                        return new \WP_Error( 'rch_import_error', __( 'File not found', 'rch-woo-import-export' ) );
                }

                if ( file_exists( RCH_DEPENCY_DIR . '/composer/vendor/autoload.php' ) ) {
                        require_once( RCH_DEPENCY_DIR . '/composer/vendor/autoload.php' );
                }

                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $file );

                unset( $reader, $file );

                if ( $type == "xls" ) {
                        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls( $spreadsheet );
                } elseif ( $type == "ods" ) {
                        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Ods( $spreadsheet );
                } else {
                        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx( $spreadsheet );
                }
                $writer->save( RCH_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . str_replace( ".csv", "." . $type, $filename ) );

                $spreadsheet->disconnectWorksheets();

                unset( $writer, $spreadsheet );

                return true;
        }

        private function csv2json( $filename = "", $fileDir = "" ) {

                $file = RCH_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . $filename;

                if ( ! file_exists( $file ) ) {
                        return new \WP_Error( 'rch_import_error', __( 'File not found', 'rch-woo-import-export' ) );
                }

                $csv = array ();

                if ( ($handle = fopen( $file, 'r' )) !== FALSE ) {
                        $i = 0;
                        while ( ($lineArray = fgetcsv( $handle, 4000, ",", '"' )) !== FALSE ) {
                                for ( $j = 0; $j < count( $lineArray ); $j ++ ) {
                                        $csv[ $i ][ $j ] = $lineArray[ $j ];
                                }
                                $i ++;
                        }
                        fclose( $handle );
                }

                unset( $file );

                array_walk( $csv, function(&$a) use ($csv) {
                        $a = array_combine( $csv[ 0 ], $a );
                } );

                array_shift( $csv );

                file_put_contents( RCH_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . str_replace( ".csv", ".json", $filename ), json_encode( $csv ) );

                unset( $csv );

                return true;
        }

        private function csv2xml( $filename = "", $fileDir = "" ) {

                $file = RCH_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . $filename;

                if ( ! file_exists( $file ) ) {
                        return new \WP_Error( 'rch_import_error', __( 'File not found', 'rch-woo-import-export' ) );
                }

                if ( file_exists( RCH_DEPENCY_DIR . '/xml/class-rch-array2xml.php' ) ) {
                        require_once(RCH_DEPENCY_DIR . '/xml/class-rch-array2xml.php');
                }

                $converter = new \rch\lib\xml\array2xml\ArrayToXml();

                $converter->create_root( "rchdata" );

                $headers = array ();

                $wfp = fopen( $file, "rb" );

                unset( $file );

                while ( ($keys = fgetcsv( $wfp, 0 )) !== false ) {

                        if ( empty( $headers ) ) {

                                foreach ( $keys as $key => $value ) {

                                        $value = trim( strtolower( preg_replace( '/[^a-z0-9_]/i', '', $value ) ) );

                                        if ( preg_match( '/^[0-9]{1}/', $value ) ) {
                                                $value = 'el_' . trim( strtolower( $value ) );
                                        }

                                        $value = ( ! empty( $value )) ? $value : 'undefined' . $key;

                                        if ( isset( $headers[ $key ] ) ) {
                                                $key = $this->unique_array_key_name( $key, $headers );
                                        }

                                        $headers[ $key ] = $value;
                                }

                                continue;
                        }

                        $fileData = array ();

                        foreach ( $keys as $key => $value ) {

                                $header = isset( $headers[ $key ] ) ? $headers[ $key ] : "";

                                if ( ! empty( $header ) ) {

                                        if ( isset( $fileData[ $header ] ) ) {
                                                $header = $this->unique_array_key_name( $header, $fileData );
                                        }

                                        $fileData[ $header ] = $value;
                                }
                                unset( $header );
                        }

                        $converter->addNode( $converter->root, "item", $fileData, 0 );

                        unset( $fileData );
                }

                $converter->saveFile( RCH_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . str_replace( ".csv", ".xml", $filename ) );

                unset( $converter, $headers );

                return true;
        }

        protected function get_item_count() {

                $export_type = isset( $_POST[ 'rch_export_type' ] ) ? rch_sanitize_field( $_POST[ 'rch_export_type' ] ) : "post";

                $return_value = array ();

                $return_value[ "totalRecords" ] = $this->init_export( $export_type, "count", $_POST );

                unset( $export_type );

                $return_value[ 'status' ] = 'success';

                echo json_encode( $return_value );

                die();
        }

        protected function get_preview() {

                $export_type = isset( $_POST[ 'rch_export_type' ] ) ? rch_sanitize_field( $_POST[ 'rch_export_type' ] ) : "post";

                $return_value = array ();

                $_POST[ 'rch_records_per_iteration' ] = isset( $_POST[ 'length' ] ) ? absint( rch_sanitize_field( $_POST[ 'length' ] ) ) : 10;

                $return_value[ 'data' ] = $this->init_export( $export_type, "preview", $_POST );

                unset( $export_type );

                $return_value[ 'recordsTotal' ] = isset( $_POST[ 'total' ] ) ? absint( sanitize_text_field($_POST[ 'total' ]) ) : 0;

                $return_value[ 'recordsFiltered' ] = $return_value[ 'recordsTotal' ];

                $return_value[ 'status' ] = 'success';

                echo json_encode( $return_value );

                die();
        }

        private function unique_array_key_name( $key = "", $array = array () ) {

                $count = 1;

                $new_key = $key;

                while ( isset( $array[ $key ] ) ) {

                        $key = $new_key . "_" . $count;
                        $count ++;
                }

                unset( $count, $new_key );

                return $key;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
