<?php

namespace rch\export\comment;

use WP_Comment_Query;

if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (file_exists(RCH_EXPORT_CLASSES_DIR . '/class-rch-export-engine.php')) {

        require_once(RCH_EXPORT_CLASSES_DIR . '/class-rch-export-engine.php');
}

class RCH_Comment extends \rch\export\engine\RCH_Export_Engine {

        private $item_where = "";
        private $item_join = array();

        protected function get_fields() {

                $standard_fields = array(
                        'title'     => __("standard", 'rch-woo-import-export'),
                        "isDefault" => true,
                        'data'      => array(
                                array(
                                        'name'      => 'ID',
                                        'type'      => 'comment_ID',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'Post ID',
                                        'type'      => 'comment_post_ID',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'       => 'Post title',
                                        'type'       => 'comment_post_title',
                                        'isDefault'  => true,
                                        'isFiltered' => false
                                ),
                                array(
                                        'name'      => 'Author',
                                        'type'      => 'comment_author',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'Author Email',
                                        'type'      => 'comment_author_email',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'Author URL',
                                        'type'      => 'comment_author_url',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'Author IP',
                                        'type'      => 'comment_author_IP',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'Date',
                                        'type'      => 'comment_date',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'Content',
                                        'type'      => 'comment_content',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'Karma',
                                        'type'      => 'comment_karma',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'Approved',
                                        'type'      => 'comment_approved',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'Agent',
                                        'type'      => 'comment_agent',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'Type',
                                        'type'      => 'comment_type',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'Comment Parent',
                                        'type'      => 'comment_parent',
                                        'isDefault' => true,
                                ),
                                array(
                                        'name'      => 'User ID',
                                        'type'      => 'user_id',
                                        'isDefault' => true,
                                )
                        )
                );

                $export_fields = array(
                        "standard" => apply_filters('rch_comment_standard_fields', $standard_fields),
                        "meta"     => apply_filters('rch_comment_meta_fields', $this->get_meta_keys()),
                );

                $addon_class = apply_filters('rch_prepare_comment_fields', array(), $this->export_type);

                if (!empty($addon_class)) {

                        foreach ($addon_class as $addon) {

                                if (class_exists($addon)) {

                                        $addon_data = new $addon();

                                        if (method_exists($addon_data, "pre_process_fields")) {

                                                $addon_data->pre_process_fields($export_fields, $this->export_type);
                                        }

                                        unset($addon_data);
                                }
                        }
                }

                $meta_data = array();

                if (!empty($export_fields["meta"])) {
                        foreach ($export_fields["meta"] as $key) {

                                if (empty(trim($key))) {
                                        continue;
                                }
                                $meta_data[] = apply_filters("rch_pre_item_meta", array(
                                        'name'    => $key,
                                        'type'    => "rch_cf",
                                        'metaKey' => $key
                                        ), $key);
                        }
                }

                $export_fields['meta'] = array(
                        "title" => __("Custom Fields", 'rch-woo-import-export'),
                        "data"  => $meta_data
                );


                unset($standard_fields, $addon_class, $meta_data);

                return apply_filters("rch_export_fields", $export_fields, $this->export_type);
        }

        private function get_meta_keys() {

                $this->opration = "ids";

                $this->process_log = array(
                        'exported' => 0,
                        'total'    => 0,
                );

                $this->manage_rules();

                $comments = $this->process_export();

                $meta = array();

                if (!empty($comments)) {

                        foreach ($comments as $comment) {

                                $comment_meta = get_comment_meta($comment, '');

                                if (!empty($comment_meta)) {

                                        foreach ($comment_meta as $_key => $_value) {

                                                if (!in_array($_key, $meta)) {

                                                        $meta[] = $_key;
                                                }
                                        }
                                }
                                unset($comment_meta);
                        }
                }

                unset($comments);

                return $meta;
        }

