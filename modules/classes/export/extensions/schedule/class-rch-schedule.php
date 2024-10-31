<?php

namespace rch\export\schedule;

use rch\export\bg\RCH_BG;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'rch-woo-import-export' ) );
}

if ( file_exists( RCH_EXPORT_CLASSES_DIR . '/class-rch-export.php' ) ) {

        require_once(RCH_EXPORT_CLASSES_DIR . '/class-rch-export.php');
}

class RCH_Schedule_Export extends \rch\export\RCH_Export {

        public function __construct() {

                add_action( 'init', array( $this, 'init_bg_export' ) );

                add_action( 'wp_ajax_rch_export_save_schedule_data', array( $this, 'save_export_schedule' ) );

                add_filter( 'rch_add_export_extension_files', array( $this, 'get_schedule_view' ), 20, 1 );

                add_filter( 'rch_manage_export_tab_files', array( $this, 'get_manage_schedule_tab' ) );

                add_action( 'rch_cron_schedule_export', array( $this, 'prepare_export_cron' ), 10, 1 );

                add_action( 'rch_export_task_complete', array( $this, 'process_schedule_tasks' ), 100, 3 );
        }

        public function process_schedule_tasks( $export_id = 0, $opration = "export", $export_option = [] ) {

                if ( $opration === "schedule_export" ) {

                        $this->process_export_file( $export_id );

                        $this->send_notification( $export_id );
                }
        }

        private function send_notification( $export_id = 0 ) {

                if ( intval( $export_id ) < 1 ) {
                        return;
                }

                $template = $this->get_template_by_id( $export_id );

                if ( $template === false ) {
                        return;
                }

                $template_options = isset( $template->options ) ? maybe_unserialize( $template->options ) : [];

                if ( isset( $template_options[ 'rch_scheduled_send_email' ] ) && $template_options[ 'rch_scheduled_send_email' ] == 1 && isset( $template_options[ 'rch_scheduled_email_recipient' ] ) && ! empty( $template_options[ 'rch_scheduled_email_recipient' ] ) ) {

                        $filename = isset( $template_options[ 'fileName' ] ) ? $template_options[ 'fileName' ] : "";

                        $filedir = isset( $template_options[ 'fileDir' ] ) ? $template_options[ 'fileDir' ] : "";

                        $attachments = array( RCH_UPLOAD_EXPORT_DIR . '/' . $filedir . '/' . $filename );

                        $recipient = explode( ',', $template_options[ 'rch_scheduled_email_recipient' ] );

                        $subject = isset( $template_options[ 'rch_scheduled_email_subject' ] ) ? rch_sanitize_field( $template_options[ 'rch_scheduled_email_subject' ] ) : "";

                        $message = isset( $template_options[ 'rch_scheduled_email_msg' ] ) ? rch_sanitize_textarea( $template_options[ 'rch_scheduled_email_msg' ] ) : "";

                        $admin_email = get_option( 'admin_email' );

                        $headers = array();

                        $headers[] = 'From: "' . get_option( 'blogname' ) . '" <' . $admin_email . '>';

                        $headers[] = 'Reply-To: ' . $admin_email;

                        $headers[] = 'Content-Type:text/html; charset="' . get_option( 'blog_charset' ) . '"';

                        $this->send_mail( $recipient, $subject, $message, $headers, $attachments );

                        unset( $filename, $filedir, $attachments, $recipient, $subject, $message, $admin_email, $headers );
                }
        }

