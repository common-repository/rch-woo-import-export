<div class="rch_acf_item_wrapper" >
    <div class="rch_item_act_label" ><?php echo esc_html($title); ?></div>
    <div class="rch_field_mapping_radio_input_wrapper">
        <input type="radio" class="rch_radio rch_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" checked="checked" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][value_option]" id="rch_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" value="direct"/>
        <label for="rch_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" class="rch_radio_label"><?php esc_html_e('Select value for all records', 'rch-woo-import-export'); ?></label>
        <div class="rch_radio_container">
            <?php
            $field['class'] = "rch_acf_checkbox_wrapper";

            $field['prefix'] = "acf" . $parent_field_key . "[" . $field_key . "]";

            echo acf_render_field($field);
            ?>
        </div>
    </div>
    <div class="rch_field_mapping_radio_input_wrapper">
        <input type="radio" class="rch_radio rch_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?> " name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][value_option]" id="rch_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" value="custom"/>
        <label for="rch_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
        <div class="rch_radio_container rch_as_specified_wrapper">
            <input type="text" class="rch_content_data_input rch_item_acf_choice_custom_data_<?php echo esc_attr($field_key); ?>_data" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][custom_value]" value=""/>
            <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Specify the value. For multiple values, separate with commas. If the choices are of the format option : Option, option-2 : Option 2, use option and option-2 for values.", "rch-woo-import-export"); ?>"></i>
        </div>
    </div>
</div>