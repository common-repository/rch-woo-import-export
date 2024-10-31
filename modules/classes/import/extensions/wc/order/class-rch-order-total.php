<?php

namespace rch\import\wc\order\total;

use WC_Payment_Gateways;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}
if (file_exists(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php')) {

    require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php');
}

class RCH_Order_Total extends \rch\import\base\RCH_Import_Base {

    /**
     * @var \WC_Order
     */
    private $order;

    public function __construct($rch_import_option = array(), $rch_import_record = array(), $item_id = 0, $is_new_item = true, &$addon_error = false, &$addon_log = array(), $order) {

        $this->rch_import_option = $rch_import_option;

        $this->rch_import_record = $rch_import_record;

        $this->item_id = $item_id;

        $this->is_new_item = $is_new_item;

        $this->order = $order;

        $this->addon_error = &$addon_error;

        $this->addon_log = &$addon_log;

        $this->prepare_total();
    }

    private function prepare_total() {

        $order_total_logic = rch_sanitize_field($this->get_field_value('rch_item_order_total'));

        if ($order_total_logic == "manually") {

            $total = rch_sanitize_field($this->get_field_value('rch_item_order_total_as_specified'));

            $this->order->set_total($total);

            unset($total);
        } else {
            $this->order->calculate_totals(false);
        }

        unset($order_total_logic);
    }

    public function __destruct() {

        parent::__destruct();

        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
