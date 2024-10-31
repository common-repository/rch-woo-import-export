<?php
if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

add_filter( 'rch_import_mapping_fields', "rch_import_user_mapping_fields", 20, 2 );

if ( ! function_exists( "rch_import_user_mapping_fields" ) ) {

        function rch_import_user_mapping_fields( $sections = array(), $rch_import_type = "" ) {

                global $wp_version;

                $uniqid = uniqid();

                ob_start();
                ?>
                <div class="rch_field_mapping_container_wrapper rch_<?php echo esc_attr( $rch_import_type ); ?>_field_mapping_container">
                        <div class="rch_field_mapping_container_title rch_active"><?php esc_html_e( "User's Data", 'rch-woo-import-export' ); ?><div class="rch_layout_header_icon_wrapper"></div></div>
                        <div class="rch_field_mapping_container_data" style="display: block;">
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'First Name', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_first_name" name="rch_item_first_name" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Last Name', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_last_name" name="rch_item_last_name" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Role', 'rch-woo-import-export' ); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e( "A string with role slug used to set the user's role. Default role is subscriber.", "rch-woo-import-export" ); ?>"></i></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_user_role" name="rch_item_user_role" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Nickname', 'rch-woo-import-export' ); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e( "The user's nickname, defaults to the user's username.", "rch-woo-import-export" ); ?>"></i></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_nickname" name="rch_item_nickname" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Description', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_description" name="rch_item_description" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title">* <?php esc_html_e( 'Login / Username', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_user_login" name="rch_item_user_login" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Password', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_user_pass" name="rch_item_user_pass" value=""/>
                                        </div>
                                        <div class="rch_field_mapping_element_other_option">
                                                <input type="checkbox" value="1" name="rch_item_set_hashed_password" id="rch_item_set_hashed_password" class="rch_checkbox rch_item_set_hashed_password">
                                                <label class="rch_checkbox_label" for="rch_item_set_hashed_password"><?php esc_html_e( 'This is a hashed password from another WordPress site', 'rch-woo-import-export' ); ?></label>
                                                <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e( "If the value being imported is a hashed password from another WordPress site, enable this option.", "rch-woo-import-export" ); ?>"></i>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Nicename', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_user_nicename" name="rch_item_user_nicename" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title">* <?php esc_html_e( 'Email', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_user_email" name="rch_item_user_email" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Registered Date', 'rch-woo-import-export' ); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e( "The date the user registered. Format is Y-m-d H:i:s", "rch-woo-import-export" ); ?>"></i></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_user_registered" name="rch_item_user_registered" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Display Name', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_display_name" name="rch_item_display_name" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Website URL', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_user_url" name="rch_item_user_url" value=""/>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $user_data = ob_get_clean();

                ob_start();
                ?>
                <div class="rch_field_mapping_container_wrapper">
                        <div class="rch_field_mapping_container_title"><?php esc_html_e( 'User Meta', 'rch-woo-import-export' ); ?><div class="rch_layout_header_icon_wrapper"></div></div>
                        <div class="rch_field_mapping_container_data">
                                <div class="rch_cf_wrapper">
                                        <div class="rch_field_mapping_radio_input_wrapper rch_cf_notice_wrapper">
                                                <input type="checkbox" id="rch_item_not_add_empty" name="rch_item_not_add_empty" checked="checked" value="1" class="rch_checkbox rch_item_not_add_empty">
                                                <label class="rch_checkbox_label" for="rch_item_not_add_empty"><?php esc_html_e( "Don't add Empty value fields in database.", 'rch-woo-import-export' ); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e( "it's highly recommended. If custom field value is empty then it skip perticular field and not add to database. it's save memory and increase import speed", "rch-woo-import-export" ); ?>"></i></label>
                                        </div>
                                        <table class="rch_cf_table">
                                                <thead>
                                                        <tr>
                                                                <th><?php esc_html_e( 'Name', 'rch-woo-import-export' ); ?></th>
                                                                <th><?php esc_html_e( 'Value', 'rch-woo-import-export' ); ?></th>
                                                                <th><?php esc_html_e( 'Options', 'rch-woo-import-export' ); ?></th>
                                                                <th></th>
                                                        </tr>
                                                </thead>
                                                <tbody class="rch_cf_option_outer_wrapper">
                                                        <tr class="rch_cf_option_wrapper rch_data_row" rch_row_id="<?php echo esc_attr( $uniqid ); ?>">
                                                                <td class="rch_item_cf_name_wrapper">
                                                                        <input type="text" class="rch_content_data_input rch_item_cf_name" value="" name="rch_item_cf[<?php echo esc_attr( $uniqid ); ?>][name]"/>
                                                                </td>
                                                                <td class="rch_item_cf_value_wrapper">
                                                                        <div class="rch_cf_normal_data">
                                                                                <input type="text" class="rch_content_data_input rch_item_cf_value" value="" name="rch_item_cf[<?php echo esc_attr( $uniqid ); ?>][value]"/>
                                                                        </div>
                                                                        <div class="rch_btn rch_btn_primary rch_cf_serialized_data_btn">
                                                                                <?php esc_html_e( 'Click to specify', 'rch-woo-import-export' ); ?>
                                                                        </div>
                                                                        <div class="rch_cf_child_data"></div>
                                                                </td>
                                                                <td class="rch_item_cf_option_wrapper">
                                                                        <select class="rch_content_data_select rch_item_cf_option" name="rch_item_cf[<?php echo esc_attr( $uniqid ); ?>][option]" >
                                                                                <option value="normal"><?php esc_html_e( 'Normal Data', 'rch-woo-import-export' ); ?></option>
                                                                                <option value="serialized"><?php esc_html_e( 'Serialized Data', 'rch-woo-import-export' ); ?></option>
                                                                        </select>
                                                                </td>
                                                                <td>
                                                                        <div class="rch_remove_cf_btn"></div>
                                                                </td>
                                                        </tr>
                                                </tbody>
                                                <tfoot>
                                                        <tr>
                                                                <th colspan="4">
                                                                        <div class="rch_btn rch_btn_primary rch_cf_add_btn">
                                                                                <?php esc_html_e( 'Add New', 'rch-woo-import-export' ); ?>
                                                                        </div> 
                                                                        <div class="rch_btn rch_btn_primary rch_cf_close_btn">
                                                                                <?php esc_html_e( 'Close', 'rch-woo-import-export' ); ?>
                                                                        </div> 
                                                                </th>
                                                </tfoot>
                                        </table>
                                </div>
                        </div>
                </div>

                <?php
                $user_meta = ob_get_clean();

                $field_mapping_sections = array(
                        '100' => $user_data,
                        '200' => $user_meta,
                );

                unset( $user_data );
                unset( $user_meta );

                return apply_filters( "rch_pre_user_field_mapping_section", array_replace( $sections, $field_mapping_sections ), $rch_import_type );
        }

}

