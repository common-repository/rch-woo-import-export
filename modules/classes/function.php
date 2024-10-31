<?php

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( ! function_exists( "rch_sanitize_field" ) ) {

        function rch_sanitize_field( $var ) {
                if ( is_array( $var ) ) {
                        return array_map( 'rch_sanitize_field', $var );
                } else {
                        return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
                }
        }

}
if ( ! function_exists( "rch_sanitize_textarea" ) ) {

        function rch_sanitize_textarea( $var ) {
                return implode( "\n", array_map( 'rch_sanitize_field', explode( "\n", $var ) ) );
        }

}


add_action( 'init', 'rch_remove_draft_entries' );

if ( ! function_exists( "rch_remove_draft_entries" ) ) {

        function rch_remove_draft_entries() {

                global $wpdb;

                $current_time = date( 'Y-m-d H:i:s', strtotime( '-1 hour', strtotime( current_time( "mysql" ) ) ) );

                $wpdb->query( $wpdb->prepare( "DELETE  FROM {$wpdb->prefix}rch_template WHERE opration = 'import-draft' AND last_update_date < %s", $current_time ) );

                unset( $current_time );
        }

}
