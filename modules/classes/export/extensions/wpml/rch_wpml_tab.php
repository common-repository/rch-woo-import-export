<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

$wpml = new SitePress();

$rch_langs = $wpml->get_active_languages();

$random = uniqid();
?>
<div class="rch_section_wrapper rch_hide_if_comment rch_hide_if_shop_coupon rch_hide_if_users">
    <div class="rch_content_data_header">
        <div class="rch_content_title"><?php esc_html_e('WPML', 'rch-woo-import-export'); ?></div>
        <div class="rch_layout_header_icon_wrapper"></div>
    </div>
    <div class="rch_section_content">
        <div class="rch_content_data_wrapper">
            <div class="rch_options_data_title"><?php esc_html_e('Language', 'rch-woo-import-export'); ?></div>
            <div class="rch_options_data_content">
                <div class="rch_wpml_lang_wrapper">
                    <input type="radio" class="rch_radio rch_wpml_lang rch_wpml_lang_all" checked="checked" id="<?php echo esc_attr($random); ?>_wpml_lang_all" name="rch_wpml_lang" value="all" />
                    <label for="<?php echo esc_attr($random); ?>_wpml_lang_all" class="rch_radio_label rch_wpml_lang_lbl"><?php esc_html_e('All', 'rch-woo-import-export'); ?></label>
                </div>
                <?php if (!empty($rch_langs)) { ?>
                    <?php foreach ($rch_langs as $code => $langInfo) { ?>
                        <div class="rch_wpml_lang_wrapper">
                            <input type="radio" class="rch_radio rch_wpml_lang rch_wpml_lang_<?php echo esc_attr($code); ?>" id="<?php echo esc_attr($random . '_wpml_lang_' . $code); ?>" name="rch_wpml_lang" value="<?php echo esc_attr($code); ?>" />
                            <label for="<?php echo esc_attr($random); ?>_wpml_lang_<?php echo esc_attr($code); ?>" class="rch_radio_label rch_wpml_lang_lbl"><img class="rch_wpml_lang_flag_img" src="<?php echo esc_url($wpml->get_flag_url($code)); ?>" /><?php echo esc_html($langInfo['display_name']); ?></label>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>