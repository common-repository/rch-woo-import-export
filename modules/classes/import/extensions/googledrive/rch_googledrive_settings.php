<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}
$rch_options = get_option('rch_import_googledrive_file_upload');

$developer_key = "";
$client_id = "";

if (!empty($rch_options)) {

    $rch_options = maybe_unserialize($rch_options);

    $developer_key = isset($rch_options['rch_gd_developer_key']) ? $rch_options['rch_gd_developer_key'] : "";

    $client_id = isset($rch_options['rch_gd_client_id']) ? $rch_options['rch_gd_client_id'] : "";
}
?>
<div class="rch_element_full_wrapper">
    <div class="rch_element_title">
        <?php esc_html_e('API Key', 'rch-woo-import-export'); ?>
        <div class="rch_import_title_hint">
            <a class="rch_import_title_hint_link" target="_blank" href="https://developers.google.com/picker/docs/"><?php esc_html_e('Picker Doc and API key', 'rch-woo-import-export'); ?></a>
        </div>
    </div>
    <div class="rch_element_data">
        <input type="text" class="rch_content_data_input rch_content_data_rule_value" name="rch_gd_developer_key" value="<?php echo esc_attr($developer_key); ?>"/>
    </div>
</div>
<div class="rch_element_full_wrapper">
    <div class="rch_element_title"><?php esc_html_e('Client Id', 'rch-woo-import-export'); ?></div>
    <div class="rch_element_data">
        <input type="text" class="rch_content_data_input rch_content_data_rule_value" name="rch_gd_client_id" value="<?php echo esc_attr($client_id); ?>"/>
    </div>
</div>
