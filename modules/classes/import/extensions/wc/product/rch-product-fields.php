<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}
if (!function_exists("rch_import_product_mapping_fields")) {

    function rch_import_product_mapping_fields($sections = array(), $rch_import_type = "") {

        $rch_product_type = apply_filters('rch_import_product_type', array(
            'as_specified' => __('As specified', 'rch-woo-import-export'),
            'simple' => __('Simple product', 'rch-woo-import-export'),
            'grouped' => __('Grouped product', 'rch-woo-import-export'),
            'external' => __('External/Affiliate product', 'rch-woo-import-export'),
            'variable' => __('Variable product', 'rch-woo-import-export')
        ));

        $product_group = array();

        $group_term = get_term_by('slug', 'grouped', 'product_type');

        if ($group_term) {
            $group_data = get_objects_in_term($group_term->term_id, 'product_type');

            if (!is_wp_error($group_data)) {
                $posts_in = array_unique($group_data);

                if (sizeof($posts_in) > 0) {
                    $posts_in = array_slice($posts_in, 0, 100);
                    $args = array(
                        'post_type' => 'product',
                        'post_status' => 'any',
                        'numberposts' => 100,
                        'orderby' => 'title',
                        'order' => 'asc',
                        'post_parent' => 0,
                        'include' => $posts_in,
                    );
                    $product_group = get_posts($args);
                    unset($args);
                }
                unset($posts_in);
            }
            unset($group_data);
        }
        unset($group_term);

        $rch_product_tax_class = array_filter(array_map('trim', explode("\n", get_option('woocommerce_tax_classes'))));

        ob_start();
        ?>
        <div class="rch_field_mapping_container_wrapper">
            <div class="rch_field_mapping_container_title"><?php esc_html_e('Product Data', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
            <div class="rch_field_mapping_container_data">
                <div class="rch_product_data_section">
                    <div class="rch_product_type_wrapper">
                        <div class="rch_product_type_label"><?php esc_html_e('Product Type', 'rch-woo-import-export'); ?></div>
                        <div class="rch_product_type_list">
                            <select class="rch_content_data_select rch_item_product_type" name="rch_item_product_type">
                                <?php if (!empty($rch_product_type)) { ?>
                                    <?php
                                    foreach ($rch_product_type as $key => $value) {
                                        if ($key == "as_specified") {
                                            $chk = ' checked="checked" ';
                                        } else {
                                            $chk = "";
                                        }
                                        ?>
                                        <option value="<?php echo esc_attr($key); ?>" <?php echo $chk; ?>> <?php echo esc_html($value); ?></option>
                                        <?php
                                        unset($chk);
                                    }
                                    ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="rch_product_type_as_specified_wrapper">
                            <input class="rch_content_data_input rch_item_product_type_as_specified_data" type="text" name="rch_item_product_type_as_specified_data" value="">
                        </div>
                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('simple', 'grouped', 'external', 'variable').", "rch-woo-import-export"); ?>"></i>
                    </div>
                    <div class="rch_product_menu_wrapper">
                        <div class="rch_product_menu_list rch_product_menu_general active_tab" display_block="rch_product_general_wrapper"><?php esc_html_e('General', 'rch-woo-import-export'); ?></div>
                        <div class="rch_product_menu_list rch_product_menu_inventory" display_block="rch_product_inventory_wrapper"><?php esc_html_e('Inventory', 'rch-woo-import-export'); ?></div>
                        <div class="rch_product_menu_list rch_product_menu_shipping" display_block="rch_product_shipping_wrapper" ><?php esc_html_e('Shipping', 'rch-woo-import-export'); ?></div>
                        <div class="rch_product_menu_list rch_product_menu_linked_products" display_block="rch_product_linked_products_wrapper"><?php esc_html_e('Linked Products', 'rch-woo-import-export'); ?></div>
                        <div class="rch_product_menu_list rch_product_menu_attributes" display_block="rch_product_attributes_wrapper"><?php esc_html_e('Attributes', 'rch-woo-import-export'); ?></div>
                        <div class="rch_product_menu_list rch_product_menu_variations" display_block="rch_product_variations_wrapper"><?php esc_html_e('Variations', 'rch-woo-import-export'); ?></div>
                        <div class="rch_product_menu_list rch_product_menu_advanced" display_block="rch_product_advanced_wrapper"><?php esc_html_e('Advanced', 'rch-woo-import-export'); ?></div>
                        <div class="rch_product_menu_list rch_product_menu_extra" display_block="rch_product_extra_wrapper"><?php esc_html_e('Plugin Extra Option', 'rch-woo-import-export'); ?></div>
                    </div>
                    <div class="rch_product_content_wrapper">
                        <div class="rch_product_data_container rch_product_general_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php esc_html_e('SKU', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_meta_sku" type="text" name="rch_item_meta_sku" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html(__('Regular Price', 'rch-woo-import-export') . " (" . get_woocommerce_currency_symbol() . ")"); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_meta_regular_price" type="text" name="rch_item_meta_regular_price" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html(__('Sale Price', 'rch-woo-import-export') . " (" . get_woocommerce_currency_symbol() . ")"); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_meta_sale_price" type="text" name="rch_item_meta_sale_price" value="">
                                    </div>
                                    <div class="rch_schedule_price_link"><?php esc_html_e('Schedule', 'rch-woo-import-export'); ?></div>
                                </div>
                                <div class="rch_product_schedule_price_wrapper">
                                    <div class="rch_product_element_wrapper">
                                        <div class="rch_product_element_data_lable"><?php esc_html_e('Sale price dates', 'rch-woo-import-export'); ?></div>
                                        <div class="rch_product_element_data">
                                            <input class="rch_content_data_input rch_item_meta_sale_price_dates_from" type="text" name="rch_item_meta_sale_price_dates_from" value="" placeholder="<?php echo esc_attr_e('From', 'rch-woo-import-export'); ?>">
                                        </div>
                                    </div>
                                    <div class="rch_product_element_wrapper">
                                        <div class="rch_product_element_data_lable"></div>
                                        <div class="rch_product_element_data">
                                            <input class="rch_content_data_input rch_item_meta_sale_price_dates_to" type="text" name="rch_item_meta_sale_price_dates_to" value="" placeholder="<?php echo esc_attr_e('To', 'rch-woo-import-export'); ?>">
                                        </div>
                                        <div class="rch_schedule_price_cancel_link"><?php esc_html_e('Cancel', 'rch-woo-import-export'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php esc_html_e('Product URL', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_meta_product_url" type="text" name="rch_item_meta_product_url" value="">
                                    </div>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The external/affiliate link URL to the product.", "rch-woo-import-export"); ?>"></i>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php esc_html_e('Button text', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_meta_button_text" type="text" name="rch_item_meta_button_text" value="">
                                    </div>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("This text will be shown on the button linking to the external product.", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio  rch_item_meta_virtual"  name="rch_item_meta_virtual" id="rch_item_meta_virtual_yes" value="yes"/>
                                        <label for="rch_item_meta_virtual_yes" class="rch_radio_label"><?php esc_html_e('Virtual', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio  rch_item_meta_virtual" checked="checked" name="rch_item_meta_virtual" id="rch_item_meta_virtual_no" value="no"/>
                                        <label for="rch_item_meta_virtual_no" class="rch_radio_label"><?php esc_html_e('Not Virtual', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_virtual rch_item_meta_virtual_as_specified" name="rch_item_meta_virtual" id="rch_item_meta_virtual_as_specified" value="as_specified"/>
                                        <label for="rch_item_meta_virtual_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper">
                                            <input type="text" class="rch_content_data_input rch_item_meta_virtual_as_specified_data" name="rch_item_meta_virtual_as_specified_data" value=""/>
                                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('yes', 'no').", "rch-woo-import-export"); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_downloadable"  name="rch_item_meta_downloadable" id="rch_item_meta_downloadable_yes" value="yes"/>
                                        <label for="rch_item_meta_downloadable_yes" class="rch_radio_label"><?php esc_html_e('Downloadable', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_downloadable" checked="checked" name="rch_item_meta_downloadable" id="rch_item_meta_downloadable_no" value="no"/>
                                        <label for="rch_item_meta_downloadable_no" class="rch_radio_label"><?php esc_html_e('Not Downloadable', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_downloadable rch_item_meta_downloadable_as_specified" name="rch_item_meta_downloadable" id="rch_item_meta_downloadable_as_specified" value="as_specified"/>
                                        <label for="rch_item_meta_downloadable_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper">
                                            <input type="text" class="rch_content_data_input rch_item_meta_downloadable_as_specified_data" name="rch_item_meta_downloadable_as_specified_data" value=""/>
                                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('yes', 'no').", "rch-woo-import-export"); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container rch_product_downloadable_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_wrapper">
                                        <div class="rch_product_element_data_lable"><?php esc_html_e('File URL', 'rch-woo-import-export'); ?></div>
                                        <div class="rch_product_element_data">
                                            <input class="rch_content_data_input rch_item_meta_downloadable_files" type="text" name="rch_item_meta_downloadable_files" value="">
                                        </div>
                                        <div class="rch_product_element_option_data">
                                            <input class="rch_content_data_input rch_item_downloadable_files_delim" type="text" name="rch_item_downloadable_files_delim" value=",">
                                        </div>
                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Multiple File paths/URLs are comma separated. i.e. <code>http://files.com/1.doc, http://files.com/2.doc</code>.", "rch-woo-import-export"); ?>"></i>
                                    </div>
                                    <div class="rch_product_element_wrapper">
                                        <div class="rch_product_element_data_lable"><?php esc_html_e('File Name', 'rch-woo-import-export'); ?></div>
                                        <div class="rch_product_element_data">
                                            <input class="rch_content_data_input rch_item_meta_downloadable_file_name" type="text" name="rch_item_meta_downloadable_file_name" value="">
                                        </div>
                                        <div class="rch_product_element_option_data">
                                            <input class="rch_content_data_input rch_item_downloadable_file_name_delim" type="text" name="rch_item_downloadable_file_name_delim" value=",">
                                        </div>
                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Multiple File names are comma separated. i.e. <code>1.doc,2.doc</code>.", "rch-woo-import-export"); ?>"></i>
                                    </div>
                                    <div class="rch_product_element_wrapper">
                                        <div class="rch_product_element_data_lable"><?php esc_html_e('Download Limit', 'rch-woo-import-export'); ?></div>
                                        <div class="rch_product_element_data">
                                            <input class="rch_content_data_input rch_item_meta_download_limit" type="text" name="rch_item_meta_download_limit" value="">
                                        </div>
                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Leave blank for unlimited re-downloads.", "rch-woo-import-export"); ?>"></i>
                                    </div>
                                    <div class="rch_product_element_wrapper">
                                        <div class="rch_product_element_data_lable"><?php esc_html_e('Download Expiry', 'rch-woo-import-export'); ?></div>
                                        <div class="rch_product_element_data">
                                            <input class="rch_content_data_input rch_item_meta_download_expiry" type="text" name="rch_item_meta_download_expiry" value="">
                                        </div>
                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Enter the number of days before a download link expires, or leave blank.", "rch-woo-import-export"); ?>"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Tax Status', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_tax_status" checked="checked" name="rch_item_meta_tax_status" id="rch_item_meta_tax_status_none" value="none"/>
                                        <label for="rch_item_meta_tax_status_none" class="rch_radio_label"><?php esc_html_e('None', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_tax_status"  name="rch_item_meta_tax_status" id="rch_item_meta_tax_status_taxable" value="taxable"/>
                                        <label for="rch_item_meta_tax_status_taxable" class="rch_radio_label"><?php esc_html_e('Taxable', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_tax_status" name="rch_item_meta_tax_status" id="rch_item_meta_tax_status_shipping" value="shipping"/>
                                        <label for="rch_item_meta_tax_status_shipping" class="rch_radio_label"><?php esc_html_e('Shipping only', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_tax_status rch_item_meta_tax_status_as_specified" name="rch_item_meta_tax_status" id="rch_item_meta_tax_status_as_specified" value="as_specified"/>
                                        <label for="rch_item_meta_tax_status_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper">
                                            <input type="text" class="rch_content_data_input rch_item_meta_tax_status_as_specified_data" name="rch_item_meta_tax_status_as_specified_data" value=""/>
                                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Value should be the slug for the tax status - 'taxable', 'shipping', and 'none' are the default slugs.", "rch-woo-import-export"); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Tax Class', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <select class="rch_content_data_select rch_item_dropdown_as_specified rch_item_meta_tax_class" name="rch_item_meta_tax_class">
                                            <option value="" ><?php esc_html_e('Standard', 'rch-woo-import-export'); ?></option>
                                            <?php
                                            if (!empty($rch_product_tax_class)) {

                                                foreach ($rch_product_tax_class as $class) {
                                                    ?>
                                                    <option value="<?php echo esc_attr(sanitize_title($class)); ?>" > <?php echo esc_html($class); ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                            <option value="as_specified" ><?php esc_html_e('As Specified', 'rch-woo-import-export'); ?></option>
                                        </select>
                                        <div class="rch_item_as_specified_wrapper rch_as_specified_wrapper rch_hide">
                                            <input type="text" class="rch_content_data_input rch_item_meta_tax_class_as_specified_data" name="rch_item_meta_tax_class_as_specified_data" value=""/>
                                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Value should be the slug for the tax class - 'reduced-rate' and 'zero-rate', are the default slugs.", "rch-woo-import-export"); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rch_product_data_container rch_product_inventory_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Manage stock?', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_manage_stock" name="rch_item_meta_manage_stock" id="rch_item_meta_manage_stock_yes" value="yes"/>
                                        <label for="rch_item_meta_manage_stock_yes" class="rch_radio_label"><?php esc_html_e('Yes', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper ">
                                        <input type="radio" class="rch_radio rch_item_meta_manage_stock" name="rch_item_meta_manage_stock"  checked="checked" id="rch_item_meta_manage_stock_no" value="no"/>
                                        <label for="rch_item_meta_manage_stock_no" class="rch_radio_label"><?php esc_html_e('No', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_manage_stock rch_item_meta_manage_stock_as_specified" name="rch_item_meta_manage_stock" id="rch_item_meta_manage_stock_as_specified" value="as_specified"/>
                                        <label for="rch_item_meta_manage_stock_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper">
                                            <input type="text" class="rch_content_data_input rch_item_meta_manage_stock_as_specified_data" name="rch_item_meta_manage_stock_as_specified_data" value=""/>
                                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('yes', 'no').", "rch-woo-import-export"); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container rch_product_stock_qty_container ">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_wrapper">
                                        <div class="rch_product_element_data_lable"><?php esc_html_e('Stock Qty', 'rch-woo-import-export'); ?></div>
                                        <div class="rch_product_element_data">
                                            <input class="rch_content_data_input rch_item_meta_stock" type="text" name="rch_item_meta_stock" value="">
                                        </div>
                                    </div>
                                    <div class="rch_product_element_wrapper">
                                        <div class="rch_product_element_data_lable"><?php esc_html_e('Low stock threshold', 'rch-woo-import-export'); ?></div>
                                        <div class="rch_product_element_data">
                                            <input class="rch_content_data_input rch_item_meta_low_stock_amount" type="text" name="rch_item_meta_low_stock_amount" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Stock status', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_stock_status" name="rch_item_meta_stock_status" id="rch_item_meta_stock_status_instock" value="instock"/>
                                        <label for="rch_item_meta_stock_status_instock" class="rch_radio_label"><?php esc_html_e('In stock', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_stock_status" name="rch_item_meta_stock_status" id="rch_item_meta_stock_status_outofstock" value="outofstock"/>
                                        <label for="rch_item_meta_stock_status_outofstock" class="rch_radio_label"><?php esc_html_e('Out of stock', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_stock_status" name="rch_item_meta_stock_status"  checked="checked" id="rch_item_meta_stock_status_auto" value="auto"/>
                                        <label for="rch_item_meta_stock_status_auto" class="rch_radio_label"><?php esc_html_e('Set automatically', 'rch-woo-import-export'); ?></label>
                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Set the stock status to In Stock for positive or blank Stock Qty values, and Out Of Stock if Stock Qty is 0.", "rch-woo-import-export"); ?>"></i>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_stock_status rch_item_meta_stock_status_as_specified" name="rch_item_meta_stock_status" id="rch_item_meta_stock_status_as_specified" value="as_specified"/>
                                        <label for="rch_item_meta_stock_status_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper">
                                            <input type="text" class="rch_content_data_input rch_item_meta_stock_status_as_specified_data" name="rch_item_meta_stock_status_as_specified_data" value=""/>
                                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('instock', 'outofstock').", "rch-woo-import-export"); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Allow Backorders?', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_backorders" name="rch_item_meta_backorders"  checked="checked" id="rch_item_meta_backorders_no" value="no"/>
                                        <label for="rch_item_meta_backorders_no" class="rch_radio_label"><?php esc_html_e('Do not allow', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_backorders" name="rch_item_meta_backorders" id="rch_item_meta_backorders_notify" value="notify"/>
                                        <label for="rch_item_meta_backorders_notify" class="rch_radio_label"><?php esc_html_e('Allow, but notify customer', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_backorders" name="rch_item_meta_backorders" id="rch_item_meta_backorders_yes" value="yes"/>
                                        <label for="rch_item_meta_backorders_yes" class="rch_radio_label"><?php esc_html_e('Allow', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_backorders rch_item_meta_backorders_as_specified" name="rch_item_meta_backorders" id="rch_item_meta_backorders_as_specified" value="as_specified"/>
                                        <label for="rch_item_meta_backorders_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper">
                                            <input type="text" class="rch_content_data_input rch_item_meta_backorders_as_specified_data" name="rch_item_meta_backorders_as_specified_data" value=""/>
                                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value of should be one of the following: ('no', 'notify', 'yes').", "rch-woo-import-export"); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Sold Individually?', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_sold_individually" name="rch_item_meta_sold_individually" id="rch_item_meta_sold_individually_yes" value="yes"/>
                                        <label for="rch_item_meta_sold_individually_yes" class="rch_radio_label"><?php esc_html_e('Yes', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_sold_individually" name="rch_item_meta_sold_individually"  checked="checked" id="rch_item_meta_sold_individually_no" value="no"/>
                                        <label for="rch_item_meta_sold_individually_no" class="rch_radio_label"><?php esc_html_e('No', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_sold_individually rch_item_meta_sold_individually_as_specified" name="rch_item_meta_sold_individually" id="rch_item_meta_sold_individually_as_specified" value="as_specified"/>
                                        <label for="rch_item_meta_sold_individually_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper">
                                            <input type="text" class="rch_content_data_input rch_item_meta_sold_individually_as_specified_data" name="rch_item_meta_sold_individually_as_specified_data" value=""/>
                                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('yes', 'no').", "rch-woo-import-export"); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rch_product_data_container rch_product_shipping_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html(__('Weight', 'rch-woo-import-export') . " (" . get_option('woocommerce_weight_unit') . ")"); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_meta_weight" type="text" name="rch_item_meta_weight" value="">
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper rch_product_dimensions">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html(__('Dimensions', 'rch-woo-import-export') . " (" . get_option('woocommerce_dimension_unit') . ")"); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_meta_length" type="text" name="rch_item_meta_length"  placeholder="<?php esc_attr_e('Length', 'rch-woo-import-export'); ?>" value="">
                                        <input class="rch_content_data_input rch_item_meta_width" type="text" name="rch_item_meta_width"  placeholder="<?php esc_attr_e('Width', 'rch-woo-import-export'); ?>" value="">
                                        <input class="rch_content_data_input rch_item_meta_height" type="text" name="rch_item_meta_height"  placeholder="<?php esc_attr_e('Height', 'rch-woo-import-export'); ?>" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_field_mapping_other_option_wrapper">
                                    <input type="radio" class="rch_radio rch_item_product_shipping_class_logic" name="rch_item_product_shipping_class_logic" checked="checked" id="rch_item_product_shipping_class_defined" value="defined"/>
                                    <label for="rch_item_product_shipping_class_defined" class="rch_radio_label"><?php esc_html_e('Shipping Class', 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container">
                                        <?php
                                        $args = array(
                                            'taxonomy' => 'product_shipping_class',
                                            'hide_empty' => 0,
                                            'show_option_none' => __('No shipping class', 'rch-woo-import-export'),
                                            'name' => 'rch_item_product_shipping_class',
                                            'id' => 'rch_item_product_shipping_class',
                                            'selected' => "",
                                            'class' => 'rch_content_data_select rch_item_product_shipping_class'
                                        );

                                        wp_dropdown_categories($args);

                                        unset($args);
                                        ?>
                                    </div>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <input type="radio" class="rch_radio rch_item_product_shipping_class_logic rch_item_product_shipping_class_logic_as_specified" name="rch_item_product_shipping_class_logic" id="rch_item_product_shipping_class_logic_as_specified" value="as_specified"/>
                                    <label for="rch_item_product_shipping_class_logic_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container rch_as_specified_wrapper">
                                        <input type="text" class="rch_content_data_input rch_item_product_shipping_class_as_specified_data" name="rch_item_product_shipping_class_as_specified_data" value=""/>
                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('yes', 'no').", "rch-woo-import-export"); ?>"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rch_product_data_container rch_product_linked_products_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Up-Sells', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_meta_upsell_ids" type="text" name="rch_item_meta_upsell_ids" value="">
                                    </div>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Products can be matched by SKU, ID, or Title, and must be comma separated.", "rch-woo-import-export"); ?>"></i>
                                </div>
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Cross-Sells', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_meta_crosssell_ids" type="text" name="rch_item_meta_crosssell_ids" value="">
                                    </div>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Products can be matched by SKU, ID, or Title, and must be comma separated.", "rch-woo-import-export"); ?>"></i>
                                </div>

                            </div>
                        </div>
                        <div class="rch_product_data_container rch_product_attributes_wrapper">
                            <div class="rch_product_element_data_container rch_attr_data_wrapper">
                                <div class="rch_product_element_wrapper  ">
                                    <div class="rch_product_attr_data rch_product_attr_data_label"><?php echo esc_html_e('Name', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_attr_data rch_product_attr_data_label">
                                        <?php echo esc_html_e('Values', 'rch-woo-import-export'); ?>
                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Separate multiple values with a |", "rch-woo-import-export"); ?>"></i>
                                    </div>
                                </div>
                                <div class="rch_attr_data_outer_container">
                                    <div class="rch_attr_data_container">
                                        <div class="rch_product_element_wrapper">
                                            <div class="rch_product_attr_data">
                                                <input class="rch_content_data_input rch_product_attr_name" type="text" name="rch_product_attr_name[0]" value="">
                                            </div>
                                            <div class="rch_product_attr_data">
                                                <input class="rch_content_data_input rch_product_attr_value" type="text" name="rch_product_attr_value[0]" value="">
                                            </div>
                                            <div class="rch_delete_attr_wrapper"><i class="fas fa-trash rch_trash_general_btn_icon rch_delete_attr_data" aria-hidden="true"></i></div>
                                        </div>
                                        <div class="rch_product_element_wrapper">
                                            <div class="rch_product_attr_data">
                                                <div class="rch_field_mapping_other_option_wrapper">
                                                    <input type="radio" class="rch_radio  rch_attr_in_variations" name="rch_attr_in_variations[0]" checked="checked" id="rch_attr_in_variations_yes_0" value="yes"/>
                                                    <label for="rch_attr_in_variations_yes_0" class="rch_radio_label rch_attr_in_variations"><?php esc_html_e('Used for variations', 'rch-woo-import-export'); ?></label>
                                                </div>
                                                <div class="rch_field_mapping_other_option_wrapper ">
                                                    <input type="radio" class="rch_radio rch_attr_in_variations" name="rch_attr_in_variations[0]"  id="rch_attr_in_variations_no_0" value="no"/>
                                                    <label for="rch_attr_in_variations_no_0" class="rch_radio_label rch_attr_in_variations"><?php esc_html_e('Not Used for Variations', 'rch-woo-import-export'); ?></label>
                                                </div>
                                                <div class="rch_field_mapping_other_option_wrapper">
                                                    <input type="radio" class="rch_radio rch_attr_in_variations rch_attr_in_variations_as_specified" name="rch_attr_in_variations[0]" id="rch_attr_in_variations_as_specified_0" value="as_specified"/>
                                                    <label for="rch_attr_in_variations_as_specified_0" class="rch_radio_label rch_attr_in_variations rch_attr_in_variations_as_specified"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                                    <div class="rch_radio_container rch_as_specified_wrapper">
                                                        <input type="text" class="rch_content_data_input rch_attr_in_variations_as_specified_data" name="rch_attr_in_variations_as_specified_data[0]" value=""/>
                                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('yes', 'no').", "rch-woo-import-export"); ?>"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rch_product_attr_data">
                                                <div class="rch_field_mapping_other_option_wrapper">
                                                    <input type="radio" class="rch_radio rch_attr_is_visible" name="rch_attr_is_visible[0]" checked="checked" id="rch_attr_is_visible_yes_0" value="yes"/>
                                                    <label for="rch_attr_is_visible_yes_0" class="rch_radio_label"><?php esc_html_e('Is Visible', 'rch-woo-import-export'); ?></label>
                                                </div>
                                                <div class="rch_field_mapping_other_option_wrapper ">
                                                    <input type="radio" class="rch_radio rch_attr_is_visible" name="rch_attr_is_visible[0]" id="rch_attr_is_visible_no_0" value="no"/>
                                                    <label for="rch_attr_is_visible_no_0" class="rch_radio_label"><?php esc_html_e('Not Visible', 'rch-woo-import-export'); ?></label>
                                                </div>
                                                <div class="rch_field_mapping_other_option_wrapper">
                                                    <input type="radio" class="rch_radio  rch_attr_is_visible rch_attr_is_visible_as_specified" name="rch_attr_is_visible[0]" id="rch_attr_is_visible_as_specified_0" value="as_specified"/>
                                                    <label for="rch_attr_is_visible_as_specified_0" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                                    <div class="rch_radio_container rch_as_specified_wrapper">
                                                        <input type="text" class="rch_content_data_input rch_attr_is_visible_as_specified_data" name="rch_attr_is_visible_as_specified_data[0]" value=""/>
                                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('yes', 'no').", "rch-woo-import-export"); ?>"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rch_product_element_wrapper">
                                            <div class="rch_product_attr_data">
                                                <div class="rch_field_mapping_other_option_wrapper">
                                                    <input type="radio" class="rch_radio rch_attr_is_taxonomy" name="rch_attr_is_taxonomy[0]" checked="checked" id="rch_attr_is_taxonomy_yes_0" value="yes"/>
                                                    <label for="rch_attr_is_taxonomy_yes_0" class="rch_radio_label"><?php esc_html_e('Is Taxonomy', 'rch-woo-import-export'); ?></label>
                                                </div>
                                                <div class="rch_field_mapping_other_option_wrapper ">
                                                    <input type="radio" class="rch_radio rch_attr_is_taxonomy" name="rch_attr_is_taxonomy[0]" id="rch_attr_is_taxonomy_no_0" value="no"/>
                                                    <label for="rch_attr_is_taxonomy_no_0" class="rch_radio_label"><?php esc_html_e('Not Taxonomy', 'rch-woo-import-export'); ?></label>
                                                </div>
                                                <div class="rch_field_mapping_other_option_wrapper">
                                                    <input type="radio" class="rch_radio rch_attr_is_taxonomy rch_attr_is_taxonomy_as_specified" name="rch_attr_is_taxonomy[0]" id="rch_attr_is_taxonomy_as_specified_0" value="as_specified"/>
                                                    <label for="rch_attr_is_taxonomy_as_specified_0" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                                    <div class="rch_radio_container rch_as_specified_wrapper">
                                                        <input type="text" class="rch_content_data_input rch_attr_is_taxonomy_as_specified_data" name="rch_attr_is_taxonomy_as_specified_data[0]" value=""/>
                                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('yes', 'no').", "rch-woo-import-export"); ?>"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rch_product_attr_data">
                                                <div class="rch_field_mapping_other_option_wrapper">
                                                    <input type="radio" class="rch_radio  rch_attr_is_auto_create_term" name="rch_attr_is_auto_create_term[0]" checked="checked" id="rch_attr_is_auto_create_term_yes_0" value="yes"/>
                                                    <label for="rch_attr_is_auto_create_term_yes_0" class="rch_radio_label"><?php esc_html_e('Auto-Create Terms', 'rch-woo-import-export'); ?></label>
                                                </div>
                                                <div class="rch_field_mapping_other_option_wrapper ">
                                                    <input type="radio" class="rch_radio rch_attr_is_auto_create_term" name="rch_attr_is_auto_create_term[0]"  id="rch_attr_is_auto_create_term_no_0" value="no"/>
                                                    <label for="rch_attr_is_auto_create_term_no_0" class="rch_radio_label"><?php esc_html_e('Do Not Create Terms', 'rch-woo-import-export'); ?></label>
                                                </div>
                                                <div class="rch_field_mapping_other_option_wrapper">
                                                    <input type="radio" class="rch_radio rch_attr_is_auto_create_term rch_attr_is_auto_create_term_as_specified" name="rch_attr_is_auto_create_term[0]" id="rch_attr_is_auto_create_term_as_specified_0" value="as_specified"/>
                                                    <label for="rch_attr_is_auto_create_term_as_specified_0" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                                    <div class="rch_radio_container rch_as_specified_wrapper">
                                                        <input type="text" class="rch_content_data_input rch_attr_is_auto_create_term_as_specified_data" name="rch_attr_is_auto_create_term_as_specified_data[0]" value=""/>
                                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('yes', 'no').", "rch-woo-import-export"); ?>"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_btn rch_btn_primary rch_import_attr_add_more_btn">
                                        <?php esc_html_e('Add More', 'rch-woo-import-export'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rch_product_data_container rch_product_variations_wrapper">
                            <div class="rch_product_element_data_container rch_variation_import_method_wrapper">
                                <div class="rch_field_mapping_other_option_wrapper">
                                    <input type="radio" class="rch_radio rch_item_variation_import_method rch_item_variation_import_method_match_unique_field" name="rch_item_variation_import_method" id="rch_item_variation_import_method_match_unique_field" value="match_unique_field"/>
                                    <label for="rch_item_variation_import_method_match_unique_field" class="rch_radio_label"><?php esc_html_e("All my variable products have SKUs or some other unique identifier. Each variation is linked to its parent with its parent's SKU or other unique identifier.", 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container">
                                        <div class="rch_product_element_wrapper">
                                            <div class="rch_product_element_data_lable"><?php echo esc_html_e('SKU element for parent', 'rch-woo-import-export'); ?></div>
                                            <div class="rch_product_element_data">
                                                <input class="rch_content_data_input rch_item_product_variation_field_parent" type="text" name="rch_item_product_variation_field_parent" value="">
                                            </div>
                                        </div>
                                        <div class="rch_product_element_wrapper">
                                            <div class="rch_product_element_data_lable"><?php echo esc_html_e('Parent SKU element for variation', 'rch-woo-import-export'); ?></div>
                                            <div class="rch_product_element_data">
                                                <input class="rch_content_data_input rch_item_product_variation_match_unique_field_parent" type="text" name="rch_item_product_variation_match_unique_field_parent" value="">
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="rch_field_mapping_other_option_wrapper">
                                    <input type="radio" class="rch_radio rch_item_variation_import_method rch_item_variation_import_method_match_group_field" name="rch_item_variation_import_method" id="rch_item_variation_import_method_match_group_field" value="match_group_field"/>
                                    <label for="rch_item_variation_import_method_match_group_field" class="rch_radio_label"><?php esc_html_e("All products with variations are grouped with a unique value that is the same for each variation and unique for each product.", 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container">
                                        <div class="rch_product_element_wrapper">
                                            <div class="rch_product_element_data_lable"><?php echo esc_html_e('Unique Value', 'rch-woo-import-export'); ?></div>
                                            <div class="rch_product_element_data">
                                                <input class="rch_content_data_input rch_item_product_variation_match_group_field" type="text" name="rch_item_product_variation_match_group_field" value="">
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="rch_field_mapping_other_option_wrapper">
                                    <input type="radio" class="rch_radio rch_item_variation_import_method rch_item_variation_import_method_match_title_field" name="rch_item_variation_import_method" id="rch_item_variation_import_method_match_title_field" value="match_title_field"/>
                                    <label for="rch_item_variation_import_method_match_title_field" class="rch_radio_label"><?php esc_html_e("All variations for a particular product have the same title as the parent product.", 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container">
                                        <div class="rch_product_element_wrapper">
                                            <div class="rch_product_element_data_lable"><?php echo esc_html_e('Product Title', 'rch-woo-import-export'); ?></div>
                                            <div class="rch_product_element_data">
                                                <input class="rch_content_data_input rch_item_variation_import_method_title_field" type="text" name="rch_item_variation_import_method_title_field" value="">
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="rch_field_mapping_other_option_wrapper">
                                    <input type="radio" class="rch_radio rch_item_variation_import_method rch_item_variation_import_method_match_title_field_no_parent" name="rch_item_variation_import_method" id="rch_item_variation_import_method_match_title_field_no_parent" value="match_title_field_no_parent"/>
                                    <label for="rch_item_variation_import_method_match_title_field_no_parent" class="rch_radio_label"><?php esc_html_e("All variations for a particular product have the same title. There are no parent products.", 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container">
                                        <div class="rch_product_element_wrapper">
                                            <div class="rch_product_element_data_lable"><?php echo esc_html_e('Product Title', 'rch-woo-import-export'); ?></div>
                                            <div class="rch_product_element_data">
                                                <input class="rch_content_data_input rch_item_variation_import_method_title_field_no_parent" type="text" name="rch_item_variation_import_method_title_field_no_parent" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Variation Enabled', 'rch-woo-import-export'); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("This option is the same as the Enabled checkbox when editing an individual variation in WooCommerce.", "rch-woo-import-export"); ?>"></i></div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_variation_enable rch_item_variation_enable_yes" name="rch_item_variation_enable"  checked="checked" id="rch_item_variation_enable_yes" value="yes"/>
                                        <label for="rch_item_variation_enable_yes" class="rch_radio_label"><?php esc_html_e('Yes', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_variation_enable rch_item_variation_enable_no" name="rch_item_variation_enable" id="rch_item_variation_enable_no" value="no"/>
                                        <label for="rch_item_variation_enable_no" class="rch_radio_label"><?php esc_html_e('No', 'rch-woo-import-export'); ?></label>
                                    </div>

                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_variation_enable rch_item_variation_enable_as_specified" name="rch_item_variation_enable" id="rch_item_variation_enable_as_specified" value="as_specified"/>
                                        <label for="rch_item_variation_enable_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper">
                                            <input type="text" class="rch_content_data_input rch_item_variation_enable_as_specified_data" name="rch_item_variation_enable_as_specified_data" value=""/>
                                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('yes', 'no').", "rch-woo-import-export"); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_other_option_wrapper ">
                                        <input type="checkbox" value="1" name="rch_item_first_variation_as_default" id="rch_item_first_variation_as_default" checked="checked" class="rch_checkbox rch_item_first_variation_as_default">
                                        <label class="rch_checkbox_label" for="rch_item_first_variation_as_default"><?php esc_html_e('Set first variation as the default selection.', 'rch-woo-import-export'); ?></label>
                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The attributes for the first variation will be automatically selected on the frontend.", "rch-woo-import-export"); ?>"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rch_product_data_container rch_product_advanced_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_product_element_data_lable"><?php echo esc_html_e('Purchase Note', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_product_element_data">
                                        <input class="rch_content_data_input rch_item_meta_purchase_note" type="text" name="rch_item_meta_purchase_note" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Featured', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_featured" name="rch_item_meta_featured" id="is_product_featured_yes" value="yes"/>
                                        <label for="is_product_featured_yes" class="rch_radio_label"><?php esc_html_e('Yes', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper ">
                                        <input type="radio" class="rch_radio rch_item_meta_featured" name="rch_item_meta_featured"  checked="checked" id="is_product_featured_no" value="no"/>
                                        <label for="is_product_featured_no" class="rch_radio_label"><?php esc_html_e('No', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_featured rch_item_meta_featured_as_specified" name="rch_item_meta_featured" id="is_product_featured_as_specified" value="as_specified"/>
                                        <label for="is_product_featured_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper">
                                            <input type="text" class="rch_content_data_input rch_item_meta_featured_as_specified_data" name="rch_item_meta_featured_as_specified_data" value=""/>
                                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('yes', 'no').", "rch-woo-import-export"); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Catalog visibility', 'rch-woo-import-export'); ?></div>
                                    <?php
                                    if (function_exists('wc_get_product_visibility_options')) {

                                        $visibility_options = wc_get_product_visibility_options();

                                        if (!empty($visibility_options)) {
                                            $check = 'checked="checked"';
                                            foreach ($visibility_options as $visibility_option_key => $visibility_option_name) {
                                                ?>
                                                <div class="rch_field_mapping_other_option_wrapper">
                                                    <input type="radio" class="rch_radio rch_item_meta_visibility" name="rch_item_meta_visibility" <?php echo $check; ?> id="rch_item_meta_visibility_<?php echo esc_attr($visibility_option_key) ?>" value="<?php echo esc_attr($visibility_option_key) ?>"/>
                                                    <label for="rch_item_meta_visibility_<?php echo esc_attr($visibility_option_key) ?>" class="rch_radio_label"><?php echo esc_html($visibility_option_name); ?></label>
                                                </div>
                                                <?php
                                                $check = "";
                                            }
                                            unset($check);
                                        }
                                        unset($visibility_options);
                                    } else {
                                        ?>
                                        <div class="rch_field_mapping_other_option_wrapper ">
                                            <input type="radio" class="rch_radio rch_item_meta_visibility" name="rch_item_meta_visibility" checked="checked" id="rch_item_meta_visibility_visible" value="visible"/>
                                            <label for="rch_item_meta_visibility_visible" class="rch_radio_label"><?php esc_html_e('Shop and search results', 'rch-woo-import-export'); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper ">
                                            <input type="radio" class="rch_radio rch_item_meta_visibility" name="rch_item_meta_visibility" id="rch_item_meta_visibility_catalog" value="catalog"/>
                                            <label for="rch_item_meta_visibility_catalog" class="rch_radio_label"><?php esc_html_e('Shop only', 'rch-woo-import-export'); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper ">
                                            <input type="radio" class="rch_radio rch_item_meta_visibility" name="rch_item_meta_visibility" id="rch_item_meta_visibility_search" value="search"/>
                                            <label for="rch_item_meta_visibility_search" class="rch_radio_label"><?php esc_html_e('Search results only', 'rch-woo-import-export'); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper ">
                                            <input type="radio" class="rch_radio rch_item_meta_visibility" name="rch_item_meta_visibility" id="rch_item_meta_visibility_hidden" value="hidden"/>
                                            <label for="rch_item_meta_visibility_hidden" class="rch_radio_label"><?php esc_html_e('Hidden', 'rch-woo-import-export'); ?></label>
                                        </div>
                                    <?php } ?>

                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_meta_visibility rch_item_meta_visibility_as_specified" name="rch_item_meta_visibility" id="rch_item_meta_visibility_as_specified" value="as_specified"/>
                                        <label for="rch_item_meta_visibility_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper">
                                            <input type="text" class="rch_content_data_input rch_item_meta_visibility_as_specified_data" name="rch_item_meta_visibility_as_specified_data" value=""/>
                                            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('visible', 'catalog', 'search', 'hidden').", "rch-woo-import-export"); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rch_product_data_container rch_product_extra_wrapper">
                            <div class="rch_product_element_data_container">
                                <div class="rch_product_element_wrapper">
                                    <div class="rch_field_mapping_other_option_wrapper ">
                                        <input type="checkbox" value="1" name="rch_item_meta_disable_auto_sku" id="rch_item_meta_disable_auto_sku"  class="rch_checkbox rch_item_meta_disable_auto_sku">
                                        <label class="rch_checkbox_label" for="rch_item_meta_disable_auto_sku"><?php esc_html_e('Disable auto SKU generation', 'rch-woo-import-export'); ?></label>
                                        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Plugin will NOT automatically generate the SKU for each product, if SKU option is empty.", "rch-woo-import-export"); ?>"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $product_section = ob_get_clean();

        $field_mapping_sections = array(
            '150' => $product_section,
        );

        unset($rch_product_type);
        unset($rch_product_tax_class);
        unset($product_group);

        return apply_filters("rch_pre_product_field_mapping_section", array_replace($sections, $field_mapping_sections), $rch_import_type);
    }

}