add_filter( 'rch_import_search_existing_item', "rch_import_user_search_existing_item", 20, 2 );

if ( ! function_exists( "rch_import_user_search_existing_item" ) ) {

        function rch_import_user_search_existing_item( $sections = "", $rch_import_type = "" ) {

                ob_start();
                ?>
                <div class="rch_field_mapping_container_element">
                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Search Existing Item on your site based on...', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_existing_item_search_logic" checked="checked" name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_login" value="login"/>
                                <label for="rch_existing_item_search_logic_login" class="rch_radio_label"><?php esc_html_e( 'match by Login', 'rch-woo-import-export' ); ?></label>
                        </div>
                        <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_existing_item_search_logic"  name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_email" value="email"/>
                                <label for="rch_existing_item_search_logic_email" class="rch_radio_label"><?php esc_html_e( 'match by Email', 'rch-woo-import-export' ); ?></label>
                        </div>
                        <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_existing_item_search_logic"  name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_cf" value="cf"/>
                                <label for="rch_existing_item_search_logic_cf" class="rch_radio_label"><?php esc_html_e( 'User Meta', 'rch-woo-import-export' ); ?></label>
                                <div class="rch_radio_container">
                                        <table class="rch_search_based_on_cf_table">
                                                <thead>
                                                        <tr>
                                                                <th><?php esc_html_e( 'Name', 'rch-woo-import-export' ); ?></th>
                                                                <th><?php esc_html_e( 'Value', 'rch-woo-import-export' ); ?></th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                        <tr>
                                                                <td><input type="text" class="rch_content_data_input rch_existing_item_search_logic_cf_key" name="rch_existing_item_search_logic_cf_key" value=""/></td>
                                                                <td><input type="text" class="rch_content_data_input rch_existing_item_search_logic_cf_value" name="rch_existing_item_search_logic_cf_value" value=""/></td>
                                                        </tr>
                                                </tbody>
                                        </table>
                                </div>
                        </div>
                        <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_existing_item_search_logic"  name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_id" value="id"/>
                                <label for="rch_existing_item_search_logic_id" class="rch_radio_label"><?php esc_html_e( 'User ID', 'rch-woo-import-export' ); ?></label>
                                <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_existing_item_search_logic_id" name="rch_existing_item_search_logic_id" value=""/></div>
                        </div>
                </div>
                <?php
                $handle_section = ob_get_clean();

                return $handle_section;
        }

}

add_filter( 'rch_import_update_existing_item_fields', "rch_import_user_update_existing_item_fields", 20, 2 );

