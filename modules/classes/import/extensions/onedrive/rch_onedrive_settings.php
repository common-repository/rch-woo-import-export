<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}
$rch_options = get_option('rch_import_onedrive_file_upload');

$_key = "";
if (!empty($rch_options)) {
    $rch_options = maybe_unserialize($rch_options);
    $_key = isset($rch_options['rch_onedrive_client_id']) ? $rch_options['rch_onedrive_client_id'] : "";
}
?>
<div class="rch_element_full_wrapper">
    <div class="rch_element_title">
        <?php esc_html_e('Onedrive Client Id', 'rch-woo-import-export'); ?>
         <div class="rch_import_title_hint">
            <a class="rch_import_title_hint_link" target="_blank" href="https://docs.microsoft.com/en-us/onedrive/developer/controls/file-pickers/js-v72/?view=odsp-graph-online"><?php esc_html_e('File Picker Doc and Client Id', 'rch-woo-import-export'); ?></a>
        </div>
    </div>
    <div class="rch_element_data">
        <input type="text" class="rch_content_data_input rch_content_data_rule_value" name="rch_onedrive_client_id" value="<?php echo esc_attr($_key); ?>"/>
    </div>
</div>
