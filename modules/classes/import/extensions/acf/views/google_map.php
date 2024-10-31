<div class="rch_acf_item_wrapper" >
    <div class="rch_item_act_label" ><?php echo esc_html($title); ?></div>
    <div class="rch_item_act_label rch_item_act_label_inner" ><?php esc_html_e('Address', 'rch-woo-import-export'); ?></div>
    <input type="text" class="rch_content_data_input rch_item_acf_link_title" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][value][address]" placeholder=""/>
    <div class="rch_item_act_label rch_item_act_label_inner" ><?php esc_html_e('Lat', 'rch-woo-import-export'); ?></div>
    <input type="text" class="rch_content_data_input rch_item_acf_link_url" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][value][lat]" placeholder=""/>
    <div class="rch_item_act_label rch_item_act_label_inner" ><?php esc_html_e('Lng', 'rch-woo-import-export'); ?></div>
    <input type="text" class="rch_content_data_input rch_item_acf_link_target" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][value][lng]" placeholder=""/>
</div>