<?php
if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}
if (!function_exists("rch_get_acf_fields")) {

        function rch_get_acf_fields($sections = array(), $rch_import_type = "") {

                global $acf;

                if ($acf && isset($acf->settings) && isset($acf->settings['version']) && version_compare($acf->settings['version'], '5.0.0') >= 0) {

                        $fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/acf/class-rch-acf.php';

                        $class = '\rch\import\acf\RCH_ACF';
                } else {
                        return $sections;
                }

                require_once($fileName);

                $rch_acf = new $class();

                $acf_groups = $rch_acf->get_acf_groups();

                unset($fileName, $class);

                ob_start();
                ?>
                <div class="rch_field_mapping_container_wrapper rch_acf_field_container">
                    <div class="rch_field_mapping_container_title"><?php esc_html_e('Advanced Custom Fields', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
                    <div class="rch_field_mapping_container_data">                       
                        <div class="rch_field_mapping_container_element rch_acf_field_outer_wrapper">
                            <div class="rch_field_mapping_radio_input_wrapper rch_cf_notice_wrapper">
                                <input type="checkbox" id="acf_skip_empty" name="skip_empty" checked="checked" value="1" class="rch_checkbox acf_skip_empty">
                                <label class="rch_checkbox_label" for="acf_skip_empty"><?php esc_html_e("Don't add Empty value fields in database.", 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_inner_title rch_acf_group_header"><?php esc_html_e('Please choose your Field Groups.', 'rch-woo-import-export'); ?></div>
                            <?php
                            if (!empty($acf_groups)) {
                                    foreach ($acf_groups as $group_key => $group) {

                                            $group_id = isset($group->ID) ? absint($group->ID) : 0;

                                            $title = isset($group->post_title) ? $group->post_title : "";
                                            ?>
                                            <div class="rch_field_mapping_other_option_wrapper rch_item_add_on_demand_wrapper">
                                                <input id="rch_item_acf_group_<?php echo esc_attr($group_key); ?>" type="checkbox" class="rch_checkbox rch_item_add_on_demand rch_item_acf_group rch_item_acf_group_<?php echo esc_attr($group_key); ?>" name="rch_item_acf_group[<?php echo esc_attr($group_key); ?>]" data-container="rch_acf_group_data_<?php echo esc_attr($group_key); ?>" value="1">
                                                <label for="rch_item_acf_group_<?php echo esc_attr($group_key); ?>" class="rch_checkbox_label"><?php echo esc_html($title); ?></label>
                                                <?php
                                                $acf_fields = $rch_acf->get_acf_field_by_group($group_id);

                                                if (!empty($acf_fields)) {
                                                        ?>
                                                        <div class="rch_checkbox_container rch_acf_field_wrapper rch_acf_group_data_<?php echo esc_attr($group_key); ?>">
                                                            <?php
                                                            $rch_acf->get_acf_fields_view($acf_fields);
                                                            ?>
                                                        </div>
                                                        <?php
                                                }
                                                unset($acf_fields);
                                                ?>
                                            </div>
                                            <?php
                                    }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $acf_section = ob_get_clean();

                $fields = array(
                        '340' => $acf_section,
                );

                $sections = array_replace($sections, $fields);

                unset($acf_section, $fields, $acf_groups, $rch_acf);

                return $sections;
        }

}