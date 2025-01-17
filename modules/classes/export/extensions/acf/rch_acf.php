<?php

if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_ACF_Extension {

        public function __construct() {

                add_filter('rch_prepare_post_fields', array($this, 'prepare_acf_addon'), 10, 2);

                add_filter('rch_prepare_taxonomy_fields', array($this, 'prepare_acf_addon'), 10, 2);

                add_filter('rch_prepare_user_fields', array($this, 'prepare_acf_addon'), 10, 2);

                add_filter('rch_prepare_export_addons', array($this, 'prepare_acf_addon'), 10, 2);
        }

        public function prepare_acf_addon($addons = array(), $export_type = array("post")) {

                global $acf;

                if ($acf && isset($acf->settings) && isset($acf->settings['version']) && version_compare($acf->settings['version'], '5.0.0') >= 0) {

                        $fileName = RCH_EXPORT_CLASSES_DIR . '/extensions/acf/class-rch-acf.php';

                        if (file_exists($fileName)) {

                                require_once($fileName);
                        }

                        $class = '\rch\export\acf\RCH_ACF';

                        if (!in_array($class, $addons)) {
                                $addons[] = $class;
                        }
                }

                return $addons;
        }

}

new RCH_ACF_Extension();
