<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}
if (!function_exists("rch_import_get_wpml_tab")) {

    function rch_import_get_wpml_tab($sections = array(), $rch_import_type = "") {

        $wpml = new SitePress();

        $rch_langs = $wpml->get_active_languages();

        $random = uniqid();

        ob_start();
        ?>
        <div class="rch_field_mapping_container_wrapper">
            <div class="rch_field_mapping_container_title"><?php esc_html_e('WPML', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
            <div class="rch_field_mapping_container_data">
                <div class="rch_field_mapping_container_element">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Content Language', 'rch-woo-import-export'); ?></div>
                    <?php if (!empty($rch_langs)) { ?>
                        <?php foreach ($rch_langs as $code => $langInfo) { ?>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_wpml_lang rch_wpml_lang_<?php echo esc_attr($code); ?>" checked="checked" name="rch_wpml_lang_code" id="<?php echo esc_attr($random . '_wpml_lang_' . $code); ?>" value="<?php echo esc_attr($code); ?>"/>
                                <label for="<?php echo esc_attr($random . '_wpml_lang_' . $code); ?>" class="rch_radio_label"><img class="rch_wpml_lang_flag_img" src="<?php echo esc_url($wpml->get_flag_url($code)); ?>" /><?php echo esc_html($langInfo['display_name']); ?></label>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_wpml_lang rch_wpml_lang_as_specified" name="rch_wpml_lang_code" checked="checked" id="rch_wpml_lang_as_specified" value="as_specified"/>
                        <label for="rch_wpml_lang_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                        <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_item_wpml_lang" name="rch_item_wpml_lang" value=""/></div>
                    </div>
                </div>
                <div class="rch_field_mapping_container_element">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Search original language Translation Based On', 'rch-woo-import-export'); ?></div>
                    <?php if ($rch_import_type == "taxonomies") { ?>
                        <div class="rch_field_mapping_other_option_wrapper">
                            <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_wpml_default_item rch_item_wpml_default_item_name" name="rch_item_wpml_default_item" checked="checked" id="rch_item_wpml_default_item_name" value="name"/>
                            <label for="rch_item_wpml_default_item_name" class="rch_radio_label"><?php esc_html_e('original language Name', 'rch-woo-import-export'); ?></label>
                            <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_item_wpml_default_item_name" name="rch_item_wpml_default_item_name" value=""/></div>
                        </div>
                        <div class="rch_field_mapping_other_option_wrapper">
                            <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_wpml_default_item rch_item_wpml_default_item_slug" name="rch_item_wpml_default_item" checked="checked" id="rch_item_wpml_default_item_slug" value="slug"/>
                            <label for="rch_item_wpml_default_item_slug" class="rch_radio_label"><?php esc_html_e('original language Slug', 'rch-woo-import-export'); ?></label>
                            <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_item_wpml_translation_slug" name="rch_item_wpml_translation_slug" value=""/></div>
                        </div>
                    <?php } else { ?>
                        <div class="rch_field_mapping_other_option_wrapper">
                            <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_wpml_default_item rch_item_wpml_default_item_title" name="rch_item_wpml_default_item" checked="checked" id="rch_item_wpml_default_item_title" value="title"/>
                            <label for="rch_item_wpml_default_item_title" class="rch_radio_label"><?php esc_html_e('original language Title', 'rch-woo-import-export'); ?></label>
                            <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_item_wpml_translation_title" name="rch_item_wpml_translation_title" value=""/></div>
                        </div>
                    <?php } ?>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_wpml_default_item rch_item_wpml_default_item_id" name="rch_item_wpml_default_item" id="rch_item_wpml_default_item_id" value="id"/>
                        <label for="rch_item_wpml_default_item_id" class="rch_radio_label"><?php esc_html_e('original language ID', 'rch-woo-import-export'); ?></label>
                        <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_item_wpml_trid" name="rch_item_wpml_trid" value=""/></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $wpml_html = ob_get_clean();

        $wpml_section = array(
            '241' => $wpml_html,
        );

        $sections = array_replace($sections, $wpml_section);

        unset($wpml, $rch_langs, $random, $wpml_section, $wpml_html);

        return $sections;
    }

}