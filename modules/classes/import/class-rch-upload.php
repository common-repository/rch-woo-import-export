<?php

namespace rch\import\upload;

use rch\import;

if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_Upload {

        public function __construct() {
                
        }

        public function rch_get_upload_section() {

                return apply_filters("rch_import_upload_sections", array());
        }

        public function rch_create_safe_dir_name($str = "", $separator = 'dash', $lowercase = true) {

                if ($separator == 'dash') {
                        $search = '_';
                        $replace = '-';
                } else {
                        $search = '-';
                        $replace = '_';
                }

                $trans = array(
                        '&\#\d+?;' => '',
                        '&\S+?;' => '',
                        '\s+' => $replace,
                        '[^a-z0-9\-\._]' => '',
                        $search . '+' => $replace,
                        $search . '$' => $replace,
                        '^' . $search => $replace,
                        '\.+$' => ''
                );

                $str = strip_tags($str);

                foreach ($trans as $key => $val) {
                        $str = preg_replace("#" . $key . "#i", $val, $str);
                }

                if ($lowercase === true) {
                        $str = strtolower($str);
                }

                unset($search, $replace, $trans);

                return md5(trim(wp_unslash($str)) . time());
        }

        protected function rch_manage_import_file($fileName = "", $fileDir = "", $rch_import_id = 0) {

                $filePath = RCH_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/original";

                $current_file = $filePath . "/" . $fileName;

                $file_data = array();

                $fileList = array();

                $active_file = "";

                if (!is_file($current_file)) {

                        unset($filePath, $current_file, $file_data, $fileList, $active_file);

                        return new \WP_Error('rch_import_error', __('Uploaded file is empty', 'rch-woo-import-export'));
                } elseif (!preg_match('%\W(xml|zip|csv|xls|xlsx|xml|ods|txt|json)$%i', trim($fileName))) {
                        unset($filePath, $current_file, $file_data, $fileList, $active_file);

                        return new \WP_Error('rch_import_error', __('Uploaded file must be XML, CSV, ZIP, XLS, XLSX, XML, ODS, TXT, JSON', 'rch-woo-import-export'));
                } elseif (preg_match('%\W(zip)$%i', trim($fileName))) {

                        WP_Filesystem();

                        wp_mkdir_p(RCH_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/config");

                        wp_mkdir_p($filePath . "/extract");

                        $is_success = $this->unzip_file($current_file, $filePath . "/extract");

                        if (true !== $is_success) {
                                unset($filePath, $current_file, $file_data, $fileList, $active_file);
                                return $is_success;
                        }

                        $file_list = $this->rch_get_file_list($filePath . "/extract", true, false);

                        if (!empty($file_list)) {

                                if (isset($file_list["config.json"])) {

                                        $configPath = RCH_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/config/config.json";

                                        $_is_success = copy($filePath . "/extract/config.json", $configPath);

                                        if ($_is_success) {

                                                unlink($filePath . "/extract/config.json");

                                                $config = json_decode(file_get_contents($configPath), true);

                                                if (is_array($config)) {

                                                        $new_key = $this->rch_create_safe_dir_name("config");

                                                        if (isset($config['fileName']) && !empty($config['fileName'])) {

                                                                $_new_filename = preg_replace("/[^a-z0-9\_\-\.]/i", '', $config['fileName']);

                                                                $_temp_path = RCH_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/original/extract/";

                                                                rename($_temp_path . $config['fileName'], $_temp_path . $_new_filename);

                                                                $file_data[$new_key] = array(
                                                                        'fileDir' => $fileDir . "/original/extract",
                                                                        'fileName' => $_new_filename,
                                                                        'originalName' => $fileName,
                                                                        'baseDir' => $fileDir
                                                                );
                                                                $fileList[] = array(
                                                                        'fileKey' => $new_key,
                                                                        'fileName' => $_new_filename
                                                                );

                                                                $active_file = $new_key;

                                                                unset($new_key, $_new_filename, $_temp_path);
                                                        }
                                                }
                                                unset($config);
                                        }

                                        unset($configPath, $_is_success);
                                } else {

                                        foreach ($file_list as $key => $value) {

                                                $new_key = $this->rch_create_safe_dir_name($key);

                                                $new_file_dir = "";

                                                if ($key == $value) {
                                                        $new_file_dir = $fileDir . "/original/extract";
                                                } else {
                                                        $new_file_dir = $fileDir . "/original/extract/" . dirname($key);
                                                }
                                                $_new_filename = preg_replace("/[^a-z0-9\_\-\.]/i", '', $value);

                                                $_temp_path = RCH_UPLOAD_IMPORT_DIR . "/" . $new_file_dir . "/";

                                                rename($_temp_path . $value, $_temp_path . $_new_filename);

                                                $file_data[$new_key] = array(
                                                        'fileDir' => $new_file_dir,
                                                        'fileName' => $_new_filename,
                                                        'originalName' => $fileName,
                                                        'baseDir' => $fileDir
                                                );

                                                $fileList[] = array(
                                                        'fileKey' => $new_key,
                                                        'fileName' => $_new_filename
                                                );
                                                if ($active_file == "") {
                                                        $active_file = $new_key;
                                                }
                                                unset($new_file_dir, $new_key, $_new_filename, $_temp_path);
                                        }
                                }
                        }

                        unset($file_list, $is_success);
                } else {

                        $file_data[$fileDir] = array(
                                'fileDir' => $fileDir . "/original",
                                'fileName' => $fileName,
                                'originalName' => $fileName,
                                'baseDir' => $fileDir
                        );

                        $fileList[] = array(
                                'fileKey' => $fileDir,
                                'fileName' => sanitize_file_name($fileName)
                        );

                        $active_file = $fileDir;
                }

                if (file_exists(RCH_IMPORT_CLASSES_DIR . '/class-rch-import.php')) {
                        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import.php');
                }

                $rch_import = new \rch\import\RCH_Import();

                if ($rch_import_id > 0) {

                        global $wpdb;

                        $new_values = array();

                        $template_data = $rch_import->get_template_by_id($rch_import_id);

                        if ($template_data) {

                                $template_options = isset($template_data->options) ? maybe_unserialize($template_data->options) : array();

                                $template_options['importFile'] = isset($template_options['importFile']) ? $template_options['importFile'] : array();

                                $template_options['importFile'] = array_merge($file_data, $template_options['importFile']);

                                $template_options['activeFile'] = $active_file;

                                $new_values['options'] = maybe_serialize($template_options);

                                unset($template_options);
                        } else {

                                $new_values['options'] = maybe_serialize(array("importFile" => $file_data, "activeFile" => $active_file));
                        }

                        $wpdb->update($wpdb->prefix . "rch_template", $new_values, array('id' => $rch_import_id));

                        unset($new_values, $template_data);
                } else {
                        $rch_import_id = $rch_import->rch_generate_template(array("importFile" => $file_data, "activeFile" => $active_file), 'import-draft', 'processing');
                }

                unset($filePath, $file_data, $active_file, $rch_import);

                return array('file_list' => $fileList, 'rch_import_id' => $rch_import_id, "file_name" => sanitize_file_name($fileName), "file_size" => filesize($current_file));
        }

        public function rch_get_file_list($targetDir = "", $remove_extra_files = true, $time_string = false) {

                $result = array();

                if (!isset($this->rch_date_format) || empty($this->rch_date_format)) {
                        $this->rch_date_format = get_option('date_format');
                        $this->rch_time_format = get_option('time_format');
                }

                $cdir = scandir($targetDir);

                if (is_array($cdir)) {

                        foreach ($cdir as $key => $value) {
                                if (!in_array($value, array(".", ".."))) {
                                        if (is_dir($targetDir . '/' . $value)) {
                                                $new_data = $this->rch_get_file_list($targetDir . '/' . $value, $remove_extra_files, $time_string);
                                                if (is_array($new_data)) {
                                                        foreach ($new_data as $new_key => $new_info) {
                                                                $result[$value . '/' . $new_key] = $new_info;
                                                        }
                                                } else {
                                                        $result[$value . '/' . $new_data] = $new_data;
                                                }
                                                unset($new_data);
                                        } else {
                                                if (preg_match('%\W(csv|xml|json|txt|xls|xlsx|ods)$%i', basename($value))) {

                                                        if ($time_string) {
                                                                $value_data = $value . '&nbsp;&nbsp;&nbsp;' . date($this->rch_date_format . ' ' . $this->rch_time_format, ( filectime($targetDir . '/' . $value)));
                                                        } else {
                                                                $value_data = $value;
                                                        }
                                                        $result[$value] = $value_data;

                                                        unset($value_data);
                                                } elseif ($remove_extra_files) {
                                                        unlink($targetDir . '/' . $value);
                                                }
                                        }
                                }
                        }
                }

                unset($cdir);

                return $result;
        }

        private function unzip_file($file, $to) {

                global $wp_version;

                if (version_compare($wp_version, '4.6', '<')) {
                        return rch_unzip_file($file, $to);
                } else {
                        return unzip_file($file, $to);
                }
        }

        public function __destruct() {
                foreach ($this as $key => $value) {
                        unset($this->$key);
                }
        }

}
