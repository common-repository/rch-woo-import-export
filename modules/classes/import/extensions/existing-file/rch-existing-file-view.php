<?php
if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

$uploader = new \rch\import\upload\RCH_Upload();

$rch_existing_file_list = $uploader->rch_get_file_list( RCH_UPLOAD_MAIN_DIR, false, true );

unset( $uploader );
?>


<div class="rch_upload_outer_container" >
        <div  class="rch_existing_file_upload_container">
                <div class="rch_element_full_wrapper">
                        <div class="rch_element_title"><?php esc_html_e( 'Choose File', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_element_data">
                                <input type="hidden" value="" name="final_existing_file" class="rch_final_existing_file">
                                <select class="rch_content_data_select rch_upload_existing_file" data-placeholder="<?php esc_html_e( 'Select a previously uploaded file', 'rch-woo-import-export' ); ?>" name="rch_upload_existing_file">
                                        <option value=""><?php esc_html_e( 'Select a previously uploaded file', 'rch-woo-import-export' ); ?></option>
                                        <?php
                                        if ( ! empty( $rch_existing_file_list ) ) {
                                                arsort( $rch_existing_file_list );
                                                foreach ( $rch_existing_file_list as $file_path => $file_name ) {
                                                        ?>
                                                        <option value="<?php echo esc_attr( $file_path ); ?>"><?php echo esc_html( $file_name ); ?></option>
                                                <?php } ?>
                                        <?php } ?>
                                </select>
                        </div>
                        <div class="rch_element_hint"><?php echo esc_html( __( 'Upload files to', 'rch-woo-import-export' ) . " " . RCH_UPLOAD_MAIN_DIR . " " . __( 'and they will appear in this list ', 'rch-woo-import-export' ) ); ?></div>
                </div>
                <div class="rch_download_btn_wrapper">
                        <div class="rch_btn rch_btn_primary rch_existing_file_btn">
                                <?php esc_html_e( 'Confirm', 'rch-woo-import-export' ); ?>
                        </div>
                </div>
        </div>
        <div class="rch_file_list_wrapper"></div>
</div>
<?php
unset( $rch_existing_file_list );
