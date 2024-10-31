<?php
if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'rch-woo-import-export'));
}

add_filter('rch_import_mapping_fields', "rch_import_post_mapping_fields", 10, 2);

if (!function_exists("rch_import_post_mapping_fields")) {

        function rch_import_post_mapping_fields($sections = array(), $rch_import_type = "") {

                global $wp_version;

                $rch_import_type_title = ucfirst($rch_import_type);

                $import = new \rch\import\RCH_Import();

                $rch_post_taxonomies = $import->rch_get_all_taxonomies(array('post_format'), array($rch_import_type), 'all');

                $uniqid = uniqid();

                unset($import);

                ob_start();
                ?>
                <div class="rch_field_mapping_container_wrapper">
                    <div class="rch_field_mapping_container_title rch_active"><?php esc_html_e('Title & Content', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
                    <div class="rch_field_mapping_container_data rch_show">
                        <div class="rch_field_mapping_container_element">
                            <input type="text" class="rch_content_data_input rch_item_title" name="rch_item_title" placeholder="<?php esc_attr_e('Title', 'rch-woo-import-export'); ?>"/>
                        </div>
                        <div class="rch_field_mapping_container_element rch_import_content_editor_wrapper rch_hide_if_shop_coupon">
                            <textarea class="rch_content_data_textarea rch_item_content" placeholder="<?php esc_attr_e('Content', 'rch-woo-import-export'); ?>" name="rch_item_content"></textarea>
                        </div>
                        <div class="rch_field_mapping_container_element">
                            <input type="text" class="rch_content_data_input rch_item_excerpt" name="rch_item_excerpt" placeholder="<?php esc_attr_e('Excerpt', 'rch-woo-import-export'); ?>"/>
                        </div>
                    </div>
                </div>

                <?php
                $title_n_content = ob_get_clean();

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
                    <div class="rch_field_mapping_container_title"><?php esc_html_e('Custom Fields', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div>
                    <div class="rch_field_mapping_container_data">
                        <div class="rch_cf_wrapper">
                            <div class="rch_field_mapping_radio_input_wrapper rch_cf_notice_wrapper">
                                <input type="checkbox" id="rch_item_not_add_empty" name="rch_item_not_add_empty" checked="checked" value="1" class="rch_checkbox rch_item_not_add_empty">
                                <label class="rch_checkbox_label" for="rch_item_not_add_empty"><?php esc_html_e("Don't add Empty value fields in database.", 'rch-woo-import-export'); ?><i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("it's highly recommended. If custom field value is empty then it skip perticular field and not add to database. it's save memory and increase import speed", "rch-woo-import-export"); ?>"></i></label>
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
                $cf_section = ob_get_clean();

                ob_start();

                if (!empty($rch_post_taxonomies)) {
                        ?>
                        <div class="rch_field_mapping_container_wrapper">
                            <div class="rch_field_mapping_container_title"><?php esc_html_e('Taxonomies, Categories, Tags', 'rch-woo-import-export'); ?><div class="rch_layout_header_icon_wrapper"></div></div>
                            <div class="rch_field_mapping_container_data">

                                <?php
                                foreach ($rch_post_taxonomies as $slug => $tax) {

                                        if (!empty($tax->labels->name) && strpos($tax->labels->name, "_") === false) {
                                                $name = $tax->labels->name;
                                        } else {
                                                $name = empty($tax->labels->singular_name) ? $tax->name : $tax->labels->singular_name;
                                        }
                                        ?>
                                        <div class="rch_field_mapping_container_element">
                                            <input id="rch_item_set_taxonomy_<?php echo esc_attr($slug); ?>" type="checkbox" class="rch_checkbox rch_field_mapping_tax_wrapper rch_item_set_taxonomy rch_item_set_taxonomy_<?php echo esc_attr($slug); ?>" name="rch_item_set_taxonomy[<?php echo esc_attr($slug); ?>]" value="1">
                                            <label for="rch_item_set_taxonomy_<?php echo esc_attr($slug); ?>" class="rch_checkbox_label"><?php echo esc_html($name); ?></label>
                                            <div class="rch_checkbox_container rch_field_mapping_tax_data">
                                                <div class="rch_field_mapping_radio_input_wrapper rch_field_mapping_cat_inner_wrapper">
                                                    <div class="rch_cat_inner_data_wrapper">
                                                        <div class="rch_half_container">
                                                            <input type="text" class="rch_content_data_input rch_item_taxonomy rch_item_taxonomy_<?php echo esc_attr($slug); ?>" name="rch_item_taxonomy[<?php echo esc_attr($slug); ?>]" value=""/>
                                                        </div>
                                                        <div class="rch_half_container rch_taxonomy_delim_wrapper">
                                                            <div class="rch_field_mapping_image_separator"><?php esc_html_e('Separate by', 'rch-woo-import-export'); ?></div>
                                                            <input type="text" class="rch_content_data_input rch_field_mapping_input_separator rch_item_taxonomy_delim rch_item_taxonomy_delim_<?php echo esc_attr($slug); ?>" name="rch_item_taxonomy_delim[<?php echo esc_attr($slug); ?>]" placeholder="," value=","/>
                                                        </div>
                                                    </div>
                                                    <?php if ($tax->hierarchical) { ?>
                                                            <div class="rch_cat_inner_data_wrapper rch_cat_group_sep_wrapper rch_cat_sep_wrapper">
                                                                <div class="rch_field_mapping_image_separator"><?php echo esc_html(sprintf(__('Separate %s hierarchy (parent/child) via symbol (i.e. Clothing > Men > TShirts)', 'rch-woo-import-export'), $name)); ?></div>
                                                                <input type="text" class="rch_content_data_input rch_field_mapping_input_separator rch_item_taxonomy_hierarchical_delim rch_item_taxonomy_hierarchical_delim_<?php echo esc_attr($slug); ?>" name="rch_item_taxonomy_hierarchical_delim[<?php echo esc_attr($slug); ?>]" placeholder=">" value=">"/>
                                                            </div>
                                                            <div class="rch_cat_inner_data_wrapper">
                                                                <input type="checkbox" class="rch_checkbox rch_item_taxonomy_child_only_<?php echo esc_attr($slug); ?>"  name="rch_item_taxonomy_child_only[<?php echo esc_attr($slug); ?>]" id="rch_item_taxonomy_child_only_<?php echo esc_attr($slug); ?>" value="1"/>
                                                                <label for="rch_item_taxonomy_child_only_<?php echo esc_attr($slug); ?>" class="rch_checkbox_label"><?php echo esc_html(sprintf(__('Only assign %s to the bottom level term in the hierarchy', 'rch-woo-import-export'), $name)); ?></label>                                            
                                                            </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        unset($name);
                                }
                                ?>
                            </div>
                        </div>

                        <?php
                }
                $taxonomy_section = ob_get_clean();

                ob_start();

                $rch_is_support_post_format = ( current_theme_supports('post-formats') && post_type_supports($rch_import_type, 'post-formats') ) ? true : false;
                ?>
                <div class="rch_field_mapping_container_wrapper rch_other_item_option_wrapper">
                    <div class="rch_field_mapping_container_title"><?php echo esc_html(__('Other', 'rch-woo-import-export') . ' ' . $rch_import_type_title . ' ' . __('Options', 'rch-woo-import-export')); ?> <div class="rch_layout_header_icon_wrapper"></div></div>
                    <div class="rch_field_mapping_container_data  rch_field_mapping_other_option_outer_wrapper">
                        <div class="rch_field_mapping_container_element">
                            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Post Status', 'rch-woo-import-export'); ?></div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_status rch_item_status_publish" checked="checked" name="rch_item_status" id="rch_field_mapping_post_type_published" value="publish"/>
                                <label for="rch_field_mapping_post_type_published" class="rch_radio_label"><?php esc_html_e('Published', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_status rch_item_status_draft"  name="rch_item_status" id="rch_field_mapping_post_type_draft" value="draft"/>
                                <label for="rch_field_mapping_post_type_draft" class="rch_radio_label"><?php esc_html_e('Draft', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_status rch_item_status_as_specified" name="rch_item_status" id="rch_field_mapping_post_type_as_specified" value="as_specified"/>
                                <label for="rch_field_mapping_post_type_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                <div class="rch_radio_container rch_as_specified_wrapper">
                                    <input type="text" class="rch_content_data_input rch_item_status_as_specified_data" name="rch_item_status_as_specified_data" value=""/>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('publish', 'draft', 'trash', 'private').", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rch_field_mapping_container_element">
                            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Post Dates', 'rch-woo-import-export'); ?></div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_date rch_item_date_now"  checked="checked"  name="rch_item_date" id="rch_field_mapping_post_date_now" value="now"/>
                                <label for="rch_field_mapping_post_date_now" class="rch_radio_label"><?php esc_html_e('Now', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_date rch_item_date_as_specified" name="rch_item_date" id="rch_field_mapping_post_date_as_specified" value="as_specified"/>
                                <label for="rch_field_mapping_post_date_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                <div class="rch_radio_container rch_as_specified_wrapper">
                                    <input type="text" class="rch_content_data_input rch_item_date_as_specified_data" name="rch_item_date_as_specified_data" value=""/>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Use any format supported by the PHP strtotime function. That means pretty much any human-readable date will work.", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rch_field_mapping_container_element">
                            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Comments', 'rch-woo-import-export'); ?></div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_comment_status rch_item_comment_status_open" checked="checked" name="rch_item_comment_status" id="rch_field_mapping_comment_open" value="open"/>
                                <label for="rch_field_mapping_comment_open" class="rch_radio_label"><?php esc_html_e('Open', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_comment_status rch_item_comment_status_closed"  name="rch_item_comment_status" id="rch_field_mapping_comment_closed" value="closed"/>
                                <label for="rch_field_mapping_comment_closed" class="rch_radio_label"><?php esc_html_e('Closed', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_comment_status rch_item_comment_status_as_specified"  name="rch_item_comment_status" id="rch_field_mapping_comment_as_specified" value="as_specified"/>
                                <label for="rch_field_mapping_comment_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                <div class="rch_radio_container rch_as_specified_wrapper">
                                    <input type="text" class="rch_content_data_input rch_item_comment_status_as_specified_data" name="rch_item_comment_status_as_specified_data" value=""/>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('open', 'closed').", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rch_field_mapping_container_element">
                            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Trackbacks and Pingbacks', 'rch-woo-import-export'); ?></div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_ping_status rch_item_ping_status_open" checked="checked" name="rch_item_ping_status" id="rch_item_ping_status_open" value="open"/>
                                <label for="rch_item_ping_status_open" class="rch_radio_label"><?php esc_html_e('Open', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_ping_status rch_item_ping_status_closed"  name="rch_item_ping_status" id="rch_import_ping_status_closed" value="closed"/>
                                <label for="rch_import_ping_status_closed" class="rch_radio_label"><?php esc_html_e('Closed', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_ping_status rch_item_ping_status_as_specified"  name="rch_item_ping_status" id="rch_import_ping_status_data" value="as_specified"/>
                                <label for="rch_import_ping_status_data" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                <div class="rch_radio_container rch_as_specified_wrapper">
                                    <input type="text" class="rch_content_data_input rch_item_ping_status_as_specified_data" name="rch_item_ping_status_as_specified_data" value=""/>
                                    <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("The value should be one of the following: ('open', 'closed').", "rch-woo-import-export"); ?>"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rch_field_mapping_container_element">
                            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Post Slug', 'rch-woo-import-export'); ?></div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="text" class="rch_content_data_input rch_item_slug" name="rch_item_slug" value=""/>
                            </div>
                        </div>
                        <div class="rch_field_mapping_container_element">
                            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Post Author', 'rch-woo-import-export'); ?></div>
                            <div class="rch_field_mapping_other_option_wrapper rch_as_specified_wrapper">
                                <input type="text" class="rch_content_data_input rch_item_author" name="rch_item_author" value=""/>
                                <i class="far fa-question-circle rch_data_tipso" data-tipso="<?php esc_attr_e("Assign the post to an existing user account by specifying the user ID, username, or e-mail address.", "rch-woo-import-export"); ?>"></i>
                            </div>
                        </div>
                        <div class="rch_field_mapping_container_element">
                            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Download & Import Attachments', 'rch-woo-import-export'); ?></div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="text" class="rch_content_data_input rch_item_attachments" name="rch_item_attachments" value=""/>
                                <div class="rch_field_mapping_post_attach_wrapper">
                                    <div class="rch_field_mapping_post_attach_sep_label"><?php esc_html_e('Separated by', 'rch-woo-import-export'); ?></div>
                                    <div class="rch_field_mapping_post_attach_sep_input"><input type="text" class="rch_content_data_input rch_item_attachments_delim" name="rch_item_attachments_delim" value="|"/></div>
                                </div>
                                <input type="checkbox" class="rch_checkbox rch_item_attachement_search_for_existing"  name="rch_item_attachement_search_for_existing" id="rch_item_attachement_search_for_existing" value="1"/>
                                <label for="rch_item_attachement_search_for_existing" class="rch_checkbox_label"><?php esc_html_e('Search for existing attachments to prevent duplicates in media library', 'rch-woo-import-export'); ?></label>
                            </div>
                        </div>

                        <?php if ($rch_is_support_post_format) { ?>
                                <div class="rch_field_mapping_container_element">
                                    <div class="rch_field_mapping_inner_title"><?php echo esc_html($rch_import_type_title . " " . __('Format', 'rch-woo-import-export')); ?></div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_post_format rch_item_post_format_standard" checked="checked" name="rch_item_post_format" id="rch_item_post_format_standard" value="standard"/>
                                        <label for="rch_item_post_format_standard" class="rch_radio_label"><?php esc_html_e('Standard', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_post_format rch_item_post_format_aside"  name="rch_item_post_format" id="rch_item_post_format_aside" value="aside"/>
                                        <label for="rch_item_post_format_aside" class="rch_radio_label"><?php esc_html_e('Aside', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_post_format rch_item_post_format_image" name="rch_item_post_format" id="rch_item_post_format_image" value="image"/>
                                        <label for="rch_item_post_format_image" class="rch_radio_label"><?php esc_html_e('Image', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_post_format rch_item_post_format_video" name="rch_item_post_format" id="rch_item_post_format_video" value="video"/>
                                        <label for="rch_item_post_format_video" class="rch_radio_label"><?php esc_html_e('Video', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_post_format rch_item_post_format_quote" name="rch_item_post_format" id="rch_item_post_format_quote" value="quote"/>
                                        <label for="rch_item_post_format_quote" class="rch_radio_label"><?php esc_html_e('Quote', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_post_format rch_item_post_format_link"  name="rch_item_post_format" id="rch_item_post_format_link" value="link"/>
                                        <label for="rch_item_post_format_link" class="rch_radio_label"><?php esc_html_e('Link', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_post_format rch_item_post_format_gallery"  name="rch_item_post_format" id="rch_item_post_format_gallery" value="gallery"/>
                                        <label for="rch_item_post_format_gallery" class="rch_radio_label"><?php esc_html_e('Gallery', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_post_format rch_item_post_format_audio" name="rch_item_post_format" id="rch_item_post_format_audio" value="audio"/>
                                        <label for="rch_item_post_format_audio" class="rch_radio_label"><?php esc_html_e('Audio', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_post_format rch_item_post_format_as_specified" name="rch_item_post_format" id="rch_item_post_format_as_specified" value="as_specified"/>
                                        <label for="rch_item_post_format_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper"><input type="text" class="rch_content_data_input rch_item_post_format_as_specified_data" name="rch_item_post_format_as_specified_data" value=""/></div>
                                    </div>
                                </div>
                        <?php } ?>

                        <?php if ('page' == $rch_import_type || version_compare($wp_version, '4.7.0', '>=')) { ?>
                                <div class="rch_field_mapping_container_element">
                                    <div class="rch_field_mapping_inner_title"><?php echo esc_html($rch_import_type_title . " " . __('Template', 'rch-woo-import-export')); ?></div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_template rch_item_template_as_specified" checked="checked"  name="rch_item_template" id="rch_item_template_as_specified" value="as_specified"/>
                                        <label for="rch_item_template_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container" style="display: block;"><input type="text" class="rch_content_data_input rch_item_template_as_specified_data" name="rch_item_template_as_specified_data" value=""/></div>
                                    </div>
                                </div>
                        <?php } ?>
                        <?php if ('page' == $rch_import_type) { ?>
                                <div class="rch_field_mapping_container_element  rch_field_mapping_data_option">
                                    <div class="rch_field_mapping_inner_title"><?php echo esc_html($rch_import_type_title . " " . __('Parent', 'rch-woo-import-export')); ?></div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_parent rch_item_parent_manually" checked="checked" name="rch_item_parent" id="rch_item_parent" value="manually"/>
                                        <label for="rch_item_parent" class="rch_radio_label"><?php esc_html_e('Select page parent', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_field_mapping_option_wrapper" style="display: block;">
                                            <?php wp_dropdown_pages(array('post_type' => 'page', 'selected' => '', 'class' => 'rch_content_data_select', 'name' => 'rch_item_parent_data', 'show_option_none' => __('(no parent)', 'rch-woo-import-export'), 'sort_column' => 'menu_order, post_title', 'number' => 500)); ?>
                                        </div>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_field_mapping_other_option_radio rch_item_parent rch_item_parent_as_specified" name="rch_item_parent" id="rch_item_parent_as_specified" value="as_specified"/>
                                        <label for="rch_item_parent_as_specified" class="rch_radio_label"><?php esc_html_e('As specified', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container rch_as_specified_wrapper"><input type="text" class="rch_content_data_input rch_item_parent_as_specified_data" name="rch_item_parent_as_specified_data" value=""/></div>
                                    </div>
                                </div>
                        <?php } ?>
                        <div class="rch_field_mapping_container_element">
                            <div class="rch_field_mapping_inner_title"><?php esc_html_e('Menu Order', 'rch-woo-import-export'); ?></div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="text" class="rch_content_data_input rch_item_order" name="rch_item_order" value=""/>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $other_section = ob_get_clean();

                $sections = array_replace($sections, array(
                        '100' => $title_n_content,
                        '200' => $image_section,
                        '300' => $cf_section,
                        '400' => $taxonomy_section,
                        '500' => $other_section,
                        )
                );

                unset($rch_import_type_title, $title_n_content, $image_section, $cf_section, $taxonomy_section, $rch_is_support_post_format, $rch_post_taxonomies);

                return apply_filters("rch_pre_post_field_mapping_section", $sections, $rch_import_type);
        }

}

add_filter('rch_import_search_existing_item', "rch_import_post_search_existing_item", 10, 2);

if (!function_exists("rch_import_post_search_existing_item")) {

        function rch_import_post_search_existing_item($sections = "", $rch_import_type = "") {

                ob_start();
                ?>
                <div class="rch_field_mapping_container_element">
                    <div class="rch_field_mapping_inner_title"><?php esc_html_e('Search Existing Item on your site based on...', 'rch-woo-import-export'); ?></div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_existing_item_search_logic rch_existing_item_search_logic_title" checked="checked" name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_title" value="title"/>
                        <label for="rch_existing_item_search_logic_title" class="rch_radio_label"><?php esc_html_e('Title', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_existing_item_search_logic rch_existing_item_search_logic_content"  name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_content" value="content"/>
                        <label for="rch_existing_item_search_logic_content" class="rch_radio_label"><?php esc_html_e('Content', 'rch-woo-import-export'); ?></label>
                    </div>
                    <div class="rch_field_mapping_other_option_wrapper">
                        <input type="radio" class="rch_radio rch_existing_item_search_logic rch_existing_item_search_logic_cf"  name="rch_existing_item_search_logic" id="rch_existing_item_search_logic_cf" value="cf"/>
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
                        <label for="rch_existing_item_search_logic_id" class="rch_radio_label"><?php esc_html_e('Post ID', 'rch-woo-import-export'); ?></label>
                        <div class="rch_radio_container"><input type="text" class="rch_content_data_input rch_existing_item_search_logic_id" name="rch_existing_item_search_logic_id" value=""/></div>
                    </div>
                </div>
                <?php
                return apply_filters("rch_import_post_search_existing_item", $sections . ob_get_clean(), $rch_import_type);
        }

}

add_filter('rch_import_update_existing_item_fields', "rch_import_post_update_existing_item_fields", 10, 2);

if (!function_exists("rch_import_post_update_existing_item_fields")) {

        function rch_import_post_update_existing_item_fields($sections = "", $rch_import_type = "") {

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
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_post_status" checked="checked" name="is_update_item_post_status" id="is_update_item_post_status" value="1"/>
                                <label for="is_update_item_post_status" class="rch_checkbox_label"><?php esc_html_e('Post status', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_title" checked="checked" name="is_update_item_title" id="is_update_item_title" value="1"/>
                                <label for="is_update_item_title" class="rch_checkbox_label"><?php esc_html_e('Title', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_author" checked="checked" name="is_update_item_author" id="is_update_item_author" value="1"/>
                                <label for="is_update_item_author" class="rch_checkbox_label"><?php esc_html_e('Author', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_slug" checked="checked" name="is_update_item_slug" id="is_update_item_slug" value="1"/>
                                <label for="is_update_item_slug" class="rch_checkbox_label"><?php esc_html_e('Slug', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_content" checked="checked" name="is_update_item_content" id="is_update_item_content" value="1"/>
                                <label for="is_update_item_content" class="rch_checkbox_label"><?php esc_html_e('Content', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_excerpt" checked="checked" name="is_update_item_excerpt" id="is_update_item_excerpt" value="1"/>
                                <label for="is_update_item_excerpt" class="rch_checkbox_label"><?php esc_html_e('Excerpt', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_dates" checked="checked" name="is_update_item_dates" id="is_update_item_dates" value="1"/>
                                <label for="is_update_item_dates" class="rch_checkbox_label"><?php esc_html_e('Dates', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_order" checked="checked" name="is_update_item_order" id="is_update_item_order" value="1"/>
                                <label for="is_update_item_order" class="rch_checkbox_label"><?php esc_html_e('Menu order', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_parent" checked="checked" name="is_update_item_parent" id="is_update_item_parent" value="1"/>
                                <label for="is_update_item_parent" class="rch_checkbox_label"><?php esc_html_e('Parent post', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_post_type" checked="checked" name="is_update_item_post_type" id="is_update_item_post_type" value="1"/>
                                <label for="is_update_item_post_type" class="rch_checkbox_label"><?php esc_html_e('Post type', 'rch-woo-import-export'); ?></label>
                            </div>
                            <?php
                            if ($rch_import_type == "product") {
                                    $commnet_status = __('Enable review setting', 'rch-woo-import-export');
                            } else {
                                    $commnet_status = __('Comment status', 'rch-woo-import-export');
                            }
                            ?>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_comment_status" checked="checked" name="is_update_item_comment_status" id="is_update_item_comment_status" value="1"/>
                                <label for="is_update_item_comment_status" class="rch_checkbox_label"><?php echo esc_html($commnet_status); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_ping_status" checked="checked" name="is_update_item_ping_status" id="is_update_item_ping_status" value="1"/>
                                <label for="is_update_item_ping_status" class="rch_checkbox_label"><?php esc_html_e('Ping Status', 'rch-woo-import-export'); ?></label>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_attachments" checked="checked" name="is_update_item_attachments" id="is_update_item_attachments" value="1"/>
                                <label for="is_update_item_attachments" class="rch_checkbox_label"><?php esc_html_e('Attachments', 'rch-woo-import-export'); ?></label>
                            </div>
                            <?php if ($rch_import_type == "product") { ?>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_product_type" checked="checked" name="is_update_item_product_type" id="is_update_item_product_type" value="1"/>
                                        <label for="is_update_item_product_type" class="rch_checkbox_label"><?php esc_html_e('Product Type', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_catalog_visibility" checked="checked" name="is_update_item_catalog_visibility" id="is_update_item_catalog_visibility" value="1"/>
                                        <label for="is_update_item_catalog_visibility" class="rch_checkbox_label"><?php esc_html_e('Catalog Visibility', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_featured_status" checked="checked" name="is_update_item_featured_status" id="is_update_item_featured_status" value="1"/>
                                        <label for="is_update_item_featured_status" class="rch_checkbox_label"><?php esc_html_e('Featured Status', 'rch-woo-import-export'); ?></label>
                                    </div>

                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_attributes" checked="checked" name="is_update_item_attributes" id="is_update_item_attributes" value="1"/>
                                        <label for="is_update_item_attributes" class="rch_checkbox_label"><?php esc_html_e('Attributes', 'rch-woo-import-export'); ?></label>
                                        <div class="rch_checkbox_container">
                                            <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="radio" class="rch_radio rch_item_update_attributes rch_item_update_attributes_all" checked="checked" name="rch_item_update_attributes" id="rch_item_update_attributes_all" value="all"/>
                                                <label for="rch_item_update_attributes_all" class="rch_radio_label"><?php esc_html_e('Update all Attributes', 'rch-woo-import-export'); ?></label>
                                            </div>
                                            <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="radio" class="rch_radio rch_item_update_attributes rch_item_update_attributes_includes" name="rch_item_update_attributes" id="rch_item_update_attributes_includes" value="includes"/>
                                                <label for="rch_item_update_attributes_includes" class="rch_radio_label"><?php esc_html_e("Update only these Attributes, leave the rest alone", 'rch-woo-import-export'); ?></label>
                                                <div class="rch_radio_container">
                                                    <input type="text" class="rch_content_data_input rch_item_update_attributes_includes_data" name="rch_item_update_attributes_includes_data" value=""/>
                                                </div>
                                            </div>
                                            <div class="rch_field_mapping_other_option_wrapper">
                                                <input type="radio" class="rch_radio " name="rch_item_update_attributes" id="rch_item_update_attributes_excludes" value="excludes"/>
                                                <label for="rch_item_update_attributes_excludes" class="rch_radio_label"><?php esc_html_e("Leave these attributes alone, update all other Attributes", 'rch-woo-import-export'); ?></label>
                                                <div class="rch_radio_container">
                                                    <input type="text" class="rch_content_data_input rch_item_update_attributes_excludes_data" name="rch_item_update_attributes_excludes_data" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php } ?>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_images" checked="checked" name="is_update_item_images" id="is_update_item_images" value="1"/>
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
                                <label for="is_update_item_cf" class="rch_checkbox_label"><?php esc_html_e('Custom Fields', 'rch-woo-import-export'); ?></label>
                                <div class="rch_checkbox_container">
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_append" checked="checked" name="rch_item_update_cf" id="rch_item_update_cf_append" value="append"/>
                                        <label for="rch_item_update_cf_append" class="rch_radio_label"><?php esc_html_e('Update all Custom Fields and keep fields if not found in file', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_all" name="rch_item_update_cf" id="rch_item_update_cf_all" value="all"/>
                                        <label for="rch_item_update_cf_all" class="rch_radio_label"><?php esc_html_e('Update all Custom Fields and Remove fields if not found in file', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_includes" name="rch_item_update_cf" id="rch_item_update_cf_includes" value="includes"/>
                                        <label for="rch_item_update_cf_includes" class="rch_radio_label"><?php esc_html_e("Update only these Custom Fields, leave the rest alone", 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container">
                                            <input type="text" class="rch_content_data_input rch_item_update_cf_includes_data" name="rch_item_update_cf_includes_data" value=""/>
                                        </div>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_cf rch_item_update_cf_excludes" name="rch_item_update_cf" id="rch_item_update_cf_excludes" value="excludes"/>
                                        <label for="rch_item_update_cf_excludes" class="rch_radio_label"><?php esc_html_e("Leave these fields alone, update all other Custom Fields", 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container">
                                            <input type="text" class="rch_content_data_input rch_item_update_cf_excludes_data" name="rch_item_update_cf_excludes_data" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rch_field_mapping_other_option_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_item_update_field is_update_item_taxonomies" checked="checked" name="is_update_item_taxonomies" id="is_update_item_taxonomies" value="1"/>
                                <label for="is_update_item_taxonomies" class="rch_checkbox_label"><?php esc_html_e('Taxonomies (incl. Categories and Tags)', 'rch-woo-import-export'); ?></label>
                                <div class="rch_checkbox_container">
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_taxonomies rch_item_update_taxonomies_includes" name="rch_item_update_taxonomies" id="rch_item_update_taxonomies_includes" value="includes"/>
                                        <label for="rch_item_update_taxonomies_includes" class="rch_radio_label"><?php esc_html_e("Update only these taxonomies, leave the rest alone", 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container">
                                            <input type="text" class="rch_content_data_input rch_item_update_taxonomies_includes_data" name="rch_item_update_taxonomies_includes_data" value=""/>
                                        </div>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_taxonomies rch_item_update_taxonomies_excludes" name="rch_item_update_taxonomies" id="rch_item_update_taxonomies_excludes" value="excludes"/>
                                        <label for="rch_item_update_taxonomies_excludes" class="rch_radio_label"><?php esc_html_e("Leave these taxonomies alone, update all others", 'rch-woo-import-export'); ?></label>
                                        <div class="rch_radio_container">
                                            <input type="text" class="rch_content_data_input rch_item_update_taxonomies_excludes_data" name="rch_item_update_taxonomies_excludes_data" value=""/>
                                        </div>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_taxonomies rch_item_update_taxonomies_all" checked="checked" name="rch_item_update_taxonomies" id="rch_item_update_taxonomies_all" value="all"/>
                                        <label for="rch_item_update_taxonomies_all" class="rch_radio_label"><?php esc_html_e('Remove existing taxonomies, add new taxonomies', 'rch-woo-import-export'); ?></label>
                                    </div>
                                    <div class="rch_field_mapping_other_option_wrapper">
                                        <input type="radio" class="rch_radio rch_item_update_taxonomies rch_item_update_taxonomies_append"  name="rch_item_update_taxonomies" id="rch_item_update_taxonomies_append" value="append"/>
                                        <label for="rch_item_update_taxonomies_append" class="rch_radio_label"><?php esc_html_e('Only add new', 'rch-woo-import-export'); ?></label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <?php
                $sections .= ob_get_clean();

                return apply_filters("rch_import_post_update_item_fields", $sections, $rch_import_type);
        }

}