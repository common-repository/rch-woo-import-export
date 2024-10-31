<?php

namespace rch\import\user;

use WP_User_Query;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}
if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-engine.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-engine.php');
}

class RCH_User_Import extends \rch\import\engine\RCH_Import_Engine {

        protected $import_type = "user";

        public function process_import_data() {

                global $wpdb;

                if ( $this->is_update_field( "fname" ) ) {

                        $this->rch_final_data[ 'first_name' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_first_name' ) );
                }
                if ( $this->is_update_field( "lname" ) ) {

                        $this->rch_final_data[ 'last_name' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_last_name' ) );
                }
                if ( $this->is_update_field( "role" ) ) {

                        $this->rch_final_data[ 'role' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_user_role' ) );
                }
                if ( $this->is_update_field( "nickname" ) ) {

                        $this->rch_final_data[ 'nickname' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_nickname' ) );
                }
                if ( $this->is_update_field( "desc" ) ) {

                        $this->rch_final_data[ 'description' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_description' ) );
                }
                if ( $this->is_update_field( "login" ) ) {

                        $this->rch_final_data[ 'user_login' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_user_login' ) );
                }

                $is_hashed_wp_password = false;

                if ( $this->is_update_field( "password" ) ) {

                        $this->rch_final_data[ 'user_pass' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_user_pass' ) );

                        $is_hashed_wp_password = ( absint( rch_sanitize_field( $this->get_field_value( 'rch_item_set_hashed_password' ) ) ) == 1);
                }

                if ( $this->is_update_field( "nicename" ) ) {

                        $this->rch_final_data[ 'user_nicename' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_user_nicename' ) );
                }
                if ( $this->is_update_field( "email" ) ) {

                        $this->rch_final_data[ 'user_email' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_user_email' ) );
                }
                if ( $this->is_update_field( "registered_date" ) ) {

                        $user_registered = rch_sanitize_field( $this->get_field_value( 'rch_item_user_registered' ) );

                        if ( empty( trim( $user_registered ) ) || strtotime( $user_registered ) === false ) {
                                $user_registered = current_time( 'mysql' );
                        }

                        $this->rch_final_data[ 'user_registered' ] = date( 'Y-m-d H:i:s', strtotime( $user_registered ) );
                }
                if ( $this->is_update_field( "display_name" ) ) {

                        $this->rch_final_data[ 'display_name' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_display_name' ) );
                }
                if ( $this->is_update_field( "url" ) ) {

                        $this->rch_final_data[ 'user_url' ] = rch_sanitize_field( $this->get_field_value( 'rch_item_user_url' ) );
                }

                $this->rch_final_data = apply_filters( 'rch_before_user_import', $this->rch_final_data, $this->rch_import_option );

                if ( $this->is_new_item ) {

                        $this->item_id = wp_insert_user( $this->rch_final_data );
                } else {

                        $this->rch_final_data[ 'ID' ] = $this->existing_item_id;

                        $this->item_id = wp_update_user( $this->rch_final_data );
                }

                $this->process_log[ 'imported' ] ++;

                if ( is_wp_error( $this->item_id ) ) {

                        $this->set_log( '<strong>' . __( 'ERROR', 'rch-woo-import-export' ) . '</strong> : ' . $this->item_id->get_error_message() );

                        $this->process_log[ 'skipped' ] ++;

                        return true;
                } elseif ( $this->item_id == 0 ) {

                        $this->set_log( '<strong>' . __( 'ERROR', 'rch-woo-import-export' ) . '</strong> : ' . __( 'something wrong, ID = 0 was generated.', 'rch-woo-import-export' ) );

                        $this->process_log[ 'skipped' ] ++;

                        return true;
                }
                if ( $this->is_new_item ) {
                        $this->process_log[ 'created' ] ++;
                } else {
                        $this->process_log[ 'updated' ] ++;
                }

                $this->item = get_user_by( "id", $this->item_id );

                $this->process_log[ 'last_records_id' ] = $this->item_id;

                $this->process_log[ 'last_records_status' ] = 'pending';

                $this->process_log[ 'last_activity' ] = date( 'Y-m-d H:i:s' );

                $wpdb->update( $wpdb->prefix . "rch_template", array( 'last_update_date' => current_time( 'mysql' ),
                        'process_log' => maybe_serialize( $this->process_log ) ), array(
                        'id' => $this->rch_import_id ) );

                if ( $is_hashed_wp_password ) {

                        $wpdb->query( $wpdb->prepare(
                                        "
				UPDATE `" . $wpdb->prefix . 'users' . "`
				SET `user_pass` = %s
				WHERE `ID` = %d
				", $this->rch_final_data[ 'user_pass' ], $this->item_id
                        ) );
                }

                unset( $is_hashed_wp_password );

                do_action( 'rch_after_user_import', $this->item_id, $this->rch_final_data, $this->rch_import_option );

                if ( $this->is_update_field( "cf" ) ) {

                        $this->rch_import_cf();
                }

                return $this->item_id;
        }

        protected function search_duplicate_item() {

                global $wpdb;

                $rch_duplicate_indicator = empty( $this->get_field_value( 'rch_existing_item_search_logic', true ) ) ? 'title' : rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic', true ) );

                if ( $rch_duplicate_indicator == "id" ) {

                        $duplicate_id = absint( rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_id' ) ) );

                        if ( $duplicate_id > 0 ) {
                                $user = get_user_by( 'id', absint( $duplicate_id ) );

                                if ( $user ) {
                                        $this->existing_item_id = $duplicate_id;
                                }
                                unset( $user );
                        }
                        unset( $duplicate_id );
                } elseif ( $rch_duplicate_indicator == "email" ) {

                        $email = rch_sanitize_field( $this->get_field_value( 'rch_item_user_email' ) );

                        if ( ! empty( $email ) ) {
                                $user = get_user_by( 'email', $email );

                                if ( $user ) {
                                        $this->existing_item_id = $user->ID;
                                }
                                unset( $user );
                        }
                        unset( $email );
                } elseif ( $rch_duplicate_indicator == "login" ) {

                        $user_login = rch_sanitize_field( $this->get_field_value( 'rch_item_user_login' ) );

                        if ( ! empty( $user_login ) ) {
                                $user = get_user_by( 'login', $user_login );

                                if ( $user ) {
                                        $this->existing_item_id = $user->ID;
                                }
                                unset( $user );
                        }
                        unset( $user_login );
                } elseif ( $rch_duplicate_indicator == "cf" ) {

                        $meta_key = rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_cf_key' ) );

                        $meta_val = rch_sanitize_field( $this->get_field_value( 'rch_existing_item_search_logic_cf_value' ) );

                        $user_query = array(
                                'meta_query' => array(
                                        0 => array(
                                                'key' => $meta_key,
                                                'value' => $meta_val,
                                                'compare' => '='
                                        )
                                )
                        );

                        $user_data = new \WP_User_Query( $user_query );

                        unset( $user_query );

                        if ( ! empty( $user_data->results ) ) {
                                foreach ( $user_data->results as $user ) {
                                        $this->existing_item_id = $user->ID;
                                        break;
                                }
                        } else {
                                $user_data_found = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS " . $wpdb->users . ".ID FROM " . $wpdb->users . " INNER JOIN " . $wpdb->usermeta . " ON (" . $wpdb->users . ".ID = " . $wpdb->usermeta . ".user_id) WHERE 1=1 AND ( (" . $wpdb->usermeta . ".meta_key = '%s' AND " . $wpdb->usermeta . ".meta_value = '%s') ) GROUP BY " . $wpdb->users . ".ID ORDER BY " . $wpdb->users . ".ID ASC LIMIT 0, 1", $meta_key, $meta_val ) );

                                if ( ! empty( $user_data_found ) ) {
                                        foreach ( $user_data_found as $user ) {
                                                $this->existing_item_id = $user->ID;
                                                break;
                                        }
                                }
                                unset( $user_data_found );
                        }
                        unset( $meta_key, $meta_val, $user_data );
                }
                unset( $rch_duplicate_indicator );
        }

}
