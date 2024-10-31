<?php
if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_EXPORT_CLASSES_DIR . '/class-rch-export.php' ) ) {
        require_once(RCH_EXPORT_CLASSES_DIR . '/class-rch-export.php');
}
$rch_export = new \rch\export\RCH_Export();

$export_type = $rch_export->get_export_type();

$rch_taxonomies_list = $rch_export->rch_get_taxonomies();

unset( $rch_export );

$advance_options_files = apply_filters( 'rch_export_advance_option_files', array () );

$extension_html_files = apply_filters( 'rch_add_export_extension_files', array () );

$extension_process_btn = apply_filters( 'rch_add_export_extension_process_btn', array () );

$rch_remote_data = apply_filters( 'rch_get_export_remote_locations', array () );
?>
<div class="rch_main_container">
    <div class="rch_content_header">
        <div class="rch_content_header_inner_wrapper">
            <div class="rch_content_header_title"><?php esc_html_e( 'Export File', 'rch-woo-import-export' ); ?></div>
            <div class="rch_total_records_wrapper">
                <div class="rch_total_record_text"><?php esc_html_e( 'Total Records Found', 'rch-woo-import-export' ); ?></div>
                <div class="rch_total_records_outer"><span class="rch_total_records rch_total_records_container"></span></div>
            </div>
            <div class="rch_fixed_header_button">
                <div class="rch_btn rch_btn_primary rch_export_preview_btn">
                    <?php esc_html_e( 'Click for download list', 'rch-woo-import-export' ); ?>
                </div>
                <div class="rch_btn rch_btn_primary rch_export_data_btn">
                    <?php esc_html_e( 'Click for download progress', 'rch-woo-import-export' ); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="rch_content_wrapper">
        <form class="rch_export_frm" method="post" action="#">
            <input type="hidden" name="rch_total_filter_records" value="0" class="rch_total_filter_records">
            <input type="hidden" name="fields_data" value="" class="rch_export_fields_data">
            <div class="rch_content_data">
                <div class="rch_section_wrapper">
                    <div class="rch_content_data_header rch_section_wrapper_selected">
                        <div class="rch_content_title"><?php esc_html_e( 'Select Export Section', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_layout_header_icon_wrapper"></div>
                    </div>
                    <div class="rch_section_content" style="display: block;">
                        <div class="rch_content_data_wrapper">
                            <select class="rch_content_data_select rch_export_type_select" name="rch_export_type">
                                <option value=""><?php esc_html_e( 'Select Export Type', 'rch-woo-import-export' ); ?></option>
                                <?php if ( ! empty( $export_type ) ) { ?>                       
                                        <?php foreach ( $export_type as $value => $export_data ) { ?>
                                                <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $export_data->labels->name ); ?></option>
                                        <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="rch_content_data_wrapper rch_taxonomies_types_wrapper">
                            <select class="rch_content_data_select rch_taxonomies_types_select" name="rch_taxonomy_type">
                                <option value=""><?php esc_html_e( 'Select Taxonomy', 'rch-woo-import-export' ); ?></option>
                                <?php if ( ! empty( $rch_taxonomies_list ) ) { ?>                       
                                        <?php foreach ( $rch_taxonomies_list as $slug => $name ) { ?>
                                                <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></option>
                                        <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="rch_section_wrapper rch_filter_section_wrapper">
                    <div class="rch_content_data_header">
                        <div class="rch_content_title"><?php esc_html_e( 'Select Filtering Options', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_layout_header_icon_wrapper"></div>
                    </div>
                    <div class="rch_section_content rch_field_selection_wrapper">
                        <div class="rch_content_data_wrapper">
                            <div class="rch_content_data_rule_header_wrapper">
                                <div class="rch_content_data_rule_header"><?php esc_html_e( 'Element', 'rch-woo-import-export' ); ?></div>
                                <div class="rch_content_data_rule_header"><?php esc_html_e( 'Rule', 'rch-woo-import-export' ); ?></div>
                                <div class="rch_content_data_rule_header"><?php esc_html_e( 'Value', 'rch-woo-import-export' ); ?></div>
                                <div class="rch_content_data_rule_btn_header"></div>
                            </div>
                            <div class="rch_content_data_rule_wrapper ">
                                <div class="rch_content_data_rule">
                                    <select class="rch_content_data_select rch_content_data_rule_fields">
                                        <option value=""><?php esc_html_e( 'Select Element', 'rch-woo-import-export' ); ?></option>
                                    </select>
                                </div>
                                <div class="rch_content_data_rule rch_content_data_rule_condition">
                                    <select class="rch_content_data_select rch_content_data_rule_select">
                                        <option value=""><?php esc_html_e( 'Select Rule', 'rch-woo-import-export' ); ?></option>
                                    </select>
                                </div>
                                <div class="rch_content_data_rule">
                                    <input type="text" class="rch_content_data_input rch_content_data_rule_value" value=""/>
                                    <div class="rch_value_hints_container">
                                        <div class="rch_value_hints">
                                            <?php esc_html_e( 'Dynamic date allowed', 'rch-woo-import-export' ); ?>
                                        </div>
                                        <div class="rch_value_hints">
                                            <?php esc_html_e( 'Example :', 'rch-woo-import-export' ); ?> yesterday, today, tomorrow...
                                        </div>
                                        <div class="rch_value_hints">
                                            <?php esc_html_e( 'For more click', 'rch-woo-import-export' ); ?> <a target="_blank" href="<?php echo esc_url( 'https://www.php.net/manual/en/datetime.formats.relative.php' ); ?>"><?php esc_html_e( 'here', 'rch-woo-import-export' ); ?> </a>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="rch_content_data_rule_btn_wrapper"> 
                                    <a class="rch_icon_btn  rch_save_add_rule_btn">
                                        <i class="fas fa-plus rch_icon_btn_icon " aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="rch_content_added_data_rule_wrapper">
                                <table class="rch_content_added_data_rule table table-bordered">

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rch_section_wrapper">
                    <div class="rch_content_data_header">
                        <div class="rch_content_title"><?php esc_html_e( 'Select Fields', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_layout_header_icon_wrapper"></div>
                    </div>
                    <div class="rch_section_content">
                        <div class="rch_content_data_wrapper">
                            <div class="rch_export_fields_hint"><?php esc_html_e( 'Use click on text for edit field. Use Drag and Drop for change any position', 'rch-woo-import-export' ); ?></div>
                            <div class="rch_field_selection"></div>
                            <div class="rch_fields_selection_btn_wrapper">
                                <div class="rch_btn rch_btn_secondary rch_btn_radius rch_fields_add_new" >
                                    <?php esc_html_e( 'Add', 'rch-woo-import-export' ); ?>
                                </div>
                                <div class="rch_btn rch_btn_secondary rch_btn_radius rch_add_bulk_fields">
                                    <?php esc_html_e( 'Add Bulk', 'rch-woo-import-export' ); ?>
                                </div>
                                <div class="rch_btn rch_btn_secondary rch_btn_radius rch_fields_add_all">
                                    <?php esc_html_e( 'Add All', 'rch-woo-import-export' ); ?>
                                </div>
                                <div class="rch_btn rch_btn_secondary rch_btn_radius rch_fields_remove_all">
                                    <?php esc_html_e( 'Remove All', 'rch-woo-import-export' ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rch_section_wrapper">
                    <div class="rch_content_data_header">
                        <div class="rch_content_title"><?php esc_html_e( 'File Types Settings', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_layout_header_icon_wrapper"></div>
                    </div>
                    <div class="rch_section_content">
                        <div class="rch_content_data_wrapper">
                            <table class="rch_content_data_tbl table-bordered">
                                <tr>
                                    <td >
                                        <div class="rch_options_data">
                                            <div class="rch_options_data_title"><?php esc_html_e( 'Export File Type', 'rch-woo-import-export' ); ?></div>
                                            <div class="rch_options_data_content">
                                                <select class="rch_content_data_select rch_export_file_type" name="rch_export_file_type">
                                                    <option value=""><?php esc_html_e( 'Choose Export file type', 'rch-woo-import-export' ); ?></option>
                                                    <option value="csv"><?php esc_html_e( 'CSV', 'rch-woo-import-export' ); ?></option>
                                                    <option value="xls"><?php esc_html_e( 'XLS', 'rch-woo-import-export' ); ?></option>
                                                    <option value="xlsx"><?php esc_html_e( 'XLSX', 'rch-woo-import-export' ); ?></option>
                                                    <option value="xml"><?php esc_html_e( 'XML', 'rch-woo-import-export' ); ?></option>
                                                    <option value="ods"><?php esc_html_e( 'ODS', 'rch-woo-import-export' ); ?></option>
                                                    <option value="json"><?php esc_html_e( 'JSON', 'rch-woo-import-export' ); ?></option>
                                                </select>
                                                <div class="rch_export_default_hint"><?php esc_html_e( 'Default : CSV', 'rch-woo-import-export' ); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rch_options_data rch_csv_field_separator_wrapper">
                                            <div class="rch_options_data_title"><?php esc_html_e( 'Field Separator', 'rch-woo-import-export' ); ?></div>
                                            <div class="rch_options_data_content">
                                                <input type="text" class="rch_content_data_input rch_csv_field_separator" value="," name="rch_csv_field_separator"/>
                                                <div class="rch_export_default_hint"><?php esc_html_e( 'Default : , (Comma)', 'rch-woo-import-export' ); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="rch_options_data">
                                            <div class="rch_options_data_title"><?php esc_html_e( 'Export File Name', 'rch-woo-import-export' ); ?></div>
                                            <div class="rch_options_data_content">
                                                <input type="text" class="rch_content_data_input rch_export_file_name" value="" name="rch_export_file_name"/>
                                                <div class="rch_export_default_hint"><?php esc_html_e( 'Default : Auto Generated', 'rch-woo-import-export' ); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rch_options_data">
                                            <div class="rch_options_data_title"><?php esc_html_e( 'Records Per iteration', 'rch-woo-import-export' ); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e( "WP Import Export must be able to process this many records in less than your server's timeout settings. If your export fails before completion, to troubleshoot you should lower this number.", "rch-woo-import-export" ); ?>"></i></div>
                                            <div class="rch_options_data_content">
                                                <input type="text" class="rch_content_data_input rch_records_per_iteration" value="50" name="rch_records_per_iteration"/>
                                                <div class="rch_export_default_hint"><?php esc_html_e( 'Default : 50', 'rch-woo-import-export' ); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="rch_options_data">
                                            <div class="rch_options_data_title"><?php esc_html_e( 'File path for extra copy in WordPress upload directory', 'rch-woo-import-export' ); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php echo esc_attr( __( "Enter relative path to", "rch-woo-import-export" ) . " " . RCH_SITE_UPLOAD_DIR . " " . __( "Enter only path that not include file name. it's useful when you sync any export data with import. Path folders must be exist", "rch-woo-import-export" ) ); ?>"></i></div>
                                            <div class="rch_options_data_content">
                                                <input type="text" class="rch_content_data_input extra_copy_path" value="" name="extra_copy_path"/>
                                                <div class="rch_export_default_hint"><?php esc_html_e( 'Default : empty', 'rch-woo-import-export' ); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rch_options_data">
                                            <div class="rch_options_data_content">
                                                <input type="checkbox" class="rch_export_include_bom_chk rch_checkbox rch_export_include_bom" id="rch_export_include_bom" name="rch_export_include_bom" value="1"/>
                                                <label for="rch_export_include_bom" class="rch_options_data_title_email rch_checkbox_label"><?php esc_html_e( 'Include BOM in export file', 'rch-woo-import-export' ); ?></label>
                                                <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e( "The BOM will help some programs like Microsoft Excel read your export file if it includes non-English characters.", "rch-woo-import-export" ); ?>"></i>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                if ( ! empty( $advance_options_files ) ) {

                                        $temp = 0;

                                        foreach ( $advance_options_files as $adv_options ) {

                                                if ( $temp % 2 == 0 ) {
                                                        ?>
                                                        <tr class="rch_advance_options_row">
                                                            <?php
                                                    }
                                                    if ( file_exists( $adv_options ) ) {
                                                            include $adv_options;
                                                    }
                                                    if ( $temp % 2 == 0 ) {
                                                            ?>
                                                        </tr>
                                                        <?php
                                                }

                                                $temp ++;
                                        }
                                }
                                ?>

                            </table>
                        </div>
                    </div>
                </div>
                <?php
                if ( ! empty( $extension_html_files ) ) {
                        foreach ( $extension_html_files as $ext_html_file ) {
                                if ( file_exists( $ext_html_file ) ) {
                                        include $ext_html_file;
                                }
                        }
                }
                ?>
            </div>
        </form>
    </div>
    <form class="rch_file_download_action" method="post" action="#">
        <input type="hidden" class="rch_download_export_id" name="rch_download_export_id" value="0">
    </form>
</div>

<div class="rch_loader rch_hidden">
    <div></div>
    <div></div>
</div>
<!-- Modal -->
<div class="modal fade rch_field_editor_model" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Export Field Editor', 'rch-woo-import-export' ); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    
                </button>
            </div>
            <div class="modal-body">
                <div class="rch_export_field_editor_wrapper">
                    <div class="rch_export_field_editor_container">
                        <div class="rch_export_field_editor_title"><?php esc_html_e( 'Field Name', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_export_field_editor_data_wrapper"><input type="text" class="rch_content_data_input rch_field_editor_data" value=""/></div>
                    </div>
                    <div class="rch_export_field_editor_container">
                        <div class="rch_export_field_editor_title"><?php esc_html_e( 'Field value', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_export_field_editor_data_wrapper rch_content_data_wrapper">
                            <select class="rch_content_data_select  rch_content_data_field_list">
                            </select>
                        </div>
                    </div>
                    <div class="rch_export_field_editor_container rch_field_editor_date_field_wrapper">
                        <div class="rch_export_field_editor_title"><?php esc_html_e( 'Date Format', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_export_field_editor_data_wrapper rch_content_data_wrapper">
                            <select class="rch_content_data_select  rch_field_editor_date_field">
                                <option value="unix"><?php esc_html_e( 'UNIX timestamp - PHP time()', 'rch-woo-import-export' ); ?></option>
                                <option value="php" selected="selected"><?php esc_html_e( 'Natural Language PHP date()', 'rch-woo-import-export' ); ?></option>
                            </select>
                            <div class="rch_field_editor_date_field_format_wrapper">
                                <input type="text" class="rch_content_data_input rch_field_editor_date_field_format" value="" placeholder="<?php esc_attr_e( 'Y-m-d', 'rch-woo-import-export' ); ?>"/>
                                <div class="rch_export_default_hint"><?php esc_html_e( 'Default : Site Date Format', 'rch-woo-import-export' ); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="rch_export_field_editor_container">
                        <div class="rch_export_field_editor_other_data">

                            <div class="rch_export_php_fun_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_export_php_fun" id="rch_export_php_fun" name="rch_export_php_fun" value="1"/>
                                <label for="rch_export_php_fun" class="rch_checkbox_label"><?php esc_html_e( 'Export the value returned by a PHP function', 'rch-woo-import-export' ); ?></label>
                            </div>
                            <div class="rch_export_php_fun_inner_wrapper">
                                <span>&lt;?php </span>
                                <span><input type="text" class="rch_content_data_small_input rch_export_php_fun_data" id="rch_export_php_fun_data" name="rch_export_php_fun_data" value=""/></span>
                                <span> ( $value ); ?&gt;</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="rch_btn rch_btn_secondary rch_btn_radius rch_export_cancel_field_btn" data-dismiss="modal">
                    <?php esc_html_e( 'Cancel', 'rch-woo-import-export' ); ?>
                </div>
                <div class="rch_btn rch_btn_secondary rch_btn_radius rch_export_save_field_btn">
                    <?php esc_html_e( 'Save', 'rch-woo-import-export' ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade rch_bulk_fields_model" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Add Fields', 'rch-woo-import-export' ); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    
                </button>
            </div>
            <div class="modal-body">
                <div class="rch_export_field_editor_wrapper">
                    <div class="rch_export_field_editor_container">
                        <div class="rch_export_field_editor_title"><?php esc_html_e( 'Select Fields', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_export_fields_hint"><?php esc_html_e( 'Use Ctrl + Click to Select Multiple Fields', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_export_field_editor_data_wrapper rch_content_data_wrapper">
                            <select class="rch_content_data_select rch_bulk_fields" multiple="multiple">
                            </select>
                        </div>
                    </div>                   
                </div>
            </div>
            <div class="modal-footer">
                <div class="rch_btn rch_btn_secondary rch_btn_radius rch_cancel_bulk_field_btn" data-dismiss="modal">
                    <?php esc_html_e( 'Cancel', 'rch-woo-import-export' ); ?>
                </div>
                <div class="rch_btn rch_btn_secondary rch_btn_radius rch_add_bulk_field_btn">
                    <?php esc_html_e( 'Add', 'rch-woo-import-export' ); ?>
                </div>
            </div>
        </div>
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
<div class="modal fade rch_preview_model" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Preview', 'rch-woo-import-export' ); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    
                </button>
            </div>
            <div class="modal-body">
                <div class="rch_preview_wrapper">
                    <table class="rch_preview table table-bordered" cellspacing="0"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade rch_export_popup_wrapper" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title rch_export_proccess_title" ><?php esc_html_e( 'Export In Process', 'rch-woo-import-export' ); ?></h5>
            </div>
            <div class="modal-body">
                <div class="rch_process_bar_inner_wrapper">
                    <div class="rch_export_notice"><?php esc_html_e( 'Exporting may take some time. Please do not close your browser or refresh the page until the process is complete.', 'rch-woo-import-export' ); ?></div>
                    <div class="progress rch_export_process">
                        <div class="progress-bar progress-bar-striped progress-bar-animated rch_export_process_per" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                    <div class="rch_export_time_elapsed"><div class="rch_export_time_elapsed_label"><?php esc_html_e( 'Time Elapsed', 'rch-woo-import-export' ); ?></div><div class="rch_export_time_elapsed_value">00:00:00</div></div>
                    <div class="rch_export_total_records_wrapper">
                        <div class="rch_export_total_records">
                            <div class="rch_export_total_records_label"><?php esc_html_e( 'Exported', 'rch-woo-import-export' ); ?></div>
                            <div class="rch_export_total_records_value">0</div>
                            <div class="rch_export_total_records_label"><?php esc_html_e( 'of', 'rch-woo-import-export' ); ?></div>
                            <span class="rch_total_records rch_export_total_records_count"></span></div>
                    </div>
                </div>
                <?php if ( ! empty( $rch_remote_data ) ) { ?>
                        <div class="rch_remote_export_wrapper">
                            <div class="rch_remote_export_title"><?php esc_html_e( 'Send Exported Data To', 'rch-woo-import-export' ); ?></div>
                            <table class="rch_remote_export_table table table-borderedtable table-bordered">
                                <?php foreach ( $rch_remote_data as $remote_key => $remote_data ) { ?>
                                        <tr>
                                        <td>
                                                <div class="rch_content_data_wrapper">
                                                    <select class="rch_content_data_select" name="rch_export_type" multiple="multiple">
                                                        <?php $remote_options = isset( $remote_data[ 'data' ] ) ? $remote_data[ 'data' ] : array (); ?>
                                                        <?php if ( ! empty( $remote_options ) ) { ?>                       
                                                                <?php foreach ( $remote_options as $option_key => $option_data ) { ?>
                                                                        <option value="<?php echo esc_attr( $option_key ); ?>"><?php echo isset( $option_data[ 'rch_export_ext_label' ] ) ? esc_html( $option_data[ 'rch_export_ext_label' ] ) : ""; ?></option>
                                                                <?php } ?>
                                                        <?php } ?>
                                                        <?php unset( $remote_options ); ?>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                <?php } ?>
                            </table>
                            <div class="rch_send_remote_data_wrapper">
                                <div class="rch_btn rch_btn_primary rch_send_remote_data">
                                    <?php esc_html_e( 'Send', 'rch-woo-import-export' ); ?>
                                </div>
                            </div>
                        </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <div class="rch_export_process_option_btn_wrapper">
                    <div class="rch_btn rch_btn_primary rch_export_process_pause_btn rch_export_process_btn">
                        <?php esc_html_e( 'Pause', 'rch-woo-import-export' ); ?>
                    </div>
                    <div class="rch_btn rch_btn_primary rch_export_process_stop_btn rch_export_process_btn">
                        <?php esc_html_e( 'Stop', 'rch-woo-import-export' ); ?>
                    </div>
                    <div class="rch_btn rch_btn_primary rch_export_process_resume_btn rch_export_process_btn">
                       <?php esc_html_e( 'Resume', 'rch-woo-import-export' ); ?>
                    </div>
                    <?php
                    if ( ! empty( $extension_process_btn ) ) {
                            foreach ( $extension_process_btn as $ext_p_btn ) {
                                    if ( file_exists( $ext_p_btn ) ) {
                                            include $ext_p_btn;
                                    }
                            }
                    }
                    ?>
                </div>
                <div class="rch_export_process_btn_wrapper ">
                    <div class="rch_btn rch_btn_primary rch_export_process_close_btn rch_export_process_btn">
                        <?php esc_html_e( 'Close', 'rch-woo-import-export' ); ?>
                    </div>
                    <a class="rch_btn rch_btn_primary rch_export_process_btn rch_export_manage_export_btn" href="<?php echo admin_url( "admin.php?page=rch-manage-export" ); ?>">
                        <?php esc_html_e( 'Export List', 'rch-woo-import-export' ); ?>
                    </a>
                    <div class="rch_btn rch_btn_primary rch_export_download_btn rch_export_process_btn">
                        <?php esc_html_e( 'Download', 'rch-woo-import-export' ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade rch_process_action" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title rch_export_proccess_title" ><?php esc_html_e( 'Please Wait', 'rch-woo-import-export' ); ?></h5>
            </div>
            <div class="modal-body">
                <div class="rch_process_action_msg"><?php esc_html_e( 'Pause Exporting may take some time. Please do not close your browser or refresh the page until the process is complete.', 'rch-woo-import-export' ); ?></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade rch_processing_data" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title rch_import_proccess_title" ><?php esc_html_e( 'Please Wait until process is complete', 'rch-woo-import-export' ); ?></h5>
            </div>
            <div class="modal-body">
                <div class="rch_task_list"></div>
            </div>
        </div>
    </div>
</div>
<?php
unset( $export_type, $rch_taxonomies_list, $advance_options, $extension_html_files, $extension_process_btn, $rch_remote_data );
