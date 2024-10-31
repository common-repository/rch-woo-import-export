<?php
if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (!function_exists("rch_import_order_search_existing_item")) {

        function rch_import_order_search_existing_item($sections = "", $rch_import_type = "") {

                ob_start();
                ?>
                <div class="rch_field_mapping_container_element">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Search Existing Item on your site based on...', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_existing_item_search_logic rch_existing_item_search_logic_cf"  name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_cf" value="cf"/>
                        <label for="rch_existing_item_search_logic_cf" class="rch_radio_label"><?php esc_html_e('Custom field', 'rch-woo-import-export'); ?></label>
                        <div class="rch_radio_container">
                            <table class="rch_search_based_on_cf_table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Name', 'rch-woo-import-export'); ?></th>
                                        <th><?php esc_html_e('Value', 'rch-woo-import-export'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" class="rch_content_data_input rch_existing_item_search_logic_cf_key" name="rch_existing_item_search_logic_cf_key" value=""/></td>
                                        <td><input type="text" class="rch_content_data_input rch_existing_item_search_logic_cf_value" name="rch_existing_item_search_logic_cf_value" value=""/></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" checked="checked" class="rch_radio rch_field_mapping_other_option_radio "  name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_id" value="id"/>
                        <label for="rch_existing_item_search_logic_id" class="rch_radio_label"><?php esc_html_e('Order ID', 'rch-woo-import-export'); ?></label>
                        <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_existing_item_search_logic_id" name="rch_existing_item_search_logic_id" value=""/></div>
                    </div>
                </div>
                <?php
                return ob_get_clean();
        }

}