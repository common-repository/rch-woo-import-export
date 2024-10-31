<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (!function_exists("rch_import_order_update_fields")) {

    function rch_import_order_update_fields() {

        ob_start();
        ?>
        <div class="rch_field_mapping_container_element">
            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Update Existing items data', 'rch-woo-import-export'); ?></div>
            <div class="rch_field_mapping_other_option_wrapper">
                <input type="radio" class="rch_radio rch_item_update rch_item_update_all" checked="checked" name="rch_item_update" id="rch_item_update_all" value="all"/>
                <label for="rch_item_update_all" class="rch_radio_label"><?php esc_html_e('Update all data', 'rch-woo-import-export'); ?></label>
            </div>
            <div class="rch_field_mapping_other_option_wrapper">
                <input type="radio" class="rch_radio rch_item_update rch_item_update_specific" name="rch_item_update" id="rch_item_update_specific" value="specific"/>
                <label for="rch_item_update_specific" class="rch_radio_label"><?php esc_html_e('Choose which data to update', 'rch-woo-import-export'); ?></label>
                <div class="rch_radio_container">
                    <div class="rch_update_item_all_action"><?php esc_html_e('Check/Uncheck All', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_post_status" checked="checked" name="is_update_item_post_status" id="is_update_item_post_status" value="1"/>
                        <label for="is_update_item_post_status" class="rch_checkbox_label"><?php esc_html_e('Order status', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_excerpt" checked="checked" name="is_update_item_excerpt" id="is_update_item_excerpt" value="1"/>
                        <label for="is_update_item_excerpt" class="rch_checkbox_label"><?php esc_html_e('Customer Note', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_dates" checked="checked" name="is_update_item_dates" id="is_update_item_dates" value="1"/>
                        <label for="is_update_item_dates" class="rch_checkbox_label"><?php esc_html_e('Dates', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_billing_details" checked="checked" name="is_update_item_billing_details" id="is_update_item_billing_details" value="1"/>
                        <label for="is_update_item_billing_details" class="rch_checkbox_label"><?php esc_html_e('Billing Details', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_shipping_details" checked="checked" name="is_update_item_shipping_details" id="is_update_item_shipping_details" value="1"/>
                        <label for="is_update_item_shipping_details" class="rch_checkbox_label"><?php esc_html_e('Shipping Details', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_payment" checked="checked" name="is_update_item_payment" id="is_update_item_payment" value="1"/>
                        <label for="is_update_item_payment" class="rch_checkbox_label"><?php esc_html_e('Payment Details', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_notes" checked="checked" name="is_update_item_notes" id="is_update_item_notes" value="1"/>
                        <label for="is_update_item_notes" class="rch_checkbox_label"><?php esc_html_e('Order Notes', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_product" checked="checked" name="is_update_item_product" id="is_update_item_product" value="1"/>
                        <label for="is_update_item_product" class="rch_checkbox_label"><?php esc_html_e('Product Items', 'rch-woo-import-export'); ?></label>
                        <div class="rch_checkbox_container">
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_product_all" checked="checked" name="rch_item_update_product" id="rch_item_update_product_all" value="all"/>
                                <label for="rch_item_update_product_all" class="rch_radio_label"><?php esc_html_e('Update all products', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_product_append" name="rch_item_update_product" id="rch_item_update_product_append" value="append"/>
                                <label for="rch_item_update_product_append" class="rch_radio_label"><?php esc_html_e("Don't touch existing products, append new products", 'rch-woo-import-export'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_fee" checked="checked" name="is_update_item_fee" id="is_update_item_fee" value="1"/>
                        <label for="is_update_item_fee" class="rch_checkbox_label"><?php esc_html_e('Fees Items', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_coupon" checked="checked" name="is_update_item_coupon" id="is_update_item_coupon" value="1"/>
                        <label for="is_update_item_coupon" class="rch_checkbox_label"><?php esc_html_e('Coupon Items', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_shipping" checked="checked" name="is_update_item_shipping" id="is_update_item_shipping" value="1"/>
                        <label for="is_update_item_shipping" class="rch_checkbox_label"><?php esc_html_e('Shipping Items', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_tax" checked="checked" name="is_update_item_tax" id="is_update_item_tax" value="1"/>
                        <label for="is_update_item_tax" class="rch_checkbox_label"><?php esc_html_e('Tax Items', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_refunds" checked="checked" name="is_update_item_refunds" id="is_update_item_refunds" value="1"/>
                        <label for="is_update_item_refunds" class="rch_checkbox_label"><?php esc_html_e('Refunds', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_total" checked="checked" name="is_update_item_total" id="is_update_item_total" value="1"/>
                        <label for="is_update_item_total" class="rch_checkbox_label"><?php esc_html_e('Order Total', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_cf" checked="checked" name="is_update_item_cf" id="is_update_item_cf" value="1"/>
                        <label for="is_update_item_cf" class="rch_checkbox_label"><?php esc_html_e('Custom Fields', 'rch-woo-import-export'); ?></label>
                        <div class="rch_checkbox_container">
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_append" checked="checked" name="rch_item_update_cf" id="rch_item_update_cf_append" value="append"/>
                                <label for="rch_item_update_cf_append" class="rch_radio_label"><?php esc_html_e('Update all Custom Fields and keep fields if not found in file', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_all" name="rch_item_update_cf" id="rch_item_update_cf_all" value="all"/>
                                <label for="rch_item_update_cf_all" class="rch_radio_label"><?php esc_html_e('Update all Custom Fields and Remove fields if not found in file', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_includes" name="rch_item_update_cf" id="rch_item_update_cf_includes" value="includes"/>
                                <label for="rch_item_update_cf_includes" class="rch_radio_label"><?php esc_html_e("Update only these Custom Fields, leave the rest alone", 'rch-woo-import-export'); ?></label>
                                <div class="rch_radio_container">
                                    <input type="text" class="rch_content_data_input rch_item_update_cf_includes_data" name="rch_item_update_cf_includes_data" value=""/>
                                </div>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_excludes" name="rch_item_update_cf" id="rch_item_update_cf_excludes" value="excludes"/>
                                <label for="rch_item_update_cf_excludes" class="rch_radio_label"><?php esc_html_e("Leave these fields alone, update all other Custom Fields", 'rch-woo-import-export'); ?></label>
                                <div class="rch_radio_container">
                                    <input type="text" class="rch_content_data_input rch_item_update_cf_excludes_data" name="rch_item_update_cf_excludes_data" value=""/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $sections = ob_get_clean();

        return $sections;
    }

}