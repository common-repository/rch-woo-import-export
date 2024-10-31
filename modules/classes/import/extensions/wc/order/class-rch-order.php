<?php

namespace rch\import\wc\order;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php' ) ) {

        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-base.php');
}

class RCH_Order_Import extends \rch\import\base\RCH_Import_Base {

        private $order;

        public function __construct( $rch_import_option = array(), $import_type = "", &$addon_error = false, &$addon_log = array() ) {

                $this->rch_import_option = $rch_import_option;

                $this->import_type = $import_type;

                $this->addon_error = &$addon_error;

                $this->addon_log = &$addon_log;

                $required_files = array(
                        'class-rch-order-address.php',
                        'class-rch-order-details.php',
                        'class-rch-order-notes.php',
                        'class-rch-order-payment.php',
                        'class-rch-order-refunds.php',
                        'class-rch-order-total.php',
                        'items/class-rch-order-coupon-item.php',
                        'items/class-rch-order-fee-item.php',
                        'items/class-rch-order-product-item.php',
                        'items/class-rch-order-shipping-item.php',
                        'items/class-rch-order-tax-item.php'
                );

                foreach ( $required_files as $file ) {

                        if ( file_exists( RCH_IMPORT_CLASSES_DIR . "/extensions/wc/order/" . $file ) ) {

                                require_once(RCH_IMPORT_CLASSES_DIR . "/extensions/wc/order/" . $file);
                        }
                }
                unset( $required_files );

                add_action( 'woocommerce_email', [ $this, 'unhook_those_pesky_emails' ] );
        }

        public function before_item_import( $rch_import_record = array(), &$existing_item_id = 0, &$is_new_item = true, $is_search_duplicates ) {

                $this->rch_import_record = $rch_import_record;

                $this->existing_item_id = $existing_item_id;
        }

        public function get_item_title( &$title = "" ) {

                if ( empty( $this->existing_item_id ) || absint( $this->existing_item_id ) == 0 ) {
                        $title = 'Order &ndash; ' . date_i18n( 'F j, Y @ h:i A', strtotime( current_time( 'mysql' ) ) );
                }
        }

        public function after_item_import( $item_id = 0, $item = null, $is_new_item = false ) {

                $this->item_id = $item_id;

                $this->item = $item;

                $this->is_new_item = $is_new_item;

                $this->order = wc_get_order( $this->item_id );

                new \rch\import\wc\order\details\RCH_Order_Details( $this->item, $this->is_new_item );

                new \rch\import\wc\order\address\RCH_Order_Address( $this->rch_import_option, $this->rch_import_record, $this->item_id, $this->is_new_item, $this->order );

                new \rch\import\wc\order\payment\RCH_Order_Payment( $this->rch_import_option, $this->rch_import_record, $this->item_id, $this->is_new_item, $this->order );

                if ( ! $this->addon_error && $this->is_update_field( "product" ) ) {
                        new \rch\import\wc\order\item\RCH_Order_Product_Item( $this->rch_import_option, $this->rch_import_record, $this->item_id, $this->is_new_item, $this->addon_error, $this->addon_log, $this->order );
                }

                if ( ! $this->addon_error && $this->is_update_field( "fee" ) ) {
                        new \rch\import\wc\order\item\RCH_Order_Fee_Item( $this->rch_import_option, $this->rch_import_record, $this->item_id, $this->is_new_item, $this->addon_error, $this->addon_log, $this->order );
                }

                if ( ! $this->addon_error && $this->is_update_field( "coupon" ) ) {
                        new \rch\import\wc\order\item\RCH_Order_Coupon_Item( $this->rch_import_option, $this->rch_import_record, $this->item_id, $this->is_new_item, $this->addon_error, $this->addon_log, $this->order );
                }

                if ( ! $this->addon_error && $this->is_update_field( "shipping" ) ) {

                        new \rch\import\wc\order\item\RCH_Order_Shipping_Item( $this->rch_import_option, $this->rch_import_record, $this->item_id, $this->is_new_item, $this->addon_error, $this->addon_log, $this->order );
                }

                if ( ! $this->addon_error && $this->is_update_field( "tax" ) ) {
                        new \rch\import\wc\order\item\RCH_Order_Tax_Item( $this->rch_import_option, $this->rch_import_record, $this->item_id, $this->is_new_item, $this->addon_error, $this->addon_log, $this->order );
                }

                if ( ! $this->addon_error && $this->is_update_field( "total" ) ) {
                        new \rch\import\wc\order\total\RCH_Order_Total( $this->rch_import_option, $this->rch_import_record, $this->item_id, $this->is_new_item, $this->addon_error, $this->addon_log, $this->order );
                }

                if ( ! $this->addon_error && $this->is_update_field( "notes" ) ) {
                        new \rch\import\wc\order\notes\RCH_Order_Notes( $this->rch_import_option, $this->rch_import_record, $this->item_id, $this->is_new_item, $this->addon_error, $this->addon_log );
                }

                $this->order->calculate_taxes();

                $this->order->save();

                if ( ! $this->addon_error && $this->is_update_field( "refunds" ) ) {
                        new \rch\import\wc\order\refunds\RCH_Order_Refunds( $this->rch_import_option, $this->rch_import_record, $this->item_id, $this->is_new_item, $this->addon_error, $this->addon_log );
                }
        }

        /**
         * Unhook and remove WooCommerce default emails.
         */
        public function unhook_those_pesky_emails( $email_class ) {

                /**
                 * Hooks for sending emails during store events
                 * */
                remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
                remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
                remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );

                // New order emails
                remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails[ 'WC_Email_New_Order' ], 'trigger' ) );
                remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails[ 'WC_Email_New_Order' ], 'trigger' ) );
                remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails[ 'WC_Email_New_Order' ], 'trigger' ) );
                remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails[ 'WC_Email_New_Order' ], 'trigger' ) );
                remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails[ 'WC_Email_New_Order' ], 'trigger' ) );
                remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails[ 'WC_Email_New_Order' ], 'trigger' ) );

                // Processing order emails
                remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails[ 'WC_Email_Customer_Processing_Order' ], 'trigger' ) );
                remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails[ 'WC_Email_Customer_Processing_Order' ], 'trigger' ) );

                // Completed order emails
                remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails[ 'WC_Email_Customer_Completed_Order' ], 'trigger' ) );

                // Note emails
                remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails[ 'WC_Email_Customer_Note' ], 'trigger' ) );
        }

        public function __destruct() {

                parent::__destruct();

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
