<?php

namespace rch\core;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

class RCH_General {

        private static $rch_page = array( 'rch-new-export', 'rch-new-import', 'rch-extensions', 'rch-settings', 'rch-manage-import', 'rch-manage-export' );

        public function __construct() {

                if ( is_admin() ) {

                        add_action( 'admin_menu', array( __CLASS__, 'rch_set_menu' ) );

                        add_action( 'init', array( __CLASS__, 'rch_db_check' ), 1 );

                        add_action( 'admin_head', array( __CLASS__, 'rch_hide_all_notice_to_admin_side' ), 10000 );

                        add_filter( 'admin_footer_text', array( __CLASS__, 'rch_replace_footer_admin' ) );

                        add_filter( 'update_footer', array( __CLASS__, 'rch_replace_footer_version' ), '1234' );

                        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'rch_set_admin_css' ), 10 );

                        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'rch_set_admin_js' ), 10 );

                        add_action( 'init', array( $this, 'rch_process_file_download' ), 10 );

                        add_action( 'admin_notices', array( __CLASS__, 'rch_admin_notices' ), 10099 );

                        add_filter( 'mod_rewrite_rules', array( __CLASS__, 'mod_rewrite_rules' ) );

                        add_action( 'admin_init', array( __CLASS__, 'update_file_security' ) );

                        add_filter( 'robots_txt', array( __CLASS__, 'update_robots_txt' ), 10, 2 );

                        add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );

                        add_action( 'shutdown', array( __CLASS__, 'flush_rewrite_rules' ) );

                        add_filter( 'plugin_action_links_' . plugin_basename( RCH_PLUGIN_FILE ), [ __CLASS__, 'plugin_action_links' ] );
                }
                add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
        }

        /**
         * Flush the rewrite rules once for upload folder security.
         */
        public static function flush_rewrite_rules() {

                if ( self::is_apache() && self::is_htaccess_writable() && get_option( 'rch_flush_rewrite_rules', false ) === false ) {

                        flush_rewrite_rules();

                        update_option( 'rch_flush_rewrite_rules', 1 );
                }
        }

        /**
         * Uninstall tables when MU blog is deleted.
         *
         * @param  array $tables List of tables that will be deleted by WP.
         * @return array
         */
        public static function wpmu_drop_tables( $tables = array() ) {

                global $wpdb;

                $tables[] = $wpdb->prefix . 'rch_template';

                return $tables;
        }


        /**
         * plugin action links
         * 
         * Show action links on the plugin screen.
         *
         * @since  1.0.0
         * @access public
         * 
         * @param  mixed $links Plugin Action links.
         * 
         * @return array
         */
        public static function plugin_action_links( $links = array() ) {

                $plugin_links = [
                        '<a href="' . admin_url( "admin.php?page=rch-new-export" ) . '">' . esc_html__( 'Export', 'rch-woo-import-export' ) . '</a>',
                        '<a href="' . admin_url( "admin.php?page=rch-new-import" ) . '">' . esc_html__( 'Import', 'rch-woo-import-export' ) . '</a>',
                ];

                return array_merge( $plugin_links, $links );
        }

        public function rch_process_file_download() {

                if ( isset( $_POST[ 'rch_download_export_id' ] ) && intval( sanitize_text_field($_POST[ 'rch_download_export_id' ] )) != 0 ) {

                        $current_data = $this->get_template_data_by_id( intval( rch_sanitize_field( $_POST[ 'rch_download_export_id' ] ) ) );

                        $options = isset( $current_data->options ) ? maybe_unserialize( $current_data->options ) : array();

                        $filename = isset( $options[ 'fileName' ] ) ? $options[ 'fileName' ] : "";

                        $filedir = isset( $options[ 'fileDir' ] ) ? $options[ 'fileDir' ] : "";

                        $filePath = RCH_UPLOAD_EXPORT_DIR . '/' . $filedir . '/' . $filename;

                        unset( $current_data, $options, $filename, $filedir );

                        $this->rch_download_file( $filePath );
                } elseif ( isset( $_POST[ 'rch_download_import_id' ] ) && intval( sanitize_text_field($_POST[ 'rch_download_import_id' ]) ) != 0 ) {

                        $current_data = $this->get_template_data_by_id( intval( rch_sanitize_field( $_POST[ 'rch_download_import_id' ] ) ) );

                        $options = isset( $current_data->options ) ? maybe_unserialize( $current_data->options ) : array();

                        $activeFile = isset( $options[ 'activeFile' ] ) ? $options[ 'activeFile' ] : "";

                        $importFile = isset( $options[ 'importFile' ] ) ? $options[ 'importFile' ] : array();

                        $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                        $fileDir = $fileData[ 'fileDir' ] ? $fileData[ 'fileDir' ] : "";

                        $fileName = $fileData[ 'fileName' ] ? $fileData[ 'fileName' ] : "";

                        $filePath = RCH_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . $fileName;

                        unset( $current_data, $options, $activeFile, $importFile, $fileData, $fileDir, $fileName );

                        $this->rch_download_file( $filePath );
                } elseif ( isset( $_POST[ 'rch_download_import_log_id' ] ) && intval( sanitize_text_field($_POST[ 'rch_download_import_log_id' ]) ) != 0 ) {

                        $current_data = $this->get_template_data_by_id( intval( rch_sanitize_field( $_POST[ 'rch_download_import_log_id' ] ) ) );

                        $options = isset( $current_data->options ) ? maybe_unserialize( $current_data->options ) : array();

                        $activeFile = isset( $options[ 'activeFile' ] ) ? $options[ 'activeFile' ] : "";

                        $importFile = isset( $options[ 'importFile' ] ) ? $options[ 'importFile' ] : array();

                        $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                        $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                        $filePath = RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/log/import_log.txt";

                        unset( $current_data, $options, $activeFile, $importFile, $fileData, $baseDir );

                        $this->rch_download_file( $filePath );
                } elseif ( isset( $_POST[ 'rch_template_list' ] ) && ! empty( $_POST[ 'rch_template_list' ] ) ) {

                        $templates = rch_sanitize_field( $_POST[ 'rch_template_list' ] );

                        if ( is_array( $templates ) && ! empty( $templates ) ) {

                                $ids = implode( ',', array_map( 'absint', $templates ) );

                                global $wpdb;

                                $results = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "rch_template where `id` IN(" . $ids . ")" );

                                $results = maybe_serialize( $results );

                                $filePath = RCH_UPLOAD_TEMP_DIR . '/' . time() . '_templates.txt';


                                if ( ( $handle = @fopen( $filePath, "w" )) !== false ) {
                                        fwrite( $handle, $results );

                                        fclose( $handle );
                                }

                                unset( $ids, $results );

                                $this->rch_download_file( $filePath );
                        }

                        unset( $templates );
                } elseif ( isset( $_POST[ 'rch_download_file' ] ) && ! empty( $_POST[ 'rch_download_file' ] ) ) {

                        $current_data = $this->get_template_data_by_id( intval( rch_sanitize_field( $_POST[ 'rch_download_file' ] ) ) );

                        $options = isset( $current_data->options ) ? maybe_unserialize( $current_data->options ) : array();

                        $activeFile = isset( $options[ 'activeFile' ] ) ? $options[ 'activeFile' ] : "";

                        $importFile = isset( $options[ 'importFile' ] ) ? $options[ 'importFile' ] : "";

                        $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : array();

                        $file_name = $fileData[ 'fileName' ] ? $fileData[ 'fileName' ] : "";

                        $fileDir = $fileData[ 'fileDir' ] ? $fileData[ 'fileDir' ] : "";

                        $filePath = RCH_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . $file_name;

                        unset( $current_data, $importFile, $activeFile, $fileData, $file_name, $fileDir );

                        $this->rch_download_file( $filePath );
                }
        }

        private function rch_download_file( $filePath ) {

                if ( file_exists( $filePath ) ) {

                        header( 'Content-Description: File Transfer' );

                        header( 'Content-Type: application/octet-stream' );

                        header( 'Content-Disposition: attachment; filename=' . basename( $filePath ) );

                        header( 'Expires: 0' );

                        header( 'Cache-Control: must-revalidate' );

                        header( 'Pragma: public' );

                        header( 'Content-Length: ' . filesize( $filePath ) );

                        if ( ob_get_length() > 0 ) {
                                @ob_clean();
                        }

                        readfile( $filePath );

                        die();
                }
        }

        private function get_template_data_by_id( $template_id = 0 ) {

                if ( $template_id != "" && $template_id > 0 ) {

                        global $wpdb;

                        $results = $wpdb->get_results( $wpdb->prepare( "SELECT `options` FROM " . $wpdb->prefix . "rch_template where `id` = %d", $template_id ) );

                        return $results[ 0 ];
                }

                return false;
        }

        public static function rch_set_admin_css() {

                $page = isset( $_GET[ 'page' ] ) ? rch_sanitize_field( $_GET[ 'page' ] ) : "";

                wp_register_style( 'rch-global-admin-css', RCH_CSS_URL . '/rch-global-admin.min.css', array(), RCH_PLUGIN_VERSION );

                wp_enqueue_style( 'rch-global-admin-css' );

                if ( ! empty( $page ) && in_array( $page, self::$rch_page ) ) {

                        wp_register_style( 'rch-export-admin-css', RCH_CSS_URL . '/rch-export-admin.min.css', array(), RCH_PLUGIN_VERSION );

                        wp_register_style( 'rch-general-admin-css', RCH_CSS_URL . '/rch-general-admin.min.css', array(), RCH_PLUGIN_VERSION );

                        wp_register_style( 'rch-import-admin-css', RCH_CSS_URL . '/rch-import-admin.min.css', array(), RCH_PLUGIN_VERSION );

                        wp_enqueue_style( 'fontawesome-css', RCH_CSS_URL . '/fontawesome-all.css' );

                        wp_enqueue_style( 'bootstrap-css', RCH_CSS_URL . '/bootstrap.css' );

                        wp_enqueue_style( 'animate-css', RCH_CSS_URL . '/animate.css' );

                        wp_enqueue_style( 'chosen-css', RCH_CSS_URL . '/chosen.css' );

                        wp_enqueue_style( 'tipso-css', RCH_CSS_URL . '/tipso.css' );

                        if ( $page == 'rch-new-export' ) {

                                wp_enqueue_style( 'rch-export-admin-css' );

                                wp_enqueue_style( 'datatables.bootstrap4-css', RCH_CSS_URL . '/dataTables.bootstrap4.css' );
                        } elseif ( $page == 'rch-new-import' ) {

                                wp_enqueue_style( 'rch-import-admin-css' );

                                wp_enqueue_style( 'datatables.bootstrap4-css', RCH_CSS_URL . '/dataTables.bootstrap4.css' );
                        } elseif ( $page == 'rch-extensions' || $page == 'rch-settings' || $page == 'rch-manage-export' || $page == 'rch-manage-import' ) {

                                wp_enqueue_style( 'rch-general-admin-css' );
                        }
                }

                unset( $page );
        }

        public static function rch_set_admin_js() {

                $page = isset( $_GET[ 'page' ] ) ? rch_sanitize_field( $_GET[ 'page' ] ) : "";

                if ( ! empty( $page ) && in_array( $page, self::$rch_page ) ) {

                        wp_register_script( 'rch-export-admin-js', RCH_JS_URL . '/rch-export-admin.min.js', array( 'jquery' ), RCH_PLUGIN_VERSION, true );

                        wp_register_script( 'rch-general-admin-js', RCH_JS_URL . '/rch-general-admin.min.js', array( 'jquery' ), RCH_PLUGIN_VERSION, true );

                        wp_register_script( 'rch-import-admin-js', RCH_JS_URL . '/rch-import-admin.min.js', array( 'jquery' ), RCH_PLUGIN_VERSION, true );

                        wp_enqueue_script( 'jquery' );

                        wp_enqueue_script( 'bootstrap-js', RCH_JS_URL . '/bootstrap.js', array( 'jquery' ), RCH_PLUGIN_VERSION, true );

                        wp_enqueue_script( 'bootstrap-notify-js', RCH_JS_URL . '/bootstrap-notify.js', array( 'jquery' ), RCH_PLUGIN_VERSION, true );

                        wp_enqueue_script( 'chosen-js', RCH_JS_URL . '/chosen.js', array( 'jquery' ), RCH_PLUGIN_VERSION, true );

                        wp_enqueue_script( 'tipso-js', RCH_JS_URL . '/tipso.js', array( 'jquery' ), RCH_PLUGIN_VERSION, true );

                        if ( file_exists( RCH_CLASSES_DIR . '/class-rch-extensions.php' ) ) {
                                require_once(RCH_CLASSES_DIR . '/class-rch-extensions.php');
                        }

                        $rch_ext = new \rch\addons\RCH_Extension();

                        $rchExtData = $rch_ext->rch_get_activated_ext();

                        unset( $rch_ext );


                        if ( $page == 'rch-new-export' ) {

                                wp_enqueue_script( 'rch-export-admin-js' );

                                $rch_localize_script_data = array(
                                        'rchAjaxURL'      => admin_url( 'admin-ajax.php' ),
                                        'rchSiteURL'      => site_url(),
                                        'rchUploadURL'    => RCH_UPLOAD_URL,
                                        'rchUploadDir'    => RCH_UPLOAD_DIR,
                                        'rchPluginURL'    => RCH_PLUGIN_URL,
                                        'rchImageURL'     => RCH_IMAGES_URL,
                                        'rchLocalizeText' => self::rch_load_msg(),
                                        'rchSiteUrl'      => home_url(),
                                        'rchPluginData'   => '',
                                        'rchExtensions'   => $rchExtData
                                );

                                wp_localize_script( 'rch-export-admin-js', 'rchPluginSettings', $rch_localize_script_data );

                                unset( $rch_localize_script_data );

                                wp_enqueue_script( 'datatables-js', RCH_JS_URL . '/datatables.js', array( 'jquery' ), RCH_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'editable-js', RCH_JS_URL . '/editable.js', array( 'jquery' ), RCH_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'dataTables.bootstrap4-js', RCH_JS_URL . '/dataTables.bootstrap4.js', array( 'jquery', 'bootstrap-js' ), RCH_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'jquery-ui-sortable' );
                        } elseif ( $page == 'rch-new-import' ) {

                                wp_enqueue_script( 'rch-import-admin-js' );

                                $rch_localize_script_data = array(
                                        'rchAjaxURL'      => admin_url( 'admin-ajax.php' ),
                                        'rchSiteURL'      => site_url(),
                                        'rchUploadURL'    => RCH_UPLOAD_URL,
                                        'rchUploadDir'    => RCH_UPLOAD_DIR,
                                        'rchPluginURL'    => RCH_PLUGIN_URL,
                                        'rchImageURL'     => RCH_IMAGES_URL,
                                        'rchLocalizeText' => self::rch_load_msg(),
                                        'rchSiteUrl'      => home_url(),
                                        'rchPluginData'   => '',
                                        'rchExtensions'   => $rchExtData
                                );

                                wp_localize_script( 'rch-import-admin-js', 'rchPluginSettings', $rch_localize_script_data );

                                unset( $rch_localize_script_data );

                                wp_enqueue_script( 'datatables-js', RCH_JS_URL . '/datatables.js', array( 'jquery' ), RCH_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'dataTables.bootstrap4-js', RCH_JS_URL . '/dataTables.bootstrap4.js', array( 'jquery', 'bootstrap-js' ), RCH_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'plupload' );

                                wp_enqueue_script( 'plupload-all' );
                        } elseif ( $page == 'rch-extensions' || $page == 'rch-settings' || $page == 'rch-manage-export' || $page == 'rch-manage-import' ) {

                                wp_enqueue_script( 'rch-general-admin-js' );

                                $rch_localize_script_data = array(
                                        'rchAjaxURL'      => admin_url( 'admin-ajax.php' ),
                                        'rchSiteURL'      => site_url(),
                                        'rchUploadURL'    => RCH_UPLOAD_URL,
                                        'rchUploadDir'    => RCH_UPLOAD_DIR,
                                        'rchPluginURL'    => RCH_PLUGIN_URL,
                                        'rchImageURL'     => RCH_IMAGES_URL,
                                        'rchLocalizeText' => self::rch_load_msg()
                                );

                                wp_localize_script( 'rch-general-admin-js', 'rchPluginSettings', $rch_localize_script_data );

                                unset( $rch_localize_script_data );
                        }
                        unset( $rchExtData );
                }
                unset( $page );
        }

        public static function rch_db_check() {

                $rch_plugin_version = get_option( 'rch_plugin_version', "" );

                if ( $rch_plugin_version == "" ) {

                        require_once(ABSPATH . 'wp-admin/modules/upgrade.php');

                        global $wpdb;

                        if ( $wpdb->has_cap( 'collation' ) ) {

                                if ( ! empty( $wpdb->charset ) )
                                        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

                                if ( ! empty( $wpdb->collate ) )
                                        $charset_collate .= " COLLATE $wpdb->collate";
                        }

                        update_option( 'rch_plugin_version', RCH_PLUGIN_VERSION );

                        update_option( 'rch_db_version', RCH_DB_VERSION );

                        update_option( 'rch_install_date', current_time( 'timestamp' ) );

                        $rch_template = $wpdb->prefix . 'rch_template';

                        $rch_template_table = "CREATE TABLE IF NOT EXISTS {$rch_template}(
							
                            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                            status VARCHAR(25),
                            opration VARCHAR(100) NOT NULL, 
                            username VARCHAR(60) NOT NULL, 
                            unique_id VARCHAR(100) NOT NULL, 
                            opration_type VARCHAR(100) NOT NULL,
                            options LONGTEXT,
                            process_log VARCHAR(255),
                            process_lock INT(3),
                            create_date DATETIME NOT NULL,
                            last_update_date DATETIME NOT NULL 

                            ){$charset_collate}";

                        dbDelta( $rch_template_table );

                        unset( $charset_collate, $rch_template, $rch_template_table );
                }

                unset( $rch_plugin_version );
        }


        public static function rch_hide_all_notice_to_admin_side() {
                if ( isset( $_GET[ 'page' ] ) && (sanitize_text_field($_GET[ 'page' ]) == 'rch-new-export' || sanitize_text_field($_GET[ 'page' ]) == 'rch-new-import') ) {
                        remove_all_actions( 'admin_notices', 10000 );
                        remove_all_actions( 'all_admin_notices', 10000 );
                        remove_all_actions( 'network_admin_notices', 10000 );
                        remove_all_actions( 'user_admin_notices', 10000 );
                }
        }

        public static function rch_set_menu() {

                global $current_user;

                if ( current_user_can( 'administrator' ) || is_super_admin() ) {
                        $rch_caps = self::rch_user_capabilities();

                        if ( ! empty( $rch_caps ) ) {
                                foreach ( $rch_caps as $rch_cap => $cap_desc ) {
                                        $current_user->add_cap( $rch_cap );
                                }
                        }
                        unset( $rch_caps );
                }

               
                add_menu_page( __( 'Woo Import Export Dashboard', 'rch-woo-import-export' ), __( 'RCH Woo I & E', 'rch-woo-import-export' ), 'rch_new_export', 'rch-new-export', array( __CLASS__, 'rch_get_page' ), 'dashicons-controls-repeat' );

                add_submenu_page( 'rch-new-export', __( 'New Export', 'rch-woo-import-export' ), __( 'Export', 'rch-woo-import-export' ), 'rch_new_export', 'rch-new-export', array( __CLASS__, 'rch_get_page' ) );

                add_submenu_page( 'rch-new-export', __( 'Export List', 'rch-woo-import-export' ), __( 'Export List', 'rch-woo-import-export' ), 'rch_manage_export', 'rch-manage-export', array( __CLASS__, 'rch_get_page' ) );

                add_submenu_page( 'rch-new-export', __( 'New Import', 'rch-woo-import-export' ), __( 'Import', 'rch-woo-import-export' ), 'rch_new_import', 'rch-new-import', array( __CLASS__, 'rch_get_page' ) );

                add_submenu_page( 'rch-new-export', __( 'Import List', 'rch-woo-import-export' ), __( 'Import List', 'rch-woo-import-export' ), 'rch_manage_import', 'rch-manage-import', array( __CLASS__, 'rch_get_page' ) );

                add_submenu_page( 'rch-new-export', __( 'Settings', 'rch-woo-import-export' ), __( 'Settings', 'rch-woo-import-export' ), 'rch_settings', 'rch-settings', array( __CLASS__, 'rch_get_page' ) );
        }

        public static function rch_get_page() {

                $page = isset( $_GET[ 'page' ] ) ? rch_sanitize_field( $_GET[ 'page' ] ) : "";

                if ( ! empty( $page ) && in_array( $page, self::$rch_page ) ) {

                        if ( $page == 'rch-new-export' && file_exists( RCH_VIEW_DIR . '/rch-new-export.php' ) ) {

                                require_once( RCH_VIEW_DIR . '/rch-new-export.php');
                        } elseif ( $page == 'rch-new-import' && file_exists( RCH_VIEW_DIR . '/rch-new-import.php' ) ) {

                                require_once( RCH_VIEW_DIR . '/rch-new-import.php');
                        } elseif ( $page == 'rch-manage-export' && file_exists( RCH_VIEW_DIR . '/rch-manage-export.php' ) ) {

                                require_once( RCH_VIEW_DIR . '/rch-manage-export.php');
                        } elseif ( $page == 'rch-manage-import' && file_exists( RCH_VIEW_DIR . '/rch-manage-import.php' ) ) {

                                require_once( RCH_VIEW_DIR . '/rch-manage-import.php');
                        } elseif ( $page == 'rch-settings' && file_exists( RCH_VIEW_DIR . '/rch-settings.php' ) ) {

                                require_once( RCH_VIEW_DIR . '/rch-settings.php');
                        } elseif ( $page == 'rch-extensions' ) {

                                $require_page = RCH_VIEW_DIR . '/rch-extensions.php';

                                $include_page = RCH_VIEW_DIR . '/rch-extension-info.php';

                                if ( isset( $_GET[ 'rch_ext' ] ) && ! empty( $_GET[ 'rch_ext' ] ) ) {

                                        if ( file_exists( RCH_CLASSES_DIR . '/class-rch-extensions.php' ) ) {
                                                require_once(RCH_CLASSES_DIR . '/class-rch-extensions.php');
                                        }
                                        $rch_ext = new \rch\addons\RCH_Extension();

                                        $is_valid_ext = $rch_ext->rch_import_extension_info( rch_sanitize_field( $_GET[ 'rch_ext' ] ) );

                                        if ( $is_valid_ext && file_exists( $include_page ) ) {

                                                require_once($include_page);
                                        } elseif ( file_exists( $require_page ) ) {

                                                require_once($require_page);
                                        }
                                        unset( $rch_ext, $is_valid_ext );
                                } elseif ( file_exists( $require_page ) ) {
                                        require_once($require_page);
                                }
                                unset( $include_page );

                                unset( $require_page );
                        }
                }
                unset( $page );
        }

        private static function rch_user_capabilities() {
                return array(
                        'rch_new_export'    => __( 'User can export new data', 'rch-woo-import-export' ),
                        'rch_manage_export' => __( 'User can manage export data', 'rch-woo-import-export' ),
                        'rch_new_import'    => __( 'User can import new data', 'rch-woo-import-export' ),
                        'rch_manage_import' => __( 'User can manage import data', 'rch-woo-import-export' ),
                        'rch_settings'      => __( 'User can manage Settings of import and export', 'rch-woo-import-export' ),
                        'rch_extensions'    => __( 'User can manage Extensions of import and export', 'rch-woo-import-export' ),
                        'rch_add_shortcode' => __( 'User Add Shortcode in import field', 'rch-woo-import-export' ),
                );
        }

        private static function get_dynamic_position( $start, $increment = 0.1 ) {

                foreach ( $GLOBALS[ 'menu' ] as $key => $menu ) {
                        $menus_positions[] = $key;
                }
                if ( ! in_array( $start, $menus_positions ) )
                        return $start;

                while ( in_array( $start, $menus_positions ) ) {
                        $start += $increment;
                }
                unset( $increment, $menus_positions );

                return $start;
        }

        public static function rch_replace_footer_admin() {
                echo '';
        }

        public static function rch_replace_footer_version() {
                return '';
        }

        private static function is_htaccess_writable() {

                require_once(ABSPATH . 'wp-admin/includes/file.php');

                $htaccess_file = get_home_path() . '.htaccess';

                if ( ! file_exists( $htaccess_file ) ) {

                        return false;
                }

                if ( wp_is_writable( $htaccess_file ) ) {
                        return true;
                }

                @chmod( $htaccess_file, 0666 );

                if ( ! wp_is_writable( $htaccess_file ) ) {
                        return false;
                }

                unset( $htaccess_file );

                return true;
        }

        public static function mod_rewrite_rules( $rules = "" ) {

                $newRule = "RewriteCond %{REQUEST_FILENAME} -s" . PHP_EOL;

                $newRule .= "RewriteCond %{HTTP_USER_AGENT} !facebookexternalhit/[0-9]" . PHP_EOL;

                $newRule .= "RewriteCond %{HTTP_USER_AGENT} !Twitterbot/[0-9]" . PHP_EOL;

                $newRule .= "RewriteCond %{HTTP_USER_AGENT} !Googlebot/[0-9]" . PHP_EOL;

                $upload_dir_url = str_replace( "https", "http", RCH_UPLOAD_URL );

                $site_url = str_replace( "https", "http", site_url() );

                $newRule .= "RewriteRule " . str_replace( trailingslashit( $site_url ), '', $upload_dir_url ) . "(\/[A-Za-z0-9_@.\/&+-]+)+\.([A-Za-z0-9_@.\/&+-]+)$ [L]" . PHP_EOL;

                update_option( 'rch_is_admin_notice_clear', 1 );

                update_option( 'rch_flush_rewrite_rules', 1 );

                unset( $site_url, $upload_dir_url );

                return $newRule . $rules . PHP_EOL;
        }

        public static function hide_notices() {

                if ( isset( $_GET[ 'rch-hide-notice' ] ) && isset( $_GET[ '_rch_notice_nonce' ] ) ) {

                        if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET[ '_rch_notice_nonce' ] ) ), 'rch_hide_notices_nonce' ) ) {
                                wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'rch-woo-import-export' ) );
                        }

                        $hide_notice = sanitize_text_field( wp_unslash( $_GET[ 'rch-hide-notice' ] ) );

                        update_user_meta( get_current_user_id(), 'dismissed_' . $hide_notice . '_notice', 1 );

                        unset( $hide_notice );
                }
        }

        public static function rch_admin_notices() {

                if ( self::is_apache() && get_option( 'rch_is_admin_notice_clear', false ) === false ) {

                        $notice = get_user_meta( get_current_user_id(), "dismissed_rch_file_security_notice", true );

                        if ( intval( $notice ) != 1 ) {
                                ?>
                                <div class="rch-message updated" >
                                        <a class="rch-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'rch-hide-notice', "rch_file_security" ), 'rch_hide_notices_nonce', '_rch_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'rch-woo-import-export' ); ?></a>
                                        <p><b><?php echo __( 'WP Import Export : ', 'rch-woo-import-export' ); ?></b> <?php _e( "If your <b>.htaccess</b> file were writable, we could do this automatically, but it isn’t. So you must either make it writable or manually update your .htaccess with the mod_rewrite rules found under <b>Settings >> Permalinks</b>. Until then, the exported and imported files are not protected from direct access.", 'rch-woo-import-export' ); ?></p>
                                </div>
                                <?php
                        }
                }
        }

        public static function is_apache() {
                // assume apache when unknown, since most common
                if ( ! isset( $_SERVER[ 'SERVER_SOFTWARE' ] ) || empty( $_SERVER[ 'SERVER_SOFTWARE' ] ) ) {
                        return true;
                }

                return isset( $_SERVER[ 'SERVER_SOFTWARE' ] ) && stristr( $_SERVER[ 'SERVER_SOFTWARE' ], 'Apache' ) !== false;
        }

        public static function update_file_security() {

                $is_updated = get_option( 'rch_is_updated_file_security', false );

                if ( $is_updated === false ) {

                        //update robots.txt
                        $robots_file = get_home_path() . 'robots.txt';

                        if ( file_exists( $robots_file ) && is_writable( $robots_file ) ) {

                                ob_start();

                                do_robots();

                                $robots_content = ob_get_clean();

                                if ( ($fp = @fopen( $robots_file, 'a+' )) !== false ) {

                                        fwrite( $fp, $robots_content );

                                        fclose( $fp );

                                        update_option( 'rch_is_updated_file_security', 1 );
                                }

                                unset( $robots_content );
                        }

                        //unset all variables & clear memory
                        unset( $robots_file );
                }

                unset( $is_updated );
        }

        public static function update_robots_txt( $robotstext = "", $public ) {

                $robotstext .= PHP_EOL . PHP_EOL . "#WP Import Export Rule";

                $robotstext .= PHP_EOL . "User-agent: *";

                $robotstext .= PHP_EOL . "Disallow: /wp-content/uploads/rch-woo-import-export/";

                return $robotstext;
        }

        private static function rch_load_msg() {
                return array(
                        "yesText"                         => __( 'Yes', 'rch-woo-import-export' ),
                        "okText"                          => __( 'Ok', 'rch-woo-import-export' ),
                        "errorText"                       => __( 'Error', 'rch-woo-import-export' ),
                        "confirmText"                     => __( 'Confirm', 'rch-woo-import-export' ),
                        "selectTemplateText"              => __( 'Select Template', 'rch-woo-import-export' ),
                        "rch_ajax_not_connect_error"     => __( 'Not connect.\n Verify Network.', 'rch-woo-import-export' ),
                        "rch_ajax_404_error"             => __( 'Requested page not found. [404]', 'rch-woo-import-export' ),
                        "rch_ajax_internal_server_error" => __( 'Internal Server Error [500].', 'rch-woo-import-export' ),
                        "rch_ajax_jason_parse_error"     => __( 'Requested JSON parse failed.', 'rch-woo-import-export' ),
                        "rch_ajax_time_out_error"        => __( 'Time out error.', 'rch-woo-import-export' ),
                        "rch_ajax_request_aborted_error" => __( 'Ajax request aborted.', 'rch-woo-import-export' ),
                        "rch_ajax_400_error"             => __( 'Bad Request', 'rch-woo-import-export' ),
                        "rch_ajax_uncaught_error"        => __( 'Uncaught Error', 'rch-woo-import-export' ),
                        "selectExportRuleText"            => __( 'Select Rule', 'rch-woo-import-export' ),
                        "selectElementText"               => __( 'Select Element', 'rch-woo-import-export' ),
                        "selectExportTypeText"            => __( 'Please choose export type', 'rch-woo-import-export' ),
                        "selectExportTaxTypeText"         => __( 'Please choose export taxonomy type', 'rch-woo-import-export' ),
                        "enterTemplateNameText"           => __( 'Please enter template Name', 'rch-woo-import-export' ),
                        "enterCsvDelimiterText"           => __( 'Please enter CSV delimiter', 'rch-woo-import-export' ),
                        "andText"                         => __( 'AND', 'rch-woo-import-export' ),
                        "orText"                          => __( 'OR', 'rch-woo-import-export' ),
                        "saveText"                        => __( 'Save', 'rch-woo-import-export' ),
                        "closeText"                       => __( 'Close', 'rch-woo-import-export' ),
                        "rchNoFieldsFoundText"           => __( "No fields found please choose other option", 'rch-woo-import-export' ),
                        "rchExportFieldEditorText"       => __( "Export Field Editor", 'rch-woo-import-export' ),
                        "rchExportEmptyFieldText"        => __( "Please Enter Field Name", 'rch-woo-import-export' ),
                        "rchExportEmptyDataText"         => __( "There aren't any Records to export.", 'rch-woo-import-export' ),
                        "rchExportCompletedText"         => __( "Export Completed", 'rch-woo-import-export' ),
                        "rchExportUserExtDisableText"    => __( "Please Activate User Export Extension", 'rch-woo-import-export' ),
                        "rchExportWCExtDisableText"      => __( "Please Activate WooCommerce Export Extension", 'rch-woo-import-export' ),
                        "rchExportEmptyColumnText"       => __( "You haven't selected any columns for export.", 'rch-woo-import-export' ),
                        "rchChooseFileText"              => __( 'Choose File', 'rch-woo-import-export' ),
                        "fileUploadSuccessText"           => __( 'File Uploaded Successfully', 'rch-woo-import-export' ),
                        "invalidFileExtensionText"        => __( 'Uploaded file must be CSV, ZIP, XLS, XLSX, XML, TXT, JSON', 'rch-woo-import-export' ),
                        "rchUploadingText"               => __( 'Uploading', 'rch-woo-import-export' ),
                        "rchUploadCompleteText"          => __( 'Upload Complete', 'rch-woo-import-export' ),
                        "rchParingUploadFileText"        => __( 'Parsing upload file', 'rch-woo-import-export' ),
                        "rchGetTemplatesText"            => __( 'Get Template List', 'rch-woo-import-export' ),
                        "rchGetConfigText"               => __( 'Get Configuration', 'rch-woo-import-export' ),
                        "rchGetFieldsText"               => __( 'Get Import Fields', 'rch-woo-import-export' ),
                        "rchGetRecordsText"              => __( 'Get Preview Recods', 'rch-woo-import-export' ),
                        "rchChangeTemplatesText"         => __( 'Set Template', 'rch-woo-import-export' ),
                        "rchSaveTemplatesText"           => __( 'Save Template', 'rch-woo-import-export' ),
                        "rchSaveSettingsText"            => __( 'Save Settings', 'rch-woo-import-export' ),
                        "rchNoRecordsFoundText"          => __( 'No Records Found. Please Try another filters', 'rch-woo-import-export' ),
                        "rchImportProcessingText"        => __( 'Import Processing', 'rch-woo-import-export' ),
                        "rchImportCompleteText"          => __( 'Import Complete!', 'rch-woo-import-export' ),
                        "rchImportPausedText"            => __( 'Import Paused', 'rch-woo-import-export' ),
                        "rchImportStoppedText"           => __( 'Import Stopped', 'rch-woo-import-export' ),
                        "rchImportProcessingNoticeText"  => __( 'Importing may take some time. Please do not close your browser or refresh the page until the process is complete.', 'rch-woo-import-export' ),
                        "rchImportPartiallyText"         => __( 'WordPress Import Export partially imported your file into your WordPress installation!', 'rch-woo-import-export' ),
                        "rchImportCompleteNoticeText"    => __( 'WordPress Import Export successfully imported your file into your WordPress installation!', 'rch-woo-import-export' ),
                        "rchChooseValidFileText"         => __( 'Please Choose Valid File', 'rch-woo-import-export' ),
                        "rchSetExistingFileText"         => __( 'Set Existing File', 'rch-woo-import-export' ),
                        "rchUploadFromURLText"           => __( 'File Upload From URL', 'rch-woo-import-export' ),
                        "rchUploadFromFTPText"           => __( 'File Upload From FTP', 'rch-woo-import-export' ),
                        "rchEmptyUsesrRole"              => __( 'Please choose user role', 'rch-woo-import-export' ),
                        "rchEmptyTemplates"              => __( 'Please Select Templates', 'rch-woo-import-export' ),
                        "rchEmptyActions"                => __( 'Please select any action', 'rch-woo-import-export' ),
                        "rchSetBGProcessText"            => __( 'Set Background Process', 'rch-woo-import-export' ),
                        "rchBgProcessingText"            => __( 'Background Process Set Successfully', 'rch-woo-import-export' ),
                        "rchImportBGText"                => __( 'Import in Background', 'rch-woo-import-export' ),
                        "rchImportBGNoticeText"          => __( 'plugin will automatically import data in Background. you can close your browser.', 'rch-woo-import-export' ),
                        "rchInvalidURLText"              => __( 'Please Enter Valid URL', 'rch-woo-import-export' ),
                        "rchInvalidHostNameText"         => __( 'Please Enter Valid Host Name', 'rch-woo-import-export' ),
                        "rchInvalidHostUsernameText"     => __( 'Please Enter Valid Host Username', 'rch-woo-import-export' ),
                        "rchInvalidHostPasswordText"     => __( 'Please Enter Valid Host Password', 'rch-woo-import-export' ),
                        "rchDownloadFileText"            => __( 'Downloading File', 'rch-woo-import-export' ),
                        "rchInvalidHostPathText"         => __( 'Please Enter Valid Host Path', 'rch-woo-import-export' ),
                        "rchImportUserExtDisableText"    => __( "Please Activate User Import Extension", 'rch-woo-import-export' ),
                        "rchImportWCExtDisableText"      => __( "Please Activate WooCommerce Import Extension", 'rch-woo-import-export' ),
                        "rchPrepareFile"                 => __( "Prepare File", 'rch-woo-import-export' ),
                        "rchPaused"                      => __( "Paused", 'rch-woo-import-export' ),
                        "rchProcessing"                  => __( "Processing", 'rch-woo-import-export' ),
                        "rchStopped"                     => __( "Stopped", 'rch-woo-import-export' ),
                        "rchSetScheduleExportText"       => __( "Set Schedule", 'rch-woo-import-export' ),
                        "rchFillRequiredFieldText"       => __( "Please Fill Required Fields", 'rch-woo-import-export' ),
                        "rchEmptyTemplate"               => __( "Please Select any template", 'rch-woo-import-export' ),
                        "rchEmptyLayout"                 => __( "Please Select any Layout", 'rch-woo-import-export' ),
                        "processingReimport"              => __( "Processing Reimport", 'rch-woo-import-export' ),
                        "rchInvalidLicense"              => __( "Please Activate Plugin Purchase Code from RCH Woo I&E => Settings", 'rch-woo-import-export' )
                );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