        protected function parse_rule($filter = array()) {

                if (isset($filter['element'])) {

                        global $wpdb;

                        $filter['condition'] = isset($filter['condition']) ? $filter['condition'] : "";

                        $filter['value'] = isset($filter['value']) ? $filter['value'] : "";

                        $filter['clause'] = isset($filter['clause']) ? $filter['clause'] : "";

                        switch ($filter['element']) {
                                case 'comment_ID':
                                case 'comment_post_ID':
                                case 'comment_karma':
                                case 'user_id':
                                case 'comment_parent':
                                        $this->item_where .= "{$wpdb->comments}.{$filter['element']} " . $this->add_filter_rule($filter, true, false);

                                        break;
                                case 'comment_date':

                                        $this->item_where .= "{$wpdb->comments}.{$filter['element']} " . $this->add_filter_rule($filter, false, false);
                                        break;
                                case 'comment_author':
                                case 'comment_author_email':
                                case 'comment_author_url':
                                case 'comment_author_IP':
                                case 'comment_approved':
                                case 'comment_agent':
                                case 'comment_type':
                                case 'comment_content':
                                        $this->item_where .= "{$wpdb->users}.{$filter['element']} " . $this->add_filter_rule($filter, false, false);
                                        break;

                                default:

                                        if (strpos($filter['element'], "rch_cf_") === 0) {

                                                $meta_key = str_replace("cf_", "", $filter['element']);

                                                if ($filter['condition'] == 'is_empty') {
                                                        $this->item_join[] = " LEFT JOIN {$wpdb->commentmeta} ON ({$wpdb->commentmeta}.comment_id = {$wpdb->comments}.comment_ID AND {$wpdb->commentmeta}.meta_key = '$meta_key') ";
                                                        $this->item_where .= "{$wpdb->commentmeta}.meta_id " . $this->add_filter_rule($filter, false, false);
                                                } else {
                                                        $this->item_join[] = " INNER JOIN {$wpdb->commentmeta} ON ({$wpdb->commentmeta}.comment_id = {$wpdb->comments}.comment_ID) ";
                                                        $this->item_where .= "{$wpdb->commentmeta}.meta_key = '$meta_key' AND {$wpdb->commentmeta}.meta_value " . $this->add_filter_rule($filter, false, false);
                                                }
                                                unset($meta_key);
                                        }
                                        break;
                        }
                }

                unset($filter);
        }

