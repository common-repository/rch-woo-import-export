<?php

namespace rch\addons;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

class RCH_Extension {

        private $rch_export_extensions = array ();
        private $rch_import_extensions = array ();
        private $rch_activated_extensions = array ();

        public function __construct() {

                add_action( 'wp_ajax_rch_ext_save_extensions', array ( $this, 'rch_ext_save_extensions' ) );

                add_action( 'wp_ajax_rch_ext_save_extension_data', array ( $this, 'rch_ext_save_extension_data' ) );

                add_filter( 'rch_get_export_remote_locations', array ( $this, 'rch_get_export_remote_locations' ), 10, 1 );
        }

        public function rch_get_export_extension() {

                if ( empty( $this->rch_export_extensions ) ) {

                        $this->rch_export_extensions = array (
                                "rch_acf_export"      => array (
                                        "name"         => __( "Advanced Custom Fields", 'rch-woo-import-export' ),
                                        "include_path" => RCH_EXPORT_CLASSES_DIR . "/extensions/acf/rch_acf.php",
                                        "short_desc"   => __( "Export Advanced Custom Fields from WordPress Site", 'rch-woo-import-export' )
                                ),
                                "rch_bg_export"       => array (
                                        "name"         => __( "Background Export", 'rch-woo-import-export' ),
                                        "include_path" => RCH_EXPORT_CLASSES_DIR . "/extensions/bg/rch_bg.php",
                                        "short_desc"   => __( "Export in Background from WordPress Site", 'rch-woo-import-export' )
                                ),
                                "rch_schedule_export" => array (
                                        "name"         => __( "Schedule Export", 'rch-woo-import-export' ),
                                        "include_path" => RCH_EXPORT_CLASSES_DIR . "/extensions/schedule/class-rch-schedule.php",
                                        "short_desc"   => __( "Export automatically and periodically from WordPress Site", 'rch-woo-import-export' )
                                ),
                                "rch_user_export"     => array (
                                        "name"         => __( "User", 'rch-woo-import-export' ),
                                        "include_path" => RCH_EXPORT_CLASSES_DIR . "/extensions/user/rch_user.php",
                                        "short_desc"   => __( "Export Users & User's metadata from WordPress Site", 'rch-woo-import-export' )
                                ),
                                "rch_wc_export"       => array (
                                        "name"         => __( "WooCommerce", 'rch-woo-import-export' ),
                                        "is_default"   => true,
                                        "include_path" => RCH_EXPORT_CLASSES_DIR . "/extensions/wc/rch_wc.php",
                                        "short_desc"   => __( "Export Products, Orders, Product Categories and coupons from WordPress Site", 'rch-woo-import-export' )
                                ),
                                "rch_wpml_export"     => array (
                                        "name"         => __( "WPML", 'rch-woo-import-export' ),
                                        "include_path" => RCH_EXPORT_CLASSES_DIR . "/extensions/wpml/rch_wpml.php",
                                        "short_desc"   => __( "Export multilingual content from WordPress Site", 'rch-woo-import-export' )
                                )
                        );
                }

                return apply_filters( "rch_export_extensions", $this->rch_export_extensions );
        }

