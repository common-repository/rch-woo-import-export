<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_User_Export_Extension {

    public function __construct() {
        add_filter('rch_export_engine_init', array($this, 'rch_export_engine_init'), 10, 3);
    }

    public function rch_export_engine_init($export_engine = "", $export_type = "", $template_data = "") {

        if ($export_type == "users" || $export_type == "shop_customer") {

            $fileName = RCH_EXPORT_CLASSES_DIR . "/extensions/user/class-rch-user.php";

            if (file_exists($fileName)) {

                require_once($fileName);
            }

            unset($fileName);

            $export_engine = '\rch\export\user\RCH_User_Export';
        }
        unset($template_data);

        unset($export_type);

        return $export_engine;
    }

}

new RCH_User_Export_Extension();
