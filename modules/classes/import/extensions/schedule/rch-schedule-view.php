<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'rch-woo-import-export'));
}

$fileName = RCH_IMPORT_CLASSES_DIR . '/extensions/schedule/class-rch-schedule.php';

if (file_exists($fileName)) {

    require_once($fileName);
}
$get_schedules_list = \rch\RCH_Schedule::get_schedules();

$schedules_start_int_time = array('00:00', '00:30', '01:00', '01:30', '02:00', '02:30', '03:00', '03:30', '04:00', '04:30', '05:00', '05:30',
        '06:00', '06:30', '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
        '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30',
        '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00', '22:30', '23:00', '23:30'
);
?>
<div class="rch_section_wrapper">
    <div class="rch_content_data_header rch_schedule_import">
        <div class="rch_content_title"><?php esc_html_e('Automatic Scheduling with Background Import', 'rch-woo-import-export'); ?></div>
        <div class="rch_layout_header_icon_wrapper"></div>
    </div>
    <div class="rch_section_content rch_show">
        <div class="rch_content_data_wrapper">
            <table class="rch_content_data_tbl table table-bordered">
                <tr>
                    <td>
                        <div class="rch_options_data">
                            <div class="rch_options_data_title"><?php esc_html_e('Schedule Friendly Name', 'rch-woo-import-export'); ?></div>
                            <div class="rch_options_data_content">
                                <input type="text" class="rch_content_data_input" value="" name="rch_scheduled_name"/>
                                <div class="rch_import_default_hint"><?php esc_html_e('Give any name for schedule', 'rch-woo-import-export'); ?></div>
                            </div>
                        </div>
                    </td>
                    <td >
                        <div class="rch_options_data">
                            <div class="rch_options_data_title"><?php esc_html_e('Import Interval', 'rch-woo-import-export'); ?></div>
                            <div class="rch_options_data_content">
                                <select class="rch_content_data_select rch_sceduled_import_interval" data-placeholder="' . esc_attr__('Select Interval', 'rch-woo-import-export') . '" name="rch_import_interval">
<?php
if (!empty($get_schedules_list)) {
    foreach ($get_schedules_list as $key => $value) {
        ?>
                                            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value['display']); ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </td>

                </tr>
                <tr>
                    <td>
                        <div class="rch_options_data">
                            <div class="rch_options_data_title"><?php esc_html_e('Import Interval Start Time', 'rch-woo-import-export'); ?></div>
                            <div class="rch_options_data_content">
                                <select class="rch_content_data_select rch_sceduled_import_interval_start_time" data-placeholder="' . esc_attr__('Select Interval TIme', 'rch-woo-import-export') . '" name="rch_interval_start_time">
                                    <option value=""><?php esc_html_e('Current time', 'rch-woo-import-export'); ?></option>
<?php
foreach ($schedules_start_int_time as $int_time) {
    ?>
                                        <option value="<?php echo esc_attr($int_time); ?>"><?php echo esc_html($int_time); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="rch_import_default_hint"><?php esc_html_e('Default : Current time, Value : 00:00 to 23:30', 'rch-woo-import-export'); ?></div>
                            </div>
                        </div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="rch_options_data">
                            <div class="rch_options_data_content">
                                <input type="checkbox" class="rch_scheduled_send_email_chk rch_scheduled_send_email rch_checkbox" id="rch_scheduled_send_email" name="rch_scheduled_send_email" value="1"/>
                                <label for="rch_scheduled_send_email" class="rch_options_data_title_email rch_checkbox_label"><?php esc_html_e('Send E-mail with attachment', 'rch-woo-import-export'); ?></label>
                                <div class="rch_checkbox_container rch_options_data_send_mail">
                                    <div class="rch_schedule_mail_wrapper">
                                        <div class="rch_options_data">
                                            <div class="rch_options_data_title"><?php esc_html_e('Enter Email Recipient(s)', 'rch-woo-import-export'); ?></div>
                                            <div class="rch_options_data_content">
                                                <input type="text" class="rch_content_data_input" value="" name="rch_scheduled_email_recipient"/>
                                                <div class="rch_import_default_hint"><?php esc_html_e('Ex. example@gmail.com, demo@yahoo.com', 'rch-woo-import-export'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rch_schedule_mail_wrapper">
                                        <div class="rch_options_data ">
                                            <div class="rch_options_data_title"><?php esc_html_e('Enter Email Subject', 'rch-woo-import-export'); ?></div>
                                            <div class="rch_options_data_content">
                                                <input type="text" class="rch_content_data_input" value="" name="rch_scheduled_email_subject"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rch_schedule_mail_wrapper">
                                        <div class="rch_options_data">
                                            <div class="rch_options_data_title"><?php esc_html_e('Enter Email message', 'rch-woo-import-export'); ?></div>
                                            <div class="rch_options_data_content">
                                                <textarea class="rch_content_data_textarea rch_sceduled_import_msg" name="rch_scheduled_email_msg"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </td>
                </tr>
            </table>
            <div class="rch_save_scheduled_btn_wrapper"> 
                <div class="rch_btn rch_btn_primary rch_save_scheduled_btn">
                    <?php esc_html_e('Save Scheduled', 'rch-woo-import-export'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
