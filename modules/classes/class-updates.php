<?php
namespace rch;

defined( 'ABSPATH' ) || exit;

/**
 * Updates class
 * 
 * Functions and actions related to updates.
 *
 * @since 1.5.0
 * 
 * @class Updates auto update for plugin
 */
class Updates {

        /**
         * Updates that need to be run
         *
         * @since  1.5.0
         * @access private
         * 
         * @var    array
         */
        private static $updates = [
                '3.0.0' => 'update-3.0.0.php'
        ];

        /**
         * Class Constructor
         * 
         * Register hooks.
         * 
         * @since  1.5.0
         * @access public
         */
        public function __construct() {

                add_action( 'admin_init', [ $this, 'do_updates' ] );
        }

        /**
         * Check if any update is required.
         * 
         * @since  1.5.0
         * @access public
         */
        public function do_updates() {

                $installed_version = get_option( 'rch_plugin_version' );

                // Maybe it's the first install.
                if ( ! $installed_version ) {
                        return;
                }

                if ( version_compare( $installed_version, RCH_PLUGIN_VERSION, '<' ) ) {
                        $this->perform_updates();
                }
        }

        /**
         * Perform plugin updates.
         * 
         * Perform all database updates.
         * 
         * @since  1.5.0
         * @access public
         */
        private function perform_updates() {

                $installed_version = get_option( 'rch_plugin_version' );

                foreach ( self::$updates as $version => $path ) {

                        $abs_path = RCH_CLASSES_DIR . "/updates/" . $path;

                        if ( version_compare( $installed_version, $version, '<' ) && file_exists( $abs_path ) ) {
                                require_once $abs_path;
                        }
                }

                // Save install date.
                if ( false === boolval( get_option( 'rch_install_date' ) ) ) {
                        update_option( 'rch_install_date', current_time( 'timestamp' ) );
                }

                update_option( 'rch_plugin_version', RCH_PLUGIN_VERSION );

                update_option( 'rch_db_version', RCH_DB_VERSION );
        }

}
