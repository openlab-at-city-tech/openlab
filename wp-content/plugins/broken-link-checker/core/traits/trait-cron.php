<?php
/**
 * Wrapper class for setting up scheduled events (wp pseudo cron).
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Traits
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Traits;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Enqueue
 *
 * @package WPMUDEV_BLC\Core\Traits
 */
trait Cron {
	/**
	 * WP Cron status.
	 *
	 * @var bool
	 */
	protected static $cron_disabled = false;

	/**
	 * WP Cron interval.
	 *
	 * @var boolean
	 */
	protected $cron_generate_interval = false;

	/**
	 * WP Cron interval title.
	 *
	 * @var string
	 */
	protected $cron_interval_title = 'daily';

	/**
	 * WP Cron interval period.
	 *
	 * @var string
	 */
	protected $cron_interval_period = '';

	/**
	 * WP Cron interval display.
	 *
	 * @var string
	 */
	protected $cron_interval_display = '';

	/**
	 * WP Cron hook to execute when event is run.
	 *
	 * @var string
	 */
	public $cron_hook = 'blc_email_schedule_trigger';

	/**
	 * Defines if single event or not. Default is false.
	 *
	 * @var bool
	 */
	protected $is_single_event = false;

	/**
	 * The schedule's timestamp.
	 *
	 * @var int
	 */
	protected $timestamp = null;

	/**
	 * Cron constructor.
	 */
	public function __construct() {
		$this->prepare_vars();

		add_action( 'init', array( $this, 'cron_callback' ) );
	}

	/**
	 * Prepares vars
	 *
	 * @return void
	 */
	public function prepare_vars() {
	}

	/**
	 * Returns the scheduled event's hook name.
	 * Should be overridden in each schedule.
	 */
	public function get_hook_name() {
		return $this->cron_hook;
	}

	/**
	 * The callback method. Child classes should contain `process_scheduled_event()` method.
	 */
	public function cron_callback() {
		add_action( $this->get_hook_name(), array( $this, 'process_scheduled_event' ) );
	}

	/**
	 * Sets up scheduled event's hooks. Needs to be called by classes that use this Trait.
	 *
	 * @return void
	 */
	public function setup_cron() {
		self::$cron_disabled = ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) || apply_filters( 'wpmudev_blc_disable_scheduled_events', false );

		if ( ! self::$cron_disabled ) {
			add_action( 'wpmudev_blc_plugin_activated', array( $this, 'activate_cron' ) );
			add_action( 'wpmudev_blc_plugin_deactivated', array( $this, 'deactivate_cron' ) );

			if ( $this->cron_generate_interval ) {
				add_filter( 'cron_schedules', array( $this, 'cron_interval' ) );
			}

			if ( $this->is_single_event ) {
				$this->activate_cron();
			}
		}
	}

	/**
	 * Activates cron on plugin activation
	 *
	 * @return bool|WP_Error
	 */
	public function activate_cron() {
		$timestamp = is_int( $this->timestamp ) ? $this->timestamp : null;
		$response  = false;
		/*
		 * Property $this->cron_hook needs to be set early in child classes.
		 */
		if ( ! wp_next_scheduled( $this->get_hook_name() ) ) {
			if ( $this->is_single_event ) {
				//if ( is_int( $timestamp ) ) {
				$response = wp_schedule_single_event( $timestamp, $this->get_hook_name() );
				//}
			} else {
				$timestamp = $timestamp ?? time();
				$response  = wp_schedule_event( $timestamp, $this->get_cron_interval_title(), $this->get_hook_name() );
			}
		}

		return $response;
	}

	/**
	 * Deactivates cron on plugin deactivation.
	 *
	 * @return void
	 */
	public function deactivate_cron() {
		wp_clear_scheduled_hook( $this->get_hook_name() );
	}

	/**
	 * Generates interval if `$this->cron_generate_interval` is true.
	 *
	 * @param array $schedules The existing cron schedules.
	 *
	 * @return mixed|void|array
	 */
	public function cron_interval( $schedules ) {
		$cron_args = apply_filters(
			'wpmudev_blc_cron_interval_args',
			$this->get_interval_args()
		);

		$schedules[ $cron_args['title'] ] = array(
			'interval' => $cron_args['interval'],
			'display'  => $cron_args['display'],
		);

		return $schedules;
	}

	/**
	 * Returns the args for custom interval to be used in scheduled event.
	 *
	 * @return array
	 */
	private function get_interval_args() {
		return array(
			'title'    => $this->cron_interval_title,
			'interval' => $this->cron_interval_period,
			'display'  => $this->cron_interval_display,
		);
	}

	/**
	 * Gives the cron interval.
	 *
	 * @return string
	 */
	public function get_cron_interval_title() {
		return $this->cron_interval_title;
	}

	/**
	 * Scheduled event's action callback
	 *
	 * @return void
	 */
	public function process_scheduled_event() {
	}
}
