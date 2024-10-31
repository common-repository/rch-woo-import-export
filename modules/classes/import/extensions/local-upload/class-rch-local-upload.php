<?php

namespace rch\import\upload\local;

use WP_Error;

if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (file_exists(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php')) {
        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php');
}

class RCH_Local_Upload extends \rch\import\upload\RCH_Upload {

        public function __construct() {
                
        }

        public function upload_local_file() {

                if (!is_dir(RCH_UPLOAD_DIR) || !wp_is_writable(RCH_UPLOAD_DIR)) {

                        return new \WP_Error('rch_import_error', __('Uploads folder is not writable', 'rch-woo-import-export'));
                }

                $fileName = isset($_POST["name"]) && !empty($_POST["name"]) ? sanitize_file_name(rch_sanitize_field(preg_replace("/[^a-z0-9\_\-\.]/i", '', sanitize_text_field($_POST["name"])))) : '';
              
                if (!preg_match('%\W(zip|csv|xls|xlsx|xml|txt|json)$%i', trim(basename($fileName)))) {

                        unset($fileName);

                        return new \WP_Error('rch_import_error', __('Uploaded file must be CSV, ZIP, XLS, XLSX, XML, TXT, JSON', 'rch-woo-import-export'));
                }

                $rch_import_id = isset($_POST["rch_import_id"]) ? intval(rch_sanitize_field($_POST["rch_import_id"])) : 0;

                $maxFileAge = 5 * 3600;

                $chunk = isset($_POST["chunk"]) ? intval(rch_sanitize_field($_POST["chunk"])) : 0;

                $chunks = isset($_POST["chunks"]) ? intval(rch_sanitize_field($_POST["chunks"])) : 0;

                if ($chunks < 2 && file_exists(RCH_UPLOAD_TEMP_DIR . '/' . $fileName)) {

                        $ext = strrpos($fileName, '.');

                        $fileName_a = substr($fileName, 0, $ext);

                        $fileName_a = (strlen($fileName_a) < 30) ? $fileName_a : substr($fileName_a, 0, 30);

                        $fileName_b = substr($fileName, $ext);

                        $count = 1;

                        while (file_exists(RCH_UPLOAD_TEMP_DIR . '/' . $fileName_a . '_' . $count . $fileName_b)) {

                                $count++;
                        }

                        $fileName = sanitize_file_name($fileName_a) . '_' . $count . $fileName_b;

                        unset($ext, $fileName_a, $fileName_b, $count);
                }

                $filePath = RCH_UPLOAD_TEMP_DIR . '/' . $fileName;

                if (is_dir(RCH_UPLOAD_TEMP_DIR) && ($dir = opendir(RCH_UPLOAD_TEMP_DIR))) {

                        while (($file = readdir($dir)) !== false) {

                                $tmpfilePath = RCH_UPLOAD_TEMP_DIR . '/' . $file;

                                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part") && file_exists($tmpfilePath)) {
                                        unlink($tmpfilePath);
                                }

                                unset($tmpfilePath);
                        }

                        closedir($dir);
                } else {
                        unset($chunk, $maxFileAge, $chunks, $filePath);
                        return new \WP_Error('rch_import_error', __('Failed to open temp directory', 'rch-woo-import-export'));
                }
                unset($maxFileAge);

                if (isset($_SERVER["CONTENT_TYPE"])) {
                        $contentType = $_SERVER["CONTENT_TYPE"];
                } elseif (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
                        $contentType = $_SERVER["HTTP_CONTENT_TYPE"];
                }

                if (strpos($contentType, "multipart") !== false) {

                        unset($contentType);

                        if ($_FILES && file_exists($_FILES['local_file']['tmp_name']) && is_uploaded_file($_FILES['local_file']['tmp_name'])) {

                                $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");

                                if ($out) {

                                        $in = fopen($_FILES['local_file']['tmp_name'], "rb");

                                        if ($in) {
                                                while ($buff = fread($in, 4096)) {
                                                        fwrite($out, $buff);
                                                }
                                        } else {

                                                fclose($out);

                                                unset($out);

                                                return new \WP_Error('rch_import_error', __('Failed to open input stream.', 'rch-woo-import-export'));
                                        }

                                        fclose($in);

                                        fclose($out);

                                        unset($in, $out);
                                } else {
                                        unset($out);
                                        return new \WP_Error('rch_import_error', __('Failed to open output stream.', 'rch-woo-import-export'));
                                }
                        } else {
                                return new \WP_Error('rch_import_error', __('Failed to move uploaded file.', 'rch-woo-import-export'));
                        }
                } else {

                        unset($contentType);

                        $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");

                        if ($out) {

                                $in = fopen("php://input", "rb");

                                if ($in) {
                                        while ($buff = fread($in, 4096)) {
                                                fwrite($out, $buff);
                                        }
                                } else {

                                        fclose($out);

                                        unset($out);

                                        return new \WP_Error('rch_import_error', __('Failed to open input stream.', 'rch-woo-import-export'));
                                }

                                fclose($in);

                                fclose($out);

                                unset($in, $out);
                        } else {

                                unset($out);

                                return new \WP_Error('rch_import_error', __('Failed to open output stream.', 'rch-woo-import-export'));
                        }
                }

                $newfiledir = "";

                if (!$chunks || $chunk == $chunks - 1) {

                        rename("{$filePath}.part", $filePath);

                        chmod($filePath, 0755);

                        $newfiledir = parent::rch_create_safe_dir_name($fileName);

                        $newFilePath = RCH_UPLOAD_IMPORT_DIR . "/" . $newfiledir;

                        wp_mkdir_p($newFilePath);

                        wp_mkdir_p($newFilePath . "/original");

                        wp_mkdir_p($newFilePath . "/parse");

                        wp_mkdir_p($newFilePath . "/parse/chunks");

                        copy($filePath, $newFilePath . "/original/" . $fileName);

                        if (file_exists($filePath)) {
                                unlink($filePath);
                        }
                        unset($newFilePath, $filePath, $chunk, $chunks);

                        return parent::rch_manage_import_file($fileName, $newfiledir, $rch_import_id);
                } else {
                        unset($chunk, $chunks, $newfiledir);
                        return 'processing';
                }
        }

        public function __destruct() {
                foreach ($this as $key => $value) {
                        unset($this->$key);
                }
        }

}
