<?php
if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-import.php' ) ) {
        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import.php');

        $rch_import = new \rch\import\RCH_Import();

        $rch_import_type = $rch_import->rch_get_import_type();

        $rch_taxonomies_list = $rch_import->rch_get_all_taxonomies( array(), array(), "keytitle" );

        unset( $rch_import );
} else {

        $rch_import_type = null;

        $rch_taxonomies_list = null;
}

if ( file_exists( RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php' ) ) {
        require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-upload.php');

        $rch_import_uploader = new \rch\import\upload\RCH_Upload();

        $upload_sections = $rch_import_uploader->rch_get_upload_section();

        unset( $rch_import_uploader );
} else {
        $upload_sections = array();
}

$final_btn_files = apply_filters( 'rch_add_import_extension_process_btn_files', array() );

$import_ext_html = apply_filters( 'rch_add_import_extension_file', array() );

$import_ref_id = "";

$error_msg = "";

$nonce = "";

$import_id = isset( $_GET[ 'import_id' ] ) ? absint( sanitize_text_field( $_GET[ 'import_id' ] ) ) : 0;

if ( $import_id > 0 ) {

        $ref_id = isset( $_GET[ 'ref_id' ] ) ? sanitize_text_field( $_GET[ 'ref_id' ] ) : "";

        if ( ! empty( $ref_id ) ) {

                $nonce = isset( $_GET[ 'nonce' ] ) ? sanitize_text_field( $_GET[ 'nonce' ] ) : "";

                if ( ! empty( $nonce ) ) {

                        $validate_nonce = wp_verify_nonce( $nonce, $import_id . $ref_id );

                        if ( $validate_nonce === 1 || $validate_nonce === 2 ) {
                                $import_ref_id = $ref_id;
                        } else {
                                $error_msg = esc_html__( 'Invalid Nonce. Go to Import List for new valid Reimport links', "rch-woo-import-export" );
                        }
                } else {
                        $error_msg = esc_html__( 'Empty Nonce', "rch-woo-import-export" );
                }
        } else {
                $error_msg = esc_html__( 'Empty Reference ID', "rch-woo-import-export" );
        }
}
?>
<div class="rch_main_container">
        <div class="rch_content_header">
                <div class="rch_content_header_inner_wrapper">
                        <div class="rch_content_header_title"><?php esc_html_e( 'New Import', "rch-woo-import-export" ); ?></div>
                </div>
        </div>
        <div class="rch_content_wrapper">
                <form class="rch_import_frm" method="post" action="#">
                        <input type="hidden" name="rch_total_filter_records" value="0" class="rch_total_filter_records">
                        <input type="hidden" name="ref_id" value="<?php echo esc_attr( $import_ref_id ); ?>" class="rch_import_ref_id">
                        <input type="hidden" name="import_id" value="<?php echo esc_attr( $import_id ); ?>" class="rch_import_id">
                        <input type="hidden" name="import_nonce" value="<?php echo esc_attr( $nonce ); ?>" class="rch_import_nonce">
                        <input type="hidden" class="rch_error_msg" msg="<?php echo esc_attr( $error_msg ); ?>">
                        <input type="hidden" name="rch_file_upload_method" value="rch_import_local_upload" class="rch_file_upload_method">
                        <div class="rch_content_data">
                                <div class="rch_section_container rch_import_step1 rch_import_step">
                                        <div class="rch_import_action_btn_wrapper rch_import_action_top_btn_wrapper">
                                                <div class="rch_import_action_container">
                                                        <div class="rch_btn rch_btn_primary rch_import_next_btn rch_import_step1_btn" rch_show="rch_import_step2">
                                                                <?php esc_html_e( 'Continue to Step 2', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="rch_section_wrapper rch_default">                                              
                                                <div class="rch_section_content rch_show">
                                                        <div class="rch_upload_menu_wrapper">
                                                            <?php
                                                            if ( ! empty( $upload_sections ) ) {

                                                                    $temp_flag = true;
                                                                    foreach ( $upload_sections as $key => $data ) {

                                                                            $icon = isset( $data[ 'icon' ] ) ? $data[ 'icon' ] : "fa-upload";

                                                                            $label = isset( $data[ 'label' ] ) ? $data[ 'label' ] : $key;

                                                                            if ( $temp_flag === true ) {
                                                                                    $selected_class = "rch_active";
                                                                                    $temp_flag = false;
                                                                            } else {
                                                                                    $selected_class = "";
                                                                            }
                                                                            ?>
                                                                                <div class="rch_upload_menu <?php echo esc_attr( $selected_class ); ?>" show_container="<?php echo esc_attr( $key ); ?>">                                    
                                                                                        <div class="rch_upload_menu_title_wrapper"><?php echo esc_html( $label ); ?></div>
                                                                                </div>
                                                                                <?php
                                                                                unset( $icon, $label, $selected_class );
                                                                        }
                                                                        unset( $temp_flag );
                                                                }
                                                                ?>
                                                        </div>
                                                        <div class="rch_upload_container_wrapper">
                                                            <?php
                                                            if ( ! empty( $upload_sections ) ) {

                                                                    $temp_flag = true;

                                                                    foreach ( $upload_sections as $key => $data ) {

                                                                            $view = isset( $data[ 'view' ] ) ? $data[ 'view' ] : "";

                                                                            if ( $temp_flag === true ) {
                                                                                    $display_style = "rch_show";
                                                                                    $temp_flag = false;
                                                                            } else {
                                                                                    $display_style = "";
                                                                            }
                                                                            ?>
                                                                                <div class="rch_upload_section_wrapper <?php echo esc_attr( $key ); ?> <?php echo esc_attr( $display_style ); ?>">
                                                                                    <?php
                                                                                    if ( ! empty( $view ) && file_exists( $view ) ) {
                                                                                            include $view;
                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                                <?php
                                                                                unset( $view, $display_style );
                                                                        }
                                                                        unset( $temp_flag );
                                                                }
                                                                ?>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="rch_import_action_btn_wrapper">
                                                <div class="rch_import_action_container">
                                                        <div class="rch_btn rch_btn_primary rch_import_next_btn rch_import_step1_btn" rch_show="rch_import_step2">
                                                                <?php esc_html_e( 'Continue to Step 2', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                                <div class="rch_section_container rch_import_step2 rch_import_step">
                                        <div class="rch_import_action_btn_wrapper rch_import_action_top_btn_wrapper">
                                                <div class="rch_import_action_container">
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_back_btn" rch_show="rch_import_step1">
                                                                <?php esc_html_e( 'Back to Step 1', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_next_btn rch_import_step2_btn" rch_show="rch_import_step3">
                                                                <?php esc_html_e( 'Continue to Step 3', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="rch_section_wrapper">
                                                <div class="rch_content_data_header rch_section_wrapper_selected">
                                                        <div class="rch_content_title"><?php esc_html_e( 'Import each record as', 'rch-woo-import-export' ); ?></div>
                                                </div>
                                                <div class="rch_section_content rch_show">
                                                        <div class="rch_handel_item_container_wrapper">
                                                                <div class="rch_import_type_outer_container">
                                                                        <div class="rch_content_data_wrapper">
                                                                                <select class="rch_content_data_select rch_import_type_select" name="rch_import_type">
                                                                                    <?php if ( ! empty( $rch_import_type ) ) { ?>
                                                                                            <?php foreach ( $rch_import_type as $key => $value ) { ?>
                                                                                                        <option value="<?php echo esc_attr( $key ); ?>"><?php echo (isset( $value->labels ) && isset( $value->labels->name )) ? esc_html( $value->labels->name ) : ""; ?></option>
                                                                                                <?php } ?>
                                                                                        <?php } ?>
                                                                                </select>
                                                                        </div>
                                                                        <div class="rch_content_data_wrapper rch_taxonomies_types_wrapper">
                                                                                <select class="rch_content_data_select rch_taxonomies_types_select" name="rch_taxonomy_type">
                                                                                    <?php if ( ! empty( $rch_taxonomies_list ) ) { ?>
                                                                                            <?php foreach ( $rch_taxonomies_list as $slug => $name ) { ?>
                                                                                                        <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></option>
                                                                                                <?php } ?>
                                                                                        <?php } ?>
                                                                                </select>
                                                                        </div>

                                                                </div>
                                                                <div class="rch_handle_item_wrapper">
                                                                        <div class="rch_handle_item_title"><?php esc_html_e( 'Handle New and Existing Items', 'rch-woo-import-export' ); ?></div>
                                                                        <div class="rch_handle_new_item_wrapper">
                                                                                <input type="radio" value="all" name="handle_items" id="handle_items_all" class="rch_radio rch_handle_items" checked="checked">
                                                                                <label class="rch_radio_label" for="handle_items_all"><?php esc_html_e( 'Import new items & Update Existing Items', 'rch-woo-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="rch_handle_new_item_wrapper">
                                                                                <input type="radio" value="new" name="handle_items" id="handle_items_new" class="rch_radio rch_handle_items">
                                                                                <label class="rch_radio_label" for="handle_items_new"><?php esc_html_e( 'Import new items only & Skip Existing Items', 'rch-woo-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="rch_handle_new_item_wrapper">
                                                                                <input type="radio" value="existing" name="handle_items" id="handle_items_existing" class="rch_radio rch_handle_items">
                                                                                <label class="rch_radio_label" for="handle_items_existing"><?php esc_html_e( 'Update Existing Items & Skip new items', 'rch-woo-import-export' ); ?></label>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="rch_import_action_btn_wrapper">
                                                <div class="rch_import_action_container">
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_back_btn" rch_show="rch_import_step1">
                                                                <?php esc_html_e( 'Back to Step 1', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_next_btn rch_import_step2_btn" rch_show="rch_import_step3">
                                                                <?php esc_html_e( 'Continue to Step 3', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                                <div class="rch_section_container rch_import_step3 rch_import_step">
                                        <div class="rch_import_action_btn_wrapper rch_import_action_top_btn_wrapper">
                                                <div class="rch_import_action_container">
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_back_btn" rch_show="rch_import_step2">
                                                                <?php esc_html_e( 'Back to Step 2', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <div class="rch_btn rch_btn_primary rch_import_next_btn rch_import_step3_btn" rch_show="rch_import_step4">
                                                                <?php esc_html_e( 'Continue to Step 4', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="rch_section_wrapper">
                                                <div class="rch_content_data_header rch_section_wrapper_selected">
                                                        <div class="rch_content_title"><?php esc_html_e( 'Data Filter', 'rch-woo-import-export' ); ?></div>
                                                </div>
                                                <div class="rch_section_content rch_show">
                                                        <table class="rch_filter_wrapper">
                                                                <tr>
                                                                        <td class="rch_filter_data_label"><?php esc_html_e( 'Element', 'rch-woo-import-export' ); ?></td>
                                                                        <td class="rch_filter_data_label"><?php esc_html_e( 'Rule', 'rch-woo-import-export' ); ?></td>
                                                                        <td class="rch_filter_data_label"><?php esc_html_e( 'Value', 'rch-woo-import-export' ); ?></td>
                                                                        <td class="rch_filter_data_add"></td>
                                                                </tr>
                                                                <tr>
                                                                        <td class="rch_filter_data_label">
                                                                                <select class="rch_content_data_select rch_element_list" name="">
                                                                                        <option value=""><?php esc_html_e( 'Select Element', 'rch-woo-import-export' ); ?></option>
                                                                                </select>
                                                                        </td>
                                                                        <td class="rch_filter_data_label">
                                                                                <select class="rch_content_data_select rch_element_rule" name="">
                                                                                        <option value=""><?php _e( 'Select Rule', 'rch-woo-import-export' ); ?></option>
                                                                                        <option value="equals"><?php _e( 'equals', 'rch-woo-import-export' ); ?></option>
                                                                                        <option value="not_equals"><?php _e( 'not equals', 'rch-woo-import-export' ); ?></option>
                                                                                        <option value="greater"><?php _e( 'greater than', 'rch-woo-import-export' ); ?></option>
                                                                                        <option value="equals_or_greater"><?php _e( 'equals or greater than', 'rch-woo-import-export' ); ?></option>
                                                                                        <option value="less"><?php _e( 'less than', 'rch-woo-import-export' ); ?></option>
                                                                                        <option value="equals_or_less"><?php _e( 'equals or less than', 'rch-woo-import-export' ); ?></option>
                                                                                        <option value="contains"><?php _e( 'contains', 'rch-woo-import-export' ); ?></option>
                                                                                        <option value="not_contains"><?php _e( 'not contains', 'rch-woo-import-export' ); ?></option>
                                                                                        <option value="is_empty"><?php _e( 'is empty', 'rch-woo-import-export' ); ?></option>
                                                                                        <option value="is_not_empty"><?php _e( 'is not empty', 'rch-woo-import-export' ); ?></option>
                                                                                </select>
                                                                        </td>
                                                                        <td class="rch_filter_data_label">
                                                                                <input class="rch_content_data_input rch_element_value" type="text" name="" value="">
                                                                        </td>
                                                                        <td class="rch_filter_data_add">
                                                                                <div class="rch_icon_btn rch_save_add_rule_btn">
                                                                                        <i class="fas fa-plus rch_icon_btn_icon " aria-hidden="true"></i>
                                                                                </div>
                                                                        </td>
                                                                </tr>
                                                        </table>
                                                        <table class="rch_filter_rule_table table table-bordered"></table>
                                                        <div class="rch_apply_rule_wrapper">
                                                                <div class="rch_btn rch_btn_primary rch_import_data_btn rch_apply_rule_btn">
                                                                        <?php esc_html_e( 'Apply to xpath', 'rch-woo-import-export' ); ?>
                                                                </div>
                                                        </div>
                                                        <table class="rch_xpath_wrapper">
                                                                <tr>
                                                                        <td class="rch_xpath_label"><?php esc_html_e( 'XPath', 'rch-woo-import-export' ); ?></td>
                                                                        <td class="rch_xpath_element"><input class="rch_content_data_input rch_xpath" type="text" name="" value=""></td>
                                                                </tr>
                                                        </table>
                                                        <table class="rch_data_element_table table table-bordered">
                                                                <tr>
                                                                        <td class="rch_element_tag_outer">
                                                                                <div class="rch_element_tag_wrapper"></div>
                                                                        </td>
                                                                        <td class="rch_element_data_wrapper">
                                                                                <div class="rch_data_element_action_wrapper">
                                                                                        <table class="rch_data_element_action_table table table-bordered">
                                                                                                <td class="rch_data_element_action">
                                                                                                        <span class="rch_data_element_nav rch_data_element_nav_prev">
                                                                                                </td>
                                                                                                <td class="rch_data_element_action_nav">
                                                                                                        <span class="rch_element_nav_input_wrapper"><input type="text" class="rch_content_data_input rch_element_nav_element" value="1"></span>
                                                                                                        <span class="rch_data_element_action_nav_text"><?php esc_html_e( 'of', 'rch-woo-import-export' ); ?></span>
                                                                                                        <span class="rch_data_element_action_nav_total rch_data_element_action_nav_text">1</span>
                                                                                                </td>
                                                                                                <td class="rch_data_element_action">
                                                                                                        <span class="rch_data_element_nav rch_data_element_nav_next">
                                                                                                </td>
                                                                                        </table>
                                                                                </div>
                                                                                <div class="rch_csv_delimiter_wrapper">
                                                                                        <table class="rch_csv_delimiter_outer_wrapper">
                                                                                                <tr>
                                                                                                        <td class="rch_csv_delimiter_container"><?php esc_html_e( 'Set delimiter for CSV fields', 'rch-woo-import-export' ); ?></td>
                                                                                                        <td class="rch_csv_delimiter_container rch_csv_delimiter_outer"><input class="rch_content_data_input rch_csv_delimiter" type="text" name="rch_csv_delimiter" value=","></td>
                                                                                                        <td class="rch_csv_delimiter_container ">
                                                                                                                <div class="rch_btn rch_btn_primary rch_csv_delimiter_btn">
                                                                                                                        <?php esc_html_e( 'Apply', 'rch-woo-import-export' ); ?>
                                                                                                                </div>
                                                                                                        </td>
                                                                                                </tr>
                                                                                        </table>
                                                                                </div>
                                                                                <div class="rch_data_preview"></div>
                                                                        </td>
                                                                </tr>
                                                        </table>
                                                </div>
                                        </div>
                                        <div class="rch_import_action_btn_wrapper">
                                                <div class="rch_import_action_container">
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_back_btn" rch_show="rch_import_step2">
                                                                <?php esc_html_e( 'Back to Step 2', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_next_btn rch_import_step3_btn" rch_show="rch_import_step4">
                                                                <?php esc_html_e( 'Continue to Step 4', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                                <div class="rch_section_container rch_import_step4 rch_import_step">
                                        <div class="rch_import_action_btn_wrapper rch_import_action_top_btn_wrapper">
                                                <div class="rch_import_action_container">
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_back_btn" rch_show="rch_import_step3">
                                                                <?php esc_html_e( 'Back to Step 3', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_next_btn rch_import_step4_btn" rch_show="rch_import_step5">
                                                                <?php esc_html_e( 'Continue to Step 5', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="rch_section_wrapper">
                                                <div class="rch_content_data_header rch_section_wrapper_selected">
                                                        <div class="rch_content_title"><?php esc_html_e( 'Field Mapping', 'rch-woo-import-export' ); ?></div>
                                                </div>
                                                <div class="rch_section_content rch_show">                                                   
                                                        <div class="rch_field_mapping_data_container">
                                                                <div class="rch_field_data_wrapper rch_field_mapping_data_wrapper">
                                                                        <table class="rch_data_element_action_table table table-bordered">
                                                                                <td class="rch_data_element_action">
                                                                                        <span class="rch_data_element_nav rch_data_element_nav_prev"></span>
                                                                                </td>
                                                                                <td class="rch_data_element_action_nav">
                                                                                        <span class="rch_element_nav_input_wrapper"><input type="text" class="rch_content_data_input rch_element_nav_element" value="1"></span>
                                                                                        <span class="rch_data_element_action_nav_text"><?php esc_html_e( 'of', 'rch-woo-import-export' ); ?></span>
                                                                                        <span class="rch_data_element_action_nav_total rch_data_element_action_nav_text">1</span>
                                                                                </td>
                                                                                <td class="rch_data_element_action">
                                                                                        <span class="rch_data_element_nav rch_data_element_nav_next"></span>
                                                                                </td>
                                                                        </table>
                                                                        <div class="rch_data_fields_container"></div>
                                                                </div>
                                                        </div>
                                                        <div class="rch_field_mapping_section"></div>
                                                        <div class="rch_import_existing_item_section">
                                                                <div class="rch_field_mapping_container_wrapper">
                                                                        <div class="rch_field_mapping_container_title"><?php esc_html_e( 'Handle Existing Items', 'rch-woo-import-export' ); ?><div class="rch_layout_header_icon_wrapper"></div></div>
                                                                        <div class="rch_field_mapping_container_data">
                                                                                <div class="rch_handle_duplicate_wrapper">
                                                                                        <div class="rch_search_item_wrapper"></div>
                                                                                        <div class="rch_update_item_wrapper"></div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="rch_import_manage_template_section">
                                                                <div class="rch_field_mapping_container_wrapper">
                                                                        <div class="rch_field_mapping_container_title"><?php esc_html_e( 'Load & Save Template', 'rch-woo-import-export' ); ?><div class="rch_layout_header_icon_wrapper"></div></div>
                                                                       
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="rch_import_action_btn_wrapper rch_import_filed_mapping_action_btn_wrapper">
                                                <div class="rch_import_action_container">
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_back_btn" rch_show="rch_import_step3">
                                                                <?php esc_html_e( 'Back to Step 3', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_next_btn rch_import_step4_btn" rch_show="rch_import_step5">
                                                                <?php esc_html_e( 'Continue to Step 5', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                                <div class="rch_section_container rch_import_step5 rch_import_step">
                                        <div class="rch_import_action_btn_wrapper rch_import_action_top_btn_wrapper">
                                                <div class="rch_import_action_container">
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_back_btn" rch_show="rch_import_step4">
                                                                <?php esc_html_e( 'Back to Step 4', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_next_btn rch_import_step5_btn" rch_show="rch_import_step6">
                                                                <?php esc_html_e( 'Continue', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="rch_section_wrapper ">
                                                <div class="rch_content_data_header rch_section_wrapper_selected">
                                                        <div class="rch_content_title"><?php esc_html_e( 'Advanced Options', 'rch-woo-import-export' ); ?></div>
                                                        <div class="rch_layout_header_icon_wrapper"></div>
                                                </div>
                                                <div class="rch_section_content rch_show">
                                                            <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Import Speed Optimization', 'rch-woo-import-export' ); ?></div>
                                                        <div class="rch_advanced_options_section">
                                                                <div class="rch_options_data_content">
                                                                        <input type="radio" value="all" name="rch_import_file_processing" id="rch_import_file_processing_all" class="rch_radio rch_import_file_processing">
                                                                        <label class="rch_radio_label" for="rch_import_file_processing_all"><?php esc_html_e( 'High Speed Small File Processing', 'rch-woo-import-export' ); ?></label>
                                                                </div>
                                                                <div class="rch_options_data_content">
                                                                        <input type="radio" value="chunk" name="rch_import_file_processing" id="rch_import_file_processing_chunk" checked="checked" class="rch_radio rch_import_file_processing">
                                                                        <label class="rch_radio_label" for="rch_import_file_processing_chunk"><?php esc_html_e( 'Iterative, Piece-by-Piece Processing', 'rch-woo-import-export' ); ?></label>
                                                                        <div class="rch_options_sub_data_content rch_iteration_process_wrapper ">
                                                                                <div class="rch_iteration_process_container ">
                                                                                        <span class="rch_records_length_lbl"><?php esc_html_e( 'In each iteration, process', 'rch-woo-import-export' ); ?></span>
                                                                                        <span class="rch_records_length_wrapper">
                                                                                                <input type="text" name="rch_records_per_request" value="20" class="rch_content_data_input rch_records_per_request">
                                                                                        </span>
                                                                                        <span class="rch_records_length_lbl"><?php esc_html_e( 'records', 'rch-woo-import-export' ); ?></span>
                                                                                </div>
                                                                                <div class="rch_options_sub_data_wrapper">
                                                                                        <input type="checkbox" value="1" name="rch_import_split_file" id="rch_import_split_file" checked="checked" class="rch_checkbox rch_import_split_file">
                                                                                        <label class="rch_checkbox_label" for="rch_import_split_file"><?php esc_html_e( 'Split file up into 1000 record chunks.', 'rch-woo-import-export' ); ?></label>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Friendly Name', 'rch-woo-import-export' ); ?></div>
                                                        <div class="rch_import_friendly_name_wrapper">
                                                                <input type="text" name="rch_import_friendly_name" value="" class="rch_content_data_input">
                                                        </div>
                                                </div>
                                        </div>
                                        <?php
                                        if ( ! empty( $import_ext_html ) ) {
                                                foreach ( $import_ext_html as $_imp_html_file ) {
                                                        if ( file_exists( $_imp_html_file ) ) {
                                                                include $_imp_html_file;
                                                        }
                                                }
                                        }
                                        ?>
                                        <div class="rch_import_action_btn_wrapper">
                                                <div class="rch_import_action_container">
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_back_btn" rch_show="rch_import_step4">
                                                                <?php esc_html_e( 'Back to Step 4', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_next_btn rch_import_step5_btn" rch_show="rch_import_step6">
                                                                <?php esc_html_e( 'Continue', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                                <div class="rch_section_container rch_import_step6 rch_import_step">
                                        <div class="rch_import_action_btn_wrapper rch_import_action_top_btn_wrapper">
                                                <div class="rch_import_action_container rch_pre_import_btn">
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_back_btn" rch_show="rch_import_step5">
                                                                <?php esc_html_e( 'Back', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <?php
                                                        if ( ! empty( $final_btn_files ) ) {
                                                                foreach ( $final_btn_files as $_btn_files ) {
                                                                        if ( file_exists( $_btn_files ) ) {
                                                                                include $_btn_files;
                                                                        }
                                                                }
                                                        }
                                                        ?>
                                                        <div class="rch_btn rch_btn_primary rch_import_next_btn rch_import_step6_btn rch_import_btn" rch_show="rch_import_step7">
                                                                <?php esc_html_e( 'Confirm & Run Import', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="rch_section_wrapper ">
                                                <div class="rch_content_data_header rch_section_wrapper_selected">
                                                        <div class="rch_content_title"><?php esc_html_e( 'Import Summary', 'rch-woo-import-export' ); ?></div>
                                                        <div class="rch_layout_header_icon_wrapper"></div>
                                                </div>
                                                <div class="rch_section_content rch_show">
                                                        <div class="rch_import_summary_text"><?php esc_html_e( 'WordPress Import Export will import the file', 'rch-woo-import-export' ); ?><span class="rch_import_filename"></span> , <?php esc_html_e( 'which is', 'rch-woo-import-export' ); ?><span class="rch_import_filesize"></span></div>
                                                        <div class="rch_import_summary_text"><?php esc_html_e( 'WordPress Import Export will process', 'rch-woo-import-export' ); ?> <span class="rch_import_total_count"></span> <?php esc_html_e( 'rows in your file', 'rch-woo-import-export' ); ?></div>
                                                        <div class="rch_import_summary_text rch_import_update_item_summary_text"><?php esc_html_e( 'WordPress Import Export will merge data into existing Items, matching the following criteria: has the same', 'rch-woo-import-export' ); ?><span class="rch_import_update_criteria"></span></div>
                                                        <div class="rch_import_summary_text rch_import_new_item_summary_text"><?php esc_html_e( 'Existing data will be skipped with the data specified in this import.', 'rch-woo-import-export' ); ?></div>
                                                        <div class="rch_import_summary_text rch_import_update_item_summary_text"><?php esc_html_e( 'Existing data will be updated with the data specified in this import.', 'rch-woo-import-export' ); ?></div>                            
                                                        <div class="rch_import_summary_text rch_import_update_item_summary_text rch_import_update_item_created_summary_text"><?php esc_html_e( "New Items will be created from records that don't match the above criteria.", 'rch-woo-import-export' ); ?></div>
                                                        <div class="rch_import_summary_text rch_import_update_item_summary_text rch_import_update_item_skip_summary_text"><?php esc_html_e( "New Items will be skipped from records that don't match the above criteria.", 'rch-woo-import-export' ); ?></div>
                                                        <div class="rch_import_summary_text rch_import_hidh_speed_text"><?php esc_html_e( "High-Speed, Small File Processing enabled. Your import will fail if it takes longer than your server's max_execution_time.", 'rch-woo-import-export' ); ?></div>
                                                        <div class="rch_import_summary_text rch_import_iteration_text"><?php esc_html_e( "Piece By Piece Processing enabled.", 'rch-woo-import-export' ); ?><span class="rch_import_per_iteration"></span><?php esc_html_e( "records will be processed each iteration. If it takes longer than your server's max_execution_time to process", 'rch-woo-import-export' ); ?><span class="rch_import_per_iteration"></span><?php esc_html_e( "records, your import will fail.", 'rch-woo-import-export' ); ?></div>                            
                                                        <div class="rch_import_summary_text rch_import_iteration_text rch_import_iteration_chunks_text"><?php esc_html_e( "Your file will be split into 1000 records chunks before processing.", 'rch-woo-import-export' ); ?></div>
                                                </div>
                                        </div>
                                        <div class="rch_import_action_btn_wrapper ">
                                                <div class="rch_import_action_container rch_pre_import_btn">
                                                        <div class="rch_btn rch_btn_primary rch_import_step_btn rch_import_back_btn" rch_show="rch_import_step5">
                                                                <?php esc_html_e( 'Back', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <?php
                                                        if ( ! empty( $final_btn_files ) ) {
                                                                foreach ( $final_btn_files as $_btn_files ) {
                                                                        if ( file_exists( $_btn_files ) ) {
                                                                                include $_btn_files;
                                                                        }
                                                                }
                                                        }
                                                        ?>
                                                        <div class="rch_btn rch_btn_primary rch_import_next_btn rch_import_step6_btn rch_import_btn" rch_show="rch_import_step7">
                                                                <?php esc_html_e( 'Confirm & Run Import', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                                <div class="rch_section_container rch_import_step7 rch_import_step">
                                        <div class="rch_import_action_btn_wrapper rch_import_action_top_btn_wrapper">
                                                <div class="rch_import_action_container rch_import_processing_btn">
                                                    <?php
                                                    if ( ! empty( $final_btn_files ) ) {
                                                            foreach ( $final_btn_files as $_btn_files ) {
                                                                    if ( file_exists( $_btn_files ) ) {
                                                                            include $_btn_files;
                                                                    }
                                                            }
                                                    }
                                                    ?>
                                                        <div class="rch_btn rch_btn_primary rch_import_action_btn rch_import_action_pause_btn" >
                                                                <?php esc_html_e( 'Pause', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <div class="rch_btn rch_btn_primary rch_import_action_btn rch_import_action_resume_btn" >
                                                                <?php esc_html_e( 'Resume', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                        <div class="rch_btn rch_btn_primary rch_import_action_btn rch_import_action_stop_btn" >
                                                               <?php esc_html_e( 'Stop', 'rch-woo-import-export' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="rch_section_wrapper ">
                                                <div class="rch_content_data_header rch_section_wrapper_selected">
                                                        <div class="rch_content_title"><?php esc_html_e( 'Import Data', 'rch-woo-import-export' ); ?></div>
                                                        <div class="rch_layout_header_icon_wrapper"></div>
                                                </div>
                                                <div class="rch_section_content rch_show">
                                                        <div class="rch_import_process_text_wrapper">
                                                                <div class="rch_import_process_text_header"></div>
                                                                <div class="rch_import_process_text_notice"></div>
                                                        </div>
                                                        <div class="rch_import_processing_wrapper">
                                                                <div class="progress rch_import_processing">
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated rch_import_process_per" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">0%</div>
                                                                </div>
                                                        </div>                            
                                                        <table class="rch_import_details_table">
                                                                <tr>
                                                                        <td class="rch_import_details">
                                                                                <div class="rch_import_details_wrapper">
                                                                                        <div class="rch_import_details_label"><?php esc_html_e( 'File Name', 'rch-woo-import-export' ); ?> : </div>
                                                                                        <div class="rch_import_details_content rch_import_filename"></div>
                                                                                </div>
                                                                        </td>
                                                                        <td class="rch_import_details">
                                                                                <div class="rch_import_details_wrapper rch_import_details_right_wrapper">
                                                                                        <div class="rch_import_details_label"><?php esc_html_e( 'File Size', 'rch-woo-import-export' ); ?> : </div>
                                                                                        <div class="rch_import_details_content rch_import_filesize"></div>
                                                                                </div>
                                                                        </td>
                                                                </tr>
                                                                <tr>
                                                                        <td class="rch_import_details">
                                                                                <div class="rch_import_details_wrapper">
                                                                                        <div class="rch_import_details_label"><?php esc_html_e( 'Time Elapsed', 'rch-woo-import-export' ); ?> : </div>
                                                                                        <div class="rch_import_details_content rch_import_time_elapsed"></div>
                                                                                </div>
                                                                        </td>
                                                                        <td class="rch_import_details">
                                                                                <div class="rch_import_process_content rch_import_details_right_wrapper">
                                                                                        <div class="rch_import_process_count_label"><?php esc_html_e( 'Created', 'rch-woo-import-export' ); ?></div>
                                                                                        <div class="rch_import_process_count rch_import_created"></div>
                                                                                        <div class="rch_import_process_count_label"> / <?php esc_html_e( 'Updated', 'rch-woo-import-export' ); ?></div>
                                                                                        <div class="rch_import_process_count rch_import_updated"></div>
                                                                                        <div class="rch_import_process_count_label"> / <?php esc_html_e( 'Skipped', 'rch-woo-import-export' ); ?></div>
                                                                                        <div class="rch_import_process_count rch_import_skipped"></div>
                                                                                        <div class="rch_import_process_count_label"><?php esc_html_e( 'of', 'rch-woo-import-export' ); ?></div>
                                                                                        <div class="rch_import_process_count rch_import_total"></div>
                                                                                        <div class="rch_import_process_count_label"><?php esc_html_e( 'records', 'rch-woo-import-export' ); ?></div>
                                                                                </div>
                                                                        </td>
                                                                </tr>
                                                        </table>
                                                        <div class="rch_log_container_wrapper">
                                                                <div class="rch_log_container_title"><?php esc_html_e( 'Log', 'rch-woo-import-export' ); ?></div>
                                                                <div class="rch_log_container"></div>
                                                        </div>
                                                </div>
                                        </div>
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
unset( $rch_import_type, $rch_taxonomies_list, $upload_sections, $final_btn_files, $import_ext_html );
