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
unset($rch_options);

$is_valid = true;
if (empty($_key)) {
    $is_valid = false;
}
?>

<div class="rch_upload_outer_container" >
    <input type="hidden" value="" class="rch_upload_final_file" />
    <input type="hidden" value="<?php echo esc_attr($_key); ?>" class="rch_onedrive_client_id" />
    <input type="hidden" value="<?php echo esc_attr(admin_url()); ?>admin.php?page=rch-new-import" class="rch_onedrive_redirect_uri" />
    <div  class="rch_file_upload_container">
        <div class="rch_element_full_wrapper">
            <div class="rch_element_title"><?php esc_html_e('Click For Choose File', 'rch-woo-import-export'); ?></div>         
        </div>
        <div class="rch_download_btn_wrapper">
            <?php if ($is_valid) { ?>
                <div class="rch_btn rch_btn_primary rch_onedrive_upload_btn">
                    <?php esc_html_e('Choose From Onedrive ', 'rch-woo-import-export'); ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="rch_file_list_wrapper"></div>
</div>