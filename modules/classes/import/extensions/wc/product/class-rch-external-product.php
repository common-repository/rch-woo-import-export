<?php

namespace rch\import\wc\product\external;

if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (file_exists(RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-product-base.php')) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-product-base.php');
}

class RCH_External_Product extends \rch\import\wc\product\base\RCH_Product_Base {

        protected $product_type = 'external';

        public function import_data() {

                parent::import_data();
        }

        public function prepare_general_properties() {

                if ($this->is_update_meta("_product_url")) {

                        $this->product_properties['product_url'] = apply_filters("rch_wc_external_product_url", esc_url($this->get_field_value('rch_item_meta_product_url')));
                }

                if ($this->is_update_meta("_button_text")) {
                        $this->product_properties['button_text'] = rch_sanitize_field($this->get_field_value('rch_item_meta_button_text'));
                }
                parent::prepare_general_properties();
        }

        public function __destruct() {
                foreach ($this as $key => $value) {
                        unset($this->$key);
                }
        }

}