        protected function process_export() {

                $query = array(
                        'orderby' => 'comment_ID',
                        'order'   => 'ASC',
                        'offset'  => isset($this->process_log['exported']) ? absint($this->process_log['exported']) : 0,
                        'number'  => isset($this->template_options['rch_records_per_iteration']) ? absint($this->template_options['rch_records_per_iteration']) : 50
                );

                if ($this->opration == "count") {
                        $query['count'] = true;

                        $query['fields'] = "ids";

                        $query['offset'] = 0;

                        $query['number'] = 0;
                } elseif ($this->opration == "ids") {
                        $query['fields'] = "ids";
                }

                $query = apply_filters('rch_pre_execute_comment_query', $query);

                add_action('comments_clauses', array($this, 'comments_clauses'), 10, 1);

                global $wp_version;

                if (version_compare($wp_version, '4.2.0', '>=')) {

                        $comment_result = new \WP_Comment_Query($query);

                        $comment_data = $comment_result->get_comments();

                        unset($comment_result);
                } else {

                        $comment_data = get_comments($query);
                }

                unset($query);

                remove_action('comments_clauses', array($this, 'comments_clauses'));

                if ($this->opration == "count" || $this->opration == "ids") {
                        return $comment_data;
                }

                if (!empty($comment_data)) {

                        $fields_data = (isset($this->template_options['fields_data']) && trim($this->template_options['fields_data']) != "") ? explode("~||~", wp_unslash(rch_sanitize_field($this->template_options['fields_data']))) : array();

                        $site_date_format = get_option('date_format');

                        foreach ($comment_data as $item) {

                                if (!empty($fields_data)) {

                                        foreach ($fields_data as $field) {

                                                if (empty($field)) {
                                                        continue;
                                                }
                                                $new_field = explode("|~|", $field);

                                                $field_label = isset($new_field[0]) ? rch_sanitize_field($new_field[0]) : "";

                                                $field_option = isset($new_field[1]) ? json_decode(rch_sanitize_field($new_field[1]), true) : "";

                                                $field_type = isset($field_option['type']) ? rch_sanitize_field($field_option['type']) : "";

                                                $is_php = isset($field_option['isPhp']) ? rch_sanitize_field($field_option['isPhp']) == 1 : false;

                                                $php_func = isset($field_option['phpFun']) ? rch_sanitize_field($field_option['phpFun']) : "";

                                                $date_type = isset($field_option['dateType']) ? rch_sanitize_field($field_option['dateType']) : "";

                                                $date_format = isset($field_option['dateFormat']) ? rch_sanitize_field($field_option['dateFormat']) : "";

                                                $field_name = strtolower(preg_replace("/[^a-zA-Z0-9]+/", "", $field_type . $field_label));

                                                if (isset($this->export_data[$field_name])) {

                                                        $base_name = $field_name;

                                                        $is_added = false;

                                                        $i = 0;

                                                        while (!$is_added) {

                                                                $element_name = $base_name . '_' . md5($i);

                                                                if (!isset($this->export_data[$element_name])) {

                                                                        $field_name = $element_name;

                                                                        $is_added = true;
                                                                }
                                                                unset($element_name);

                                                                $i++;
                                                        }
                                                        unset($base_name, $is_added, $i);
                                                }

                                                if ($this->process_log['exported'] == 0) {
                                                        $this->export_labels[$field_name] = $field_label;
                                                }

                                                switch ($field_type) {

                                                        case 'comment_ID':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_id', $this->apply_user_function($item->comment_ID, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_post_ID':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_post_id', $this->apply_user_function($item->comment_post_ID, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_post_title':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_post_title', $this->apply_user_function(get_the_title($item->comment_post_ID), $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_author':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_author', $this->apply_user_function($item->comment_author, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_author_email':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_author_email', $this->apply_user_function($item->comment_author_email, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_author_url':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_author_url', $this->apply_user_function($item->comment_author_url, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_author_IP':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_author_ip', $this->apply_user_function($item->comment_author_IP, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_karma':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_karma', $this->apply_user_function($item->comment_karma, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_content':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_content', $this->apply_user_function($item->comment_content, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_date':
                                                                $comment_date = $this->get_date_field($date_type, strtotime($item->comment_date), $date_format, $site_date_format);

                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_date', $this->apply_user_function($comment_date, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_approved':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_approved', $this->apply_user_function($item->comment_approved, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_agent':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_agent', $this->apply_user_function($item->comment_agent, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_type':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_type', $this->apply_user_function($item->comment_type, $is_php, $php_func), $item);
                                                                break;
                                                        case 'comment_parent':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_parent', $this->apply_user_function($item->comment_parent, $is_php, $php_func), $item);
                                                                break;
                                                        case 'user_id':
                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_user_id', $this->apply_user_function($item->user_id, $is_php, $php_func), $item);
                                                                break;

                                                        case 'rch_cf':

                                                                $meta_value = "";

                                                                $meta_key = isset($field_option['metaKey']) ? rch_sanitize_field($field_option['metaKey']) : "";

                                                                if ($meta_key != "") {

                                                                        $metas = get_comment_meta($item->comment_ID, $meta_key);

                                                                        if (!empty($metas) && is_array($metas)) {

                                                                                foreach ($metas as $_key => $_value) {
                                                                                        if (empty($meta_value)) {
                                                                                                $meta_value = maybe_serialize($_value);
                                                                                        } else {
                                                                                                $meta_value = $meta_value . maybe_serialize($_value);
                                                                                        }
                                                                                }
                                                                        }
                                                                        unset($metas);
                                                                }

                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_meta', $this->apply_user_function($meta_value, $is_php, $php_func), $meta_key, $item);

                                                                unset($meta_value, $meta_key);

                                                                break;

                                                        default:
                                                                $defaults = apply_filters('rch_export_pre_comment_default_field', "", $field_name, $field_option, $item);

                                                                $this->export_data[$field_name] = apply_filters('rch_export_comment_default_field', $this->apply_user_function($defaults, $is_php, $php_func), $item);

                                                                unset($defaults);

                                                                break;
                                                }

                                                unset($new_field, $field_label, $field_option, $field_type, $is_php, $php_func, $date_type, $date_format);
                                        }

                                        $this->process_data();
                                }
                        }

                        unset($fields_data, $site_date_format);
                }

                unset($comment_data);
        }

        public function comments_clauses($data = array()) {

                if (!empty($this->item_join)) {
                        $data['join'] .= implode(' ', array_unique($this->item_join));
                }

                if ($this->item_where != "") {
                        $data['where'] .= " AND ( $this->item_where )";
                }

                return $data;
        }

}
