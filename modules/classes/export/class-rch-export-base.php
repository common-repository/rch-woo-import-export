<?php

namespace rch\export\base;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

abstract class RCH_Export_Base {

    protected $export_type;
    protected $export_id;
    protected $template_options;
    protected $process_log;
    protected $export_data;
    protected $is_preview = false;
    protected $opration = "export";
    protected $preview_data = array();
    protected $export_labels = array();
    protected $addons = array();
    protected $has_multiple_rows = false;

    public function __construct() {
        
    }

    protected function taxonomies_by_object_type($object_type = null, $output = 'names') {

        global $wp_taxonomies;

        is_array($object_type) or $object_type = array($object_type);

        $field = ('names' == $output) ? 'name' : false;

        $taxonomy = array();

        if (!empty($wp_taxonomies)) {

            foreach ($wp_taxonomies as $key => $obj) {

                if (array_intersect($object_type, $obj->object_type)) {

                    $taxonomy[$key] = $obj;
                }
            }
        }

        if ($field) {

            $taxonomy = wp_list_pluck($taxonomy, $field);
        }

        unset($field, $object_type);

        return $taxonomy;
    }

    protected function add_filter_rule($filters = array(), $is_int = false, $table_alias = false) {

        $condition = isset($filters['condition']) ? $filters['condition'] : "";

        $value = isset($filters['value']) ? $filters['value'] : "";

        $element = isset($filters['element']) ? $filters['element'] : "";

        $clause = isset($filters['clause']) ? $filters['clause'] : "";

        $return_data = "";

        if (!empty($condition)) {

            switch ($condition) {
                case 'equals':
                    if (in_array($element, array('post_date', 'comment_date', 'user_registered', 'user_role'))) {
                        $return_data = "LIKE '%" . $value . "%'";
                    } else {
                        $return_data = "= " . (($is_int && is_numeric($value)) ? $value : "'" . $value . "'");
                    }
                    break;
                case 'not_equals':
                    if (in_array($element, array('post_date', 'comment_date', 'user_registered', 'user_role'))) {
                        $return_data = "NOT LIKE '%" . $value . "%'";
                    } else {
                        $return_data = "!= " . (($is_int && is_numeric($value)) ? $value : "'" . $value . "'");
                    }
                    break;
                case 'greater':
                    $return_data = "> " . (($is_int && is_numeric($value)) ? $value : "'" . $value . "'");
                    break;
                case 'equals_or_greater':
                    $return_data = ">= " . (($is_int && is_numeric($value)) ? $value : "'" . $value . "'");
                    break;
                case 'less':
                    $return_data = "< " . (($is_int && is_numeric($value)) ? $value : "'" . $value . "'");
                    break;
                case 'equals_or_less':
                    $return_data = "<= " . (($is_int && is_numeric($value)) ? $value : "'" . $value . "'");
                    break;
                case 'contains':
                    $return_data = "LIKE '%" . $value . "%'";
                    break;
                case 'not_contains':
                    $return_data = "NOT LIKE '%" . $value . "%'";
                    break;
                case 'is_empty':
                    $return_data = "IS NULL";
                    break;
                case 'is_not_empty':
                    $return_data = "IS NOT NULL";
                    if ($table_alias) {
                        $return_data .= " AND $table_alias.meta_value <> '' ";
                    }
                    break;
                default:
                    break;
            }
        }

        if (!empty($clause)) {
            $return_data .= " " . $clause . " ";
        }

        unset($condition, $value, $element, $clause);

        return $return_data;
    }

    protected function add_date_filter_rule($rule = array()) {

        $value = isset($rule['value']) ? $rule['value'] : "";

        $condition = isset($rule['condition']) ? $rule['condition'] : "";

        if (strpos($value, "+") !== 0 && strpos($value, "-") !== 0 && strpos($value, "next") === false && strpos($value, "last") === false && (strpos($value, "second") !== false || strpos($value, "minute") !== false || strpos($value, "hour") !== false || (strpos($value, "day") !== false && strpos($value, "today") === false && strpos($value, "yesterday") === false) || strpos($value, "week") !== false || strpos($value, "month") !== false || strpos($value, "year") !== false)) {
            $value = "-" . trim(str_replace("ago", "", $value));
        }

        $value = strpos($value, ":") !== false ? date("Y-m-d H:i:s", strtotime($value)) : ( in_array($condition, array('greater')) ? date("Y-m-d", strtotime('+1 day', strtotime($value))) : date("Y-m-d", strtotime($value)));

        unset($condition);

        return $value;
    }

    protected function remove_prefix($str = "", $prefix = "") {

        if (substr($str, 0, strlen($prefix)) == $prefix) {
            $str = substr($str, strlen($prefix));
        }

        return $str;
    }

    protected function apply_user_function($data = "", $is_enabled = false, $php_fun = "") {

        try {
            if ($is_enabled && !empty($php_fun) && function_exists($php_fun)) {

                $data = call_user_func($php_fun, $data);
            }
        } catch (Exception $ex) {
            
        } catch (Error $err) {
            
        }

        return $data;
    }

    public function get_taxonomies_by_post_type($export_type = array("post"), $cats_type = 'rch_tax', $is_attr = false) {

        $post_taxonomies = array_diff_key($this->taxonomies_by_object_type($export_type, 'object'), array_flip(array('post_format')));

        $taxonomies = array();

        if (!empty($post_taxonomies)) {

            foreach ($post_taxonomies as $tax) {

                if ((!$is_attr && strpos($tax->name, "pa_") !== 0) || ( $is_attr && strpos($tax->name, "pa_") === 0)) {

                    if ($tax->name == "product_type") {
                        $tax_name = __("Product Type", 'rch-woo-import-export');
                    } elseif ($tax->name == "product_visibility") {
                        $tax_name = __("Product Visibility", 'rch-woo-import-export');
                    } else {
                        $tax_name = isset($tax->label) ? $tax->label : $tax->name;
                    }
                    $taxonomies[] = array(
                        'name' => $tax_name,
                        'type' => $cats_type,
                        'taxName' => $tax->name,
                        'isTax' => true,
                        'hierarchical' => $tax->hierarchical
                    );
                    unset($tax_name);
                }
            }
        }
        unset($post_taxonomies);

        return $taxonomies;
    }

    protected function get_date_field($date_type = "", $timestamp = "", $date_format = "", $default_format = "") {

        if (empty($timestamp)) {
            return $timestamp;
        } else {
            $timestamp = (int) $timestamp;
        }
        if ($date_type == "unix") {
            $date = $timestamp;
        } else {

            if (empty($date_format)) {
                $date_format = $default_format;
            }

            $date = date($date_format, $timestamp);

            unset($date_format);
        }

        return $date;
    }

}
