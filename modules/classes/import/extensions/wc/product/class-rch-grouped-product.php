<?php

namespace rch\import\wc\product\grouped;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (file_exists(RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-product-base.php')) {

    require_once(RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-product-base.php');
}

class RCH_Grouped_Product extends \rch\import\wc\product\base\RCH_Product_Base {

    protected $product_type = 'grouped';

    public function import_data() {

        parent::import_data();
    }

    public function prepare_general_properties() {

        parent::prepare_general_properties();

        $this->product_properties['children'] = array();
    }
    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
