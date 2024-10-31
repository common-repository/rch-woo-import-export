<?php

namespace rch\import\acf;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/extensions/acf/class-rch-acf-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/extensions/acf/class-rch-acf-base.php');
}

class RCH_ACF extends \rch\import\acf\base\RCH_ACF_Base {

        public function get_acf_groups() {

                $saved_acfs = get_posts( array ( 'posts_per_page' => -1, 'post_type' => 'acf-field-group', "order" => "ASC", "orderby" => "post_title" ) );

                $acfs = $this->get_local_fields();

                if ( ! empty( $saved_acfs ) ) {
                        foreach ( $saved_acfs as $key => $obj ) {
                                if ( isset( $obj->post_name ) && is_array( $acfs ) && ! isset( $acfs[ $obj->post_name ] ) ) {
                                        $acfs[ $obj->post_name ] = $obj;
                                }
                        }
                }

                unset( $saved_acfs );

                return $acfs;
        }

        public function get_acf_field_by_group( $group_id = 0 ) {

                $fields = [];

                if ( absint( $group_id ) > 0 ) {

                        $fields = get_posts(
                                array (
                                        'posts_per_page' => -1,
                                        'post_type'      => 'acf-field',
                                        'post_parent'    => absint( $group_id ),
                                        'post_status'    => 'publish',
                                        'fields'         => "ids",
                                        'orderby'        => 'menu_order',
                                        'order'          => 'ASC'
                                )
                        );
                }

                if ( empty( $fields ) && function_exists( 'acf_get_local_fields' ) ) {
                        $fields = acf_get_local_fields( $group_id );
                }
                return $fields;
        }

        private function get_local_fields() {

                $fields = [];

                if ( function_exists( 'acf_local' ) ) {
                        $fields = \acf_local()->groups;
                }

                if ( empty( $fields ) && function_exists( 'acf_get_local_field_groups' ) ) {
                        $fields = \acf_get_local_field_groups();
                }

                if ( empty( $fields ) ) {
                        return [];
                }

                $acfs = [];
                foreach ( $fields as $key => $value ) {
                        $data = new \stdClass();
                        $data->ID = $key;
                        $data->post_title = isset( $value[ 'title' ] ) ? $value[ 'title' ] : "";
                        $acfs[ $key ] = $data;
                        unset( $data );
                }
                return $acfs;
        }

}
