<?php
if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_CLASSES_DIR . '/class-rch-extensions.php' ) ) {
        require_once(RCH_CLASSES_DIR . '/class-rch-extensions.php');
}

$rch_ext = new \rch\addons\RCH_Extension();

$rch_ext_data = isset( $_GET[ 'rch_ext' ] ) ? rch_sanitize_field( $_GET[ 'rch_ext' ] ) : "";

$rch_import_ext = $rch_ext->rch_get_import_extension();

$ext_data = isset( $rch_import_ext[ $rch_ext_data ] ) ? $rch_import_ext[ $rch_ext_data ] : array ();
?>
<div class="rch_main_container">
    <div class="rch_content_header">
        <div class="rch_content_header_inner_wrapper">
            <div class="rch_content_header_title"><?php echo isset( $ext_data[ 'name' ] ) ? esc_html( $ext_data[ 'name' ] ) : ""; ?></div>
        </div>
    </div>
    <div class="rch_content_wrapper">
        <?php
        $settings = isset( $ext_data[ 'settings' ] ) ? $ext_data[ 'settings' ] : "";

        if ( ! empty( $settings ) && file_exists( $settings ) ) {
                ?>
                <div class="rch_section_wrapper">
                    <div class="rch_content_data_header  rch_section_wrapper_selected">
                        <div class="rch_content_title"><?php esc_html_e( 'Settings', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_layout_header_icon_wrapper"></div>
                    </div>
                    <div class="rch_section_content rch_show">
                        <form class="rch_ext_settings_frm">
                            <input type="hidden" name="rch_ext" value="<?php echo esc_attr( $rch_ext_data ); ?>"/>
                            <div class="rch_content_data_wrapper">
                                <?php
                                include($settings);
                                ?>
                                <div class="rch_ext_save_wrapper">
                                    <div class="rch_btn rch_btn_primary rch_ext_save_data">
                                        <?php esc_html_e( 'Save', 'rch-woo-import-export' ); ?>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                <?php
        }
        ?>
    </div>
</div>

<div class="rch_loader rch_hidden">
    <div></div>
    <div></div>
</div>
<div class="modal fade rch_error_model" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content rch_error">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'ERROR', 'rch-woo-import-export' ); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    
                </button>
            </div>
            <div class="modal-body">
                <div class="rch_error_content"></div>
            </div>
            <div class="modal-footer">
                <div class="rch_btn rch_btn_red rch_btn_radius " data-dismiss="modal">
                    <?php esc_html_e( 'Ok', 'rch-woo-import-export' ); ?>
                </div>
            </div>
        </div>
    </div>
</div>