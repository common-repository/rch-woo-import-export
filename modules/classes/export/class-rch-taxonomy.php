<?php

namespace rch\export\taxonomy;

use rch\export\media;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (file_exists(RCH_EXPORT_CLASSES_DIR . '/class-rch-export-engine.php')) {

    require_once(RCH_EXPORT_CLASSES_DIR . '/class-rch-export-engine.php');
}

class RCH_Taxonomy extends \rch\export\engine\RCH_Export_Engine {

    private $item_where = "";
    private $item_join = array();

    protected function get_fields() {

        $standard_fields = array(
            'title' => __("Standard", 'rch-woo-import-export'),
            "isDefault" => true,
            'data' => array(
                array(
                    'name' => 'Term ID',
                    'type' => 'term_id',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Term Name',
                    'type' => 'term_name',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Term Slug',
                    'type' => 'term_slug',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Description',
                    'type' => 'term_description',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Parent ID',
                    'type' => 'term_parent_id',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Parent Name',
                    'type' => 'term_parent_name',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Parent Slug',
                    'type' => 'term_parent_slug',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Count',
                    'type' => 'term_posts_count',
                    'isDefault' => true
                )
            )
        );

        $image_fields = array(
            "title" => "Images",
            "isFiltered" => false,
            "data" => array(
                array(
                    'name' => 'Image URL',
                    'type' => 'image_url',
                ),
                array(
                    'name' => 'Images Filename',
                    'type' => 'image_filename',
                ),
                array(
                    'name' => 'Images Path',
                    'type' => 'image_path',
                ),
                array(
                    'name' => 'Images ID',
                    'type' => 'image_id',
                ),
                array(
                    'name' => 'Images Title',
                    'type' => 'image_title',
                ),
                array(
                    'name' => 'Images Caption',
                    'type' => 'image_caption',
                ),
                array(
                    'name' => 'Images Description',
                    'type' => 'image_description',
                ),
                array(
                    'name' => 'Images Alt Text',
                    'type' => 'image_alt',
                ),
            )
        );

        $export_fields = array(
            "standard" => apply_filters('rch_taxonomy_standard_fields', $standard_fields),
            "meta" => apply_filters('rch_taxonomy_meta_fields', $this->get_meta_keys()),
            "image" => apply_filters('rch_taxonomy_image_fields', $image_fields)
        );

        $addon_class = apply_filters('rch_prepare_taxonomy_fields', array(), $this->export_type);

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
            "data" => $meta_data
        );


        unset($standard_fields, $image_fields, $addon_class, $meta_data);

