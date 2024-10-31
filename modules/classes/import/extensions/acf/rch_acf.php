<?php

if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_ACF_Import_Extension {

        public function __construct() {

                if (class_exists("ACF")) {

                        add_action('admin_enqueue_scripts', array($this, 'rch_enqueue_wc_scripts'), 10);

                        add_filter('rch_import_addon', array($this, "acf_addon_init"), 10, 2);

                        add_filter('rch_pre_post_field_mapping_section', array($this, "rch_acf_fields"), 10, 2);

                        add_filter('rch_pre_term_field_mapping_section', array($this, "rch_acf_fields"), 10, 2);
                }
        }

        public function rch_enqueue_wc_scripts() {

                wp_enqueue_script('rch-import-acf-js', RCH_IMPORT_ADDON_URL . '/acf/rch-import-acf.min.js', array('jquery'), RCH_PLUGIN_VERSION);
        }

        public function rch_acf_fields($sections = array(), $rch_import_type = "") {

                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/acf/rch-acf-fields.php';

                if (file_exists($fileName)) {

                        require_once($fileName);

                        if (function_exists("rch_get_acf_fields")) {
                                $sections = rch_get_acf_fields($sections, $rch_import_type);
                        }
                }
                unset($fileName);

                return $sections;
        }

        public function acf_addon_init($addons = array(), $rch_import_type = "") {

                global $acf;

                if ($acf && isset($acf->settings) && isset($acf->settings['version']) && version_compare($acf->settings['version'], '5.0.0') >= 0) {

                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/acf/class-rch-acf.php';

                        $class = '\rch\import\acf\RCH_ACF';

                        if (file_exists($fileName)) {

                                require_once($fileName);
                        }

                        if (!in_array($class, $addons)) {
                                $addons[] = $class;
                        }

                        unset($class, $fileName);
                }

                return $addons;
        }

}

new RCH_ACF_Import_Extension();
