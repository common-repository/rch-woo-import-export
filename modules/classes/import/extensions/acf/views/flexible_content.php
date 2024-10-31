<?php
$rch_acf_fields = new \rch\import\acf\RCH_ACF();
?>
<div class="rch_item_act_inner_wrapper" data-key="<?php echo esc_attr($field_key) ?>">
    <div class="rch_item_act_label" ><?php echo esc_html($title); ?></div>

    <div class="rch_field_mapping_radio_input_wrapper">
        <input type="radio" class="rch_radio rch_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" checked="checked" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][value_option]" id="rch_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" value="direct"/>
        <label for="rch_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" class="rch_radio_label"><?php esc_html_e('Select value for all records', 'rch-woo-import-export'); ?></label>
        <div class="rch_radio_container rch_fc_direct_container">
            <div class="rch_acf_fc_layout_wrapper rch_acf_layout_data_wrapper">
                <?php
                if (isset($field['layouts']) && !empty($field['layouts'])) {
                        foreach ($field['layouts'] as $i => $layout) {
                                $id = isset($layout['ID']) ? $layout['ID'] : $i;
                                ?>
                                <div class="rch_acf_cf_layout_container rch_acf_layout_data_container" data-container="rch_acf_cf_layout_<?php echo esc_attr($id); ?>">
                                    <input type="hidden" class="rch_item_acf_fc_<?php echo esc_attr($field_key) ?>" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][rch_row_number][layout]" value="<?php echo esc_attr($id); ?>"/>
                                    <div class="rch_acf_cf_layout_label"><?php echo isset($layout['name']) ? esc_html($layout['name']) : ""; ?></div>
                                    <?php
                                    if (isset($layout['sub_fields']) && !empty($layout['sub_fields'])) {
                                            foreach ($layout['sub_fields'] as $field_data) {
                                                    $rch_acf_fields->get_acf_field_views($field_data, $field, true);
                                            }
                                    }
                                    ?>
                                </div>
                                <?php
                        }
                }
                ?>
            </div>
            <div class="rch_acf_fc_layout_action_wrapper">
                <select class="rch_content_data_select rch_acf_cf_layout_data" >
                    <option selected="selected" value=""><?php esc_html_e('Select Layout', 'rch-woo-import-export'); ?></option>
                    <?php
                    foreach ($field['layouts'] as $key => $layout) {
                            $id = isset($layout['ID']) ? $layout['ID'] : $key;
                            ?>
                            <option value="rch_acf_cf_layout_<?php echo esc_attr($id); ?>"><?php echo esc_html($layout['label']); ?></option>
                    <?php }
                    ?>
                </select>
                <div class="rch_acf_fc_btn_wrapper">
                    <div class="rch_btn rch_btn_primary rch_acf_fc_add_layout">
                        <?php esc_html_e('Add Layout', 'rch-woo-import-export'); ?>
                    </div>
                    <div class="rch_btn rch_btn_primary rch_acf_fc_remove_layout">
                        <?php esc_html_e('Delete Layout', 'rch-woo-import-export'); ?>
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
