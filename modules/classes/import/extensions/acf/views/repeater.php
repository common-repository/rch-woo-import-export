<?php
$rch_acf_fields = new \rch\import\acf\RCH_ACF();
?>
<div class="rch_item_act_inner_wrapper" data-key="<?php echo esc_attr($field_key) ?>">
    <div class="rch_item_act_label" ><?php echo esc_html($title); ?></div>
    <div class="rch_field_mapping_radio_input_wrapper">
        <input type="radio" class="rch_radio rch_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" checked="checked" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][value_option]" id="rch_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" value="direct"/>
        <label for="rch_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" class="rch_radio_label"><?php esc_html_e('Select value for all records', 'rch-woo-import-export'); ?></label>
        <div class="rch_radio_container  rch_repreater_direct_container">
            <div class="rch_acf_repreater_wrapper rch_acf_layout_data_wrapper">       
                <div class="rch_acf_repeater_container rch_acf_layout_data_container" data-container="rch_acf_repeater_<?php echo isset($field['ID']) ? esc_attr($field['ID']) : 0; ?>">
                    <?php
                    if (isset($field['sub_fields']) && !empty($field['sub_fields'])) {
                            foreach ($field['sub_fields'] as $field_data) {
                                    $rch_acf_fields->get_acf_field_views($field_data, $field, true);
                            }
                    }
                    ?>
                </div>
            </div>
            <div class="rch_acf_fc_layout_action_wrapper">
                <div class="rch_acf_fc_btn_wrapper">
                    <div class="rch_btn rch_btn_primary rch_acf_repeater_add_row" data-container="rch_acf_repeater_<?php echo isset($field['ID']) ? esc_attr($field['ID']) : 0; ?>" >
                        <?php esc_html_e('Add Row', 'rch-woo-import-export'); ?>
                    </div>
                    <div class="rch_btn rch_btn_primary rch_acf_repeater_remove_row">
                        <?php esc_html_e('Delete Row', 'rch-woo-import-export'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="rch_field_mapping_radio_input_wrapper">
        <input type="radio" class="rch_radio rch_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?> " name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][value_option]" id="rch_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" value="custom"/>
        <label for="rch_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
        <div class="rch_radio_container rch_as_specified_wrapper">
            <input type="text" class="rch_content_data_input rch_item_acf_choice_custom_data_<?php echo esc_attr($field_key); ?>_data" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][custom_value]" value=""/>
        </div>
    </div>
</div>

<?php
unset($rch_acf_fields);
