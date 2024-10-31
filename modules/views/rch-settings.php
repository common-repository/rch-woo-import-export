<?php
if ( ! defined( 'ABSPATH' ) )
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );

global $wp_roles;

$user_roles = $wp_roles->get_names();

if ( isset( $user_roles[ 'administrator' ] ) ) {
        unset( $user_roles[ 'administrator' ] );
}
$templates = array();

if ( file_exists( RCH_CLASSES_DIR . '/class-rch-common-action.php' ) ) {

        require_once(RCH_CLASSES_DIR . '/class-rch-common-action.php');

        $cmm_act = new RCH_Common_Actions();

        $templates = $cmm_act->rch_get_templates();

        unset( $cmm_act );
}
$delete_on_uninstall = get_option( "rch_delete_on_uninstall", 0 );
?>

<div class="rch_main_container">
        <div class="rch_content_header">
                <div class="rch_content_header_inner_wrapper">
                        <div class="rch_content_header_title"><?php esc_html_e( 'Settings', 'rch-woo-import-export' ); ?></div>
                </div>
        </div>
        <div class="rch_content_wrapper">
                 <?php if ( current_user_can( 'administrator' ) || is_super_admin() ) { ?>
                        <div>
                                <div>
                                        <div class="rch_content_title"><?php esc_html_e( 'Plugin Access Permission', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_layout_header_icon_wrapper"></div>
                                </div>
                                <div class="">
                                        <div class="rch_setting_element_wrapper">
                                                <form class="rch_user_role_frm">
                                                        <div class="rch_setting_element_only_data">
                                                                <div class="rch_setting_element">
                                                                        <select class="rch_content_data_select rch_role_list" name="rch_user_role" data-placeholder="<?php esc_attr_e( 'Choose Role', 'rch-woo-import-export' ); ?>">
                                                                                <option value=""><?php esc_html_e( 'Choose Role', 'rch-woo-import-export' ); ?></option>                                   
                                                                                <?php
                                                                                if ( ! empty( $user_roles ) ) {
                                                                                        foreach ( $user_roles as $key => $name ) {
                                                                                                ?>
                                                                                                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $name ); ?></option>
                                                                                                <?php
                                                                                        }
                                                                                }
                                                                                ?>
                                                                        </select>
                                                                </div>
                                                                <div class="rch_import_cap_wrapper">
                                                                        <div class="rch_import_cap_container">
                                                                                <input type="checkbox" class="rch_checkbox rch_user_cap rch_new_export" id="rch_cap_new_export" name="rch_cap_new_export" value="1"/>
                                                                                <label for="rch_cap_new_export" class="rch_checkbox_label"><?php esc_html_e( 'New Export', 'rch-woo-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="rch_import_cap_container">
                                                                                <input type="checkbox" class="rch_checkbox rch_manage_export rch_user_cap" id="rch_cap_manage_export" name="rch_cap_manage_export" value="1"/>
                                                                                <label for="rch_cap_manage_export" class="rch_checkbox_label"><?php esc_html_e( 'Export List', 'rch-woo-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="rch_import_cap_container">
                                                                                <input type="checkbox" class="rch_checkbox rch_new_import rch_user_cap" id="rch_cap_new_import" name="rch_cap_new_import" value="1"/>
                                                                                <label for="rch_cap_new_import" class="rch_checkbox_label"><?php esc_html_e( 'New Import', 'rch-woo-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="rch_import_cap_container">
                                                                                <input type="checkbox" class="rch_checkbox rch_manage_import rch_user_cap" id="rch_cap_manage_import" name="rch_cap_manage_import" value="1"/>
                                                                                <label for="rch_cap_manage_import" class="rch_checkbox_label"><?php esc_html_e( 'Import List', 'rch-woo-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="rch_import_cap_container">
                                                                                <input type="checkbox" class="rch_checkbox rch_settings rch_user_cap" rch_user_cap id="rch_cap_settings" name="rch_cap_settings" value="1"/>
                                                                                <label for="rch_cap_settings" class="rch_checkbox_label"><?php esc_html_e( 'Settings', 'rch-woo-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="rch_import_cap_container">
                                                                                <input type="checkbox" class="rch_checkbox rch_extensions rch_user_cap" id="rch_cap_ext" name="rch_cap_ext" value="1"/>
                                                                                <label for="rch_cap_ext" class="rch_checkbox_label"><?php esc_html_e( 'Manage Extensions', 'rch-woo-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="rch_import_cap_container">
                                                                                <input type="checkbox" class="rch_checkbox rch_add_shortcode rch_user_cap" id="rch_cap_add_shortcode" name="rch_cap_add_shortcode" value="1"/>
                                                                                <label for="rch_cap_add_shortcode" class="rch_checkbox_label"><?php esc_html_e( 'Add Shortcode', 'rch-woo-import-export' ); ?></label>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </form>
                                                <div class="rch_setting_element_btn">
                                                        <div class="rch_btn rch_btn_secondary rch_btn_radius rch_save_cap_btn">
                                                                <?php esc_html_e( 'Save', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                <?php } ?>
        </div>
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

<div class="rch_loader rch_hidden">
        <div></div>
        <div></div>
</div>
<?php
unset( $user_roles, $delete_on_uninstall, $templates );
