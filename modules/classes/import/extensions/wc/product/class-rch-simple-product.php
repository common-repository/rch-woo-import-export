<?php

namespace rch\import\wc\product\simple;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (file_exists(RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-product-base.php')) {

    require_once(RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-product-base.php');
}

class RCH_Simple_Product extends \rch\import\wc\product\base\RCH_Product_Base {

    protected $product_type = 'simple';

    public function __construct($rch_import_option = array(), $rch_import_record = array(), $item_id = "", $item = null, $is_new_item = false) {

        parent::__construct($rch_import_option, $rch_import_record, $item_id, $item, $is_new_item);
    }

    public function __destruct() {

        parent::__destruct();

        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
