<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}
if (file_exists(RCH_IMPORT_CLASSES_DIR . '/class-rch-import.php')) {
    require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import.php');
}

class RCH_Import_Actions extends \rch\import\RCH_Import {

    public function __construct() {

        $this->rch_init_import_addons();

        add_action('wp_ajax_rch_import_validate_uploads', array($this, 'rch_import_validate_uploads'));

        add_action('wp_ajax_rch_import_get_filtered_records', array($this, 'rch_import_get_filtered_records'));

        add_action('wp_ajax_rch_import_change_file', array($this, 'rch_import_change_file'));

        add_action('wp_ajax_rch_import_get_fields', array($this, 'rch_import_get_fields'));

        add_action('wp_ajax_rch_import_update_data', array($this, 'rch_import_update_data'));

        add_action('wp_ajax_rch_import_data', array($this, 'rch_import_site_data'));

        add_action('wp_ajax_rch_import_get_templates', array($this, 'rch_import_get_templates'));

        add_action('wp_ajax_rch_import_save_templates', array($this, 'rch_import_save_templates'));

        add_action('wp_ajax_rch_import_get_template_data', array($this, 'rch_import_get_template_data'));

        add_action('wp_ajax_rch_import_update_status', array($this, 'rch_import_update_status'));

        add_action('wp_ajax_rch_import_get_config', array($this, 'rch_import_get_config'));

        add_action('wp_ajax_rch_import_process_reimport', array($this, 'process_reimport'));
    }

    private function rch_init_import_addons() {

        if (file_exists(RCH_CLASSES_DIR . '/class-rch-extensions.php')) {
            require_once(RCH_CLASSES_DIR . '/class-rch-extensions.php');

            $rch_ext = new \rch\addons\RCH_Extension();

            $rch_ext->rch_init_extensions("import");

            unset($rch_ext);
        }
    }

    public function rch_import_validate_uploads() {
        parent::rch_parse_upload_file();
    }

    public function rch_import_get_filtered_records() {
        parent::rch_import_get_filtered_records();
    }

    public function rch_import_get_fields() {
        parent::rch_get_import_fields();
    }

    public function rch_import_update_data() {
        parent::rch_import_save_data();
    }

    public function rch_import_site_data() {
        parent::rch_import_data();
    }

    public function rch_import_get_templates() {
        parent::rch_get_template_list();
    }

    public function rch_import_save_templates() {
        parent::rch_import_save_template_data();
    }

    public function rch_import_get_template_data() {
        parent::rch_import_get_template_info();
    }

    public function rch_import_update_status() {
        parent::rch_import_update_process_status();
    }

    public function rch_import_get_config() {
        parent::get_config_file();
    }

    public function process_reimport() {

        parent::process_reimport_data();
    }

    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
