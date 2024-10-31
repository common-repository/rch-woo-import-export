<?php

namespace rch\import\chunk\csv;

use WP_Error;
use rch\lib\xml\array2xml;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (file_exists(RCH_IMPORT_CLASSES_DIR . '/class-rch-chunk.php')) {
    require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-chunk.php');
}

class RCH_CSV_Chunk extends \rch\import\chunk\RCH_Chunk {

    public function __construct() {
        
    }

    public function process_csv($fileDir = "", $file_name = "", $baseDir = "", $rch_csv_delimiter = ",", $rch_xml_fileName) {

        $file = RCH_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . $file_name;

        $newFileDir = RCH_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse";

        if (!file_exists($file)) {
            return new \WP_Error('rch_import_error', __('File not found', 'rch-woo-import-export'));
        }

        if (file_exists(RCH_DEPENCY_DIR . '/xml/class-rch-array2xml.php')) {
            require_once(RCH_DEPENCY_DIR . '/xml/class-rch-array2xml.php');
        }

        $converter = new \rch\lib\xml\array2xml\ArrayToXml();

        $converter->create_root("rchdata");

        $headers = array();

        $wfp = fopen($file, "rb");

        while (($keys = fgetcsv($wfp, 0, $rch_csv_delimiter)) !== false) {

            if (empty($headers)) {

                foreach ($keys as $key => $value) {

                    $value = trim(strtolower(preg_replace('/[^a-z0-9_]/i', '', $value)));

                    if (preg_match('/^[0-9]{1}/', $value)) {
                        $value = 'el_' . trim(strtolower($value));
                    }

                    $value = (!empty($value)) ? $value : 'undefined' . $key;

                    if (isset($headers[$key])) {
                        $key = $this->unique_array_key_name($key, $headers);
                    }

                    $headers[$key] = $value;
                }

                continue;
            }

            $fileData = array();

            foreach ($keys as $key => $value) {

                $header = isset($headers[$key]) ? $headers[$key] : "";

                if (!empty($header)) {

                    if (isset($fileData[$header])) {
                        $header = $this->unique_array_key_name($header, $fileData);
                    }

                    $fileData[$header] = $value;
                }
            }

            $converter->addNode($converter->root, "item", $fileData, 0);

            unset($fileData);
        }

        $converter->saveFile($newFileDir . '/' . $rch_xml_fileName . '1.xml');

        unset($file, $newFileDir, $converter, $headers);

        return true;
    }

    private function unique_array_key_name($key = "", $array = array()) {

        $count = 1;

        $new_key = $key;

        while (isset($array[$key])) {

            $key = $new_key . "_" . $count;
            $count++;
        }

        unset($count, $new_key);

        return $key;
    }

}
