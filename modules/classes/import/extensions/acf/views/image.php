<div class="rch_acf_item_wrapper" >
    <div class="rch_item_act_label" ><?php echo esc_html($title); ?></div>
    <input type="text" class="rch_content_data_input rch_item_acf_text" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][value]" placeholder=""/>
    <div class="rch_field_mapping_radio_input_wrapper">
        <input type="checkbox" class="rch_update_data_inner_option rch_checkbox rch_item_acf_content_search_through_media_<?php echo esc_attr($field_key) ?>" name="acf[<?php echo esc_attr($field_key) ?>][search_through_media]" id="rch_item_acf_content_search_through_media_<?php echo esc_attr($field_key) ?>" value="1"/>
        <label for="rch_item_acf_content_search_through_media_<?php echo esc_attr($field_key) ?>" class="rch_checkbox_label"><?php esc_html_e(' Search through the Media Library for existing images before importing new images', 'rch-woo-import-export'); ?></label>    
        <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("If an image with the same file name is found in the Media Library then that image will be attached to this record instead of importing a new image. Disable this setting if your import has different images with the same file name.", "rch-woo-import-export"); ?>"></i>
    </div>
    <div class="rch_field_mapping_radio_input_wrapper">
        <input type="checkbox" class="rch_update_data_inner_option rch_checkbox rch_item_acf_content_search_through_upload_dir_<?php echo esc_attr($field_key) ?>" name="acf[<?php echo esc_attr($field_key) ?>][use_upload_dir]" id="rch_item_acf_content_search_through_upload_dir_<?php echo esc_attr($field_key) ?>" value="1"/>
        <label for="rch_item_acf_content_search_through_upload_dir_<?php echo esc_attr($field_key) ?>" class="rch_checkbox_label"><?php echo esc_html(__('Use images currently uploaded in', 'rch-woo-import-export') . " " . RCH_UPLOAD_TEMP_DIR); ?></label>
    </div>
</div>