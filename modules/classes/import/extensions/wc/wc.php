<?php

if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_WC_Import_Extension {

        public function __construct() {

                add_action('admin_enqueue_scripts', array($this, 'rch_enqueue_wc_scripts'), 10);

                add_filter('rch_pre_post_field_mapping_section', array($this, "rch_product_field_mapping_section"), 10, 2);

                add_filter('rch_import_post_search_existing_item', array($this, "rch_import_order_search_existing_item"), 10, 2);

                add_filter('rch_import_addon', array($this, "wc_addon_init"), 10, 2);

                add_filter('rch_import_post_update_item_fields', array($this, "rch_get_update_fields"), 10, 2);
        }

        public function rch_enqueue_wc_scripts() {

                wp_register_script('rch-import-wc-js', RCH_IMPORT_ADDON_URL . '/wc/rch-import-wc.min.js', array('jquery'), RCH_PLUGIN_VERSION);

                wp_enqueue_script('rch-import-wc-js');
        }

        public function rch_product_field_mapping_section($sections = array(), $rch_import_type = "") {

                if ($rch_import_type == "product") {

                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/rch-product-fields.php';

                        if (file_exists($fileName)) {

                                require_once($fileName);

                                if (function_exists("rch_import_product_mapping_fields")) {
                                        $sections = rch_import_product_mapping_fields($sections, $rch_import_type);
                                }
                        }
                        unset($fileName);
                } elseif ($rch_import_type == "shop_order") {
                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/order/rch-order-fields.php';

                        if (file_exists($fileName)) {

                                require_once($fileName);

                                if (function_exists("rch_import_order_mapping_fields")) {
                                        $sections = rch_import_order_mapping_fields($sections, $rch_import_type);
                                }
                        }
                        unset($fileName);
                } elseif ($rch_import_type == "shop_coupon") {

                        if (isset($sections["200"])) {
                                unset($sections["200"]);
                        }
                        if (isset($sections["400"])) {
                                unset($sections["400"]);
                        }
                }

                return $sections;
        }

        public function rch_import_order_search_existing_item($sections = "", $rch_import_type = "") {

                if ($rch_import_type == "shop_order") {

                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/order/rch-order-existing-item-fields.php';

                        if (file_exists($fileName)) {

                                require_once($fileName);

                                if (function_exists("rch_import_order_search_existing_item")) {
                                        $sections = rch_import_order_search_existing_item();
                                }
                        }
                        unset($fileName);
                }
                return $sections;
        }

        public function rch_get_update_fields($sections = "", $rch_import_type = "") {

                if ($rch_import_type == "shop_order") {

                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/order/rch-order-update-fields.php';

                        if (file_exists($fileName)) {

                                require_once($fileName);

                                if (function_exists("rch_import_order_update_fields")) {
                                        $sections = rch_import_order_update_fields();
                                }
                        }
                        unset($fileName);
                }


                return $sections;
        }

        public function wc_addon_init($addons = array(), $rch_import_type = "") {

                $fileName = "";

                if ($rch_import_type == "product" && !in_array('\rch\import\wc\product\RCH_Product_Import', $addons)) {

                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-rch-product.php';

                        $addons[] = '\rch\import\wc\product\RCH_Product_Import';
                } elseif ($rch_import_type == "shop_coupon" && !in_array('\rch\import\wc\coupon\RCH_Coupon_Import', $addons)) {

                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/coupon/class-rch-coupon.php';

                        $addons[] = '\rch\import\wc\coupon\RCH_Coupon_Import';
                } elseif ($rch_import_type == "shop_order" && !in_array('\rch\import\wc\order\RCH_Order_Import', $addons)) {

                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/wc/order/class-rch-order.php';

                        $addons[] = '\rch\import\wc\order\RCH_Order_Import';
                }

                if (!empty($fileName) && file_exists($fileName)) {

                        require_once($fileName);
                }

                unset($fileName);

                return $addons;
        }

        public function __destruct() {
                foreach ($this as $key => $value) {
                        unset($this->$key);
                }
        }

}

new RCH_WC_Import_Extension();
