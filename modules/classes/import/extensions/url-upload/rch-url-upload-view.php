<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}
?>


<div class="rch_upload_outer_container" >
    <input type="hidden" value="" class="rch_upload_final_file" />
    <input type="hidden" value="" class="rch_upload_final_file_url" name="rch_upload_final_file_url"/>
    <div  class="rch_existing_file_upload_container">
        <div class="rch_element_full_wrapper">
            <div class="rch_element_title"><?php esc_html_e('File URL', 'rch-woo-import-export'); ?></div>
            <div class="rch_element_data">
                <input class="rch_content_data_input rch_file_upload_url" type="text" name="" value="" placeholder="http://xyz.com/sample.csv">
            </div>
        </div>
        <div class="rch_download_btn_wrapper">
            <div class="rch_btn rch_btn_primary rch_url_upload_btn">
                <?php esc_html_e('Download', 'rch-woo-import-export'); ?>
            </div>
        </div>
    </div>
    <div class="rch_file_list_wrapper"></div>
</div>