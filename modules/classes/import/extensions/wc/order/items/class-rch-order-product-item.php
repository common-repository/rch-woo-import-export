<?php

namespace rch\import\wc\order\item;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}
if (file_exists(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php')) {

    require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php');
}

class RCH_Order_Product_Item extends \rch\import\base\RCH_Import_Base {

    /**
     * @var \WC_Order
     */
    private $order;

    public function __construct($rch_import_option = array(), $rch_import_record = array(), $item_id = 0, $is_new_item = true, &$addon_error = false, &$addon_log = array(), $order = null) {

        $this->rch_import_option = $rch_import_option;

        $this->rch_import_record = $rch_import_record;

        $this->item_id = $item_id;

        $this->order = $order;

        $this->is_new_item = $is_new_item;

        $this->addon_error = &$addon_error;

        $this->addon_log = &$addon_log;

        $this->prepare_line_item();
    }

    private function prepare_line_item() {

        $delimiter = rch_sanitize_field($this->get_field_value('rch_item_order_item_product_delim'));

        $product_name = rch_sanitize_field($this->get_field_value('rch_item_order_item_product_name'));

        if (!$this->is_new_item) {
            $current_product = $this->order->get_items();
        } else {
            $current_product = array();
        }

        if (!empty($product_name)) {

            if (empty($delimiter)) {
                $delimiter = "|";
            }

            $product_name = explode($delimiter, $product_name);

            $product_price = rch_sanitize_field($this->get_field_value('rch_item_order_item_product_price'));

            if (!empty($product_price)) {
                $product_price = explode($delimiter, $product_price);
            }

            $product_quantity = rch_sanitize_field($this->get_field_value('rch_item_order_item_product_quantity'));

            if (!empty($product_quantity)) {
                $product_quantity = explode($delimiter, $product_quantity);
            }

            $product_sku = rch_sanitize_field($this->get_field_value('rch_item_order_item_product_sku'));

            if (!empty($product_sku)) {
                $product_sku = explode($delimiter, $product_sku);
            }

            $meta_name = rch_sanitize_field($this->get_field_value('rch_item_order_item_product_meta_name'));

            $meta_value = rch_sanitize_field($this->get_field_value('rch_item_order_item_product_meta_value'));

            $product_meta = array();

            if (count($meta_value) > 0) {

                for ($i = 0; $i < count($meta_value); $i++) {

                    $_values = isset($meta_value[$i]) && !empty($meta_value[$i]) ? explode($delimiter, $meta_value[$i]) : array();

                    $_name = isset($meta_value[$i]) && !empty($meta_value[$i]) ? $meta_value[$i] : "";

                    if (!empty($_names) && !empty($_values)) {

                        foreach ($_values as $_key => $_value) {
                            $product_meta[$key][$_name] = $_value;
                        }
                    }
                }
            }

            foreach ($product_name as $key => $name) {

                $sku = isset($product_sku[$key]) ? $product_sku[$key] : "";

                $quantity = isset($product_quantity[$key]) ? absint($product_quantity[$key]) : 0;

                $price = isset($product_price[$key]) ? $product_price[$key] : 0;

                $subtotal = floatval($price) * absint($quantity);

                $product_id = 0;

                $product = false;

                $item_id = false;

                if (!empty($sku)) {
                    $product_id = $this->get_product_by_meta("_sku", $sku);
                }

                if (absint($product_id) > 0) {

                    if (!empty($current_product)) {

                        foreach ($current_product as $order_item_id => $order_item) {

                            $item_product_id = $order_item['product_id'] ? $order_item['product_id'] : 0;

                            if ($item_product_id == $product_id) {

                                $item_id = $order_item_id;

                                break;
                            }

                            unset($item_product_id);
                        }
                    }

                    $product = wc_get_product($product_id);
                }

                $line_item = array(
                    'name' => $name,
                    'tax_class' => "",
                    'product_id' => $product && $product->is_type('variation') ? $product->get_parent_id() : $product_id,
                    'variation_id' => $product && $product->is_type('variation') ? $product->get_id() : 0,
                    'variation' => $product && $product->is_type('variation') ? $product->get_attributes() : array(),
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                    'quantity' => $quantity,
                    'meta' => $product_meta,
                );

                if ($item_id) {
                    $this->update_product($item_id, $line_item, $product);
                } else {
                    $this->add_product($line_item, $product);
                }
            }
        }
    }

    private function update_product($item_id = 0, $line_item = array(), $product) {

        $item = new \WC_Order_Item_Product($item_id);

        if (isset($line_item['quantity'])) {
            $item->set_quantity($line_item['quantity']);
        }
        if (isset($line_item['total'])) {
            $item->set_total(floatval($line_item['total']));
        }
        if (isset($line_item['total_tax'])) {
            $item->set_total_tax(floatval($line_item['total_tax']));
        }
        if (isset($line_item['subtotal'])) {
            $item->set_subtotal(floatval($line_item['subtotal']));
        }
        if (isset($line_item['subtotal_tax'])) {
            $item->set_subtotal_tax(floatval($line_item['subtotal_tax']));
        }

        $item->save();
    }

    private function add_product($line_item = array(), $product) {

        if ($product === false) {
            return $this->manually_add_product($line_item);
        }

        $item = new \WC_Order_Item_Product();

        $item->set_props($line_item);

        $item->set_backorder_meta();

        $item->set_order_id($this->item_id);

        $item->save();

        $this->order->add_item($item);
    }

    private function manually_add_product($item = array()) {

        $item_id = wc_add_order_item($this->item_id, array(
            'order_item_name' => $item['name'],
            'order_item_type' => 'line_item'
        ));

        $item_qty = isset($item['quantity']) ? absint($item['quantity']) : 0;

        $tax_class = isset($item['tax_class']) ? $item['tax_class'] : "";

        $subtotal = isset($item['subtotal']) ? $item['subtotal'] : 0;

        $subtotal_tax = isset($item['subtotal_tax']) ? $item['subtotal_tax'] : 0;

        $total = isset($item['total']) ? $item['total'] : 0;

        $total_tax = isset($item['total_tax']) ? $item['total_tax'] : 0;

        $meta = isset($item['meta']) ? $item['meta'] : array();

        wc_add_order_item_meta($item_id, '_qty', wc_stock_amount($item_qty));

        wc_add_order_item_meta($item_id, '_tax_class', '');

        wc_add_order_item_meta($item_id, '_line_subtotal', wc_format_decimal($subtotal));

        wc_add_order_item_meta($item_id, '_line_total', wc_format_decimal($total));

        wc_add_order_item_meta($item_id, '_line_subtotal_tax', wc_format_decimal($subtotal_tax));

        wc_add_order_item_meta($item_id, '_line_tax', wc_format_decimal($total_tax));

        wc_add_order_item_meta($item_id, '_line_tax_data', array(
            'total' => $total_tax,
            'subtotal' => array()
        ));

        if (!empty($meta)) {
            foreach ($meta as $key => $meta_name) {
                wc_add_order_item_meta($item_id, $meta_name, isset($productItem['meta_value'][$key]) ? $productItem['meta_value'][$key] : '');
            }
        }

        unset($item_qty, $tax_class, $subtotal, $subtotal_tax, $total, $total_tax, $meta, $item_id);
    }

    private function get_product_by_meta($meta_key = "_sku", $meta_val = "") {

        if (empty($meta_val)) {
            return 0;
        }
        global $wpdb;

        $product_id = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT posts.ID
				FROM $wpdb->posts AS posts
				LEFT JOIN $wpdb->postmeta AS postmeta ON ( posts.ID = postmeta.post_id )
				WHERE posts.post_type IN ( 'product', 'product_variation' )
					AND posts.post_status != 'trash'
					AND postmeta.meta_key = %s
					AND postmeta.meta_value = %s
				LIMIT 1",
                        $meta_key,
                        $meta_val
                )
        );

        unset($meta_key, $meta_val);

        return $product_id;
    }

    public function __destruct() {

        parent::__destruct();

        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
