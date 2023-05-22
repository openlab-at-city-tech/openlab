<?php
/**
 * Legacy Scheduled event for BLC v1.
 * Handles legacy cron jobs.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Schedule_Events\Legacy
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Scheduled_Events\Legacy;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Schedule_Events\Scan
 */
class Controller extends Base {
	/**
	 * @var array $legacy_crons List of all legacy scheduled events.
	 */
	private $legacy_crons = array(
		'blc_cron_check_links',
		'blc_cron_email_notifications',
		'blc_cron_database_maintenance',
		'blc_corn_clear_log_file',
		'blc_cron_check_news',
	);

	/**
	 * @var array $v2_crons List of all v2 scheduled events.
	 */
	private $v2_crons = array(
		'blc_recipients_activation_email_schedule',
		'blc_schedule_sync_scan_results',
		'blc_schedule_scan',
	);

	/**
	 * Init Schedule
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wpmudev_blc_plugin_deactivated', array( $this, 'deactivate_legacy_crons' ) );
		add_action( 'wpmudev_blc_rest_enpoints_switch_version_mode', array( $this, 'switch_version_mode' ) );
	}

	/**
	 * Switched cron jobs depending on plugin version mode (legacy or v2).
	 *
	 * @since 2.0.0
	 *
	 * @param bool $legacy_active A boolean indicating if legacy mode is set.
	 *
	 * @retun void
	 */
	public function switch_version_mode( bool $legacy_active = true ) {
		if ( $legacy_active ) {
			// Enable legacy crons.
			$this->activate_legacy_crons();

			// Disable v2 cron events.
			$this->deactivate_v2_crons();

		} else {
			// Disable legacy crons.
			$this->deactivate_legacy_crons();

			// Enable v2 crons.
			do_action( 'wpmudev_blc_plugin_activated' );
			\WPMUDEV_BLC\App\Scheduled_Events\Scan\Controller::instance()->set_scan_schedule();
			\WPMUDEV_BLC\App\Scheduled_Events\Sync_Scan_Results\Controller::instance()->activate_cron();
		}
	}

	/**
	 * Deactivates all legacy plugin's scheduled events.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function deactivate_legacy_crons() {
		$this->deactivate_crons( $this->legacy_crons );

		// We still need to force clear `blc_schedule_scan` cron.
		wp_clear_scheduled_hook( 'blc_schedule_scan' );

		add_filter( 'blc_allow_send_email_notification', '__return_false' );
	}

	/**
	 * Deactivates all v2 plugin's scheduled events.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function deactivate_v2_crons() {
		$this->deactivate_crons( $this->v2_crons );
	}

	/**
	 * Deactivates given list of scheduled events.
	 *
	 * @since 2.0.0
	 *
	 * @param array $crons List of scheduled events to be deactivated.
	 *
	 * @return void
	 */
	public function deactivate_crons( array $cron_hooks = array() ) {
		if ( empty( $cron_hooks ) ) {
			return;
		}

		foreach ( $cron_hooks as $cron_hook ) {
			wp_clear_scheduled_hook( $cron_hook );
		}
	}

	public function activate_legacy_crons() {
		global $blc_config_manager;

		$ws_link_checker = null;

		if ( ! class_exists( 'wsBrokenLinkChecker' ) ) {
			require_once BLC_DIRECTORY_LEGACY . '/core/core.php';
		}

		if ( $blc_config_manager instanceof \blcConfigurationManager ) {
			$ws_link_checker = new \wsBrokenLinkChecker( BLC_PLUGIN_FILE_LEGACY, $blc_config_manager );
		}

		if ( ! is_null( $ws_link_checker ) ) {
			// Enable legacy cron events.
			$ws_link_checker->setup_cron_events();
		}
	}
}
