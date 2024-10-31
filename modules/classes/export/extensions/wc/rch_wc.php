<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

class RCH_WC_Export_Extension {

    public function __construct() {

        global $woocommerce;

        $plugins = get_option('active_plugins');

        $wc_plugin = 'woocommerce/woocommerce.php';

        if (in_array($wc_plugin, $plugins) ) {

            if (class_exists('Woocommerce')) {
                $this->init_wc();
            } else {
                add_action('woocommerce_loaded', array($this, 'init_wc'));
            }
        }

        unset($plugins, $wc_plugin);
    }

    public function init_wc() {

        add_filter('rch_prepare_post_fields', array($this, 'prepare_wc_addon'), 10, 2);

        add_filter('rch_prepare_export_addons', array($this, 'prepare_wc_addon'), 10, 2);

                add_filter( 'rch_export_advance_option_files', array ( $this, 'add_order_advance_option' ), 10, 1 );

                add_filter( 'rch_apply_post_filter', array ( $this, 'rch_apply_post_filter' ), 10, 3 );
        }

    public function prepare_wc_addon($addons = array(), $export_type = array("post")) {

        $class = "";

        $fileName = "";

        if (in_array("product", $export_type)) {

            $fileName = RCH_EXPORT_CLASSES_DIR . '/extensions/wc/class-rch-wc-product.php';

            $class = '\rch\export\wc\product\RCH_WC_Product';
        } elseif (in_array("shop_coupon", $export_type)) {

            $fileName = RCH_EXPORT_CLASSES_DIR . '/extensions/wc/class-rch-wc-coupon.php';

            $class = '\rch\export\wc\coupon\RCH_WC_Coupon';
        } elseif (in_array("shop_order", $export_type)) {

            $fileName = RCH_EXPORT_CLASSES_DIR . '/extensions/wc/class-rch-wc-order.php';

            $class = '\rch\export\wc\order\RCH_WC_Order';
        }

        if ($fileName != "" && file_exists($fileName)) {

            require_once($fileName);
        }

        if ($class != "" && !in_array($class, $addons)) {
            $addons[] = $class;
        }

        unset($class, $fileName);

        return $addons;
    }

    public function add_order_advance_option($files = array()) {

        $fileName = RCH_EXPORT_CLASSES_DIR . '/extensions/wc/wc-advance_option.php';

        if (!in_array($fileName, $files)) {
            $files[] = $fileName;
        }

        return $files;
    }

        public function rch_apply_post_filter( $data = [], $export_type = [], $filter = [] ) {

                if ( ! empty( $export_type ) ) {
                        if ( in_array( "shop_order", $export_type ) ) {
                                return $this->apply_order_filter( $data, $filter );
                        } elseif ( in_array( "product", $export_type ) ) {
                                return $this->apply_product_filter( $data, $filter );
                        }
                }

                return $data;
        }

        public function apply_order_filter( $data = [], $filter = [] ) {

                $fileName = RCH_EXPORT_CLASSES_DIR . '/extensions/wc/class-rch-wc-order-filter.php';

                if ( ! file_exists( $fileName ) ) {
                        return $data;
                }

                require_once $fileName;

                $order_filter = new rch\export\wc\filter\RCH_WC_Order_Filter();

                $new_data = $order_filter->apply_order_filter( $data, $filter );

                unset( $order_filter );

                return $new_data;
        }

        public function apply_product_filter( $data = [], $filter = [] ) {

                $fileName = RCH_EXPORT_CLASSES_DIR . '/extensions/wc/class-rch-wc-product-filter.php';

                if ( ! file_exists( $fileName ) ) {
                        return $data;
                }

                require_once $fileName;

                $product_filter = new rch\export\wc\filter\RCH_WC_Product_Filter();

                $new_data = $product_filter->apply_product_filter( $data, $filter );

                unset( $product_filter );

                return $new_data;
        }

}

new RCH_WC_Export_Extension();
