<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_URL_Upload_Extension {

    public function __construct() {

        add_filter('rch_import_upload_sections', array($this, 'get_url_upload_views'), 10, 1);

        add_action('wp_ajax_rch_import_upload_file_from_url', array($this, 'upload_file_from_url'));
    }

    public function get_url_upload_views($rch_sections = array()) {

        $rch_sections["rch_import_url_file_upload"] = array(
            "label" => __("Upload From URL", 'rch-woo-import-export'),
            "icon" => 'fas fa-link',
            "view" => RCH_IMPORT_CLASSES_DIR . "/extensions/url-upload/rch-url-upload-view.php",
        );

        return $rch_sections;
    }

    public function upload_file_from_url() {

        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/url-upload/class-rch-url-upload.php';

        if (file_exists($fileName)) {

            require_once($fileName);
        }

        $upload = new rch\import\upload\url\RCH_URL_Upload();

        $rch_import_id = isset($_POST['rch_import_id']) ? intval(rch_sanitize_field($_POST['rch_import_id'])) : 0;

        $file_url = isset($_POST["file_url"]) ? rch_sanitize_field($_POST["file_url"]) : '';

        $file = $upload->rch_download_file_from_url($rch_import_id, $file_url);

        unset($upload, $fileName, $file_url, $rch_import_id);

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

        echo json_encode($return_value);

        die();
    }

}

new RCH_URL_Upload_Extension();
