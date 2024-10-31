<?php

namespace rch;

defined( 'ABSPATH' ) || exit;

class RCH_Schedule {

        public function __construct() {
                add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) );
        }

        public static function cron_schedules( $schedules = array() ) {

                return array_merge( self::get_schedules(), $schedules );
        }

        public static function get_schedules() {

                return array(
                        'rch_30_min'     => array(
                                'interval' => 30 * MINUTE_IN_SECONDS,
                                'display'  => __( 'Every 30 Minutes', 'rch-woo-import-export' ),
                        ),
                        'rch_hourly'     => array(
                                'interval' => HOUR_IN_SECONDS,
                                'display'  => __( 'Once Hourly', 'rch-woo-import-export' ),
                        ),
                        'rch_twicedaily' => array(
                                'interval' => 12 * HOUR_IN_SECONDS,
                                'display'  => __( 'Twice Daily', 'rch-woo-import-export' ),
                        ),
                        'rch_daily'      => array(
                                'interval' => DAY_IN_SECONDS,
                                'display'  => __( 'Once Daily', 'rch-woo-import-export' ),
                        ),
                        'rch_weekly'     => array(
                                'interval' => WEEK_IN_SECONDS,
                                'display'  => __( 'Once Weekly', 'rch-woo-import-export' ),
                        ),
                        'rch_monthly'    => array(
                                'interval' => MONTH_IN_SECONDS,
                                'display'  => __( 'Once Monthly', 'rch-woo-import-export' ),
                        ),
                );
        }

}
