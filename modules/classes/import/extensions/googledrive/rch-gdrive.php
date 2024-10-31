<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_GDrive_Extension {

    public function __construct() {

        add_filter('rch_import_upload_sections', array($this, 'get_gd_upload_views'), 10, 1);

        add_action('wp_ajax_rch_import_upload_file_from_googledrive', array($this, 'upload_from_gd'));

        add_action('admin_enqueue_scripts', array($this, 'enqueue_gd_scripts'), 10);
    }

    public function enqueue_gd_scripts() {

        wp_register_script('rch-import-gdrive-admin-js', "https://apis.google.com/js/api.js", array('jquery'), RCH_PLUGIN_VERSION, true);

        wp_register_script('rch-import-gdrive-local-admin-js', RCH_IMPORT_ADDON_URL . '/googledrive/rch-import-gdrive.js', array('jquery'), RCH_PLUGIN_VERSION, true);

        wp_enqueue_script('rch-import-gdrive-admin-js');

        wp_enqueue_script('rch-import-gdrive-local-admin-js');
    }

    public function get_gd_upload_views($rch_sections = array()) {

        $rch_sections["rch_import_googledrive_file_upload"] = array(
            "label" => __("Upload From Google Drive", 'rch-woo-import-export'),
            "icon" => 'fab fa-google-drive',
            "view" => RCH_IMPORT_CLASSES_DIR . "/extensions/googledrive/rch-gdrive-view.php",
        );

        return $rch_sections;
    }

    public function upload_from_gd() {

        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/googledrive/class-gdrive.php';

        if (file_exists($fileName)) {

            require_once($fileName);
        }

        $upload = new \rch\import\upload\googledrive\RCH_GDrive();

        $file = $upload->download_gdrive_file();

        unset($upload, $fileName);

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

new RCH_GDrive_Extension();
