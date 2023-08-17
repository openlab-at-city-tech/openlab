<?php
/**
 * Schedule to sync plugin with latest scan results from BLC API.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Schedule_Events\Sync_Scan_Results
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Scheduled_Events\Sync_Scan_Results;

// Abort if called directly.
defined( 'WPINC' ) || die;

// use WPMUDEV_BLC\App\Http_Requests\Sync_Scan_Results\Controller as Scan_API;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\Core\Traits\Cron;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Schedule_Events\Sync_Scan_Results
 */
class Controller extends Base {
	use Cron;

	/**
	 * WP Cron hook to execute when event is run.
	 *
	 * @var string
	 */
	public $cron_hook_name = 'blc_schedule_sync_scan_results';

	/**
	 * BLC settings from options table.
	 *
	 * @var array
	 */
	private $settings = null;

	/**
	 * Init Mailer
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wpmudev_blc_plugin_deactivated', array( $this, 'deactivate_cron' ) );

		if ( wp_doing_ajax() || Settings::instance()->get( 'use_legacy_blc_version' ) ) {
			return;
		}

		$this->setup_cron();
	}

	/**
	 * Prepares vars
	 *
	 * @return void
	 */
	public function prepare_vars() {
		$this->cron_interval_title = 'hourly';
		$this->timestamp           = time() + MINUTE_IN_SECONDS;
		$this->cron_interval_title = 'twicedaily';
	}

	/**
	 * Gives the cron interval.
	 *
	 * @return string
	 */
	public function ___get_cron_interval_title() {
		return 'twicedaily';
	}

	/**
	 * Returns the scheduled event's hook name.
	 * Overriding Trait's method.
	 */
	public function get_hook_name() {
		return $this->cron_hook_name;
	}

	/**
	 * Starts the scheduled scan.
	 */
	public function process_scheduled_event() {
		Utilities::log( 'Scheduled event for sync started' );

		$scan = new \WPMUDEV_BLC\App\Http_Requests\Sync_Scan_Results\Controller();
		$scan->start();
	}
}
