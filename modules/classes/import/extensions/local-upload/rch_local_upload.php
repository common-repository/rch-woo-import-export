<?php

if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_Local_Upload_Extension {

        public function __construct() {

                add_filter('rch_import_upload_sections', array($this, 'get_local_upload_view'), 10, 1);

                add_action('wp_ajax_rch_import_local_upload_file', array($this, 'upload_local_file'));
        }

        public function get_local_upload_view($rch_sections = array()) {

                $rch_sections["rch_import_local_upload"] = array(
                        "label" => __("Upload from Desktop", 'rch-woo-import-export'),
                        "icon" => 'fas fa-upload',
                        "view" => RCH_IMPORT_CLASSES_DIR . "/extensions/local-upload/rch-local-upload-view.php",
                );

                return $rch_sections;
        }

        public function upload_local_file() {


                $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/local-upload/class-rch-local-upload.php';

                if (file_exists($fileName)) {

                        require_once($fileName);
                }
                $upload = new \rch\import\upload\local\RCH_Local_Upload();

                $file = $upload->upload_local_file();

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

                        $return_value = array_merge($return_value, $file);

                        $return_value['file_count'] = isset($file['file_list']) ? count($file['file_list']) : 0;

                        $return_value['status'] = 'success';
                }

                unset($file);

                echo json_encode($return_value);

                die();
        }

}

new RCH_Local_Upload_Extension();
