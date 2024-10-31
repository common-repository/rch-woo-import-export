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

$is_valid = true;
if (empty($client_id)) {
    $is_valid = false;
}
?>
<div class="rch_upload_outer_container" >
    <input type="hidden" value="" class="rch_upload_final_file" />
    <input type="hidden" value="<?php echo esc_attr($developer_key); ?>" class="rch_gd_developer_key" />
    <input type="hidden" value="<?php echo esc_attr($client_id); ?>" class="rch_gd_client_id" />
    <div  class="rch_file_upload_container">
        <div class="rch_element_full_wrapper">
            <div class="rch_element_title"><?php esc_html_e('Click For Choose File', 'rch-woo-import-export'); ?></div>         
        </div>
        <div class="rch_download_btn_wrapper">
            <?php if ($is_valid) { ?>
                <div class="rch_btn rch_btn_primary rch_google_drive_upload_btn">
                    <?php esc_html_e('Pick From google Drive', 'rch-woo-import-export'); ?>
                </div>
            <?php } ?>
          </div>
    </div>
    <div class="rch_file_list_wrapper"></div>
</div>