<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}
if (!function_exists("rch_import_order_mapping_fields")) {

    function rch_import_order_mapping_fields($sections = array(), $rch_import_type = "") {

        ob_start();
        ?>
        <div class="rch_field_mapping_container_wrapper">
            <div class="rch_field_mapping_container_title rch_active"><?php esc_html_e('Order Details', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
            <div class="rch_field_mapping_container_data rch_show">
                <div class="rch_field_mapping_container_element">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Order Status', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper ">
                        <select class="rch_content_data_select rch_item_status rch_item_dropdown_as_specified" name="rch_item_status" >
                            <?php
                            $statuses = wc_get_order_statuses();

                            if (!empty($statuses)) {
                                foreach ($statuses as $status => $status_name) {
                                    echo '<option value="' . esc_attr($status) . '" >' . esc_html($status_name) . '</option>';
                                }
                            }
                            unset($statuses);
                            ?>
                            <option value="as_specified" ><?php esc_html_e('As Specified', 'rch-woo-import-export'); ?></option>
                        </select>    
                        <div class="rch_item_status_as_specified_wrapper rch_item_as_specified_wrapper rch_hide rch_as_specified_wrapper">
                            <input type="text" class="rch_content_data_input rch_item_status_as_specified_data" name="rch_item_status_as_specified_data" value=""/>
                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Order status can be matched by title or slug: wc-pending, wc-processing, wc-on-hold, wc-completed, wc-cancelled, wc-refunded, wc-failed. If order status is not found 'Pending Payment' will be applied to order.", "rch-woo-import-export"); ?>"></i>
                        </div>
                    </div>
                </div>
                <div class="rch_field_mapping_container_element">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Date', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper rch_as_specified_wrapper">
                        <input type="text" class="rch_content_data_input rch_item_date" name="rch_item_date" value=""/>
                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.", "rch-woo-import-export"); ?>"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $item_details = ob_get_clean();

        ob_start();
        ?>
        <div class="rch_field_mapping_container_wrapper">
            <div class="rch_field_mapping_container_title"><?php esc_html_e('Billing & Shipping Details', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
            <div class="rch_field_mapping_container_data">
                <div class="rch_product_data_section">
                    <div class="rch_product_menu_wrapper">
                        <div class="rch_order_item_list rch_product_menu_general active_tab" display_block="rch_order_billing_wrapper"><?php esc_html_e('Billing', 'rch-woo-import-export'); ?></div>
                        <div class="rch_order_item_list rch_product_menu_inventory" display_block="rch_order_shipping_wrapper"><?php esc_html_e('Shipping', 'rch-woo-import-export'); ?></div>
                        <div class="rch_order_item_list rch_product_menu_shipping" display_block="rch_order_payment_wrapper" ><?php esc_html_e('Payment', 'rch-woo-import-export'); ?></div>                        
                    </div>
                    <div class="rch_product_content_wrapper">
                        <div class="rch_order_item_data_container rch_order_billing_wrapper rch_show">
                            <div class="rch_product_element_data_container">
                                <div class="rch_field_mapping_other_option_wrapper">
                                    <input type="radio" class="rch_radio rch_item_order_billing_source" name="rch_item_order_billing_source" id="rch_item_order_billing_source_existing" value="existing" checked="checked"/>
                                    <label for="rch_item_order_billing_source_existing" class="rch_radio_label"><?php esc_html_e('Try to load data from existing customer', 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container">
                                        <div class="rch_product_element_wrapper">
                                            <div class="rch_product_element_data_lable"><?php echo esc_html_e('Match by:', 'rch-woo-import-export'); ?></div>
                                            <div class="rch_product_element_data">
                                                <select class="rch_content_data_select rch_item_order_billing_match_by" name="rch_item_order_billing_match_by">
                                                    <option value="username" ><?php esc_html_e('Username', 'rch-woo-import-export'); ?></option>
                                                    <option value="email" ><?php esc_html_e('Email', 'rch-woo-import-export'); ?></option>
                                                    <option value="cf" ><?php esc_html_e('Custom Field', 'rch-woo-import-export'); ?></option>
                                                    <option value="id" ><?php esc_html_e('User Id', 'rch-woo-import-export'); ?></option>
                                                </select>
                                                <div class="rch_order_billing_match_data_wrapper">
                                                    <input class="rch_content_data_input rch_item_order_billing_match_by_data rch_item_order_billing_match_by_username rch_show" type="text" name="rch_item_order_billing_match_by_username" value="" placeholder="<?php esc_attr_e('Username', 'rch-woo-import-export'); ?>">
                                                    <input class="rch_content_data_input rch_item_order_billing_match_by_data rch_item_order_billing_match_by_email" type="text" name="rch_item_order_billing_match_by_email" value="" placeholder="<?php esc_attr_e('Email', 'rch-woo-import-export'); ?>">
                                                    <input class="rch_content_data_input rch_item_order_billing_match_by_data rch_item_order_billing_match_by_cf_name" type="text" name="rch_item_order_billing_match_by_cf_name" value="" placeholder="<?php esc_attr_e('Field Name', 'rch-woo-import-export'); ?>">
                                                    <input class="rch_content_data_input rch_item_order_billing_match_by_data rch_item_order_billing_match_by_cf_value" type="text" name="rch_item_order_billing_match_by_cf_value" value="" placeholder="<?php esc_attr_e('Field Value', 'rch-woo-import-export'); ?>">
                                                    <input class="rch_content_data_input rch_item_order_billing_match_by_data rch_item_order_billing_match_by_user_id" type="text" name="rch_item_order_billing_match_by_user_id" value="" placeholder="<?php esc_attr_e('User ID', 'rch-woo-import-export'); ?>">
                                                </div>
                                            </div>
                                            <div class="rch_field_mapping_other_option_wrapper ">
                                                <input type="checkbox" value="1" name="rch_item_order_billing_no_match_guest" id="rch_item_order_billing_no_match_guest" class="rch_checkbox rch_item_order_billing_no_match_guest">
                                                <label class="rch_checkbox_label" for="rch_item_order_billing_no_match_guest"><?php esc_html_e('If no match found, import as guest customer', 'rch-woo-import-export'); ?></label>
                                                <div class="rch_checkbox_container">
                                                    <div class="rch_order_user_billing_data">
                                                        <div class="rch_order_user_billing_data_outer">
                                                            <div class="rch_order_user_billing_data_inner">
                                                                <div class="rch_order_user_billing_data_label">
                                                                    <?php esc_html_e('First Name', 'rch-woo-import-export'); ?>
                                                                </div>
                                                                <div class="rch_order_user_billing_data_container">
                                                                    <input type="text" class="rch_content_data_input rch_item_guest_billing_first_name" name="rch_item_guest_billing_first_name" value=""/>
                                                                </div>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_inner">
                                                                <div class="rch_order_user_billing_data_label">
                                                                    <?php esc_html_e('Last Name', 'rch-woo-import-export'); ?>
                                                                </div>
                                                                <div class="rch_order_user_billing_data_container">
                                                                    <input type="text" class="rch_content_data_input rch_item_guest_billing_last_name" name="rch_item_guest_billing_last_name" value=""/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="rch_order_user_billing_data_outer">
                                                            <div class="rch_order_user_billing_data_inner">
                                                                <div class="rch_order_user_billing_data_label">
                                                                    <?php esc_html_e('Address 1', 'rch-woo-import-export'); ?>
                                                                </div>
                                                                <div class="rch_order_user_billing_data_container">
                                                                    <input type="text" class="rch_content_data_input rch_item_guest_billing_address_1" name="rch_item_guest_billing_address_1" value=""/>
                                                                </div>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_inner">
                                                                <div class="rch_order_user_billing_data_label">
                                                                    <?php esc_html_e('Address 2', 'rch-woo-import-export'); ?>
                                                                </div>
                                                                <div class="rch_order_user_billing_data_container">
                                                                    <input type="text" class="rch_content_data_input rch_item_guest_billing_address_2" name="rch_item_guest_billing_address_2" value=""/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="rch_order_user_billing_data_outer">
                                                            <div class="rch_order_user_billing_data_inner">
                                                                <div class="rch_order_user_billing_data_label">
                                                                    <?php esc_html_e('City', 'rch-woo-import-export'); ?>
                                                                </div>
                                                                <div class="rch_order_user_billing_data_container">
                                                                    <input type="text" class="rch_content_data_input rch_item_guest_billing_city" name="rch_item_guest_billing_city" value=""/>
                                                                </div>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_inner">
                                                                <div class="rch_order_user_billing_data_label">
                                                                    <?php esc_html_e('Postcode', 'rch-woo-import-export'); ?>
                                                                </div>
                                                                <div class="rch_order_user_billing_data_container">
                                                                    <input type="text" class="rch_content_data_input rch_item_guest_billing_postcode" name="rch_item_guest_billing_postcode" value=""/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="rch_order_user_billing_data_outer">
                                                            <div class="rch_order_user_billing_data_inner">
                                                                <div class="rch_order_user_billing_data_label">
                                                                    <?php esc_html_e('Country', 'rch-woo-import-export'); ?>
                                                                </div>
                                                                <div class="rch_order_user_billing_data_container">
                                                                    <input type="text" class="rch_content_data_input rch_item_guest_billing_country" name="rch_item_guest_billing_country" value=""/>
                                                                </div>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_inner">
                                                                <div class="rch_order_user_billing_data_label">
                                                                    <?php esc_html_e('State/Country', 'rch-woo-import-export'); ?>
                                                                </div>
                                                                <div class="rch_order_user_billing_data_container">
                                                                    <input type="text" class="rch_content_data_input rch_item_guest_billing_state" name="rch_item_guest_billing_state" value=""/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="rch_order_user_billing_data_outer">
                                                            <div class="rch_order_user_billing_data_inner">
                                                                <div class="rch_order_user_billing_data_label">
                                                                    <?php esc_html_e('Email', 'rch-woo-import-export'); ?>
                                                                </div>
                                                                <div class="rch_order_user_billing_data_container">
                                                                    <input type="text" class="rch_content_data_input rch_item_guest_billing_email" name="rch_item_guest_billing_email" value=""/>
                                                                </div>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_inner">
                                                                <div class="rch_order_user_billing_data_label">
                                                                    <?php esc_html_e('Phone', 'rch-woo-import-export'); ?>
                                                                </div>
                                                                <div class="rch_order_user_billing_data_container">
                                                                    <input type="text" class="rch_content_data_input rch_item_guest_billing_phone" name="rch_item_guest_billing_phone" value=""/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="rch_order_user_billing_data_outer">
                                                            <div class="rch_order_user_billing_data_inner">
                                                                <div class="rch_order_user_billing_data_label">
                                                                    <?php esc_html_e('Company', 'rch-woo-import-export'); ?>
                                                                </div>
                                                                <div class="rch_order_user_billing_data_container">
                                                                    <input type="text" class="rch_content_data_input rch_item_guest_billing_company" name="rch_item_guest_billing_company" value=""/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <input type="radio" class="rch_radio rch_item_order_billing_source" name="rch_item_order_billing_source" id="rch_item_order_billing_source_guest" value="guest"/>
                                    <label for="rch_item_order_billing_source_guest" class="rch_radio_label"><?php esc_html_e('Import as guest customer', 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container">
                                        <div class="rch_order_user_billing_data">
                                            <div class="rch_order_user_billing_data_outer">
                                                <div class="rch_order_user_billing_data_inner">
                                                    <div class="rch_order_user_billing_data_label">
                                                        <?php esc_html_e('First Name', 'rch-woo-import-export'); ?>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_container">
                                                        <input type="text" class="rch_content_data_input rch_item_billing_first_name" name="rch_item_billing_first_name" value=""/>
                                                    </div>
                                                </div>
                                                <div class="rch_order_user_billing_data_inner">
                                                    <div class="rch_order_user_billing_data_label">
                                                        <?php esc_html_e('Last Name', 'rch-woo-import-export'); ?>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_container">
                                                        <input type="text" class="rch_content_data_input rch_item_billing_last_name" name="rch_item_billing_last_name" value=""/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rch_order_user_billing_data_outer">
                                                <div class="rch_order_user_billing_data_inner">
                                                    <div class="rch_order_user_billing_data_label">
                                                        <?php esc_html_e('Address 1', 'rch-woo-import-export'); ?>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_container">
                                                        <input type="text" class="rch_content_data_input rch_item_billing_address_1" name="rch_item_billing_address_1" value=""/>
                                                    </div>
                                                </div>
                                                <div class="rch_order_user_billing_data_inner">
                                                    <div class="rch_order_user_billing_data_label">
                                                        <?php esc_html_e('Address 2', 'rch-woo-import-export'); ?>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_container">
                                                        <input type="text" class="rch_content_data_input rch_item_billing_address_2" name="rch_item_billing_address_2" value=""/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rch_order_user_billing_data_outer">
                                                <div class="rch_order_user_billing_data_inner">
                                                    <div class="rch_order_user_billing_data_label">
                                                        <?php esc_html_e('City', 'rch-woo-import-export'); ?>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_container">
                                                        <input type="text" class="rch_content_data_input rch_item_billing_city" name="rch_item_billing_city" value=""/>
                                                    </div>
                                                </div>
                                                <div class="rch_order_user_billing_data_inner">
                                                    <div class="rch_order_user_billing_data_label">
                                                        <?php esc_html_e('Postcode', 'rch-woo-import-export'); ?>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_container">
                                                        <input type="text" class="rch_content_data_input rch_item_billing_postcode" name="rch_item_billing_postcode" value=""/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rch_order_user_billing_data_outer">
                                                <div class="rch_order_user_billing_data_inner">
                                                    <div class="rch_order_user_billing_data_label">
                                                        <?php esc_html_e('Country', 'rch-woo-import-export'); ?>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_container">
                                                        <input type="text" class="rch_content_data_input rch_item_billing_country" name="rch_item_billing_country" value=""/>
                                                    </div>
                                                </div>
                                                <div class="rch_order_user_billing_data_inner">
                                                    <div class="rch_order_user_billing_data_label">
                                                        <?php esc_html_e('State/Country', 'rch-woo-import-export'); ?>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_container">
                                                        <input type="text" class="rch_content_data_input rch_item_billing_state" name="rch_item_billing_state" value=""/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rch_order_user_billing_data_outer">
                                                <div class="rch_order_user_billing_data_inner">
                                                    <div class="rch_order_user_billing_data_label">
                                                        <?php esc_html_e('Email', 'rch-woo-import-export'); ?>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_container">
                                                        <input type="text" class="rch_content_data_input rch_item_billing_email" name="rch_item_billing_email" value=""/>
                                                    </div>
                                                </div>
                                                <div class="rch_order_user_billing_data_inner">
                                                    <div class="rch_order_user_billing_data_label">
                                                        <?php esc_html_e('Phone', 'rch-woo-import-export'); ?>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_container">
                                                        <input type="text" class="rch_content_data_input rch_item_billing_phone" name="rch_item_billing_phone" value=""/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rch_order_user_billing_data_outer">
                                                <div class="rch_order_user_billing_data_inner">
                                                    <div class="rch_order_user_billing_data_label">
                                                        <?php esc_html_e('Company', 'rch-woo-import-export'); ?>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_container">
                                                        <input type="text" class="rch_content_data_input rch_item_billing_company" name="rch_item_billing_company" value=""/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rch_order_item_data_container rch_order_shipping_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_field_mapping_other_option_wrapper">
                                    <input type="radio" class="rch_radio rch_item_order_shipping_source" name="rch_item_order_shipping_source" id="rch_item_order_shipping_source_copy" value="copy" checked="checked"/>
                                    <label for="rch_item_order_shipping_source_copy" class="rch_radio_label"><?php esc_html_e('Copy from billing', 'rch-woo-import-export'); ?></label>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <input type="radio" class="rch_radio rch_item_order_shipping_source" name="rch_item_order_shipping_source" id="rch_item_order_shipping_source_guest" value="guest"/>
                                    <label for="rch_item_order_shipping_source_guest" class="rch_radio_label"><?php esc_html_e('Import shipping address', 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container">
                                        <div class="rch_field_mapping_other_option_wrapper ">
                                            <input type="checkbox" value="1" name="rch_item_order_shipping_no_match_billing" id="rch_item_order_shipping_no_match_billing" class="rch_checkbox rch_item_order_shipping_no_match_billing">
                                            <label class="rch_checkbox_label" for="rch_item_order_shipping_no_match_billing"><?php esc_html_e('If order has no shipping info, copy from billing', 'rch-woo-import-export'); ?></label>
                                            <div class="rch_checkbox_container">
                                                <div class="rch_order_user_billing_data">
                                                    <div class="rch_order_user_billing_data_outer">
                                                        <div class="rch_order_user_billing_data_inner">
                                                            <div class="rch_order_user_billing_data_label">
                                                                <?php esc_html_e('First Name', 'rch-woo-import-export'); ?>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_container">
                                                                <input type="text" class="rch_content_data_input rch_item_shipping_first_name" name="rch_item_shipping_first_name" value=""/>
                                                            </div>
                                                        </div>
                                                        <div class="rch_order_user_billing_data_inner">
                                                            <div class="rch_order_user_billing_data_label">
                                                                <?php esc_html_e('Last Name', 'rch-woo-import-export'); ?>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_container">
                                                                <input type="text" class="rch_content_data_input rch_item_shipping_last_name" name="rch_item_shipping_last_name" value=""/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_outer">
                                                        <div class="rch_order_user_billing_data_inner">
                                                            <div class="rch_order_user_billing_data_label">
                                                                <?php esc_html_e('Address 1', 'rch-woo-import-export'); ?>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_container">
                                                                <input type="text" class="rch_content_data_input rch_item_shipping_address_1" name="rch_item_shipping_address_1" value=""/>
                                                            </div>
                                                        </div>
                                                        <div class="rch_order_user_billing_data_inner">
                                                            <div class="rch_order_user_billing_data_label">
                                                                <?php esc_html_e('Address 2', 'rch-woo-import-export'); ?>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_container">
                                                                <input type="text" class="rch_content_data_input rch_item_shipping_address_2" name="rch_item_shipping_address_2" value=""/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_outer">
                                                        <div class="rch_order_user_billing_data_inner">
                                                            <div class="rch_order_user_billing_data_label">
                                                                <?php esc_html_e('City', 'rch-woo-import-export'); ?>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_container">
                                                                <input type="text" class="rch_content_data_input rch_item_shipping_city" name="rch_item_shipping_city" value=""/>
                                                            </div>
                                                        </div>
                                                        <div class="rch_order_user_billing_data_inner">
                                                            <div class="rch_order_user_billing_data_label">
                                                                <?php esc_html_e('Postcode', 'rch-woo-import-export'); ?>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_container">
                                                                <input type="text" class="rch_content_data_input rch_item_shipping_postcode" name="rch_item_shipping_postcode" value=""/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_outer">
                                                        <div class="rch_order_user_billing_data_inner">
                                                            <div class="rch_order_user_billing_data_label">
                                                                <?php esc_html_e('Country', 'rch-woo-import-export'); ?>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_container">
                                                                <input type="text" class="rch_content_data_input rch_item_shipping_country" name="rch_item_shipping_country" value=""/>
                                                            </div>
                                                        </div>
                                                        <div class="rch_order_user_billing_data_inner">
                                                            <div class="rch_order_user_billing_data_label">
                                                                <?php esc_html_e('State/Country', 'rch-woo-import-export'); ?>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_container">
                                                                <input type="text" class="rch_content_data_input rch_item_shipping_state" name="rch_item_shipping_state" value=""/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="rch_order_user_billing_data_outer">
                                                        <div class="rch_order_user_billing_data_inner">
                                                            <div class="rch_order_user_billing_data_label">
                                                                <?php esc_html_e('Company', 'rch-woo-import-export'); ?>
                                                            </div>
                                                            <div class="rch_order_user_billing_data_container">
                                                                <input type="text" class="rch_content_data_input rch_item_shipping_company" name="rch_item_shipping_company" value=""/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Customer Provided Note', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_customer_provided_note" type="text" name="rch_item_order_customer_provided_note" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rch_order_item_data_container rch_order_payment_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Payment Method', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <select class="rch_content_data_select rch_item_dropdown_as_specified rch_item_order_payment_method" name="rch_item_order_payment_method">
                                            <?php
                                            $payment_gateways = WC_Payment_Gateways::instance()->payment_gateways();

                                            if (!empty($payment_gateways)) {
                                                foreach ($payment_gateways as $id => $gateway) {
                                                    echo '<option value="' . esc_attr($id) . '" >' . esc_html($gateway->title) . '</option>';
                                                }
                                            }
                                            unset($payment_gateways);
                                            ?>
                                            <option value="as_specified" ><?php esc_html_e('As Specified', 'rch-woo-import-export'); ?></option>
                                        </select>
                                        <div class="rch_item_as_specified_wrapper"><input type="text" class="rch_content_data_input rch_item_order_payment_method_as_specified_data" name="rch_item_order_payment_method_as_specified_data" value=""/></div>                                    
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Transaction ID', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_transaction_id" type="text" name="rch_item_order_transaction_id" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $item_billing = ob_get_clean();

        ob_start();
        ?>
        <div class="rch_field_mapping_container_wrapper">
            <div class="rch_field_mapping_container_title"><?php esc_html_e('Order Items', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
            <div class="rch_field_mapping_container_data">
                <div class="rch_product_data_section">
                    <div class="rch_product_menu_wrapper">
                        <div class="rch_order_item_list rch_product_menu_general active_tab" display_block="rch_order_item_product_wrapper"><?php esc_html_e('Products', 'rch-woo-import-export'); ?></div>
                        <div class="rch_order_item_list rch_product_menu_inventory" display_block="rch_order_item_fees_wrapper"><?php esc_html_e('Fees', 'rch-woo-import-export'); ?></div>
                        <div class="rch_order_item_list rch_product_menu_shipping" display_block="rch_order_item_coupons_wrapper" ><?php esc_html_e('Coupons', 'rch-woo-import-export'); ?></div>
                        <div class="rch_order_item_list rch_product_menu_linked_products" display_block="rch_order_item_shipping_wrapper"><?php esc_html_e('Shipping', 'rch-woo-import-export'); ?></div>
                        <div class="rch_order_item_list rch_product_menu_attributes" display_block="rch_order_item_taxes_wrapper"><?php esc_html_e('Taxes', 'rch-woo-import-export'); ?></div>
                        <div class="rch_order_item_list rch_product_menu_variations" display_block="rch_order_item_refunds_wrapper"><?php esc_html_e('Refunds', 'rch-woo-import-export'); ?></div>
                        <div class="rch_order_item_list rch_product_menu_advanced" display_block="rch_order_item_total_wrapper"><?php esc_html_e('Total', 'rch-woo-import-export'); ?></div>
                    </div>
                    <div class="rch_product_content_wrapper">
                        <div class="rch_order_item_data_container rch_order_item_product_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Product Name', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_product_name" type="text" name="rch_item_order_item_product_name" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Price per Unit', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_product_price" type="text" name="rch_item_order_item_product_price" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Quantity', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_product_quantity" type="text" name="rch_item_order_item_product_quantity" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('SKU', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_product_sku" type="text" name="rch_item_order_item_product_sku" value="">
                                    </div>
                                </div>

                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_element_half_wrapper">
                                        <div class="rch_product_element_data_lable"><?php echo esc_html_e('Meta Name', 'rch-woo-import-export'); ?></div>
                                    </div>
                                    <div class="rch_element_half_wrapper">
                                        <div class="rch_product_element_data_lable"><?php echo esc_html_e('Meta Value', 'rch-woo-import-export'); ?></div>
                                    </div>
                                </div>
                                <div class="rch_order_item_product_meta_wrapper">
                                    <div class="rch_product_element_wrapper">
                                        <div class="rch_element_half_wrapper">
                                            <input class="rch_content_data_input rch_item_order_item_product_meta_name" type="text" name="rch_item_order_item_product_meta_name[]" value="">
                                        </div>
                                        <div class="rch_element_half_wrapper">
                                            <input class="rch_content_data_input rch_item_order_item_product_meta_value" type="text" name="rch_item_order_item_product_meta_value[]" value="">
                                        </div>
                                        <div class="rch_delete_orer_item_product_meta_wrapper"><i class="fas fa-trash rch_trash_general_btn_icon rch_order_item_product_meta_delete_btn" aria-hidden="true"></i></div>
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_btn rch_btn_primary rch_order_item_product_meta_add_btn">
                                        <?php esc_html_e('Add More', 'rch-woo-import-export'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_order_item_delim_label"><?php echo esc_html_e('Multiple products separated by', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_order_item_delim_data">
                                        <input class="rch_content_data_input rch_item_order_item_product_delim" type="text" name="rch_item_order_item_product_delim" value="|">
                                    </div>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("For example, two products would be imported like this SKU1|SKU2, and their quantities like this 15|20", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rch_order_item_data_container rch_order_item_fees_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_element_half_wrapper">
                                        <div class="rch_product_element_data_lable"><?php echo esc_html_e('Fee Name', 'rch-woo-import-export'); ?></div>
                                    </div>
                                    <div class="rch_element_half_wrapper">
                                        <div class="rch_product_element_data_lable"><?php echo esc_html_e('Amount', 'rch-woo-import-export'); ?></div>
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_element_half_wrapper">
                                        <input class="rch_content_data_input rch_item_order_item_fee" type="text" name="rch_item_order_item_fee" value="">
                                    </div>
                                    <div class="rch_element_half_wrapper">
                                        <input class="rch_content_data_input rch_item_order_item_fee_amount" type="text" name="rch_item_order_item_fee_amount" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_order_item_delim_label"><?php echo esc_html_e('Multiple Fees separated by', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_order_item_delim_data">
                                        <input class="rch_content_data_input rch_item_order_item_fees_delim" type="text" name="rch_item_order_item_fees_delim" value="|">
                                    </div>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("For example, two fees would be imported like this 'Fee 1|Fee 2' and the fee amounts like this 10|20", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rch_order_item_data_container rch_order_item_coupons_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Coupon Code', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_coupon" type="text" name="rch_item_order_item_coupon" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Discount Amount', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_coupon_amount" type="text" name="rch_item_order_item_coupon_amount" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_order_item_delim_label"><?php echo esc_html_e('Multiple Coupons separated by', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_order_item_delim_data">
                                        <input class="rch_content_data_input rch_item_order_item_coupon_delim" type="text" name="rch_item_order_item_coupon_delim" value="|">
                                    </div>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("For example, two coupons would be imported like this coupon1|coupon2", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rch_order_item_data_container rch_order_item_shipping_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Shipping Name', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_shipping_name" type="text" name="rch_item_order_item_shipping_name" value="">
                                    </div>
                                </div>                              
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Shipping Method', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_shipping_method" type="text" name="rch_item_order_item_shipping_method" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Amount', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_shipping_amount" type="text" name="rch_item_order_item_shipping_amount" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_order_item_delim_label"><?php echo esc_html_e('Multiple Shipping costs separated by', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_order_item_delim_data">
                                        <input class="rch_content_data_input rch_item_order_item_shipping_costs_delim" type="text" name="rch_item_order_item_shipping_costs_delim" value="|">
                                    </div>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("For example, two shipping names would be imported like this 'Shipping 1|Shipping 2' and the shipping amounts like this 10|20", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rch_order_item_data_container rch_order_item_taxes_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Tax Rate', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_tax_rate" type="text" name="rch_item_order_item_tax_rate" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Tax Rate Amount', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_tax_rate_amount" type="text" name="rch_item_order_item_tax_rate_amount" value="">
                                    </div>
                                </div>                                                                
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_order_item_delim_label"><?php echo esc_html_e('Multiple taxes separated by', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_order_item_delim_data">
                                        <input class="rch_content_data_input rch_item_order_item_taxes_delim" type="text" name="rch_item_order_item_taxes_delim" value="|">
                                    </div>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("For example, two tax rate amounts would be imported like this '10|20'", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rch_order_item_data_container rch_order_item_refunds_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Refund Amount', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_refund_amount" type="text" name="rch_item_order_item_refund_amount" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Reason', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_refund_reason" type="text" name="rch_item_order_item_refund_reason" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Date', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_refund_date" type="text" name="rch_item_order_item_refund_date" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Refund Name', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_order_item_refund_name" type="text" name="rch_item_order_item_refund_name" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Refund Issued By', 'rch-woo-import-export'); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("If no user is matched, refund issuer will be left blank.", "rch-woo-import-export"); ?>"></i></div>
                                    <div class="rch_product_element_all_data_lable">
                                        <input type="radio" class="rch_radio rch_item_order_item_refund_issued_match_by_existing rch_item_order_item_refund_issued_match_by" name="rch_item_order_item_refund_issued_match_by" id="rch_item_order_item_refund_issued_match_by_existing" value="existing" checked="checked"/>
                                        <label for="rch_item_order_item_refund_issued_match_by_existing" class="rch_radio_label"><?php esc_html_e('Load details from existing user', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container">
                                            <div class="rch_product_element_data_lable"><?php echo esc_html_e('Match user by:', 'rch-woo-import-export'); ?></div>
                                            <div class="rch_product_element_all_data_lable">
                                                <input type="radio" class="rch_radio rch_item_order_item_refund_issued_by" name="rch_item_order_item_refund_issued_by" id="rch_item_order_item_refund_issued_by_username" value="login" checked="checked"/>
                                                <label for="rch_item_order_item_refund_issued_by_username" class="rch_radio_label"><?php esc_html_e('Username', 'rch-woo-import-export'); ?></label>
                                                <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_item_refund_customer_login" name="rch_item_refund_customer_login" value=""/></div>
                                            </div>
                                            <div class="rch_product_element_all_data_lable">
                                                <input type="radio" class="rch_radio rch_item_order_item_refund_issued_by" name="rch_item_order_item_refund_issued_by" id="rch_item_order_item_refund_issued_by_email" value="email"/>
                                                <label for="rch_item_order_item_refund_issued_by_email" class="rch_radio_label"><?php esc_html_e('Email', 'rch-woo-import-export'); ?></label>
                                                <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_item_refund_customer_email" name="rch_item_refund_customer_email" value=""/></div>
                                            </div>
                                            <div class="rch_product_element_all_data_lable">
                                                <input type="radio" class="rch_radio rch_item_order_item_refund_issued_by" name="rch_item_order_item_refund_issued_by" id="rch_item_order_item_refund_issued_by_cf" value="cf"/>
                                                <label for="rch_item_order_item_refund_issued_by_cf" class="rch_radio_label"><?php esc_html_e('Custom Field', 'rch-woo-import-export'); ?></label>
                                                <div class="rch_radio_container">
                                                    <input type="text" class="rch_content_data_input rch_item_refund_customer_meta_key" name="rch_item_refund_customer_meta_key" value=""/>
                                                    <input type="text" class="rch_content_data_input rch_item_refund_customer_meta_val" name="rch_item_refund_customer_meta_val" value=""/>
                                                </div>
                                            </div>
                                            <div class="rch_product_element_all_data_lable">
                                                <input type="radio" class="rch_radio rch_item_order_item_refund_issued_by" name="rch_item_order_item_refund_issued_by" id="rch_item_order_item_refund_issued_by_id" value="id"/>
                                                <label for="rch_item_order_item_refund_issued_by_id" class="rch_radio_label"><?php esc_html_e('User Id', 'rch-woo-import-export'); ?></label>
                                                <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_item_refund_customer_id" name="rch_item_refund_customer_id" value=""/></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rch_product_element_all_data_lable">
                                        <input type="radio" class="rch_radio  rch_item_order_item_refund_issued_match_by rch_item_order_item_refund_issued_match_by_blank" name="rch_item_order_item_refund_issued_match_by" id="rch_item_order_item_refund_issued_match_by_blank" value="blank"/>
                                        <label for="rch_item_order_item_refund_issued_match_by_blank" class="rch_radio_label"><?php esc_html_e('Leave refund issuer blank', 'rch-woo-import-export'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_order_item_delim_label"><?php echo esc_html_e('Multiple Refunds separated by', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_order_item_delim_data">
                                        <input class="rch_content_data_input rch_item_order_item_refund_delim" type="text" name="rch_item_order_item_refund_delim" value="|">
                                    </div>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("For example, two refunds amounts would be imported like this 'refund 1|refund 2'", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                        </div>                        
                        <div class="rch_order_item_data_container rch_order_item_total_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_all_data_lable">
                                    <input type="radio" class="rch_radio rch_item_order_total" name="rch_item_order_total" id="rch_order_total_auto" value="auto" checked="checked"/>
                                    <label for="rch_order_total_auto" class="rch_radio_label"><?php esc_html_e('Calculate order total automatically', 'rch-woo-import-export'); ?></label>
                                </div>
                                <div class="rch_product_element_all_data_lable">
                                    <input type="radio" class="rch_radio rch_item_order_total" name="rch_item_order_total" id="rch_order_total_manually" value="manually"/>
                                    <label for="rch_order_total_manually" class="rch_radio_label"><?php esc_html_e('Calculate order total Manually', 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_item_order_total_as_specified" name="rch_item_order_total_as_specified" value=""/></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $order_item = ob_get_clean();

        ob_start();
        ?>
        <div class="rch_field_mapping_container_wrapper">
            <div class="rch_field_mapping_container_title"><?php esc_html_e('Notes', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
            <div class="rch_field_mapping_container_data">
                <div class="rch_product_element_wrapper">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Content', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <textarea class="rch_content_data_textarea rch_item_import_order_note_content"  name="rch_item_import_order_note_content"></textarea>
                    </div>
                </div>
                <div class="rch_product_element_wrapper">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Date', 'rch-woo-import-export'); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.", "rch-woo-import-export"); ?>"></i></div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="text" class="rch_content_data_input rch_item_import_order_note_date" name="rch_item_import_order_note_date" value=""/>
                    </div>
                </div>
                <div class="rch_product_element_wrapper">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Visibility', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_item_import_order_note_visibility" name="rch_item_import_order_note_visibility" id="rch_import_order_note_visibility_private" value="private" checked="checked" />
                        <label for="rch_import_order_note_visibility_private" class="rch_radio_label"><?php esc_html_e('Private note', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper ">
                        <input type="radio" class="rch_radio rch_item_import_order_note_visibility" name="rch_item_import_order_note_visibility" id="rch_import_order_note_visibility_customer" value="customer"/>
                        <label for="rch_import_order_note_visibility_customer" class="rch_radio_label"><?php esc_html_e('Note to customer', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_item_import_order_note_visibility rch_item_import_order_note_visibility_as_specified" name="rch_item_import_order_note_visibility" id="rch_import_order_note_visibility_as_specified" value="as_specified"/>
                        <label for="rch_import_order_note_visibility_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                        <div class="rch_radio_container rch_as_specified_wrapper">
                            <input type="text" class="rch_content_data_input rch_item_import_order_note_visibility_as_specified_data" name="rch_item_import_order_note_visibility_as_specified_data" value=""/>
                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Use 'private' or 'customer'.", "rch-woo-import-export"); ?>"></i>
                        </div>
                    </div>
                </div>
                <div class="rch_product_element_wrapper">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Username', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="text" class="rch_content_data_input rch_item_import_order_note_username" name="rch_item_import_order_note_username" value=""/>
                    </div>
                </div>
                <div class="rch_product_element_wrapper">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Email', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="text" class="rch_content_data_input rch_item_import_order_note_email" name="rch_item_import_order_note_email" value=""/>
                    </div>
                </div>
                <div class="rch_product_element_wrapper">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Multiple notes separated by', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper rch_as_specified_wrapper">
                        <input type="text" class="rch_content_data_input rch_item_import_order_note_delim" name="rch_item_import_order_note_delim" value="|"/>
                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("For example, two notes would be imported like this 'Note 1|Note 2'", "rch-woo-import-export"); ?>"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $item_notes = ob_get_clean();

        $order_fields = array(
            '160' => $item_details,
            '170' => $item_billing,
            '180' => $order_item,
            '190' => $item_notes
        );

        if (isset($sections["100"])) {
            unset($sections["100"]);
        }
        if (isset($sections["200"])) {
            unset($sections["200"]);
        }
        if (isset($sections["400"])) {
            unset($sections["400"]);
        }
        if (isset($sections["500"])) {
            unset($sections["500"]);
        }

        $sections = array_replace($sections, $order_fields);

        unset($item_details, $item_billing, $order_item, $item_notes, $order_fields);

        return apply_filters("rch_pre_order_field_mapping_section", $sections, $rch_import_type);
    }

}
    