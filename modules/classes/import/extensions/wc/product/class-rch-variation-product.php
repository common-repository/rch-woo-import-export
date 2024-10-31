<?php

namespace rch\import\wc\product\variation;

if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (file_exists(RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-product-base.php')) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-product-base.php');
}

class RCH_Variation_Product extends \rch\import\wc\product\base\RCH_Product_Base {

        protected $product_type = 'product_variation';
        private $parent_id;

        public function __construct($rch_import_option = array(), $rch_import_record = array(), $item_id = "", $item = null, $is_new_item = false, $parent_id = 0) {

                $this->parent_id = $parent_id;

                parent::__construct($rch_import_option, $rch_import_record, $item_id, $item, $is_new_item);
        }

        public function import_data() {

                parent::import_data();

                $set_default = rch_sanitize_field($this->get_field_value('rch_item_first_variation_as_default', true));

                if (intval($set_default) == 1) {
                        $this->set_default_attribute();
                }

                unset($set_default);
        }

        private function set_default_attribute() {

                $is_set_default_attr = false;

                $product = \wc_get_product($this->parent_id);

                $default_attributes = $product->get_default_attributes("edit");

                if (!empty($default_attributes)) {
                        return true;
                }

                $product_variation = $product->get_children();

                if (!empty($product_variation)) {

                        foreach ($product_variation as $child_id) {

                                $child = \wc_get_product($child_id);

                                $product->set_default_attributes($child->get_attributes());

                                $is_set_default_attr = true;

                                unset($child);

                                break;
                        }
                }

                if (!$is_set_default_attr) {

                        $attributes = wc_list_pluck(array_filter($product->get_attributes(), 'wc_attributes_array_filter_variation'), 'get_slugs');

                        if (!empty($attributes)) {

                                $possible_attributes = array_reverse(wc_array_cartesian($attributes));

                                foreach ($possible_attributes as $possible_attribute) {

                                        $product->set_default_attributes($possible_attribute);

                                        $is_set_default_attr = true;

                                        break;
                                }
                                unset($possible_attributes);
                        }
                        unset($attributes);
                }

                if ($is_set_default_attr) {
                        $product->save();
                }
                unset($is_set_default_attr);
        }

        protected function prepare_properties() {

                $this->prepare_variation_status();

                $this->prepare_general_properties();

                $this->prepare_inventory_properties();

                $this->prepare_shipping_properties();

                $this->prepare_attributes_properties();
        }

        public function prepare_link_all_variation_properties() {

                $this->prepare_variation_status();

                $this->prepare_general_properties();

                $this->prepare_inventory_properties();

                $this->prepare_shipping_properties();

                $this->product->set_props($this->product_properties);

                $this->save();
        }

        private function prepare_variation_status() {

                $status = strtolower(trim(rch_sanitize_field($this->get_field_value('rch_item_variation_enable', false, true)))) === "yes" ? 'publish' : 'private';

                $this->get_product()->set_status($status);
        }

        private function prepare_attributes_properties() {

                $attributes = $this->get_attr_data();

                $parent_attributes = get_post_meta($this->parent_id, "_product_attributes", true);

                if (empty($parent_attributes) || !is_array($parent_attributes)) {
                        $parent_attributes = array();
                }

                $is_update_parent_attribute = false;

                if (isset($attributes['attribute_names']) && !empty($attributes['attribute_names'])) {

                        foreach ($attributes['attribute_names'] as $key => $attribute) {

                                $value = isset($attributes['attribute_values'][$key]) ? $attributes['attribute_values'][$key] : array();

                                if (empty($value) || !isset($value[0])) {
                                        continue;
                                }

                                $term_id = $value[0];

                                $term = get_term_by('id', $term_id, $attribute);

                                $is_taxonomy = 0;

                                if (is_object($term) && isset($term->slug)) {

                                        $is_taxonomy = 1;

                                        $term_slug = $term->slug;

                                        $post_term_ids = wp_get_post_terms($this->parent_id, $attribute, array('fields' => 'ids'));

                                        if (is_array($post_term_ids) && !in_array($term_id, $post_term_ids)) {
                                                wp_set_post_terms($this->parent_id, array($term_id), $attribute, true);
                                        }

                                        unset($post_term_ids);
                                } else {
                                        $term_slug = $value[0];
                                }

                                if (!isset($parent_attributes[$attribute]) || empty($parent_attributes[$attribute])) {

                                        $is_update_parent_attribute = true;

                                        $parent_attributes[$attribute] = array(
                                                "name"         => $attribute,
                                                "value"        => $value,
                                                "is_visible"   => isset($attributes['attribute_visibility']) && isset($attributes['attribute_visibility'][$key]) ? $attributes['attribute_visibility'][$key] : "",
                                                "is_taxonomy"  => $is_taxonomy,
                                                "is_variation" => 1,
                                                "position"     => isset($attributes['attribute_position']) && isset($attributes['attribute_position'][$key]) ? $attributes['attribute_position'][$key] : ""
                                        );
                                }

                                update_post_meta($this->item_id, 'attribute_' . $attribute, $term_slug);

                                unset($term, $value, $term_id);
                        }
                }

                if ($is_update_parent_attribute) {
                        update_post_meta($this->parent_id, "_product_attributes", $parent_attributes);
                }

                unset($attributes);
        }

        public function is_variation_exist() {

                return $this->get_variation_by_attribute($this->parent_id);
        }

        private function get_variation_by_attribute($parent_id = 0) {

                $attr_names = rch_sanitize_field($this->get_field_value('rch_product_attr_name'));

                if (!empty($attr_names)) {

                        $attr_values = rch_sanitize_field($this->get_field_value('rch_product_attr_value'));

                        global $wpdb;

                        $join = "";

                        $where = " where ";

                        foreach ($attr_names as $key => $attribute) {

                                $value = isset($attr_values[$key]) ? $attr_values[$key] : "";

                                if (empty($value)) {
                                        continue;
                                }

                                $attribute = wc_attribute_taxonomy_name($attribute);

                                $join .= " JOIN {$wpdb->prefix}postmeta as `pm_{$attribute}` ON p.ID = `pm_{$attribute}`.`post_id`";

                                $where .= " `pm_{$attribute}`.`meta_key` = 'attribute_{$attribute}' AND `pm_{$attribute}`.`meta_value` LIKE '{$value}' AND ";

                                unset($value);
                        }

                        $query = 'SELECT p.ID FROM ' . $wpdb->prefix . 'posts as p ' . $join . $where . ' p.post_parent =' . $parent_id;

                        unset($join, $where, $is_include);

                        return $wpdb->get_var($query);
                }

                return false;
        }

        public function get_variation_id() {
                return $this->variation_id;
        }

        public function __destruct() {

                parent::__destruct();

                foreach ($this as $key => $value) {
                        unset($this->$key);
                }
        }

}
