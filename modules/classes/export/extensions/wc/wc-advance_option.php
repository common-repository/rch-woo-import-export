<?php
if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}
?>
<td>
    <div class="rch_options_data rch_order_item_row_option_wrapper">
        <div class="rch_options_data_content">
            <input type="checkbox" class="rch_checkbox rch_order_item_sigle_row" id="rch_order_item_sigle_row"  name="rch_order_item_sigle_row" value="1"/>
            <label for="rch_order_item_sigle_row" class="rch_checkbox_label"><?php esc_html_e('Display each product in its own row', 'rch-woo-import-export'); ?></label>
        </div>
        <div class="rch_order_item_fill_empty_wrapper rch_hide">
            <input type="checkbox" class="rch_checkbox rch_order_item_fill_empty" checked="checked" id="rch_order_item_fill_empty" name="rch_order_item_fill_empty" value="1"/>
            <label for="rch_order_item_fill_empty" class="rch_checkbox_label"><?php esc_html_e('Fill in empty columns', 'rch-woo-import-export'); ?></label>
        </div>
    </div>
</td>