        private function send_mail( $recipient, $subject, $message, $header, $attachments ) {

                if ( ! wp_mail( $recipient, $subject, $message, $header, $attachments ) ) {

                        $semi_rand = md5( time() );

                        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

                        $headers = 'From: ' . get_option( 'blogname' ) . ' <' . $admin_email . '>' . '\n';

                        $date = date( "Y-m-d H:i:s" );

                        $headers .= "\n" . "Date:$date " . "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

                        $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";

                        $message .= "--{$mime_boundary}\n";

                        if ( count( $attachments ) > 0 ) {

                                foreach ( $attachments as $filename ) {

                                        $attachmnt = chunk_split( base64_encode( file_get_contents( $filename ) ) );

                                        $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"" . basename( $filename ) . "\"\n" . "Content-Disposition: attachment;\n" . " filename=\"" . basename( $filename ) . "\"\n" . "Content-Transfer-Encoding: base64\n\n" . $attachmnt . "\n\n";

                                        $message .= "--{$mime_boundary}\n";
                                }
                        }

                        mail( $recipient, $subject, $message, $headers );

                        unset( $semi_rand, $mime_boundary, $headers, $date, $message );
                }
        }

        public function get_manage_schedule_tab( $files = array() ) {

                $fileName = RCH_EXPORT_CLASSES_DIR . '/extensions/schedule/rch-manage-schedule.php';

                if ( ! in_array( $fileName, $files ) ) {

                        $files[] = $fileName;
                }

                return $files;
        }

        public function get_schedule_view( $files = array() ) {

                $fileName = RCH_EXPORT_CLASSES_DIR . '/extensions/schedule/rch-schedule-view.php';

                if ( ! in_array( $fileName, $files ) ) {
                        $files[] = $fileName;
                }

                return $files;
        }

        public function prepare_export_cron( $template_id = "" ) {

                $template = parent::get_template_by_id( $template_id );

                if ( $template && isset( $template->options ) ) {

                        $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                        $export_type = isset( $template->opration_type ) ? $template->opration_type : "post";

                        $total = $this->init_export( $export_type, "count", $options );

                        if ( is_wp_error( $total ) || absint( $total ) < 1 ) {
                                return false;
                        }

                        $options[ 'total' ] = $total;

                        parent::generate_template( $options, 'schedule_export', 'background' );

                        unset( $options );
                }
                unset( $template );
        }

        public function save_export_schedule() {

                global $wpdb;

                $rch_export_interval = isset( $_POST[ 'rch_export_interval' ] ) ? rch_sanitize_field( $_POST[ 'rch_export_interval' ] ) : "";

                $this_export_time_new = isset( $_POST[ 'rch_interval_start_time' ] ) ? rch_sanitize_field( $_POST[ 'rch_interval_start_time' ] ) : "";

                if ( ! empty( $this_export_time_new ) ) {
                        $this_export_time = strtotime( $this_export_time_new );
                } else {
                        $this_export_time = time();
                }
                unset( $this_export_time_new );

                $return_value = array();

                $rch_export_type = isset( $_POST[ 'rch_export_type' ] ) ? rch_sanitize_field( $_POST[ 'rch_export_type' ] ) : "";

                if ( $rch_export_interval != "" && $rch_export_type != "" ) {

                        $scheduled_id = parent::generate_template( $_POST, 'schedule_export_template', 'completed' );

                        wp_schedule_event( $this_export_time, $rch_export_interval, 'rch_cron_schedule_export', array( $scheduled_id ) );

                        unset( $scheduled_id );

                        $return_value[ 'status' ] = 'success';

                        $return_value[ 'message' ] = __( 'Scheduled has been saved successfully', 'rch-woo-import-export' );
                } else {

                        $return_value[ 'status' ] = 'error';

                        $return_value[ 'message' ] = __( 'Scheduled has been saved successfully', 'rch-woo-import-export' );
                }

                unset( $this_export_time, $rch_export_type, $rch_export_interval );

                echo json_encode( $return_value );

                die();
        }

        public function init_bg_export() {

                if ( ! class_exists( '\rch\export\bg\RCH_BG' ) ) {

                        $fileName = RCH_EXPORT_CLASSES_DIR . '/extensions/bg/class-rch-bg.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                $bg_export = new RCH_BG();

                                $bg_export->init_bg_export( "schedule_export" );

                                unset( $bg_export );
                        }
                }
        }

}

new RCH_Schedule_Export();
