<?php

namespace rch\import\wc\order\item;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}
if (file_exists(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php')) {

    require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php');
}

class RCH_Order_Coupon_Item extends \rch\import\base\RCH_Import_Base {

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

        $this->prepare_order_coupon();
    }

    private function prepare_order_coupon() {

        $total_discount_amount = 0;

        $delimiter = rch_sanitize_field($this->get_field_value('rch_item_order_item_coupon_delim'));

        $item_coupon = rch_sanitize_field($this->get_field_value('rch_item_order_item_coupon'));

        if (!$this->is_new_item) {
            $current_coupon = $this->order->get_items('coupon');
        } else {
            $current_coupon = array();
        }

        $item_list = array();

        if (!empty($item_coupon)) {

            $coupon_amount = rch_sanitize_field($this->get_field_value('rch_item_order_item_coupon_amount'));

            if (!empty($coupon_amount)) {
                $coupon_amount = explode($delimiter, $coupon_amount);
            }

            $item_coupon = explode($delimiter, $item_coupon);
           
            foreach ($item_coupon as $key => $code) {

                if (empty($code)) {
                    continue;
                }

                $_amount = isset($coupon_amount[$key]) ? abs(floatval($coupon_amount[$key])) : 0;

                $item_id = false;

                if (!empty($current_coupon)) {

                    foreach ($current_coupon as $order_item_id => $order_item) {

                        if ($order_item['name'] == $code) {

                            $item_id = $order_item_id;

                            $item_list[] = $order_item_id;

                            break;
                        }
                    }
                }

                $coupon_item = array(
                    'code' => $code,
                    'amount' => $_amount
                );

                $total_discount_amount += $_amount;

                if ($item_id) {
                    $this->update_coupon($item_id, $coupon_item);
                } else {
                    $this->add_coupon($coupon_item);
                }

                unset($_amount, $item_id, $coupon_item);
            }

            unset($coupon_amount);
        }

        if (!empty($current_coupon)) {

            foreach ($current_coupon as $order_item_id => $order_item) {

                if (!in_array($order_item_id, $item_list)) {

                    $this->remove_item($order_item_id);

                    break;
                }
            }
        }

        $this->order->set_discount_total($total_discount_amount);

        $this->order->set_discount_tax(0);

        unset($total_discount_amount, $delimiter, $item_coupon, $item_list, $current_coupon);
    }

    private function update_coupon($item_id = 0, $coupon_item = array()) {

        $item = new \WC_Order_Item_Coupon($item_id);

        if (isset($coupon['code'])) {
            $item->set_code($coupon['code']);
        }

        if (isset($coupon['amount'])) {
            $item->set_discount(floatval($coupon['amount']));
        }

        $coupon_id = $item->save();

        if (!$coupon_id) {

            $this->addon_log[] = "<strong>" . __('WARNING', 'rch-woo-import-export') . '</strong> : ' . __('Unable to update order coupon line.', 'rch-woo-import-export');
        }

        unset($item, $coupon_id);
    }

    private function add_coupon($coupon = array()) {

        $item = new \WC_Order_Item_Coupon();

        $item->set_name($coupon['code']);

        $item->set_code($coupon['code']);

        $item->set_discount(isset($coupon['amount']) ? abs(floatval($coupon['amount'])) : 0 );

        $item->set_order_id($this->item_id);

        $item->save();

        $this->order->add_item($item);
    }

    private function remove_item($item_id) {

        if (method_exists($this->order, "remove_item")) {
            $this->order->remove_item($item_id);
        } elseif (function_exists("wc_delete_order_item")) {
            wc_delete_order_item($item_id);
        }
    }

    public function __destruct() {

        parent::__destruct();

        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