if ( ! function_exists( "rch_import_user_update_existing_item_fields" ) ) {

        function rch_import_user_update_existing_item_fields( $sections = "", $rch_import_type = "" ) {

                ob_start();
                ?>
                <div class="rch_field_mapping_container_element">
                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Update Existing items data', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_item_update rch_item_update_all" checked="checked" name="rch_item_update" id="rch_item_update_all" value="all"/>
                                <label for="rch_item_update_all" class="rch_radio_label"><?php esc_html_e( 'Update all data', 'rch-woo-import-export' ); ?></label>
                        </div>
                        <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_item_update rch_item_update_specific" name="rch_item_update" id="rch_item_update_specific" value="specific"/>
                                <label for="rch_item_update_specific" class="rch_radio_label"><?php esc_html_e( 'Choose which data to update', 'rch-woo-import-export' ); ?></label>
                                <div class="rch_radio_container">
                                        <div class="rch_update_item_all_action"><?php esc_html_e( 'Check/Uncheck All', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_fname" checked="checked" name="is_update_item_fname" id="is_update_item_fname" value="1"/>
                                                <label for="is_update_item_fname" class="rch_checkbox_label"><?php esc_html_e( 'First Name', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_lname" checked="checked" name="is_update_item_lname" id="is_update_item_lname" value="1"/>
                                                <label for="is_update_item_lname" class="rch_checkbox_label"><?php esc_html_e( 'Last Name', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_role" checked="checked" name="is_update_item_role" id="is_update_item_role" value="1"/>
                                                <label for="is_update_item_role" class="rch_checkbox_label"><?php esc_html_e( 'Role', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_nickname" checked="checked" name="is_update_item_nickname" id="is_update_item_nickname" value="1"/>
                                                <label for="is_update_item_nickname" class="rch_checkbox_label"><?php esc_html_e( 'Nickname', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_desc" checked="checked" name="is_update_item_desc" id="is_update_item_desc" value="1"/>
                                                <label for="is_update_item_desc" class="rch_checkbox_label"><?php esc_html_e( 'Description', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_login" checked="checked" name="is_update_item_login" id="is_update_item_login" value="1"/>
                                                <label for="is_update_item_login" class="rch_checkbox_label"><?php esc_html_e( 'Login', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_password" checked="checked" name="is_update_item_password" id="is_update_item_password" value="1"/>
                                                <label for="is_update_item_password" class="rch_checkbox_label"><?php esc_html_e( 'Password', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_nicename" checked="checked" name="is_update_item_nicename" id="is_update_item_nicename" value="1"/>
                                                <label for="is_update_item_nicename" class="rch_checkbox_label"><?php esc_html_e( 'Nicename', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_email" checked="checked" name="is_update_item_email" id="is_update_item_email" value="1"/>
                                                <label for="is_update_item_email" class="rch_checkbox_label"><?php esc_html_e( 'Email', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_registered_date" checked="checked" name="is_update_item_registered_date" id="is_update_item_registered_date" value="1"/>
                                                <label for="is_update_item_registered_date" class="rch_checkbox_label"><?php esc_html_e( 'Registered Date', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_display_name" checked="checked" name="is_update_item_display_name" id="is_update_item_display_name" value="1"/>
                                                <label for="is_update_item_display_name" class="rch_checkbox_label"><?php esc_html_e( 'Display Name', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_url" checked="checked" name="is_update_item_url" id="is_update_item_url" value="1"/>
                                                <label for="is_update_item_url" class="rch_checkbox_label"><?php esc_html_e( 'Website URL', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_cf" checked="checked" name="is_update_item_cf" id="is_update_item_cf" value="1"/>
                                                <label for="is_update_item_cf" class="rch_checkbox_label"><?php esc_html_e( 'User Meta', 'rch-woo-import-export' ); ?></label>
                                                <div class="rch_checkbox_container">
                                                        <div class="rch_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_append" checked="checked" name="rch_item_update_cf" id="rch_item_update_cf_append" value="append"/>
                                                                <label for="rch_item_update_cf_append" class="rch_radio_label"><?php esc_html_e( 'Update all User Meta and keep meta if not found in file', 'rch-woo-import-export' ); ?></label>
                                                        </div>
                                                        <div class="rch_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_all" name="rch_item_update_cf" id="rch_item_update_cf_all" value="all"/>
                                                                <label for="rch_item_update_cf_all" class="rch_radio_label"><?php esc_html_e( 'Update all User Meta and Remove meta if not found in file', 'rch-woo-import-export' ); ?></label>
                                                        </div>
                                                        <div class="rch_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_includes" name="rch_item_update_cf" id="rch_item_update_cf_includes" value="includes"/>
                                                                <label for="rch_item_update_cf_includes" class="rch_radio_label"><?php esc_html_e( "Update only these User Meta, leave the rest alone", 'rch-woo-import-export' ); ?></label>
                                                                <div class="rch_radio_container">
                                                                        <input type="text" class="rch_content_data_input rch_item_update_cf_includes_data" name="rch_item_update_cf_includes_data" value=""/>
                                                                </div>
                                                        </div>
                                                        <div class="rch_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_excludes" name="rch_item_update_cf" id="rch_item_update_cf_excludes" value="excludes"/>
                                                                <label for="rch_item_update_cf_excludes" class="rch_radio_label"><?php esc_html_e( "Leave these User Meta alone, update all other User Meta", 'rch-woo-import-export' ); ?></label>
                                                                <div class="rch_radio_container">
                                                                        <input type="text" class="rch_content_data_input rch_item_update_cf_excludes_data" name="rch_item_update_cf_excludes_data" value=""/>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>

                <?php
                $existing_item = ob_get_clean();

                return $existing_item;
        }

}
