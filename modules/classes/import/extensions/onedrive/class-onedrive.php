<?php

namespace rch\import\upload\onedrive;

use WP_Error;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (file_exists(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php')) {
    require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php');
}

class RCH_Onedrive_Upload extends \rch\import\upload\RCH_Upload {

    public function __construct() {
        
    }

    public function download_onedrive_file() {

        if (!is_dir(RCH_UPLOAD_IMPORT_DIR) || !wp_is_writable(RCH_UPLOAD_IMPORT_DIR)) {

            return new \WP_Error('rch_import_error', __('Uploads folder is not writable', 'rch-woo-import-export'));
        }

        $file_url = isset($_POST["file_url"]) ? rch_sanitize_field($_POST["file_url"]) : '';

        $fileName = isset($_POST["fileName"]) ? rch_sanitize_field($_POST["fileName"]) : '';

        $newfiledir = parent::rch_create_safe_dir_name($fileName);

        wp_mkdir_p(RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir);


        wp_mkdir_p(RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original");

        wp_mkdir_p(RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse");

        wp_mkdir_p(RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse/chunks");

        $filePath = RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName;

        chmod(RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/", 0755);

        $response = wp_safe_remote_get($file_url, array('timeout' => 3000, 'stream' => true, 'filename' => $filePath));

        unset($file_url);

        if (is_wp_error($response)) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            unset($fileName, $newfiledir, $filePath);

            return $response;
        } elseif (200 != wp_remote_retrieve_response_code($response)) {

            if (file_exists($filePath)) {
                unlink($filePath);
            }
            unset($fileName, $newfiledir, $filePath);

            return new \WP_Error('http_404', trim(wp_remote_retrieve_response_message($response)));
        }

        $content_md5 = wp_remote_retrieve_header($response, 'content-md5');

        unset($response);

        if ($content_md5) {

            $md5_check = verify_file_md5($filePath, $content_md5);

            if (is_wp_error($md5_check)) {

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                unset($fileName, $newfiledir, $filePath, $content_md5);

                return $md5_check;
            }
        }
        unset($filePath, $content_md5);

        $rch_import_id = isset($_POST['rch_import_id']) ? intval(rch_sanitize_field($_POST['rch_import_id'])) : 0;

        return parent::rch_manage_import_file($fileName, $newfiledir, $rch_import_id);
    }

}
