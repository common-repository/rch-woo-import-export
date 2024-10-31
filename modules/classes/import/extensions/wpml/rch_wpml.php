<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_WPML_Import_Extension {

    public function __construct() {

        if (class_exists('SitePress')) {

            add_filter('rch_pre_post_field_mapping_section', array($this, "get_wpml_tab_view"), 10, 2);

            add_filter('rch_pre_term_field_mapping_section', array($this, "get_wpml_tab_view"), 10, 2);

            add_filter('rch_import_addon', array($this, "wpml_addon_init"), 10, 2);
        }
    }

    public function get_wpml_tab_view($sections = array(), $rch_import_type = "") {

        if ($rch_import_type == "shop_coupon") {
            return $sections;
        }

        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wpml/rch-wpml-tab.php';

        if (file_exists($fileName)) {

            require_once($fileName);

            if (function_exists("rch_import_get_wpml_tab")) {
                $sections = rch_import_get_wpml_tab($sections, $rch_import_type);
            }
        }
        unset($fileName);

        return $sections;
    }

    public function wpml_addon_init($addons = array(), $rch_import_type = "") {

        if ($rch_import_type == "shop_coupon" || !class_exists('SitePress')) {
            return $addons;
        }

        if (!in_array('\rch\import\wpml\RCH_WPML_Import', $addons)) {

            $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wpml/class-rch-wpml.php';

            if (file_exists($fileName)) {

                require_once($fileName);
            }
            unset($fileName);

            $addons[] = '\rch\import\wpml\RCH_WPML_Import';
        }

        return $addons;
    }

    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}

new RCH_WPML_Import_Extension();
