<?php
if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

add_filter( 'rch_import_mapping_fields', "rch_import_comment_mapping_fields", 10, 2 );

if ( ! function_exists( "rch_import_comment_mapping_fields" ) ) {

        function rch_import_comment_mapping_fields( $sections = array(), $rch_import_type = "" ) {

                global $wp_version;

                $uniqid = uniqid();

                $rch_import_type_title = ucfirst( $rch_import_type );

                $rch_import_type = get_post_types( array( '_builtin' => true ), 'objects' ) + get_post_types( array( '_builtin' => false, 'show_ui' => true ), 'objects' ) + get_post_types( array( '_builtin' => false, 'show_ui' => false ), 'objects' );

                foreach ( $rch_import_type as $key => $ct ) {
                        if ( in_array( $key, array( 'attachment', 'revision', 'nav_menu_item', 'import_users', 'shop_webhook', 'acf-field', 'acf-field-group', "shop_order", "shop_coupon", "shop_order_refund", "product_variation" ) ) ) {
                                unset( $rch_import_type[ $key ] );
                        }
                }

                ob_start();
                ?>
                <div class="rch_field_mapping_container_wrapper rch_comment_field_mapping_container_wrapper">
                        <div class="rch_field_mapping_container_title rch_active" ><?php esc_html_e( 'Search Parent Post', 'rch-woo-import-export' ); ?> <div class="rch_layout_header_icon_wrapper"></div></div>
                        <div class="rch_field_mapping_container_data  rch_field_mapping_other_option_outer_wrapper rch_show" >
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Includes only these post types', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_content_data_wrapper">
                                                <select class="rch_content_data_select" name="rch_comment_parent_include_post_types[]" multiple="multiple">
                                                    <?php if ( ! empty( $rch_import_type ) ) { ?>
                                                            <?php foreach ( $rch_import_type as $key => $value ) { ?>
                                                                        <option value="<?php echo esc_attr( $key ); ?>" selected="selected"><?php echo (isset( $value->labels ) && isset( $value->labels->name )) ? esc_html( $value->labels->name ) : ""; ?></option>
                                                                <?php } ?>
                                                        <?php } ?>
                                                </select>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Search Parent Post on your site based on...', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="radio" class="rch_radio rch_item_search_post_based_on" checked="checked"  name="rch_item_search_post_based_on" id="rch_item_search_post_based_on_title" value="title"/>
                                                <label for="rch_item_search_post_based_on_title" class="rch_radio_label"><?php esc_html_e( 'Parent Post Title', 'rch-woo-import-export' ); ?></label>
                                                <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_item_comment_post_title" name="rch_item_comment_post_title" value=""/></div>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="radio" class="rch_radio rch_item_search_post_based_on"  name="rch_item_search_post_based_on" id="rch_item_search_post_based_on_id" value="id"/>
                                                <label for="rch_item_search_post_based_on_id" class="rch_radio_label"><?php esc_html_e( 'Parent Post ID', 'rch-woo-import-export' ); ?></label>
                                                <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_item_comment_post_id" name="rch_item_comment_post_id" value=""/></div>
                                        </div>
                                </div>

                        </div>
                </div>
                <?php
                $post_fields = ob_get_clean();
                ob_start();
                ?>
                <div class="rch_field_mapping_container_wrapper rch_comment_field_mapping_container_wrapper">
                        <div class="rch_field_mapping_container_title" ><?php esc_html_e( 'Comments Data', 'rch-woo-import-export' ); ?> <div class="rch_layout_header_icon_wrapper"></div></div>
                        <div class="rch_field_mapping_container_data  rch_field_mapping_other_option_outer_wrapper">
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Comment Author', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_comment_author" name="rch_item_comment_author" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Comment Author Email', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_comment_author_email" name="rch_item_comment_author_email" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Comment Author URL', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_comment_author_url" name="rch_item_comment_author_url" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Comment Author IP', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_comment_author_ip rch_item_comment_author_IP" name="rch_item_comment_author_ip" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Comment Date', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_comment_date" name="rch_item_comment_date" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Comment Content', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <textarea class="rch_content_data_textarea rch_item_comment_content" name="rch_item_comment_content" ></textarea>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Comment Karma', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_comment_karma" name="rch_item_comment_karma" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Comment Approved', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_comment_approved" name="rch_item_comment_approved" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Comment Agent', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_comment_agent" name="rch_item_comment_agent" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Comment Type', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_comment_type" name="rch_item_comment_type" value=""/>
                                        </div>
                                </div>
                                <div class="rch_field_mapping_container_element">
                                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Comment Parent', 'rch-woo-import-export' ); ?></div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="text" class="rch_content_data_input rch_item_comment_parent" name="rch_item_comment_parent" value=""/>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $general_fields = ob_get_clean();

                ob_start();
                ?>
                <div class="rch_field_mapping_container_wrapper">
                        <div class="rch_field_mapping_container_title"><?php esc_html_e( 'Comment Meta', 'rch-woo-import-export' ); ?><div class="rch_layout_header_icon_wrapper"></div></div>
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
                                                                                <i class="fas fa-hand-point-up rch_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Click to specify', 'rch-woo-import-export' ); ?>
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
                                                                        <div class="rch_remove_cf_btn"><i class="fas fa-trash rch_trash_general_btn_icon " aria-hidden="true"></i></div>
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
                $cf_section = ob_get_clean();

                $sections = array_replace( $sections, array(
                        '100_post_fields'            => $post_fields,
                        '200_general_fields_section' => $general_fields,
                        '300_cf_section'             => $cf_section
                        )
                );

                unset( $post_fields, $rch_import_type_title, $general_fields, $cf_section );

                return apply_filters( "rch_pre_comment_field_mapping_section", $sections, $rch_import_type );
        }

}

