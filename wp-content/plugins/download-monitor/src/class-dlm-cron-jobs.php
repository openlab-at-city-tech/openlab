<?php

class DLM_CRON_Jobs {

	/**
	 * Holds the class object.
	 *
	 * @since 4.4.7
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Class constructor
	 *
	 * @return void
	 */
	private function __construct() {
		add_filter( 'cron_schedules', array( $this, 'create_monthly_cron_schedule' ) );
		add_action( 'admin_init', array( $this, 'set_monthly_cron_schedule' ) );
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @return object The DLM_CRON_Jobs object.
	 * @since 4.4.7
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DLM_CRON_Jobs ) ) {
			self::$instance = new DLM_CRON_Jobs();
		}

		return self::$instance;

	}

	/**
	 * Create dlm_monthly cron schedule.
	 *
	 * @param array $schedule Array of schedules.
	 *
	 * @return array
	 * @since 4.8.6
	 */
	public function create_monthly_cron_schedule( $schedule ) {
		$schedule['dlm_monthly'] = array(
			'interval' => MONTH_IN_SECONDS,
			'display'  => __( 'DLM Once Monthly', 'download-monitor' ),
		);

		return $schedule;
	}

	/**
	 * Set dlm_monthly cron schedule.
	 *
	 * @since 4.9.5
	 */
	public function set_monthly_cron_schedule() {
		if ( ! wp_next_scheduled( 'dlm_monthly_event' ) ) {
			wp_schedule_event( time(), 'dlm_monthly', 'dlm_monthly_event' );
		}
	}
}
