<div class="rch_acf_item_wrapper rch_acf_item_post_object_wrapper" >
    <div class="rch_item_act_label" ><?php echo esc_html($title); ?></div>
    <input type="text" class="rch_content_data_input rch_item_acf_text rch_content_data_input_medium" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][value]" placeholder=""/>
    <input type="text" class="rch_content_data_input rch_field_mapping_input_separator rch_item_acf_post_object_delim rch_item_acf_user_delim_<?php echo esc_attr($field_key) ?>" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][delim]" placeholder="," value=","/>
    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Specify the user ID, username, or user e-mail address. Separate multiple values with commas.", "rch-woo-import-export"); ?>"></i>
</div>