add_filter( 'rch_import_search_existing_item', "rch_import_comment_search_existing_item", 10, 2 );

if ( ! function_exists( "rch_import_comment_search_existing_item" ) ) {

        function rch_import_comment_search_existing_item( $sections = "", $rch_import_type = "" ) {

                ob_start();
                ?>
                <div class="rch_field_mapping_container_element">
                        <div class="rch_field_mapping_inner_title"><?php esc_html_e( 'Search Existing Item on your site based on...', 'rch-woo-import-export' ); ?></div>
                        <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_existing_item_search_logic" checked="checked"  name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_content" value="content"/>
                                <label for="rch_existing_item_search_logic_content" class="rch_radio_label"><?php esc_html_e( 'Content', 'rch-woo-import-export' ); ?></label>
                        </div>
                        <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_existing_item_search_logic"  name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_cf" value="cf"/>
                                <label for="rch_existing_item_search_logic_cf" class="rch_radio_label"><?php esc_html_e( 'Custom field', 'rch-woo-import-export' ); ?></label>
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
                                <label for="rch_existing_item_search_logic_id" class="rch_radio_label"><?php esc_html_e( 'Comment ID', 'rch-woo-import-export' ); ?></label>
                                <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_existing_item_search_logic_id" name="rch_existing_item_search_logic_id" value=""/></div>
                        </div>
                </div>
                <?php
                return ob_get_clean();
        }

}

add_filter( 'rch_import_update_existing_item_fields', "rch_import_comment_update_existing_item_fields", 10, 2 );

