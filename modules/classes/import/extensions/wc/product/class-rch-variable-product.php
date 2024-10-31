<?php

namespace rch\import\wc\product\variable;

use rch\import\wc\product\variation\RCH_Variation_Product;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (file_exists(RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-product-base.php')) {

    require_once(RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-product-base.php');
}

class RCH_Variable_Product extends \rch\import\wc\product\base\RCH_Product_Base {

    protected $product_type = 'variable';

    public function __construct($rch_import_option = array(), $rch_import_record = array(), $item_id = "", $item = null, $is_new_item = false) {

        parent::__construct($rch_import_option, $rch_import_record, $item_id, $item, $is_new_item);
    }

    public function import_data() {

        parent::import_data();

        $variation_method = rch_sanitize_field($this->get_field_value('rch_item_variation_import_method', true));

        if ($variation_method == "attributes") {
            $this->link_all_variations();
        } else if ($variation_method == "match_group_field" || $variation_method == "match_title_field_no_parent") {
            $this->create_child_based_on_parent();
        }

        $set_default = rch_sanitize_field($this->get_field_value('rch_item_first_variation_as_default', true));

        if (intval($set_default) == 1) {
            $this->set_default_attribute();
        }

        unset($variation_method, $set_default);
    }

    private function set_default_attribute() {

        $is_set_default_attr = false;

        $product = \wc_get_product($this->item_id);

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

    private function link_all_variations() {

        $attributes = wc_list_pluck(array_filter($this->product->get_attributes(), 'wc_attributes_array_filter_variation'), 'get_slugs');

        if (!empty($attributes)) {

            $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-variation-product.php';

            if (file_exists($fileName)) {

                require_once($fileName);
            }

            $existing_attributes = array();

            if (!$this->is_new_item) {

                $existing_variations = array_map('wc_get_product', $this->product->get_children());

                if (!empty($existing_variations)) {
                    foreach ($existing_variations as $existing_variation) {
                        $existing_attributes[] = $existing_variation->get_attributes();
                    }
                }
            }

            $possible_attributes = array_reverse(wc_array_cartesian($attributes));

            foreach ($possible_attributes as $possible_attribute) {

                if (in_array($possible_attribute, $existing_attributes)) {
                    continue;
                }

                $variation = new \WC_Product_Variation();

                $variation->set_parent_id($this->item_id);

                $variation->set_attributes($possible_attribute);

                $variation_id = $variation->save();

                do_action('product_variation_linked', $variation_id);

                $item = \get_post($variation_id);

                $variation_data = new \rch\import\wc\product\variation\RCH_Variation_Product($this->rch_import_option, $this->rch_import_record, $variation_id, $item, true, $this->item_id);

                $variation_product = \wc_get_product($variation_id);

                $variation_data->set_product($variation_product);

                $variation_data->prepare_link_all_variation_properties();

                unset($variation, $variation_data, $variation_id, $item);
            }

            unset($possible_attributes, $existing_attributes);
        }

        unset($attributes);

        $data_store = $this->product->get_data_store();

        $data_store->sort_all_product_variations($this->item_id);
    }

    private function create_child_based_on_parent() {

        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-variation-product.php';

        if (file_exists($fileName)) {

            require_once($fileName);
        }

        $variation_id = false;

        if (!$this->is_new_item) {

            $_variation = new \rch\import\wc\product\variation\RCH_Variation_Product($this->rch_import_option, $this->rch_import_record, 0, null, false, $this->item_id);

            $_variation_id = $_variation->is_variation_exist();

            if ($_variation_id !== false && intval($_variation_id) > 0) {

                $variation_id = $_variation_id;
            }
        }

        if ($variation_id === false) {

            $product = wc_get_product($this->item_id);

            $variation_post = array(
                'post_title' => $product->get_title(),
                'post_name' => 'product-' . $this->item_id . '-variation',
                'post_status' => 'publish',
                'post_parent' => $this->item_id,
                'post_type' => 'product_variation',
                'guid' => $product->get_permalink()
            );

            $variation_id = \wp_insert_post($variation_post);
        }

        $item = get_post($variation_id);

        $variation = new \rch\import\wc\product\variation\RCH_Variation_Product($this->rch_import_option, $this->rch_import_record, $variation_id, $item, true, $this->item_id);

        $variation_product = \wc_get_product($variation_id);

        $variation->set_product($variation_product);

        $variation->import_data();

        unset($fileName, $product, $variation_post, $variation_id, $item, $variation, $variation_product);
    }

    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