        return apply_filters("rch_export_fields", $export_fields, $this->export_type);
    }

    private function get_meta_keys() {

        $this->opration = "ids";

        $this->process_log = array(
            'exported' => 0,
            'total' => 0,
        );

        $this->manage_rules();

        $taxonomies = $this->process_export();

        $meta = array();

        if (!empty($taxonomies)) {

            foreach ($taxonomies as $taxonomy) {

                $term_meta = get_term_meta($taxonomy, '');

                if (!empty($term_meta)) {

                    foreach ($term_meta as $_key => $_value) {

                        if (!in_array($_key, $meta)) {

                            $meta[] = $_key;
                        }
                    }
                }
                unset($term_meta);
            }
        }

        unset($taxonomies);

        return $meta;
    }

    protected function parse_rule($filter = array()) {

        if (isset($filter['element'])) {

            $filter['condition'] = isset($filter['condition']) ? $filter['condition'] : "";

            $filter['value'] = isset($filter['value']) ? $filter['value'] : "";

            $filter['clause'] = isset($filter['clause']) ? $filter['clause'] : "";

            switch ($filter['element']) {
                case 'term_id':
                case 'term_group':
                    $this->item_where .= "t." . $filter['element'] . " " . $this->add_filter_rule($filter, true, false);
                    break;
                case 'term_name':
                case 'term_slug':
                    $this->item_where .= "t." . str_replace("term_", "", $filter['element']) . " " . $this->add_filter_rule($filter, false, false);
                    break;
                case 'term_parent_id':
                    switch ($filter['condition']) {
                        case 'is_empty':
                            $filter['value'] = 0;
                            $filter['condition'] = 'equals';
                            break;
                        case 'is_not_empty':
                            $filter['value'] = 0;
                            $filter['condition'] = 'not_equals';
                            break;
                    }
                    $this->item_where .= "tt.parent " . $this->add_filter_rule($filter, false, false);
                    break;
                case 'term_parent_name':

                    switch ($filter['condition']) {

                        case 'contains':

                            $result = new \WP_Term_Query(array('taxonomy' => $options['rch_taxonomy_type'], 'name__like' => $filter['value'], 'hide_empty' => false));

                            $parent_terms = $result->get_terms();

                            unset($result);

                            if ($parent_terms) {

                                $parent_term_ids = array();

                                foreach ($parent_terms as $p_term) {
                                    $parent_term_ids[] = $p_term->term_id;
                                }

                                $parent_term_ids_str = implode(",", $parent_term_ids);

                                $this->item_where .= "tt.parent IN ($parent_term_ids_str)";

                                unset($parent_term_ids, $parent_term_ids_str);
                            }

                            unset($parent_terms);

                            break;
                        case 'not_contains':

                            $result = new \WP_Term_Query(array('taxonomy' => $options['rch_taxonomy_type'], 'name__like' => $filter['value'], 'hide_empty' => false));

                            unset($result);

                            $parent_terms = $result->get_terms();

                            if ($parent_terms) {

                                $parent_term_ids = array();

                                foreach ($parent_terms as $p_term) {

                                    $parent_term_ids[] = $p_term->term_id;
                                }

                                $parent_term_ids_str = implode(",", $parent_term_ids);

                                $this->item_where .= "tt.parent NOT IN ($parent_term_ids_str)";

                                unset($parent_term_ids, $parent_term_ids_str);
                            }

                            unset($parent_terms);

                            break;
                        default:

                            switch ($filter['condition']) {
                                case 'is_empty':
                                    $filter['value'] = 0;
                                    $filter['condition'] = 'equals';
                                    break;
                                case 'is_not_empty':
                                    $filter['value'] = 0;
                                    $filter['condition'] = 'not_equals';
                                    break;
                                default:
                                    $parent_term = get_term_by('name', $filter['value'], isset($this->template_options['rch_taxonomy_type']) ? $this->template_options['rch_taxonomy_type'] : "");

                                    if ($parent_term && isset($parent_term->term_id)) {
                                        $filter['value'] = $parent_term->term_id;
                                    }

                                    unset($parent_term);

                                    break;
                            }

                            $this->item_where .= "tt.parent " . $this->add_filter_rule($filter, false, false);
                            break;
                    }
                    break;
                case 'term_parent_slug':

                    switch ($filter['condition']) {
                        case 'is_empty':
                            $filter['value'] = 0;
                            $filter['condition'] = 'equals';
                            break;
                        case 'is_not_empty':
                            $filter['value'] = 0;
                            $filter['condition'] = 'not_equals';
                            break;
                        default:
                            $parent_term = get_term_by('slug', $filter['value'], isset($this->template_options['rch_taxonomy_type']) ? $this->template_options['rch_taxonomy_type'] : "");

                            if ($parent_term && isset($parent_term->term_id)) {

                                $filter['value'] = $parent_term->term_id;
                            }

                            unset($parent_term);

                            break;
                    }

                    $this->item_where .= "tt.parent " . $this->add_filter_rule($filter, false, false);

                    break;

                case 'term_posts_count':
                    $this->item_where .= "tt.count " . $this->add_filter_rule($filter, false, false);
                    break;
                default:
                    if (strpos($filter['element'], "rch_cf_") === 0) {

                        $meta_key = str_replace("rch_cf_", "", $filter['element']);

                        global $wpdb;

                        if ($filter['condition'] == 'is_empty') {
                            $this->item_join [] = " LEFT JOIN {$wpdb->termmeta} ON ({$wpdb->termmeta}.term_id = t.term_id AND {$wpdb->termmeta}.meta_key = '$meta_key') ";
                            $this->item_where .= "{$wpdb->termmeta}.meta_id " . $this->add_filter_rule($filter, false, false);
                        } else {
                            $this->item_join [] = " INNER JOIN {$wpdb->termmeta} ON ({$wpdb->termmeta}.term_id = t.term_id) ";
                            $this->item_where .= "{$wpdb->termmeta}.meta_key = '$meta_key' AND {$wpdb->termmeta}.meta_value " . $this->add_filter_rule($filter, false, false);
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
            'taxonomy' => isset($this->template_options['rch_taxonomy_type']) ? $this->template_options['rch_taxonomy_type'] : "",
            'orderby' => 'id',
            'order' => 'ASC',
            'hide_empty' => false,
            'offset' => isset($this->process_log['exported']) ? absint($this->process_log['exported']) : 0,
            'fields' => "ids",
            'number' => isset($this->template_options['rch_records_per_iteration']) ? absint($this->template_options['rch_records_per_iteration']) : 50
        );

        if ($this->opration == "count") {

            $query['count'] = true;

            $query['offset'] = 0;

            $query['number'] = 0;
        }

        $query = apply_filters('rch_pre_execute_taxonomy_query', $query);

        add_filter('terms_clauses', array($this, 'terms_clauses'), 10, 3);

        $taxonomy_result = new \WP_Term_Query($query);

        unset($query);

        if (!empty($taxonomy_result->get_terms())) {
            $taxonomy_data = $taxonomy_result->get_terms();
        } else {
            $taxonomy_data = array();
        }

        remove_filter('terms_clauses', array($this, 'terms_clauses'));

        unset($taxonomy_result);

        if ($this->opration == "count") {
            return count($taxonomy_data);
        } elseif ($this->opration == "ids") {
            return $taxonomy_data;
        }

        $this->process_items($taxonomy_data);

        unset($taxonomy_data);

        return true;
    }

    protected function process_items($taxonomy_data = array()) {

        if (!empty($taxonomy_data)) {

            $fields_data = (isset($this->template_options['fields_data']) && trim($this->template_options['fields_data']) != "") ? explode("~||~", wp_unslash(rch_sanitize_field($this->template_options['fields_data']))) : array();

            $media = array();

            $taxonomy = isset($this->template_options['rch_taxonomy_type']) ? $this->template_options['rch_taxonomy_type'] : "";
            
            $site_date_format = get_option('date_format');

            foreach ($taxonomy_data as $item_term_id) {

                $item = get_term_by("id", $item_term_id, $taxonomy);

                if ($item && !empty($fields_data)) {

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

                            $element_name = "";

                            while (!$is_added) {

                                $element_name = $base_name . '_' . md5($i);

                                if (!isset($this->export_data[$element_name])) {

                                    $field_name = $element_name;

                                    $is_added = true;
                                }


                                $i++;
                            }
                            unset($element_name, $base_name, $i, $is_added);
                        }

                        if ($this->process_log['exported'] == 0) {
                            $this->export_labels[$field_name] = $field_label;
                        }
                        if ($this->process_log['exported'] == 0) {

                            $this->export_labels[$field_name] = $field_label;

                            if ($this->addons && is_array($this->addons)) {

                                foreach ($this->addons as $addon) {

                                    if (method_exists($addon, "change_export_labels")) {

                                        $addon->change_export_labels($this->export_labels, $field_type, $field_name, $field_label, $field_option);
                                    }
                                }
                            }
                        }
                        if ($this->addons && is_array($this->addons)) {

                            foreach ($this->addons as $addon) {

                                if (method_exists($addon, "pre_process_data")) {

                                    $addon->pre_process_data($this->export_labels, $field_type, $field_name, $field_label);
                                }
                            }
                        }

                        switch ($field_type) {

                            case 'term_id':
                                $this->export_data[$field_name] = apply_filters('rch_export_term_id', $this->apply_user_function($item->term_id, $is_php, $php_func), $item);
                                break;
                            case 'term_name':
                                $this->export_data[$field_name] = apply_filters('rch_export_term_name', $this->apply_user_function($item->name, $is_php, $php_func), $item);
                                break;

                            case 'term_slug':
                                $this->export_data[$field_name] = apply_filters('rch_export_term_slug', $this->apply_user_function($item->slug, $is_php, $php_func), $item);
                                break;

                            case 'term_description':
                                $this->export_data[$field_name] = apply_filters('rch_export_term_description', $this->apply_user_function($item->description, $is_php, $php_func), $item);
                                break;

                            case 'term_parent_id':
                                $this->export_data[$field_name] = apply_filters('rch_export_term_parent', $this->apply_user_function($item->parent, $is_php, $php_func), $item);
                                break;

                            case 'term_parent_name':

                                $term_parent_name = '';

                                if ($item->parent) {

                                    $parent_term = get_term($item->parent, $item->taxonomy);

                                    if ($parent_term) {

                                        $term_parent_name = $parent_term->name;
                                    }
                                    unset($parent_term);
                                }

                                $this->export_data[$field_name] = apply_filters('rch_export_term_parent_name', $this->apply_user_function($term_parent_name, $is_php, $php_func), $item);

                                unset($term_parent_name);

                                break;

                            case 'term_parent_slug':

                                $term_parent_slug = '';

                                if ($item->parent) {

                                    $parent_term = get_term($item->parent, $item->taxonomy);

                                    if ($parent_term) {

                                        $term_parent_slug = $parent_term->slug;
                                    }
                                    unset($parent_term);
                                }

                                $this->export_data[$field_name] = apply_filters('rch_export_term_parent_slug', $this->apply_user_function($term_parent_slug, $is_php, $php_func), $item);

                                unset($term_parent_slug);

                                break;

                            case 'term_posts_count':

                                $this->export_data[$field_name] = apply_filters('rch_export_term_count', $this->apply_user_function($item->count, $is_php, $php_func), $item);

                                break;

                            case 'rch_cf':

                                $meta_value = "";

                                $meta_key = isset($field_option['metaKey']) ? rch_sanitize_field($field_option['metaKey']) : "";

                                if ($meta_key != "") {

                                    $term_metas = get_term_meta($item->term_id, $meta_key);

                                    if (!empty($term_metas) && is_array($term_metas)) {

                                        foreach ($term_metas as $key => $_value) {
                                            if (empty($meta_value)) {
                                                $meta_value = maybe_serialize($_value);
                                            } else {
                                                $meta_value = $meta_value . maybe_serialize($_value);
                                            }
                                        }
                                    }
                                    unset($term_metas);
                                }

                                $this->export_data[$field_name] = apply_filters('rch_export_term_meta', $this->apply_user_function($meta_value, $is_php, $php_func), $meta_key, $item);

                                unset($meta_value, $meta_key);
                                break;
                            case 'media':
                            case 'image_id':
                            case 'image_url':
                            case 'image_filename':
                            case 'image_path':
                            case 'image_title':
                            case 'image_caption':
                            case 'image_description':
                            case 'image_alt':


                                if (file_exists(RCH_EXPORT_CLASSES_DIR . '/class-rch-media.php')) {

                                    require_once(RCH_EXPORT_CLASSES_DIR . '/class-rch-media.php');
                                }

                                $rch_media = new \rch\export\media\RCH_Media();

                                if (!(isset($media[$item->term_id]))) {

                                    $media[$item->term_id] = $rch_media->get_media($item->term_id);
                                }

                                $image_media = "";

                                if (isset($media[$item->term_id])) {

                                    $image_data = $rch_media->get_images($item->term_id, $media[$item->term_id], $field_type, 'texonomy');

                                    if (!empty($image_data)) {
                                        $image_media = implode("||", $image_data);
                                    }

                                    unset($image_data);
                                }

                                $this->export_data[$field_name] = apply_filters('rch_export_texonomy_image_' . $field_type, $this->apply_user_function($image_media, $is_php, $php_func), $item);

                                unset($rch_media, $image_media);

                                break;

                            default:
                                $defaults = apply_filters('rch_export_pre_term_default_field', "", $field_name, $field_option, $item);

                                $this->export_data[$field_name] = apply_filters('rch_export_term_default_field', $this->apply_user_function($defaults, $is_php, $php_func), $item);

                                unset($defaults);
                                break;
                        }
                        if ($this->addons && is_array($this->addons)) {
                            foreach ($this->addons as $addon) {
                                if (method_exists($addon, "process_addon_data")) {
                                    $addon->process_addon_data($this->export_data, $field_type, $field_name, $field_option, $item, $site_date_format);
                                }
                            }
                        }

                        unset($new_field, $field_label, $field_option, $field_type, $is_php, $php_func, $date_type, $date_format, $field_name);
                    }
                    if ($this->addons && is_array($this->addons)) {

                        foreach ($this->addons as $addon) {

                            if (method_exists($addon, "finalyze_export_process")) {

                                $addon->finalyze_export_process($this->export_data, $this->has_multiple_rows);
                            }
                        }
                    }

                    $this->process_data();
                }
            }

            unset($fields_data, $media);
        }
    }

    public function terms_clauses($data = array()) {

        if (!empty($this->item_join)) {
            $data['join'] .= implode(' ', array_unique($this->item_join));
        }

        if ($this->item_where != "") {
            $data['where'] .= " AND ( $this->item_where )";
        }

        return $data;
    }

}
