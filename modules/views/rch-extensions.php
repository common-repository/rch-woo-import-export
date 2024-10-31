<?php
if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_CLASSES_DIR . '/class-rch-extensions.php' ) ) {
        require_once(RCH_CLASSES_DIR . '/class-rch-extensions.php');

        $rch_ext = new \rch\addons\RCH_Extension();

        $rch_export_ext = $rch_ext->rch_get_export_extension();

        $rch_import_ext = $rch_ext->rch_get_import_extension();

        $rchExtData = $rch_ext->rch_get_activated_ext();
} else {
        $rch_export_ext = array();

        $rch_import_ext = array();

        $rchExtData = array();
}

$page = isset( $_GET[ 'page' ] ) ? rch_sanitize_field( $_GET[ 'page' ] ) : "";
?>
<div class="rch_main_container">
        <div class="rch_content_header">
                <div class="rch_content_header_inner_wrapper">
                        <div class="rch_content_header_title"><?php esc_html_e( 'Extensions', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_fixed_header_button">
                                <div class="rch_btn rch_btn_primary rch_ext_save">
                                        <?php esc_html_e( 'Save', 'rch-woo-import-export' ); ?>
                                </div>
                        </div>
                </div>

        </div>
        <div class="rch_content_wrapper">
                <form class="rch_general_frm" method="post" action="#">
                        <div class="rch_section_wrapper">
                                <div class="rch_content_data_header rch_section_wrapper_selected">
                                        <div class="rch_content_title"><?php esc_html_e( 'Export Extensions', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_layout_header_icon_wrapper"></div>
                                </div>
                                <div class="rch_section_content" style="display: block;">
                                        <table class="rch_ext_list_table">
                                                <tr>
                                                    <?php
                                                    if ( ! empty( $rch_export_ext ) ) {

                                                            $temp = 0;
                                                            foreach ( $rch_export_ext as $key => $extData ) {

                                                                    if ( isset( $extData[ "is_default" ] ) && $extData[ "is_default" ] == true ) {
                                                                            continue;
                                                                    }
                                                                    if ( $temp % 3 == 0 ) {
                                                                            ?>
                                                                        </tr>
                                                                        <tr>
                                                                            <?php
                                                                    }
                                                                    ?>
                                                                        <td class="rch_ext_container">
                                                                                <div class="rch_ext_wrapper" >
                                                                                        <div class="rch_ext_name_wrapper"><?php echo esc_html( isset( $extData[ "name" ] ) ? $extData[ "name" ] : ""  ); ?></div>
                                                                                        <div class="rch_ext_desc_wrapper"><?php echo esc_html( isset( $extData[ "short_desc" ] ) ? $extData[ "short_desc" ] : ""  ); ?></div>
                                                                                        <div class="rch_ext_btn_wrapper">
                                                                                                <div class="rch_switch">
                                                                                                        <input type="checkbox" name="rch_ext[]" class="rch_switch_checkbox" value="<?php echo esc_attr( $key ); ?>" id="rch_switch_<?php echo esc_attr( $temp ); ?>" <?php if ( is_array( $rchExtData ) && in_array( $key, $rchExtData ) ) { ?>checked="checked"<?php } ?>>
                                                                                                        <label class="rch_switch_label" for="rch_switch_<?php echo esc_attr( $temp ); ?>">
                                                                                                                <span class="rch_switch_inner">
                                                                                                                        <span class="rch_switch_active"><span class="rch_switch_switch"><?php esc_html_e( "ON", 'rch-woo-import-export' ) ?></span></span>
                                                                                                                        <span class="rch_switch_inactive"><span class="rch_switch_switch"><?php esc_html_e( "OFF", 'rch-woo-import-export' ) ?></span></span>
                                                                                                                </span>
                                                                                                        </label>
                                                                                                </div>
                                                                                                <?php if ( isset( $extData[ "settings" ] ) ) { ?>
                                                                                                        <div class="rch_ext_setting_btn">
                                                                                                                <a class="rch_btn rch_btn_secondary rch_btn_radius rch_export_save_field_btn" href="<?php echo esc_url( admin_url( "admin.php?page=" . $page . "&rch_ext=" . $key ) ); ?>">
                                                                                                                        <?php esc_html_e( 'Settings', 'rch-woo-import-export' ); ?>
                                                                                                                </a>
                                                                                                        </div>
                                                                                                <?php } ?>
                                                                                        </div>
                                                                                </div>
                                                                        </td>
                                                                        <?php $temp ++; ?>
                                                                <?php } ?>
                                                        <?php } else { ?>
                                                                <td class="rch_ext_empty_msg"><?php esc_html_e( "No Extension installed. Please install extension for use all features.", 'rch-woo-import-export' ) ?></td>
                                                        <?php } ?>
                                                </tr>
                                        </table>
                                </div>
                        </div>
                        <div class="rch_section_wrapper">
                                <div class="rch_content_data_header rch_section_wrapper_selected">
                                        <div class="rch_content_title"><?php esc_html_e( 'Import Extensions', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_layout_header_icon_wrapper"></div>
                                </div>
                                <div class="rch_section_content" style="display: block;">
                                        <table class="rch_ext_list_table">
                                                <tr>
                                                    <?php
                                                    if ( ! empty( $rch_import_ext ) ) {

                                                            $temp = 0;
                                                            foreach ( $rch_import_ext as $key => $extData ) {

                                                                    if ( isset( $extData[ "is_default" ] ) && $extData[ "is_default" ] == true ) {
                                                                            continue;
                                                                    }
                                                                    if ( $temp % 3 == 0 ) {
                                                                            ?>
                                                                        </tr>
                                                                        <tr>
                                                                            <?php
                                                                    }
                                                                    ?>
                                                                        <td class="rch_ext_container">
                                                                                <div class="rch_ext_wrapper" >
                                                                                        <div class="rch_ext_name_wrapper"><?php echo esc_html( isset( $extData[ "name" ] ) ? $extData[ "name" ] : ""  ); ?></div>
                                                                                        <div class="rch_ext_desc_wrapper"><?php echo esc_html( isset( $extData[ "short_desc" ] ) ? $extData[ "short_desc" ] : ""  ); ?></div>
                                                                                        <div class="rch_ext_btn_wrapper">
                                                                                                <div class="rch_switch">
                                                                                                        <input type="checkbox" name="rch_ext[]" class="rch_switch_checkbox" value="<?php echo esc_attr( $key ); ?>" id="rch_switch_import_<?php echo esc_attr( $temp ); ?>" <?php if ( is_array( $rchExtData ) && in_array( $key, $rchExtData ) ) { ?>checked="checked"<?php } ?>>
                                                                                                        <label class="rch_switch_label" for="rch_switch_import_<?php echo esc_attr( $temp ); ?>">
                                                                                                                <span class="rch_switch_inner">
                                                                                                                        <span class="rch_switch_active"><span class="rch_switch_switch"><?php esc_html_e( "ON", 'rch-woo-import-export' ) ?></span></span>
                                                                                                                        <span class="rch_switch_inactive"><span class="rch_switch_switch"><?php esc_html_e( "OFF", 'rch-woo-import-export' ) ?></span></span>
                                                                                                                </span>
                                                                                                        </label>
                                                                                                </div>
                                                                                                <?php if ( isset( $extData[ "settings" ] ) ) { ?>
                                                                                                        <div class="rch_ext_setting_btn">
                                                                                                                <a class="rch_btn rch_btn_secondary rch_btn_radius rch_export_save_field_btn" href="<?php echo esc_url( admin_url( "admin.php?page=" . $page . "&rch_ext=" . $key ) ); ?>">
                                                                                                                        <?php esc_html_e( 'Settings', 'rch-woo-import-export' ); ?>
                                                                                                                </a>
                                                                                                        </div>
                                                                                                <?php } ?>
                                                                                        </div>
                                                                                </div>
                                                                        </td>
                                                                        <?php $temp ++; ?>
                                                                <?php } ?>
                                                        <?php } else { ?>
                                                                <td class="rch_ext_empty_msg"><?php esc_html_e( "No Extension installed. Please install extension for use all features.", 'rch-woo-import-export' ) ?></td>
                                                        <?php } ?>
                                                </tr>
                                        </table>
                                </div>
                        </div>
                </form>
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