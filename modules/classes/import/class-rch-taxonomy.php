<?php


namespace rch\import\taxonomy;

use rch\import\addons;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

if (file_exists(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-engine.php')) {

    require_once(RCH_IMPORT_CLASSES_DIR . '/class-rch-import-engine.php');
}

class RCH_Taxonomy extends \rch\import\engine\RCH_Import_Engine {

    protected $import_type = "taxonomy";

    public function process_import_data() {

        global $wpdb;

        $this->rch_final_data['taxonomy_type'] = rch_sanitize_field($this->get_field_value('rch_taxonomy_type', true));

        if ($this->is_update_field("name")) {

            $this->rch_final_data['name'] = rch_sanitize_field($this->get_field_value('rch_item_term_name'));
        }
        if ($this->is_update_field("description")) {

            $this->rch_final_data['description'] = rch_sanitize_field($this->get_field_value('rch_item_term_description'));
        }
        if ($this->is_update_field("slug")) {

            $term_slug = rch_sanitize_field($this->get_field_value('rch_item_term_slug', false, true));

            if ($term_slug != "auto") {
                $this->rch_final_data['slug'] = $term_slug;
            }

            unset($term_slug);
        }

        if ($this->is_update_field("parent")) {

            $parent = rch_sanitize_field($this->get_field_value('rch_item_term_parent'));

            $parent_term = get_term_by('slug', $parent, $this->rch_final_data['taxonomy_type']) or $parent_term = get_term_by('name', $parent, $this->rch_final_data['taxonomy_type']) or ( ctype_digit($parent) and $parent_term = get_term_by('id', $parent, $this->rch_final_data['taxonomy_type']));

            if (!empty($parent_term) && !is_wp_error($parent_term)) {
                $this->rch_final_data['parent'] = $parent_term->term_id;
            }

            unset($parent, $parent_term);
        }

        $this->rch_final_data = apply_filters('rch_before_term_import', $this->rch_final_data, $this->rch_import_option);


        if ($this->is_new_item) {

            $term = wp_insert_term($this->rch_final_data['name'], $this->rch_final_data['taxonomy_type'], $this->rch_final_data);
        } else {

            $term = wp_update_term($this->existing_item_id, $this->rch_final_data['taxonomy_type'], $this->rch_final_data);
        }

        $this->process_log['imported'] ++;

        if (is_wp_error($term)) {

            $this->set_log('<strong>' . __('ERROR', 'rch-woo-import-export') . '</strong> : ' . $term->get_error_message());

            $this->process_log['skipped'] ++;

            return true;
        } elseif ($term == 0) {

            $this->set_log('<strong>' . __('ERROR', 'rch-woo-import-export') . '</strong> : ' . __('something wrong, ID = 0 was generated.', 'rch-woo-import-export'));

            $this->process_log['skipped'] ++;

            return true;
        }

        $this->item_id = isset($term['term_id']) ? $term['term_id'] : 0;

        unset($term);

        if ($this->is_new_item) {
            $this->process_log['created'] ++;
        } else {
            $this->process_log['updated'] ++;
        }

        if ($this->backup_service !== false && $this->is_new_item) {
            $this->backup_service->create_backup($this->item_id, true);
        }
        $this->item = get_term_by('id', $this->item_id, $this->rch_final_data['taxonomy_type']);

        $this->process_log['last_records_id'] = $this->item_id;

        $this->process_log['last_records_status'] = 'pending';

        $this->process_log['last_activity'] = date('Y-m-d H:i:s');

        $wpdb->update($wpdb->prefix . "rch_template", array('last_update_date' => current_time('mysql'), 'process_log' => maybe_serialize($this->process_log)), array('id' => $this->rch_import_id));

        do_action('rch_after_term_import', $this->item_id, $this->rch_final_data, $this->rch_import_option);

        if ($this->is_update_field("cf")) {

            $this->rch_import_cf();
        }
        if ($this->is_update_field("images")) {

            $this->rch_import_images();
        }

        return $this->item_id;
    }

    protected function search_duplicate_item() {

        global $wpdb;

        $rch_duplicate_indicator = rch_sanitize_field($this->get_field_value('rch_existing_item_search_logic', true));

        $taxonomy_type = rch_sanitize_field($this->get_field_value('rch_taxonomy_type'));

        if ($rch_duplicate_indicator == "id") {

            $duplicate_id = absint(rch_sanitize_field($this->get_field_value('rch_existing_item_search_logic_id')));

            if ($duplicate_id > 0) {

                $term = get_term_by('id', $duplicate_id, $taxonomy_type);

                if (!empty($term) && !is_wp_error($term)) {
                    $this->existing_item_id = $duplicate_id;
                }
                unset($term);
            }
            unset($duplicate_id);
        } elseif ($rch_duplicate_indicator == "slug") {

            $slug = rch_sanitize_field($this->get_field_value('rch_item_term_slug', false, true));

            if (!empty($slug)) {

                $term = get_term_by('slug', $slug, $taxonomy_type);

                if (!empty($term) && !is_wp_error($term)) {
                    $this->existing_item_id = $term->term_id;
                }
                unset($term);
            }
            unset($slug);
        } elseif ($rch_duplicate_indicator == "name") {

            $name = rch_sanitize_field($this->get_field_value('rch_item_term_name'));

            if (!empty($name)) {

                $term = get_term_by('name', $name, $taxonomy_type);
                if (!empty($term) && !is_wp_error($term)) {
                    $this->existing_item_id = $term->term_id;
                }
                unset($term);
            }

            unset($name);
        } elseif ($rch_duplicate_indicator == "cf") {

            $meta_key = rch_sanitize_field($this->get_field_value('rch_existing_item_search_logic_cf_key'));

            $meta_val = rch_sanitize_field($this->get_field_value('rch_existing_item_search_logic_cf_value'));

            if (!empty($meta_key)) {

                $args = array(
                    'taxonomy' => $taxonomy_type,
                    'number' => 1,
                    'hide_empty' => false,
                    'meta_query' => array(
                        array(
                            'key' => $meta_key,
                            'value' => $meta_val,
                            'compare' => '='
                        )
                    )
                );


                global $wp_version;

                if (version_compare($wp_version, '4.5.0', '<')) {

                    $terms = get_terms($taxonomy_type, $args);
                } else {
                    $terms = get_terms($args);
                }

                if (!empty($terms) && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $this->existing_item_id = $term->term_id;
                        break;
                    }
                }
                unset($terms, $args);
            }

            unset($meta_key, $meta_val);
        }

        unset($taxonomy_type, $rch_duplicate_indicator);
    }

    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
