<?php

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( substr( rch_sanitize_field( $_REQUEST[ 'action' ] ), 0, 11 ) == 'rch_export_' ) {

        if ( file_exists( RCH_EXPORT_CLASSES_DIR . '/class-rch-export-actions.php' ) ) {

                require_once(RCH_EXPORT_CLASSES_DIR . '/class-rch-export-actions.php');

                $action = new \rch\export\actions\RCH_Export_Actions();

                unset( $action );
        }
} elseif ( substr( rch_sanitize_field( $_REQUEST[ 'action' ] ), 0, 11 ) == 'rch_import_' ) {

        if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-actions.php' ) ) {
                require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-actions.php');
                $action = new RCH_Import_Actions();

                unset( $action );
        }
} elseif ( substr( rch_sanitize_field( $_REQUEST[ 'action' ] ), 0, 8 ) == 'rch_ext_' ) {

        if ( file_exists( RCH_CLASSES_DIR . '/class-rch-extensions.php' ) ) {
                require_once(RCH_CLASSES_DIR . '/class-rch-extensions.php');
                $rch_ext = new \rch\addons\RCH_Extension();
                unset( $rch_ext );
        }
} else {
        if ( file_exists( RCH_CLASSES_DIR . '/class-rch-common-action.php' ) ) {
                require_once(RCH_CLASSES_DIR . '/class-rch-common-action.php');
                $action = new RCH_Common_Actions();
                unset( $action );
        }
}