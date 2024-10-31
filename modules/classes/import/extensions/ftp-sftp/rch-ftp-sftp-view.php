<?php
if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}
?>
<div class="rch_upload_outer_container" >
        <input type="hidden" name="rch_ftp_details" class="rch_ftp_details" value="" >
        <div  class="rch_file_upload_container rch_ftp_upload_container">
                <div class="rch_element_half_wrapper">
                        <div class="rch_element_title"><?php esc_html_e( 'Hostname', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_element_data">
                                <input class="rch_content_data_input rch_ftp_hostname" type="text" name="rch_ftp_hostname" value="">
                        </div>
                        <div class="rch_element_hint">xyz.com</div>
                </div>
                <div class="rch_element_half_wrapper">
                        <div class="rch_element_title"><?php esc_html_e( 'Host Port', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_element_data">
                                <input class="rch_content_data_input rch_ftp_host_port" type="text" name="rch_ftp_host_port" value="">
                        </div>
                        <div class="rch_element_hint"><?php esc_html_e( 'Default Port : 21', 'rch-woo-import-export' ); ?></div>
                </div>
                <div class="rch_element_half_wrapper">
                        <div class="rch_element_title"><?php esc_html_e( 'Host Username', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_element_data">
                                <input class="rch_content_data_input rch_ftp_host_username" type="text" name="rch_ftp_host_username" value="">
                        </div>
                </div>
                <div class="rch_element_half_wrapper">
                        <div class="rch_element_title"><?php esc_html_e( 'Host Password', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_element_data">
                                <input class="rch_content_data_input rch_ftp_host_password" type="password" name="rch_ftp_host_password" value="" >
                        </div>
                </div>
                <div class="rch_element_half_wrapper">
                        <div class="rch_element_title"><?php esc_html_e( 'Host Path', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_element_data">
                                <input class="rch_content_data_input rch_ftp_host_path" type="text" name="rch_ftp_host_path" value="">
                        </div>
                        <div class="rch_element_hint">/home/example/sample.csv</div>
                </div>
                <div class="rch_download_btn_wrapper">
                        <div class="rch_btn rch_btn_primary rch_ftp_upload_btn">
                                <?php esc_html_e( 'Download', 'rch-woo-import-export' ); ?>
                        </div>
                </div>
        </div>
        <div class="rch_file_list_wrapper"></div>
</div>