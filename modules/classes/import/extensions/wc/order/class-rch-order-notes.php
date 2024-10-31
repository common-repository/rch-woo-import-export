<?php

namespace rch\import\wc\order\notes;

use WC_Payment_Gateways;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}
if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php');
}

class RCH_Order_Notes extends \rch\import\base\RCH_Import_Base {

        public function __construct( $rch_import_option = array(), $rch_import_record = array(), $item_id = 0, $is_new_item = true, &$addon_error = false, &$addon_log = array() ) {

                $this->rch_import_option = $rch_import_option;

                $this->rch_import_record = $rch_import_record;

                $this->item_id = $item_id;

                $this->is_new_item = $is_new_item;

                $this->addon_error = &$addon_error;

                $this->addon_log = &$addon_log;

                $this->prepare_notes();
        }

        private function prepare_notes() {

                $delimiter = rch_sanitize_field( $this->get_field_value( 'rch_item_import_order_note_delim' ) );

                $note_content = rch_sanitize_field( $this->get_field_value( 'rch_item_import_order_note_content' ) );

                if ( ! empty( $note_content ) ) {

                        $note_date = rch_sanitize_field( $this->get_field_value( 'rch_item_import_order_note_date' ) );

                        $note_visibility = rch_sanitize_field( strtolower( trim( $this->get_field_value( 'rch_item_import_order_note_visibility', false, true ) ) ) );

                        $note_username = rch_sanitize_field( $this->get_field_value( 'rch_item_import_order_note_username' ) );

                        $note_email = rch_sanitize_field( $this->get_field_value( 'rch_item_import_order_note_email' ) );

                        if ( ! empty( $note_date ) ) {
                                $note_date = explode( $delimiter, $note_date );
                        } else {
                                $note_date = array();
                        }
                        if ( ! empty( $note_visibility ) ) {
                                $note_visibility = explode( $delimiter, $note_visibility );
                        } else {
                                $note_visibility = array();
                        }
                        if ( ! empty( $note_username ) ) {
                                $note_username = explode( $delimiter, $note_username );
                        } else {
                                $note_username = array();
                        }
                        if ( ! empty( $note_email ) ) {
                                $note_email = explode( $delimiter, $note_email );
                        } else {
                                $note_email = array();
                        }

                        $current_notes = array();

                        if ( ! $this->is_new_item ) {
                                $current_notes = $this->get_order_notes();
                        }

                        $note_content = explode( $delimiter, $note_content );

                        foreach ( $note_content as $key => $note ) {

                                if ( empty( $note ) ) {
                                        continue;
                                }

                                $comment_author = isset( $note_username[ $key ] ) ? $note_username[ $key ] : "";

                                $comment_author_email = isset( $note_email[ $key ] ) ? $note_email[ $key ] : "";

                                $comment_date = isset( $note_date[ $key ] ) ? $note_date[ $key ] : "";

                                if ( empty( trim( $comment_date ) ) || strtotime( $comment_date ) === false ) {
                                        $comment_date = current_time( 'mysql' );
                                }

                                $comment_visibility = isset( $note_visibility[ $key ] ) ? $note_visibility[ $key ] : "";

                                if ( empty( $comment_author ) && empty( $comment_author_email ) ) {

                                        if ( is_user_logged_in() ) {
                                                $user = get_user_by( 'id', get_current_user_id() );
                                                $comment_author = $user->display_name;
                                                $comment_author_email = $user->user_email;
                                        } else {
                                                $comment_author = __( 'WooCommerce', 'rch-woo-import-export' );
                                                $comment_author_email = strtolower( __( 'WooCommerce', 'rch-woo-import-export' ) ) . '@';
                                                $comment_author_email .= isset( $_SERVER[ 'HTTP_HOST' ] ) ? str_replace( 'www.', '', rch_sanitize_field( wp_unslash( $_SERVER[ 'HTTP_HOST' ] ) ) ) : 'noreply.com'; // WPCS: input var ok.
                                                $comment_author_email = sanitize_email( $comment_author_email );
                                        }
                                }

                                $commentdata = array(
                                        'comment_post_ID' => $this->item_id,
                                        'comment_author' => $comment_author,
                                        'comment_author_email' => $comment_author_email,
                                        'comment_author_url' => '',
                                        'comment_content' => $note,
                                        'comment_agent' => 'WooCommerce',
                                        'comment_type' => 'order_note',
                                        'comment_parent' => 0,
                                        'comment_approved' => 1,
                                        'comment_date' => $comment_date,
                                );

                                $comment_id = false;

                                if ( ! empty( $current_notes ) ) {

                                        foreach ( $current_notes as $_id => $current_note ) {

                                                if ( $current_note->comment_content == $commentdata[ 'comment_content' ] ) {

                                                        $comment_id = $_id;

                                                        unset( $current_notes[ $_id ] );

                                                        break;
                                                }
                                        }
                                }

                                if ( $comment_id ) {

                                        $commentdata[ 'comment_ID' ] = $comment_id;

                                        wp_update_comment( $commentdata );

                                        if ( $comment_visibility != 'private' ) {
                                                update_comment_meta( $comment_id, 'is_customer_note', 1 );
                                        } else {
                                                delete_comment_meta( $comment_id, 'is_customer_note' );
                                        }
                                } else {

                                        $comment_id = wp_insert_comment( $commentdata );

                                        if ( $comment_visibility != 'private' ) {

                                                add_comment_meta( $comment_id, 'is_customer_note', 1 );
                                        }

                                        do_action( 'woocommerce_new_customer_note', array(
                                                'order_id' => $this->item_id, 'customer_note' => $commentdata[ 'comment_content' ] ) );
                                }

                                unset( $comment_author, $comment_author_email, $commentdata, $comment_id, $comment_date );
                        }

                        if ( ! empty( $current_notes ) ) {

                                foreach ( $current_notes as $_id => $current_note ) {

                                        wp_delete_comment( $_id, true );
                                }
                        }

                        unset( $note_date, $note_visibility, $note_username, $note_email );
                }
                unset( $delimiter, $note_content );
        }

        private function get_order_notes() {

                $notes = array();

                $args = array(
                        'post_id' => $this->item_id,
                        'approve' => 'approve',
                        'type' => 'order_note',
                );

                remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

                $comments = get_comments( $args );

                foreach ( $comments as $comment ) {
                        if ( $comment->comment_approved != 'trash' ) {
                                $notes[ $comment->comment_ID ] = $comment;
                        }
                }

                add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

                unset( $args, $comments );

                return $notes;
        }

        public function __destruct() {

                parent::__destruct();

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
