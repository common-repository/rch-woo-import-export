<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

global $wpdb;

$schedule_templates = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "rch_template where `opration`='schedule_import_template'");
?>
<div class="rch_section_wrapper">
    <div class="rch_content_data_header rch_section_wrapper_selected">
        <div class="rch_content_title"><?php esc_html_e('Manage Schedule Import', 'rch-woo-import-export'); ?></div>
        <div class="rch_layout_header_icon_wrapper"></div>
    </div>
    <div class="rch_section_content rch_show rch_schedule_import_section">
        <div class="rch_table_action_wrapper">
            <div class="rch_table_action_container">
                <select class="rch_content_data_select rch_log_bulk_action">
                    <option value=""><?php esc_html_e('Bulk Actions', 'rch-woo-import-export'); ?></option>   
                    <option value="delete"><?php esc_html_e('Delete', 'rch-woo-import-export'); ?></option>   
                </select>
            </div>
            <div class="rch_table_action_btn_container">
                <div class="rch_btn rch_btn_secondary rch_btn_radius rch_log_action_btn">
                    <?php esc_html_e('Apply', 'rch-woo-import-export'); ?>
                </div>
            </div>
        </div>
        <table class="rch_log_table table table-bordered">
            <thead>
                <tr>
                    <td class="rch_log_check_wrapper">
                        <input type="checkbox" class="rch_checkbox rch_log_check_all" id="rch_scheduled_log_check_all" value="1"/>
                        <label for="rch_scheduled_log_check_all" class="rch_checkbox_label"></label>
                    </td>
                    <td class="rch_log_lable"><?php esc_html_e('Scheduled ID', 'rch-woo-import-export'); ?></td>
                    <td class="rch_log_lable"><?php esc_html_e('Scheduled Name', 'rch-woo-import-export'); ?></td>
                    <td class="rch_log_lable"><?php esc_html_e('Export Type', 'rch-woo-import-export'); ?></td>
                    <td class="rch_log_lable"><?php esc_html_e('Recurrence Time', 'rch-woo-import-export'); ?></td>
                    <td class="rch_log_lable"><?php esc_html_e('Send E-mail', 'rch-woo-import-export'); ?></td>
                    <td class="rch_log_lable"><?php esc_html_e('Recipients', 'rch-woo-import-export'); ?></td>
                    <td class="rch_log_lable"><?php esc_html_e('Next event', 'rch-woo-import-export'); ?></td>
                    <td class="rch_log_lable"><?php esc_html_e('Actions', 'rch-woo-import-export'); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $is_empty_template = "";
                if (!empty($schedule_templates)) {
                    $is_empty_template = "rch_hidden";
                    foreach ($schedule_templates as $template) {

                        $id = isset($template->id) ? $template->id : 0;

                        $opration_type = isset($template->opration_type) ? $template->opration_type : "";

                        $options = isset($template->options) ? maybe_unserialize($template->options) : array();

                        $interval = isset($options['rch_import_interval']) ? $options['rch_import_interval'] : "";

                        $s_name = isset($options['rch_scheduled_name']) && !empty($options['rch_scheduled_name']) ? $options['rch_scheduled_name'] : "";

                        if (empty($s_name)) {

                            $date_format = get_option('date_format');

                            $time_format = get_option('time_format');

                            $create_date = isset($template->create_date) ? $template->create_date : date("Y-m-d h:i:s");

                            $s_name = __('Scheduled', 'rch-woo-import-export') . " " . date($date_format . " " . $time_format, strtotime($create_date));
                        }
                        $send_email = isset($options['rch_scheduled_send_email']) && $options['rch_scheduled_send_email'] == 1 ? __('Yes', 'rch-woo-import-export') : __('No', 'rch-woo-import-export');

                        $recipient = isset($options['rch_scheduled_email_recipient']) ? $options['rch_scheduled_email_recipient'] : 0;

                        $next_scheduled = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), wp_next_scheduled('rch_cron_schedule_import', array(absint($id))));
                        ?>
                        <tr class="rch_log_wrapper rch_log_wrapper_<?php echo esc_attr($id); ?>">
                            <td class="rch_log_check_wrapper">
                                <input type="checkbox" class="rch_checkbox rch_log_check" id="rch_sschedule_log_check_<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($id); ?>"/>
                                <label for="rch_sschedule_log_check_<?php echo esc_attr($id); ?>" class="rch_checkbox_label"></label>
                            </td>
                            <td class="rch_log_data"><?php echo esc_html($id); ?></td>
                            <td class="rch_log_data"><?php echo esc_html($s_name); ?></td>
                            <td class="rch_log_data"><?php echo esc_html($opration_type); ?></td>
                            <td class="rch_log_data"><?php echo esc_html($interval); ?></td>
                            <td class="rch_log_data"><?php echo esc_html($send_email); ?></td>
                            <td class="rch_log_data"><?php echo esc_html($recipient); ?></td>
                            <td class="rch_log_data"><?php echo esc_html($next_scheduled); ?></td>
                            <td class="rch_log_data">
                                <div class="rch_log_action_btns rch_delete_template_btn"></div>
                            </td>
                        </tr>
                        <?php
                        unset($id, $opration_type, $options, $interval, $send_email, $recipient, $next_scheduled);
                    }
                    ?>
                <?php } ?>
                <tr class="<?php echo $is_empty_template; ?> rch_log_empty">
                    <td colspan="9">
                        <div class="rch_empty_records"><?php esc_html_e('No Records Found', 'rch-woo-import-export'); ?></div>
                    </td>
                </tr>
                <?php unset($is_empty_template); ?>
            </tbody>
        </table>
    </div>
</div>
