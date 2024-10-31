<?php
if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

add_filter('rch_import_mapping_fields', "rch_import_taxonomy_mapping_fields", 10, 2);

if (!function_exists("rch_import_taxonomy_mapping_fields")) {

        function rch_import_taxonomy_mapping_fields($sections = array(), $rch_import_type = "") {

                global $wp_version;

                $uniqid = uniqid();

                $rch_import_type_title = ucfirst($rch_import_type);

                ob_start();
                ?>
                <div class="rch_field_mapping_container_wrapper">
                    <div class="rch_field_mapping_container_title rch_active"><?php esc_html_e('Name & Description', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
                    <div class="rch_field_mapping_container_data" style="display: block;">
                        <div class="rch_field_mapping_container_element">
                            <input type="text" class="rch_content_data_input rch_item_term_name" name="rch_item_term_name" placeholder="<?php esc_html_e('Name', 'rch-woo-import-export'); ?>"/>
                        </div>
                        <div class="rch_field_mapping_container_element rch_import_content_editor_wrapper">
                            <textarea class="rch_content_data_textarea rch_item_term_description" name="rch_item_term_description" placeholder="<?php esc_html_e('Description', 'rch-woo-import-export'); ?>"></textarea>
                        </div>

                    </div>
                </div>
                <?php
                $name_and_desc = ob_get_clean();

                ob_start();
                ?>
                <div class="rch_field_mapping_container_wrapper">
                    <div class="rch_field_mapping_container_title"><?php esc_html_e('Images', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
                    <div class="rch_field_mapping_container_data">
                        <div class="rch_field_mapping_container_element">
                            <div class="rch_image_download_options">
                                <div class="rch_field_mapping_radio_input_wrapper rch_radio_wrapper">
                                    <input type="radio" class="rch_radio rch_item_image_option" checked="checked" name="rch_item_image_option" id="rch_download_images" value="download_images"/>
                                    <label for="rch_download_images" class="rch_radio_label"><?php esc_html_e('Download images hosted elsewhere', 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container">
                                        <div class="rch_field_mapping_image_separator_wrapper">
                                            <div class="rch_field_mapping_image_separator"><?php esc_html_e('Enter image filenames one per line, or separate them with a', 'rch-woo-import-export'); ?></div>
                                            <input type="text" class="rch_content_data_input rch_item_delim rch_item_image_url_delim" name="rch_item_image_url_delim"  value="|"/>
                                        </div>
                                        <textarea class="rch_content_data_textarea rch_item_image_url" name="rch_item_image_url" placeholder="<?php esc_attr_e('URL', 'rch-woo-import-export'); ?>"></textarea>
                                    </div>
                                </div>
                                <div class="rch_field_mapping_radio_input_wrapper">
                                    <input type="radio" class="rch_radio rch_item_image_option " name="rch_item_image_option" id="rch_media_library" value="media_library"/>
                                    <label for="rch_media_library" class="rch_radio_label"><?php esc_html_e('Use images currently in Media Library', 'rch-woo-import-export'); ?></label>
                                    <div class="rch_radio_container">
                                        <div class="rch_field_mapping_image_separator_wrapper">
                                            <div class="rch_field_mapping_image_separator"><?php esc_html_e('Enter image filenames one per line, or separate them with a', 'rch-woo-import-export'); ?></div>
                                            <input type="text" class="rch_content_data_input rch_item_image_media_library_delim rch_item_delim" name="rch_item_image_media_library_delim" value="|"/>
                                        </div>
                                        <textarea class="rch_content_data_textarea rch_item_image_media_library" name="rch_item_image_media_library" placeholder="<?php esc_attr_e('Images.jpg', 'rch-woo-import-export'); ?>"></textarea>
                                    </div>
                                </div>
                                <div class="rch_field_mapping_radio_input_wrapper">
                                    <input type="radio" class="rch_radio rch_item_image_option " name="rch_item_image_option" id="rch_local_images" value="local_images"/>
                                    <label for="rch_local_images" class="rch_radio_label"><?php echo esc_html(__('Use images currently uploaded in', 'rch-woo-import-export') . " " . RCH_UPLOAD_TEMP_DIR); ?> </label>
                                    <div class="rch_radio_container">
                                        <div class="rch_field_mapping_image_separator_wrapper">
                                            <div class="rch_field_mapping_image_separator"><?php esc_html_e('Enter image filenames one per line, or separate them with a', 'rch-woo-import-export'); ?></div>
                                            <input type="text" class="rch_content_data_input rch_item_image_local_delim rch_item_delim" name="rch_item_image_local_delim"  value="|"/>
                                        </div>
                                        <textarea class="rch_content_data_textarea rch_item_image_local" name="rch_item_image_local" placeholder="<?php esc_attr_e('Images.jpg', 'rch-woo-import-export'); ?>"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Image Options', 'rch-woo-import-export'); ?></div>
                            <div class="rch_image_option_wrapper">
                                <div class="rch_field_mapping_radio_input_wrapper rch_image_media_option_data">
                                    <input type="checkbox" id="rch_item_search_existing_images" name="rch_item_search_existing_images" checked="checked" value="1" class="rch_checkbox rch_search_existing_images">
                                    <label class="rch_checkbox_label" for="rch_item_search_existing_images"><?php esc_html_e('Search through the Media Library for existing images before importing new images', 'rch-woo-import-export'); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("If an image with the same file name or remote URL is found in the Media Library then that image will be attached to this record instead of importing a new image. Disable this setting if you always want to download a new image.", "rch-woo-import-export"); ?>"></i></label>
                                </div>
                                <div class="rch_field_mapping_radio_input_wrapper rch_image_media_option_data">
                                    <input type="checkbox" id="rch_item_keep_images" name="rch_item_keep_images" checked="checked" value="1" class="rch_checkbox rch_item_keep_images">
                                    <label class="rch_checkbox_label" for="rch_item_keep_images"><?php esc_html_e('Keep images currently in Media Library', 'rch-woo-import-export'); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("If disabled, images attached to imported posts will be deleted and then all images will be imported.", "rch-woo-import-export"); ?>"></i></label>
                                </div> 
                                <div class="rch_field_mapping_radio_input_wrapper">
                                    <input type="checkbox"  id="rch_item_first_imaege_is_featured" name="rch_item_first_imaege_is_featured" checked="checked" value="1" class="rch_checkbox rch_item_first_imaege_is_featured">
                                    <label class="rch_checkbox_label" for="rch_item_first_imaege_is_featured"><?php esc_html_e('Set the first image to the Featured Image (_thumbnail_id)', 'rch-woo-import-export'); ?></label>
                                </div>
                                <div class="rch_field_mapping_radio_input_wrapper">
                                    <input type="checkbox" id="rch_item_unsuccess_set_draft" value="1" name="rch_item_unsuccess_set_draft" class="rch_checkbox rch_item_unsuccess_set_draft">
                                    <label class="rch_checkbox_label" for="rch_item_unsuccess_set_draft"><?php esc_html_e('If no images are downloaded successfully, create entry as Draft.', 'rch-woo-import-export'); ?></label>
                                </div>
                            </div>
                            <div class="rch_field_advanced_option_wrapper">
                                <div class="rch_field_mapping_inner_title"><?php esc_html_e('Media SEO & Advanced Options', 'rch-woo-import-export'); ?></div>
                                <div class="rch_field_advanced_option_container">
                                    <div class="rch_field_advanced_option_data_lbl"><?php esc_html_e('Meta Data', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_field_advanced_option_data_container">
                                        <div class="rch_field_mapping_radio_input_wrapper">
                                            <input type="checkbox" class="rch_update_data_inner_option rch_checkbox rch_item_set_image_title" name="rch_item_set_image_title" id="rch_item_set_image_title" value="1"/>
                                            <label for="rch_item_set_image_title" class="rch_checkbox_label"><?php esc_html_e('Set Title(s)', 'rch-woo-import-export'); ?></label>
                                            <div class="rch_checkbox_container">
                                                <div class="rch_field_mapping_container_element" >
                                                    <div class="rch_field_mapping_image_separator"><?php esc_html_e('Enter one per line, or separate them with a', 'rch-woo-import-export'); ?></div>
                                                    <input type="text" class="rch_content_data_input rch_item_set_image_title_delim rch_item_delim" name="rch_item_set_image_title_delim" value=","/>
                                                </div>
                                                <div class="rch_import_image_seo_hint"><?php esc_html_e('The first title will be linked to the first image, the second title will be linked to the second image, ...', 'rch-woo-import-export'); ?></div>
                                                <div class="rch_field_mapping_container_element">
                                                    <textarea class="rch_content_data_textarea rch_item_image_title" name="rch_item_image_title"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rch_field_mapping_radio_input_wrapper">
                                            <input type="checkbox" class="rch_update_data_inner_option rch_checkbox rch_item_set_image_caption" name="rch_item_set_image_caption" id="rch_item_set_image_caption" value="1"/>
                                            <label for="rch_item_set_image_caption" class="rch_checkbox_label"><?php esc_html_e('Set Caption(s)', 'rch-woo-import-export'); ?></label>
                                            <div class="rch_checkbox_container">
                                                <div class="rch_field_mapping_container_element" >
                                                    <div class="rch_field_mapping_image_separator"><?php esc_html_e('Enter one per line, or separate them with a', 'rch-woo-import-export'); ?></div>
                                                    <input type="text" class="rch_content_data_input rch_item_set_image_caption_delim rch_item_delim" name="rch_item_set_image_caption_delim" value=","/>
                                                </div>
                                                <div class="rch_import_image_seo_hint"><?php esc_html_e('The first caption will be linked to the first image, the second caption will be linked to the second image, ...', 'rch-woo-import-export'); ?></div>
                                                <div class="rch_field_mapping_container_element">
                                                    <textarea class="rch_content_data_textarea  rch_item_image_caption" name="rch_item_image_caption" ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rch_field_mapping_radio_input_wrapper">
                                            <input type="checkbox" class="rch_update_data_inner_option rch_checkbox rch_item_set_image_alt" name="rch_item_set_image_alt" id="rch_item_set_image_alt" value="1"/>
                                            <label for="rch_item_set_image_alt" class="rch_checkbox_label"><?php esc_html_e('Set Alt Text(s)', 'rch-woo-import-export'); ?></label>
                                            <div class="rch_checkbox_container">
                                                <div class="rch_field_mapping_container_element" >
                                                    <div class="rch_field_mapping_image_separator"><?php esc_html_e('Enter one per line, or separate them with a', 'rch-woo-import-export'); ?></div>
                                                    <input type="text" class="rch_content_data_input rch_item_set_image_alt_delim rch_item_delim" name="rch_item_set_image_alt_delim"  value=","/>
                                                </div>
                                                <div class="rch_import_image_seo_hint"><?php esc_html_e('The first alt text will be linked to the first image, the second alt text will be linked to the second image, ...', 'rch-woo-import-export'); ?></div>
                                                <div class="rch_field_mapping_container_element">
                                                    <textarea class="rch_content_data_textarea rch_item_image_alt" name="rch_item_image_alt"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rch_field_mapping_radio_input_wrapper">
                                            <input type="checkbox" class="rch_update_data_inner_option rch_checkbox rch_item_set_image_description" name="rch_item_set_image_description" id="rch_item_set_image_description" value="1"/>
                                            <label for="rch_item_set_image_description" class="rch_checkbox_label"><?php esc_html_e('Set Description(s)', 'rch-woo-import-export'); ?></label>
                                            <div class="rch_checkbox_container">
                                                <div class="rch_field_mapping_container_element" >
                                                    <div class="rch_field_mapping_image_separator"><?php esc_html_e('Enter one per line, or separate them with a', 'rch-woo-import-export'); ?></div>
                                                    <input type="text" class="rch_content_data_input rch_item_set_image_description_delim rch_item_delim" name="rch_item_set_image_description_delim" value=","/>
                                                </div>
                                                <div class="rch_import_image_seo_hint"><?php esc_html_e('The first description will be linked to the first image, the second description will be linked to the second image, ...', 'rch-woo-import-export'); ?></div>
                                                <div class="rch_field_mapping_container_element">
                                                    <textarea class="rch_content_data_textarea  rch_item_image_description" name="rch_item_image_description" ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="rch_field_advanced_option_container">
                                    <div class="rch_field_advanced_option_data_lbl"><?php esc_html_e('Files', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_field_advanced_option_data_container">
                                        <div class="rch_field_mapping_radio_input_wrapper">
                                            <input type="checkbox" class="rch_update_data_inner_option rch_checkbox rch_item_image_rename" name="rch_item_image_rename" id="rch_item_image_rename" value="1"/>
                                            <label for="rch_item_image_rename" class="rch_checkbox_label"><?php esc_html_e('Change image file names to', 'rch-woo-import-export'); ?></label>
                                            <div class="rch_checkbox_container">
                                                <div class="rch_field_mapping_container_element">
                                                    <input type="text" class="rch_content_data_input rch_item_image_new_name" value="" name="rch_item_image_new_name"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rch_field_mapping_radio_input_wrapper">
                                            <input type="checkbox" class="rch_update_data_inner_option rch_checkbox rch_item_change_ext" name="rch_item_change_ext" id="rch_item_change_ext" value="1"/>
                                            <label for="rch_item_change_ext" class="rch_checkbox_label"><?php esc_html_e('Change image file extensions', 'rch-woo-import-export'); ?></label>
                                            <div class="rch_checkbox_container">
                                                <input type="text" class="rch_content_data_input rch_item_new_ext " value="" name="rch_item_new_ext"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $image_section = ob_get_clean();

                ob_start();
                ?>
                <div class="rch_field_mapping_container_wrapper">
                    <div class="rch_field_mapping_container_title"><?php esc_html_e('Term Meta', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
                    <div class="rch_field_mapping_container_data">
                        <div class="rch_cf_wrapper">
                            <div class="rch_field_mapping_radio_input_wrapper rch_cf_notice_wrapper">
                                <input type="checkbox" id="rch_item_not_add_empty" name="rch_item_not_add_empty" checked="checked" value="1" class="rch_checkbox rch_item_not_add_empty">
                                <label class="rch_checkbox_label" for="rch_item_not_add_empty"><?php esc_html_e('Do not add empty value fields in database', 'rch-woo-import-export'); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("If custom field value is empty then it skip perticular field and not add to database", "rch-woo-import-export"); ?>"></i></label>
                            </div>
                            <table class="rch_cf_table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Name', 'rch-woo-import-export'); ?></th>
                                        <th><?php esc_html_e('Value', 'rch-woo-import-export'); ?></th>
                                        <th><?php esc_html_e('Options', 'rch-woo-import-export'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="rch_cf_option_outer_wrapper">
                                    <tr class="rch_cf_option_wrapper rch_data_row" rch_row_id="<?php echo esc_attr($uniqid); ?>">
                                        <td class="rch_item_cf_name_wrapper">
                                            <input type="text" class="rch_content_data_input rch_item_cf_name" value="" name="rch_item_cf[<?php echo esc_attr($uniqid); ?>][name]"/>
                                        </td>
                                        <td class="rch_item_cf_value_wrapper">
                                            <div class="rch_cf_normal_data">
                                                <input type="text" class="rch_content_data_input rch_item_cf_value" value="" name="rch_item_cf[<?php echo esc_attr($uniqid); ?>][value]"/>
                                            </div>
                                            <div class="rch_btn rch_btn_primary rch_cf_serialized_data_btn">
                                                <?php esc_html_e('Click to specify', 'rch-woo-import-export'); ?>
                                            </div>
                                            <div class="rch_cf_child_data"></div>
                                        </td>
                                        <td class="rch_item_cf_option_wrapper">
                                            <select class="rch_content_data_select rch_item_cf_option" name="rch_item_cf[<?php echo esc_attr($uniqid); ?>][option]" >
                                                <option value="normal"><?php esc_html_e('Normal Data', 'rch-woo-import-export'); ?></option>
                                                <option value="serialized"><?php esc_html_e('Serialized Data', 'rch-woo-import-export'); ?></option>
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
                                                <?php esc_html_e('Add New', 'rch-woo-import-export'); ?>
                                            </div> 
                                            <div class="rch_btn rch_btn_primary rch_cf_close_btn">
                                                <?php esc_html_e('Close', 'rch-woo-import-export'); ?>
                                            </div> 
                                        </th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <?php
                $term_meta = ob_get_clean();

                ob_start();
                ?>
                <div class="rch_field_mapping_container_wrapper">
                    <div class="rch_field_mapping_container_title"><?php esc_html_e('Other Category Options', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
                    <div class="rch_field_mapping_container_data  rch_field_mapping_other_option_outer_wrapper">

                        <div class="rch_field_mapping_container_element">
                            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Parent Term', 'rch-woo-import-export'); ?></div>
                            <div class="rch_field_mapping_other_option_wrapper rch_as_specified_wrapper">
                                <input type="text" class="rch_content_data_input rch_item_term_parent" name="rch_item_term_parent" value=""/>
                                <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("If your taxonomies have parent/child relationships, use this field to set the parent for the imported taxonomy term. Terms can be matched by slug, name, or ID.", "rch-woo-import-export"); ?>"></i>
                            </div>
                        </div>
                        <div class="rch_field_mapping_container_element">
                            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Category Slug', 'rch-woo-import-export'); ?></div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_item_term_slug rch_item_term_slug_auto" checked="checked" name="rch_item_term_slug" id="rch_item_slug_auto" value="auto"/>
                                <label for="rch_item_slug_auto" class="rch_radio_label"><?php esc_html_e('Set slug automatically', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper ">
                                <input type="radio" class="rch_radio rch_item_term_slug rch_item_term_slug_as_specified" name="rch_item_term_slug" id="rch_item_slug_as_specified" value="as_specified"/>
                                <label for="rch_item_slug_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                <div class="rch_radio_container rch_as_specified_wrapper">
                                    <input type="text" class="rch_content_data_input rch_item_term_slug_as_specified_data" name="rch_item_term_slug_as_specified_data" value=""/>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The term slug must be unique.", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $other_section = ob_get_clean();

                $sections = array_replace($sections, array(
                        '100' => $name_and_desc,
                        '200' => $image_section,
                        '300' => $term_meta,
                        '400' => $other_section
                        )
                );

                unset($rch_import_type_title, $name_and_desc, $image_section, $term_meta, $other_section);

                return apply_filters("rch_pre_term_field_mapping_section", $sections, $rch_import_type);
        }

}

add_filter('rch_import_search_existing_item', "rch_import_taxonomy_search_existing_item", 10, 2);

if (!function_exists("rch_import_taxonomy_search_existing_item")) {

        function rch_import_taxonomy_search_existing_item($sections = "", $rch_import_type = "") {

                ob_start();
                ?>
                <div class="rch_field_mapping_container_element">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Search Existing Item on your site based on...', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_existing_item_search_logic rch_existing_item_search_logic_name" checked="checked" name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_name" value="name"/>
                        <label for="rch_existing_item_search_logic_name" class="rch_radio_label"><?php esc_html_e('Name', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_existing_item_search_logic rch_existing_item_search_logic_slug"  name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_slug" value="slug"/>
                        <label for="rch_existing_item_search_logic_slug" class="rch_radio_label"><?php esc_html_e('Slug', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_existing_item_search_logic rch_existing_item_search_logic_cf"  name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_cf" value="cf"/>
                        <label for="rch_existing_item_search_logic_cf" class="rch_radio_label"><?php esc_html_e('Custom field', 'rch-woo-import-export'); ?></label>
                        <div class="rch_radio_container">
                            <table class="rch_search_based_on_cf_table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Name', 'rch-woo-import-export'); ?></th>
                                        <th><?php esc_html_e('Value', 'rch-woo-import-export'); ?></th>
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
                        <label for="rch_existing_item_search_logic_id" class="rch_radio_label"><?php esc_html_e('Term ID', 'rch-woo-import-export'); ?></label>
                        <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_existing_item_search_logic_id" name="rch_existing_item_search_logic_id" value=""/></div>
                    </div>
                </div>
                <?php
                return ob_get_clean();
        }

}

add_filter('rch_import_update_existing_item_fields', "rch_import_taxonomy_update_existing_item_fields", 10, 2);

if (!function_exists("rch_import_taxonomy_update_existing_item_fields")) {

        function rch_import_taxonomy_update_existing_item_fields($sections = "", $rch_import_type = "") {

                ob_start();
                ?>
                <div class="rch_field_mapping_container_element">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Update Existing items data', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_item_update rch_item_update_all" checked="checked" name="rch_item_update" id="rch_item_update_all" value="all"/>
                        <label for="rch_item_update_all" class="rch_radio_label"><?php esc_html_e('Update all data', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_item_update rch_item_update_specific" name="rch_item_update" id="rch_item_update_specific" value="specific"/>
                        <label for="rch_item_update_specific" class="rch_radio_label"><?php esc_html_e('Choose which data to update', 'rch-woo-import-export'); ?></label>
                        <div class="rch_radio_container">
                            <div class="rch_update_item_all_action"><?php esc_html_e('Check/Uncheck All', 'rch-woo-import-export'); ?></div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_name" checked="checked" name="is_update_item_name" id="is_update_item_name" value="1"/>
                                <label for="is_update_item_name" class="rch_checkbox_label"><?php esc_html_e('Name', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_description" checked="checked" name="is_update_item_description" id="is_update_item_description" value="1"/>
                                <label for="is_update_item_description" class="rch_checkbox_label"><?php esc_html_e('Description', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_slug" checked="checked" name="is_update_item_slug" id="is_update_item_slug" value="1"/>
                                <label for="is_update_item_slug" class="rch_checkbox_label"><?php esc_html_e('Slug', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_parent" checked="checked" name="is_update_item_parent" id="is_update_item_parent" value="1"/>
                                <label for="is_update_item_parent" class="rch_checkbox_label"><?php esc_html_e('Parent term', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_images " checked="checked" name="is_update_item_images" id="is_update_item_images" value="1"/>
                                <label for="is_update_item_images" class="rch_checkbox_label"><?php esc_html_e('Images', 'rch-woo-import-export'); ?></label>
                                <div class="rch_checkbox_container">
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_images  rch_item_update_images_all" checked="checked" name="rch_item_update_images" id="rch_item_update_images_all" value="all"/>
                                        <label for="rch_item_update_images_all" class="rch_radio_label"><?php esc_html_e('Update all images', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_images rch_item_update_images_append" name="rch_item_update_images" id="rch_item_update_images_append" value="append"/>
                                        <label for="rch_item_update_images_append" class="rch_radio_label"><?php esc_html_e("Don't touch existing images, append new images", 'rch-woo-import-export'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_cf" checked="checked" name="is_update_item_cf" id="is_update_item_cf" value="1"/>
                                <label for="is_update_item_cf" class="rch_checkbox_label"><?php esc_html_e('Term Meta', 'rch-woo-import-export'); ?></label>
                                <div class="rch_checkbox_container">
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_append" checked="checked" name="rch_item_update_cf" id="rch_item_update_cf_append" value="append"/>
                                        <label for="rch_item_update_cf_append" class="rch_radio_label"><?php esc_html_e('Update all Term Meta and keep meta if not found in file', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_all" name="rch_item_update_cf" id="rch_item_update_cf_all" value="all"/>
                                        <label for="rch_item_update_cf_all" class="rch_radio_label"><?php esc_html_e('Update all Term Meta and Remove meta if not found in file', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_includes" name="rch_item_update_cf" id="rch_item_update_cf_includes" value="includes"/>
                                        <label for="rch_item_update_cf_includes" class="rch_radio_label"><?php esc_html_e("Update only these Term Meta, leave the rest alone", 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container">
                                            <input type="text" class="rch_content_data_input rch_item_update_cf_includes_data" name="rch_item_update_cf_includes_data" value=""/>
                                        </div>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_excludes" name="rch_item_update_cf" id="rch_item_update_cf_excludes" value="excludes"/>
                                        <label for="rch_item_update_cf_excludes" class="rch_radio_label"><?php esc_html_e("Leave these fields alone, update all other Term Meta", 'rch-woo-import-export'); ?></label>
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
