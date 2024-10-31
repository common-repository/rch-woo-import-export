<?php
/*
Plugin Name: RCH Import/Export for WooCommerce
Description: A tool for importing and exporting data to WooCommerce. Import and Export to WooCommerce Store Products, Orders, Users, Product Categories, Coupons. 
Version: 1.0.0
Author: importerwc
WC requires at least: 3.2.0
WC tested up to: 4.1
*/

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

// Plugin version
if ( ! defined( 'RCH_PLUGIN_VERSION' ) ) {
        define( 'RCH_PLUGIN_VERSION', '1.0.0' );
}
// Plugin version
if ( ! defined( 'RCH_DB_VERSION' ) ) {
        define( 'RCH_DB_VERSION', '2.0.0' );
}

// Plugin base name
if ( ! defined( 'RCH_PLUGIN_FILE' ) ) {
        define( 'RCH_PLUGIN_FILE', __FILE__ );
}


// Plugin Folder Path
if ( ! defined( 'RCH_PLUGIN_DIR' ) ) {
        define( 'RCH_PLUGIN_DIR', realpath( plugin_dir_path( RCH_PLUGIN_FILE ) ) . '/' );
}

$plugin_url = plugin_dir_url( RCH_PLUGIN_FILE );


if ( is_ssl() ) {
        $plugin_url = str_replace( 'http://', 'https://', $plugin_url );
}
if ( ! defined( 'RCH_PLUGIN_URL' ) ) {
        define( 'RCH_PLUGIN_URL', untrailingslashit( $plugin_url ) );
}

$wpupload_dir = wp_upload_dir();

$rch_upload_dir = $wpupload_dir[ 'basedir' ] . '/rch-woo-import-export';

$rch_upload_url = $wpupload_dir[ 'baseurl' ] . '/rch-woo-import-export';

if ( ! defined( 'RCH_SITE_UPLOAD_DIR' ) ) {
        define( 'RCH_SITE_UPLOAD_DIR', $wpupload_dir[ 'basedir' ] );
}

unset( $wpupload_dir );

if ( ! defined( 'RCH_UPLOAD_DIR' ) ) {
        define( 'RCH_UPLOAD_DIR', $rch_upload_dir );
}

if ( ! defined( 'RCH_UPLOAD_URL' ) ) {
        define( 'RCH_UPLOAD_URL', $rch_upload_url );
}
unset( $rch_upload_url );

if ( ! defined( 'RCH_ASSETS_URL' ) ) {
        define( 'RCH_ASSETS_URL', RCH_PLUGIN_URL . '/assets' );
}

if ( ! defined( 'RCH_UPLOAD_EXPORT_DIR' ) ) {
        define( 'RCH_UPLOAD_EXPORT_DIR', RCH_UPLOAD_DIR . "/export" );
}

if ( ! defined( 'RCH_UPLOAD_IMPORT_DIR' ) ) {
        define( 'RCH_UPLOAD_IMPORT_DIR', RCH_UPLOAD_DIR . "/import" );
}

if ( ! defined( 'RCH_UPLOAD_TEMP_DIR' ) ) {
        define( 'RCH_UPLOAD_TEMP_DIR', RCH_UPLOAD_DIR . "/temp" );
}
if ( ! defined( 'RCH_UPLOAD_MAIN_DIR' ) ) {
        define( 'RCH_UPLOAD_MAIN_DIR', RCH_UPLOAD_DIR . "/upload" );
}

wp_mkdir_p( $rch_upload_dir );

unset( $rch_upload_dir );

if ( ! is_dir( RCH_UPLOAD_EXPORT_DIR ) ) {
        wp_mkdir_p( RCH_UPLOAD_EXPORT_DIR );
}

if ( ! is_dir( RCH_UPLOAD_IMPORT_DIR ) ) {
        wp_mkdir_p( RCH_UPLOAD_IMPORT_DIR );
}
if ( ! is_dir( RCH_UPLOAD_TEMP_DIR ) ) {
        wp_mkdir_p( RCH_UPLOAD_TEMP_DIR );
}
if ( ! is_dir( RCH_UPLOAD_MAIN_DIR ) ) {
        wp_mkdir_p( RCH_UPLOAD_MAIN_DIR );
}

if ( wp_is_writable( RCH_UPLOAD_DIR ) && is_dir( RCH_UPLOAD_DIR ) ) {
        @touch( RCH_UPLOAD_DIR . '/index.php' );
}

