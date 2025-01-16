<?php
/**
 * The actions class to perform scheduled actions.
 *
 * @link    http://wpmudev.com
 * @since   1.0.0
 * @author  Joel James <joel@incsub.com>
 * @package WPMUDEV\Hub\Connector
 */

namespace WPMUDEV\Hub\Connector;

/**
 * Class Actions
 */
class Actions {

	use Singleton;

	/**
	 * Cron job hook name.
	 *
	 * @since 1.0.0
	 */
	const CRON = 'wpmudev_scheduled_jobs';

	/**
	 * Flag to trigger Hub Sync.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $sync_hub = false;

	/**
	 * Initialize UI class.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		// Schedule automatic data update on the main site of the network.
		if ( is_main_site() && API::get()->is_logged_in() ) {
			// Schedule a cron job for twice daily.
			if ( ! wp_next_scheduled( self::CRON ) ) {
				wp_schedule_event( time(), 'twicedaily', self::CRON );
			}

			// Sync with Hub.
			add_action( 'wpmudev_scheduled_jobs', array( $this, 'hub_sync' ) );
			// On theme change.
			add_action( 'after_switch_theme', array( $this, 'set_shutdown_sync' ) );
		}

		// Do hub sync when a plugin/theme is changed.
		add_action( 'activated_plugin', array( $this, 'set_shutdown_sync' ) );
		add_action( 'deactivated_plugin', array( $this, 'set_shutdown_sync' ) );
		add_action( 'deleted_plugin', array( $this, 'set_shutdown_sync' ) );
		add_action( 'deleted_theme', array( $this, 'set_shutdown_sync' ) );
		add_action( 'upgrader_process_complete', array( $this, 'set_shutdown_sync' ) );

		// Perform shut down actions.
		add_action( 'shutdown', array( $this, 'shutdown_action' ) );
	}

	/**
	 * Perform shut down actions.
	 *
	 * We will set a flag for shut down hook to check and perform
	 * a sync with hub.
	 *
	 * @since 1.0.0
	 */
	public function set_shutdown_sync() {
		$this->sync_hub = true;
	}

	/**
	 * Perform shut down actions.
	 *
	 * @since 1.0.0
	 */
	public function shutdown_action() {
		// Do a sync with Hub.
		if ( $this->sync_hub && ! defined( '\WPMUDEV_REMOTE_SKIP_SYNC' ) ) {
			$this->hub_sync();
		}
	}

	/**
	 * Do a periodic sync with the Hub.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function hub_sync() {
		// Do hub sync.
		API::get()->sync_site();
	}
}