        public function rch_get_import_extension() {

                if ( empty( $this->rch_import_extensions ) ) {

                        $this->rch_import_extensions = array (
                                "rch_import_local_upload"            => array (
                                        "name"         => __( "Upload From Desktop", 'rch-woo-import-export' ),
                                        "is_default"   => true,
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/local-upload/rch_local_upload.php",
                                ),
                                "rch_import_existing_file_upload"    => array (
                                        "name"         => __( "Use existing file", 'rch-woo-import-export' ),
                                        "is_default"   => true,
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/existing-file/rch_existing_file.php",
                                ),
                                "rch_acf_import"                     => array (
                                        "name"         => __( "Advanced Custom Fields", 'rch-woo-import-export' ),
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/acf/rch_acf.php",
                                        "short_desc"   => __( "Import Advanced Custom Fields to WordPress Site", 'rch-woo-import-export' )
                                ),
                                "rch_bg_import"                      => array (
                                        "name"         => __( "Background Import", 'rch-woo-import-export' ),
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/bg/rch_bg.php",
                                        "short_desc"   => __( "Import in Background to WordPress Site", 'rch-woo-import-export' )
                                ),
                                "rch_import_ftp_file_upload"         => array (
                                        "name"         => __( "Upload From FTP/SFTP", 'rch-woo-import-export' ),
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/ftp-sftp/rch_ftp_sftp.php",
                                        "short_desc"   => __( "Import File from FTP/SFTP to WordPress Site", 'rch-woo-import-export' ),
                                ),
                                "rch_import_googledrive_file_upload" => array (
                                        "name"         => __( "Google Drive", 'rch-woo-import-export' ),
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/googledrive/rch-gdrive.php",
                                        "short_desc"   => __( "Import File from Google Drive to WordPress Site", 'rch-woo-import-export' ),
                                        "settings"     => RCH_IMPORT_CLASSES_DIR . "/extensions/googledrive/rch_googledrive_settings.php"
                                ),
                                "rch_import_onedrive_file_upload"    => array (
                                        "name"         => __( "Microsoft Onedrive", 'rch-woo-import-export' ),
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/onedrive/rch_onedrive.php",
                                        "short_desc"   => __( "Import File from Microsoft Onedrive to WordPress Site", 'rch-woo-import-export' ),
                                        "settings"     => RCH_IMPORT_CLASSES_DIR . "/extensions/onedrive/rch_onedrive_settings.php"
                                ),
                                "rch_schedule_import"                => array (
                                        "name"         => __( "Schedule Import", 'rch-woo-import-export' ),
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/schedule/rch_schedule.php",
                                        "short_desc"   => __( "Import automatically & periodically to WordPress Site", 'rch-woo-import-export' )
                                ),
                                "rch_import_url_file_upload"         => array (
                                        "name"         => __( "Upload From URL", 'rch-woo-import-export' ),
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/url-upload/rch_url_upload.php",
                                        "short_desc"   => __( "Import File from URL to WordPress Site", 'rch-woo-import-export' ),
                                ),
                                "rch_user_import"                    => array (
                                        "name"         => __( "User Import", 'rch-woo-import-export' ),
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/user/user.php",
                                        "short_desc"   => __( "Import Users & User's metadata to WordPress Site", 'rch-woo-import-export' )
                                ),
                                "rch_wc_import"                      => array(
                                        "is_default"   => true,
                                        "name"         => __( "WooCommerce Import", 'rch-woo-import-export' ),
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/wc/wc.php",
                                        "short_desc"   => __( "Import Products, Orders, Product Categories and coupons to WordPress Site", 'rch-woo-import-export' )
                                ),
                                "rch_wpml_import"                    => array (
                                        "name"         => __( "WPML", 'rch-woo-import-export' ),
                                        "include_path" => RCH_IMPORT_CLASSES_DIR . "/extensions/wpml/rch_wpml.php",
                                        "short_desc"   => __( "Import multilingual content to WordPress Site", 'rch-woo-import-export' )
                                )
                        );
                }

                return apply_filters( "rch_import_extensions", $this->rch_import_extensions );
        }

        public function rch_ext_save_extensions() {

                $return_value = array ();

                $rch_ext = isset( $_POST[ 'rch_ext' ] ) ? rch_sanitize_field( $_POST[ 'rch_ext' ] ) : "";

                if ( ! empty( $rch_ext ) ) {
                        $rch_ext = maybe_serialize( $rch_ext );
                } else {
                        $rch_ext = "";
                }

                update_option( "rch_extensions", $rch_ext );

                unset( $rch_ext );

                $return_value[ 'status' ] = 'success';

                $return_value[ 'message' ] = __( "Settings Successfully Updated", 'rch-woo-import-export' );

                echo json_encode( $return_value );

                die();
        }

        public function rch_ext_save_extension_data() {

                $return_value = array ();

                $rch_ext = isset( $_POST[ 'rch_ext' ] ) ? rch_sanitize_field( $_POST[ 'rch_ext' ] ) : "";

                if ( ! empty( $rch_ext ) ) {

                        $rch_ext_data = maybe_serialize( $_POST );

                        update_option( $rch_ext, $rch_ext_data );

                        unset( $settings, $rch_ext_id, $rch_ext_data, $option_name );

                        $return_value[ 'status' ] = 'success';

                        $return_value[ 'message' ] = __( "Settings Successfully Updated", 'rch-woo-import-export' );
                } else {

                        $return_value[ 'status' ] = 'error';

                        $return_value[ 'message' ] = __( "Extension Not Found", 'rch-woo-import-export' );
                }
                unset( $rch_ext );

                echo json_encode( $return_value );

                die();
        }