if ( wp_is_writable( RCH_UPLOAD_EXPORT_DIR ) && is_dir( RCH_UPLOAD_EXPORT_DIR ) ) {
        @touch( RCH_UPLOAD_EXPORT_DIR . '/index.php' );
}

if ( wp_is_writable( RCH_UPLOAD_IMPORT_DIR ) && is_dir( RCH_UPLOAD_IMPORT_DIR ) ) {
        @touch( RCH_UPLOAD_IMPORT_DIR . '/index.php' );
}
if ( wp_is_writable( RCH_UPLOAD_TEMP_DIR ) && is_dir( RCH_UPLOAD_TEMP_DIR ) ) {
        @touch( RCH_UPLOAD_TEMP_DIR . '/index.php' );
}
if ( wp_is_writable( RCH_UPLOAD_MAIN_DIR ) && is_dir( RCH_UPLOAD_MAIN_DIR ) ) {
        @touch( RCH_UPLOAD_MAIN_DIR . '/index.php' );
}

if ( ! defined( 'RCH_IMPORT_ADDON_URL' ) ) {
        define( 'RCH_IMPORT_ADDON_URL', RCH_PLUGIN_URL . '/modules/classes/import/extensions' );
}
if ( ! defined( 'RCH_EXPORT_ADDON_URL' ) ) {
        define( 'RCH_EXPORT_ADDON_URL', RCH_PLUGIN_URL . '/modules/classes/export/extensions' );
}

if ( ! defined( 'RCH_CSS_URL' ) ) {
        define( 'RCH_CSS_URL', RCH_ASSETS_URL . '/css' );
}

if ( ! defined( 'RCH_JS_URL' ) ) {
        define( 'RCH_JS_URL', RCH_ASSETS_URL . '/js' );
}

if ( ! defined( 'RCH_IMAGES_URL' ) ) {
        define( 'RCH_IMAGES_URL', RCH_ASSETS_URL . '/images' );
}

if ( ! defined( 'RCH_INCLUDES_DIR' ) ) {
        define( 'RCH_INCLUDES_DIR', RCH_PLUGIN_DIR . '/modules' );
}

if ( ! defined( 'RCH_DEPENCY_DIR' ) ) {
        define( 'RCH_DEPENCY_DIR', RCH_PLUGIN_DIR . '/dependencies' );
}
if ( ! defined( 'RCH_CLASSES_DIR' ) ) {
        define( 'RCH_CLASSES_DIR', RCH_INCLUDES_DIR . '/classes' );
}

if ( ! defined( 'RCH_IMPORT_CLASSES_DIR' ) ) {
        define( 'RCH_IMPORT_CLASSES_DIR', RCH_CLASSES_DIR . '/import' );
}

if ( ! defined( 'RCH_EXPORT_CLASSES_DIR' ) ) {
        define( 'RCH_EXPORT_CLASSES_DIR', RCH_CLASSES_DIR . '/export' );
}

if ( ! defined( 'RCH_VIEW_DIR' ) ) {
        define( 'RCH_VIEW_DIR', RCH_INCLUDES_DIR . '/views' );
}

if ( file_exists( RCH_CLASSES_DIR . '/class-rch-schedule.php' ) ) {
        require_once(RCH_CLASSES_DIR . '/class-rch-schedule.php');

        new \rch\RCH_Schedule();
}

if ( file_exists( RCH_CLASSES_DIR . '/function.php' ) ) {
        require_once(RCH_CLASSES_DIR . '/function.php');
}

if ( file_exists( RCH_CLASSES_DIR . '/class-rch-extensions.php' ) ) {
        require_once(RCH_CLASSES_DIR . '/class-rch-extensions.php');

        $rch_ext = new \rch\addons\RCH_Extension();

        $rch_ext->rch_init_extensions();

        unset( $rch_ext );
}


if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST[ 'action' ] ) && substr( rch_sanitize_field( $_REQUEST[ 'action' ] ), 0, 4 ) == 'rch_' ) { 

        if ( file_exists( RCH_CLASSES_DIR . '/class-rch-action.php' ) ) {
                require_once(RCH_CLASSES_DIR . '/class-rch-action.php');
        }
} elseif ( file_exists( RCH_CLASSES_DIR . '/class-rch-general.php' ) ) { 
        require_once(RCH_CLASSES_DIR . '/class-rch-general.php');

        new \rch\core\RCH_General();
}