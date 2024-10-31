<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_User_Import_Extension {

    public function __construct() {

        add_filter('rch_import_engine_init', array($this, "rch_import_engine_init"), 10, 3);

        add_filter('rch_import_mapping_fields_file', array($this, "rch_import_mapping_fields_file"), 10, 2);
    }

    public function rch_import_mapping_fields_file($fileName = "", $import_type = "") {

        if ($import_type == "users" || $import_type == "shop_customer") {

            $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/user/rch-user-fields.php';
        }

        return $fileName;
    }

    public function rch_import_engine_init($import_engine = "", $rch_import_type = "", $template_data = "") {

        if ($rch_import_type == "users" || $rch_import_type == "shop_customer") {

            $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/user/class-rch-user.php';

            if (file_exists($fileName)) {

                require_once($fileName);
            }
            unset($fileName);

            $import_engine = '\rch\import\user\RCH_User_Import';
        }

        return $import_engine;
    }

}

new RCH_User_Import_Extension();
