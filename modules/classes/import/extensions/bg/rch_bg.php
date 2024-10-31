<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_BG_Import_Extension {

    public function __construct() {

       $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/bg/class-rch-bg.php';

        if (file_exists($fileName)) {

            require_once($fileName);

            new \rch\import\bg\RCH_BG_Import();
        }
        unset($fileName);
    }
}

new RCH_BG_Import_Extension();