if ( ! function_exists( "rch_import_comment_update_existing_item_fields" ) ) {

        function rch_import_comment_update_existing_item_fields( $sections = "", $rch_import_type = "" ) {

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
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_post_id" checked="checked" name="is_update_item_post_id" id="is_update_item_post_id" value="1"/>
                                                <label for="is_update_item_post_id" class="rch_checkbox_label"><?php esc_html_e( 'Comment Post Id', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_author" checked="checked" name="is_update_item_author" id="is_update_item_author" value="1"/>
                                                <label for="is_update_item_author" class="rch_checkbox_label"><?php esc_html_e( 'Comment Author', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_author_email" checked="checked" name="is_update_item_author_email" id="is_update_item_author_email" value="1"/>
                                                <label for="is_update_item_author_email" class="rch_checkbox_label"><?php esc_html_e( 'Comment Author Email', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_author_url" checked="checked" name="is_update_item_author_url" id="is_update_item_author_url" value="1"/>
                                                <label for="is_update_item_author_url" class="rch_checkbox_label"><?php esc_html_e( 'Comment Author URL', 'rch-woo-import-export' ); ?></label>
                                        </div>

                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_author_ip" checked="checked" name="is_update_item_author_ip" id="is_update_item_author_ip" value="1"/>
                                                <label for="is_update_item_author_ip" class="rch_checkbox_label"><?php esc_html_e( 'Comment Author IP', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_date" checked="checked" name="is_update_item_date" id="is_update_item_date" value="1"/>
                                                <label for="is_update_item_date" class="rch_checkbox_label"><?php esc_html_e( 'Comment Date', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_content" checked="checked" name="is_update_item_content" id="is_update_item_content" value="1"/>
                                                <label for="is_update_item_content" class="rch_checkbox_label"><?php esc_html_e( 'Comment Content', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_karma" checked="checked" name="is_update_item_karma" id="is_update_item_karma" value="1"/>
                                                <label for="is_update_item_karma" class="rch_checkbox_label"><?php esc_html_e( 'Comment Karma', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_approved" checked="checked" name="is_update_item_approved" id="is_update_item_approved" value="1"/>
                                                <label for="is_update_item_approved" class="rch_checkbox_label"><?php esc_html_e( 'Comment Approved', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_agent" checked="checked" name="is_update_item_agent" id="is_update_item_agent" value="1"/>
                                                <label for="is_update_item_agent" class="rch_checkbox_label"><?php esc_html_e( 'Comment Agent', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_type" checked="checked" name="is_update_item_type" id="is_update_item_type" value="1"/>
                                                <label for="is_update_item_type" class="rch_checkbox_label"><?php esc_html_e( 'Comment Type', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_parent" checked="checked" name="is_update_item_parent" id="is_update_item_parent" value="1"/>
                                                <label for="is_update_item_parent" class="rch_checkbox_label"><?php esc_html_e( 'Comment Parent', 'rch-woo-import-export' ); ?></label>
                                        </div>
                                        <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_cf" checked="checked" name="is_update_item_cf" id="is_update_item_cf" value="1"/>
                                                <label for="is_update_item_cf" class="rch_checkbox_label"><?php esc_html_e( 'Comment Meta', 'rch-woo-import-export' ); ?></label>
                                                <div class="rch_checkbox_container">
                                                        <div class="rch_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_append" checked="checked" name="rch_item_update_cf" id="rch_item_update_cf_append" value="append"/>
                                                                <label for="rch_item_update_cf_append" class="rch_radio_label"><?php esc_html_e( 'Update all Comment Meta and keep meta if not found in file', 'rch-woo-import-export' ); ?></label>
                                                        </div>
                                                        <div class="rch_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_all" name="rch_item_update_cf" id="rch_item_update_cf_all" value="all"/>
                                                                <label for="rch_item_update_cf_all" class="rch_radio_label"><?php esc_html_e( 'Update all Comment Meta and Remove meta if not found in file', 'rch-woo-import-export' ); ?></label>
                                                        </div>
                                                        <div class="rch_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_includes" name="rch_item_update_cf" id="rch_item_update_cf_includes" value="includes"/>
                                                                <label for="rch_item_update_cf_includes" class="rch_radio_label"><?php esc_html_e( "Update only these Comment Meta, leave the rest alone", 'rch-woo-import-export' ); ?></label>
                                                                <div class="rch_radio_container">
                                                                        <input type="text" class="rch_content_data_input rch_item_update_cf_includes_data" name="rch_item_update_cf_includes_data" value=""/>
                                                                </div>
                                                        </div>
                                                        <div class="rch_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_excludes" name="rch_item_update_cf" id="rch_item_update_cf_excludes" value="excludes"/>
                                                                <label for="rch_item_update_cf_excludes" class="rch_radio_label"><?php esc_html_e( "Leave these fields alone, update all other Comment Meta", 'rch-woo-import-export' ); ?></label>
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
                return ob_get_clean();
        }

}