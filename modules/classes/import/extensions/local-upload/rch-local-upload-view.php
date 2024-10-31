<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}
?>

<div class="rch_upload_outer_container">
    <div id="rch_upload_container" class="rch_upload_container" >
        <div id="rch_upload_drag_drop" class="rch_upload_drag_drop">
            <div class="rch_upload_file_label"><?php esc_html_e('Drop file here', 'rch-woo-import-export'); ?></div>
            <div class="rch_upload_file_label_small"><?php esc_html_e('OR', 'rch-woo-import-export'); ?></div>
            <div class="rch_upload_file_btn">
                <input id="plupload_browse_button" type="button" value="<?php esc_attr_e('Select Files', 'rch-woo-import-export'); ?>" class="rch_btn rch_btn_primary rch_btn_radius rch_plupload_browse_button" />
            </div>
        </div>
        <input type="hidden" value="" class="rch_upload_drag_drop_data" rch_status="processing"/>
    </div>
    <div class="rch_uploaded_file_list_wrapper">
        <div class="rch_local_uploaded_filename_wrapper">
            <div class="rch_local_uploaded_filename_label"><?php esc_html_e('Uploading', 'rch-woo-import-export'); ?></div>
            <div class="rch_local_uploaded_file_sep">-</div>
            <div class="rch_local_uploaded_filename"></div>
        </div>
        <div class="progress rch_import_upload_process">
            <div class="progress-bar progress-bar-striped progress-bar-animated rch_import_upload_process_per" role="progressbar" style="" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>

    </div>
    <div class="rch_file_list_wrapper"></div>
</div>