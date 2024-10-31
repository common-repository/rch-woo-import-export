<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_BG_Extension {

    public function __construct() {

        $fileName = RCH_EXPORT_CLASSES_DIR . '/extensions/bg/class-rch-bg.php';

        if (file_exists($fileName)) {

            require_once($fileName);

            $bg_export = new \rch\export\bg\RCH_BG();

            $bg_export->init();

            unset($bg_export);
        }
    }

}

new RCH_BG_Extension();
