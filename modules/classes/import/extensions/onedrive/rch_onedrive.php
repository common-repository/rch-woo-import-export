<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_Onedrive_Extension {

    public function __construct() {

        add_filter('rch_import_upload_sections', array($this, 'get_onedrive_view'), 10, 1);

        add_action('wp_ajax_rch_import_upload_file_from_onedrive', array($this, 'prepare_onedrive_file'));

        add_action('admin_enqueue_scripts', array($this, 'rch_set_onedrive_scripts'), 10);
    }

    public function rch_set_onedrive_scripts() {

        wp_register_script('rch-import-onedrive-admin-js', "https://js.live.net/v7.2/OneDrive.js", array('jquery'), RCH_PLUGIN_VERSION, true);

        wp_register_script('rch-import-upload-onedrive-js', RCH_IMPORT_ADDON_URL . '/onedrive/rch-import-onedrive.js', array('jquery'), RCH_PLUGIN_VERSION, true);

        wp_enqueue_script('rch-import-onedrive-admin-js');

        wp_enqueue_script('rch-import-upload-onedrive-js');
    }

    public function get_onedrive_view($rch_sections = array()) {

        $rch_sections["rch_import_onedrive_file_upload"] = array(
            "label" => __("Upload From Onedrive", 'rch-woo-import-export'),
            "icon" => 'fas fa-cloud',
            "view" => RCH_IMPORT_CLASSES_DIR . "/extensions/onedrive/rch-onedrive-view.php",
        );

        return $rch_sections;
    }

    public function prepare_onedrive_file() {

        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/onedrive/class-onedrive.php';

        if (file_exists($fileName)) {

            require_once($fileName);
        }

        $upload = new \rch\import\upload\onedrive\RCH_Onedrive_Upload();

        $file = $upload->download_onedrive_file();

        unset($fileName, $upload);

        $return_value = array('status' => 'error');

        if (is_wp_error($file)) {
            $return_value['message'] = $file->get_error_message();
        } elseif (empty($file)) {
            $return_value['erorr_message'] = __('Failed to upload files', 'rch-woo-import-export');
        } elseif ($file == "processing") {
            $return_value['status'] = 'success';
            $return_value['message'] = 'processing';
        } else {

            $return_value['file_list'] = isset($file['file_list']) ? $file['file_list'] : array();

            $return_value['file_count'] = count($return_value['file_list']);

            $return_value['rch_import_id'] = isset($file['rch_import_id']) ? $file['rch_import_id'] : 0;

            $return_value['file_name'] = isset($file['file_name']) ? $file['file_name'] : "";

            $return_value['file_size'] = isset($file['file_size']) ? $file['file_size'] : "";

            $return_value['status'] = 'success';
        }

        unset($file);

        echo json_encode($return_value);

        die();
    }

}

new RCH_Onedrive_Extension();
