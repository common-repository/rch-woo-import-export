<?php
if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

$templates = array ();

if ( file_exists( RCH_CLASSES_DIR . '/class-rch-common-action.php' ) ) {
        require_once(RCH_CLASSES_DIR . '/class-rch-common-action.php');
        $cmm_act = new RCH_Common_Actions();
        $templates = $cmm_act->get_export_list();
        unset( $cmm_act );
}

$ext_tab_files = apply_filters( 'rch_manage_export_tab_files', array () );
?>

<div class="rch_main_container">
    <div class="rch_content_header">
        <div class="rch_content_header_inner_wrapper">
            <div class="rch_content_header_title"><?php esc_html_e( 'Export List', 'rch-woo-import-export' ); ?></div>
        </div>
    </div>
    <div class="rch_content_wrapper">
        <div class="rch_section_wrapper">
            <div class="rch_content_data_header rch_section_wrapper_selected">
                <div class="rch_content_title"><?php esc_html_e( 'Export Log', 'rch-woo-import-export' ); ?></div>
                <div class="rch_layout_header_icon_wrapper"></div>
            </div>
            <div class="rch_section_content rch_show">
                <div class="rch_table_action_wrapper">
                    <div class="rch_table_action_container">
                        <select class="rch_content_data_select rch_log_bulk_action">
                            <option value=""><?php esc_html_e( 'Bulk Actions', 'rch-woo-import-export' ); ?></option>   
                            <option value="delete"><?php esc_html_e( 'Delete', 'rch-woo-import-export' ); ?></option>   
                        </select>
                    </div>
                    <div class="rch_table_action_btn_container">
                        <div class="rch_btn rch_btn_secondary rch_btn_radius rch_log_action_btn">
                            <?php esc_html_e( 'Apply', 'rch-woo-import-export' ); ?>
                        </div>
                    </div>
                </div>
                <table class="rch_log_table table table-bordered">
                    <thead>
                        <tr>
                            <td class="rch_log_check_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_log_check_all" id="rch_log_check_all" value="1"/>
                                <label for="rch_log_check_all" class="rch_checkbox_label"></label>
                            </td>
                            <td class="rch_log_lable"><?php esc_html_e( 'File Name', 'rch-woo-import-export' ); ?></td>
                            <td class="rch_log_lable"><?php esc_html_e( 'Query', 'rch-woo-import-export' ); ?></td>
                            <td class="rch_log_lable"><?php esc_html_e( 'Summary', 'rch-woo-import-export' ); ?></td>
                            <td class="rch_log_lable"><?php esc_html_e( 'Date', 'rch-woo-import-export' ); ?></td>
                            <td class="rch_log_lable"><?php esc_html_e( 'Status', 'rch-woo-import-export' ); ?></td>
                            <td class="rch_log_lable"><?php esc_html_e( 'actions', 'rch-woo-import-export' ); ?></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $is_empty_template = "";
                        if ( ! empty( $templates ) ) {

                                $date_format = get_option( 'date_format' );

                                $time_format = get_option( 'time_format' );

                                $date_time_format = $date_format . " " . $time_format;

                                $is_empty_template = "rch_hidden";

                                foreach ( $templates as $template ) {

                                        $date = isset( $template->create_date ) ? $template->create_date : "";

                                        $id = isset( $template->id ) ? $template->id : 0;

                                        $opration_type = isset( $template->opration_type ) ? $template->opration_type : "";

                                        $last_update_date = isset( $template->last_update_date ) ? $template->last_update_date : "";

                                        $process_log = isset( $template->process_log ) ? maybe_unserialize( $template->process_log ) : array ();

                                        $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array ();

                                        $fileName = isset( $options[ 'fileName' ] ) ? $options[ 'fileName' ] : "";

                                        $status = isset( $template->status ) ? $template->status : "";

                                        $process_status = __( 'Processing', 'rch-woo-import-export' );

                                        if ( $status == "completed" ) {
                                                $process_status = __( 'Completed', 'rch-woo-import-export' );
                                        } elseif ( $status == "paused" ) {
                                                $process_status = __( 'Paused', 'rch-woo-import-export' );
                                        } elseif ( $status == "stopped" ) {
                                                $process_status = __( 'Stopped', 'rch-woo-import-export' );
                                        }
                                        ?>
                                        <tr class="rch_log_wrapper rch_log_wrapper_<?php echo esc_attr( $id ); ?>">
                                            <td class="rch_log_check_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_log_check" id="rch_log_check_<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $id ); ?>"/>
                                                <label for="rch_log_check_<?php echo esc_attr( $id ); ?>" class="rch_checkbox_label"></label>
                                            </td>
                                            <td class="rch_log_data"><?php echo esc_html( $fileName ); ?></td>
                                            <td class="rch_log_data"><?php echo esc_html( maybe_unserialize( $opration_type ) ); ?></td>
                                            <td class="rch_log_data">
                                                <?php
                                                echo esc_html( __( "Last run", 'rch-woo-import-export' ) . " : " . date_i18n( $date_time_format, strtotime( $last_update_date ) ) );
                                                ?>
                                                <br /> 
                                                <?php
                                                echo esc_html( (isset( $process_log[ 'exported' ] ) ? $process_log[ 'exported' ] : 0 ) . " " . __( "Records Exported", 'rch-woo-import-export' ) );
                                                ?>
                                                <br /> 
                                                <?php
                                                echo esc_html( (isset( $process_log[ 'total' ] ) ? $process_log[ 'total' ] : 0 ) . " " . __( "Total", 'rch-woo-import-export' ) );
                                                ?>
                                            </td>
                                            <td class = "rch_log_data"><?php echo esc_html( date_i18n( $date_time_format, strtotime( $date ) ) );
                                                ?></td>
                                            <td class="rch_log_data rch_log_status"><?php echo esc_html( $process_status ); ?></td>
                                            <td class="rch_log_data rch_action_<?php echo esc_attr( $status ); ?>" >
                                                <a class="rch_delete_template_btn">Delete</a> | <a class="rch_download_template_file_btn">Download</a></div>
                                            </td>
                                        </tr>
                                        <?php
                                        unset( $date, $id, $opration_type, $last_update_date, $process_log, $options, $fileName, $status, $process_status );
                                }
                                ?>
                        <?php } ?>
                        <tr class="<?php echo esc_attr( $is_empty_template ); ?> rch_log_empty">
                            <td colspan="6">
                                <div class="rch_empty_records"><?php esc_html_e( 'No Templates Found', 'rch-woo-import-export' ); ?></div>
                            </td>
                        </tr>
                        <?php unset( $is_empty_template ); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        if ( ! empty( $ext_tab_files ) ) {

                foreach ( $ext_tab_files as $_file ) {

                        if ( file_exists( $_file ) ) {
                                include $_file;
                        }
                }
        }
        ?>
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
<div class="modal fade rch_delete_templates_data" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title rch_import_proccess_title" ><?php esc_html_e( 'Confirm', 'rch-woo-import-export' ); ?></h5>
            </div>
            <div class="modal-body">
                <div class="rch_delete_text_msg"><?php esc_html_e( 'Are you sure want to delete?', 'rch-woo-import-export' ); ?></div>
            </div>
            <div class="modal-footer">
                <div class="rch_btn rch_btn_primary rch_btn_radius " data-dismiss="modal">
                    <?php esc_html_e( 'cancel', 'rch-woo-import-export' ); ?>
                </div>
                <div class="rch_btn  rch_btn_primary rch_btn_radius rch_delete_templates" data-dismiss="modal" >
                    <?php esc_html_e( 'Ok', 'rch-woo-import-export' ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<form class="rch_download_file_frm" method="post">
    <input type="hidden" class="rch_download_file" name="rch_download_export_id" value="" />
</form>
<?php
unset( $templates );