        public function rch_get_activated_ext() {

                if ( empty( $this->rch_activated_extensions ) ) {

                        $rch_extensions = get_option( 'rch_extensions' );

                        $rch_ext = $this->rch_get_export_extension();

                        $rch_imp_ext = $this->rch_get_import_extension();

                        $rch_ext = array_merge( $rch_ext, $rch_imp_ext );

                        if ( $rch_extensions ) {

                                $default_ext = [];
                                if ( ! empty( $rch_ext ) ) {
                                        foreach ( $rch_ext as $key => $ext ) {

                                                if ( isset( $ext[ 'is_default' ] ) && $ext[ 'is_default' ] == true ) {
                                                        $default_ext[] = $key;
                                                }
                                        }
                                }
                                $this->rch_activated_extensions = array_unique( array_merge( $default_ext, maybe_unserialize( $rch_extensions ) ) );
                        } else {

                                $this->rch_activated_extensions = array_keys( $rch_ext );
                        }

                        unset( $rch_extensions );
                }

                return apply_filters( "rch_activated_extensions", $this->rch_activated_extensions );
        }

        public function rch_init_extensions( $type = "all" ) {

                $rch_activated_ext = $this->rch_get_activated_ext();

                try {
                        $rch_ext = array ();

                        if ( is_array( $rch_activated_ext ) && ! empty( $rch_activated_ext ) ) {
                                $data = $rch_activated_ext;
                        } else {
                                $data = array ();
                        }

                        if ( $type == "all" || $type == "export" ) {

                                $rch_ext = $this->rch_get_export_extension();
                        }

                        if ( $type == "all" || $type == "import" ) {

                                $rch_imp_ext = $this->rch_get_import_extension();

                                $rch_ext = array_merge( $rch_ext, $rch_imp_ext );

                                unset( $rch_imp_ext );
                        }

                        if ( ! empty( $rch_ext ) ) {
                                foreach ( $rch_ext as $key => $ext ) {

                                        if ( ((isset( $ext[ 'is_default' ] ) && $ext[ 'is_default' ] == true) || (in_array( $key, $data ) && isset( $ext[ 'include_path' ] ))) && file_exists( $ext[ 'include_path' ] ) ) {
                                                require_once($ext[ 'include_path' ]);
                                        }
                                }
                        }
                        unset( $data, $rch_ext );
                } catch ( Exception $e ) {
                        // echo $e->getMessage();
                }
                unset( $rch_activated_ext );
        }

        public function rch_export_extension_info( $rch_ext = "" ) {

                $rch_export_ext = $this->rch_get_export_extension();

                if ( ! empty( $rch_ext ) && isset( $rch_export_ext[ $rch_ext ] ) ) {
                        unset( $rch_export_ext );
                        return true;
                }

                unset( $rch_export_ext );

                return false;
        }

        public function rch_import_extension_info( $rch_ext = "" ) {

                $rch_import_ext = $this->rch_get_import_extension();

                if ( ! empty( $rch_ext ) && isset( $rch_import_ext[ $rch_ext ] ) ) {
                        unset( $rch_import_ext );
                        return true;
                }

                unset( $rch_import_ext );

                return false;
        }

        public function rch_get_export_remote_locations( $remote_loc = array () ) {

                $rch_export_ext = $this->rch_get_export_extension();

                $rch_activated_ext = $this->rch_get_activated_ext();

                if ( ! empty( $rch_export_ext ) && is_array( $rch_activated_ext ) && ! empty( $rch_activated_ext ) ) {

                        foreach ( $rch_activated_ext as $rch_ext ) {

                                if ( ! (isset( $rch_export_ext[ $rch_ext ] ) && isset( $rch_export_ext[ $rch_ext ][ 'is_external_save' ] ) && $rch_export_ext[ $rch_ext ][ 'is_external_save' ] === true) ) {
                                        continue;
                                }
                                if ( isset( $remote_loc[ $rch_ext ] ) ) {
                                        continue;
                                }

                                $option_name = "rch_export_ext_" . $rch_ext;

                                $settings = maybe_unserialize( get_option( $option_name ) );

                                $remote_loc[ $rch_ext ] = array (
                                        "label" => isset( $rch_export_ext[ $rch_ext ] ) && isset( $rch_export_ext[ $rch_ext ][ 'name' ] ) ? $rch_export_ext[ $rch_ext ][ 'name' ] : "",
                                        "data"  => $settings
                                );
                                unset( $option_name, $settings );
                        }
                }
                unset( $rch_activated_ext, $rch_export_ext );

                return $remote_loc;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
