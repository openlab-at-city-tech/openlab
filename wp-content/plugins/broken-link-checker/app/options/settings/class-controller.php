<?php
/**
 * Settings controller.
 * A single scheduled event that gets triggered based on options set in "Schedule Scan"
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Options\Settings
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Options\Settings;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Models\Option;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings_Model;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Options\Settings
 */
class Controller extends Base {
	/**
	 * Plugin settings
	 */
	private $settings = null;

	/**
	 * Init Settings
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->settings = new Settings_Model();
		$this->settings->init();

		add_action( 'wpmudev_blc_plugin_activated', array( $this, 'activation_actions' ), 9 );
		//add_action( 'wpmudev_blc_plugin_deactivated', array( $this, 'deactivation_actions' ) );

		add_action( 'load-toplevel_page_blc_dash', array( $this, 'blc_pages_init' ) );
		add_action( 'load-link-checker_page_view-broken-links', array( $this, 'blc_pages_init' ) );
		add_action( 'load-link-checker_page_link-checker-settings', array( $this, 'blc_pages_init' ) );
		//add_action( 'load-link-checker_page_blc_local', array( $this, 'blc_pages_init' ) );
		//add_action( 'delete_user', array( $this,'adapt_schedule_recipients' ), 10, 2 );
		add_action( 'deleted_user', array( $this, 'adapt_schedule_recipients' ), 10, 2 );
		add_action( 'remove_user_from_blog', array( $this, 'remove_user_from_blog' ), 10, 3 );
	}

	/**
	 * Actions to be done when v2 or legacy blc pages are loaded.
	 * @return void
	 */
	public function blc_pages_init() {
		// Activate V2 if there was a request to activate.
		// This way we ensure that after site gets connected V2 gets activated when there was a settings request to activate V2 but at the time site was disconnected.
		if ( $this->settings->get( 'v2_activation_request' ) && Utilities::site_connected() ) {
			$this->settings->set( array( 'use_legacy_blc_version' => false ) );
			$this->settings->set( array( 'v2_activation_request' => false ) );
			$this->settings->save();

			// After connecting site and setting V2 enabled we also need to reload the BLC page(s) so that as page content has already been set.
			$parsed_query_params = wp_parse_url( $_SERVER['REQUEST_URI'] );

			if ( ! empty( $parsed_query_params['query'] ) ) {
				$query_params = $parsed_query_params['query'];
				wp_safe_redirect( admin_url( "admin.php?{$query_params}" ) );
				exit;
			}
		}

		// Disable the welcome modal after plugin activation when BLC Dash or other BLC screen is visited.
		$this->settings->set( array( 'activation_modal_shown' => true ) );
		$this->settings->save();
	}

	/**
	 * Deleted the settings when plugin gets deactivated.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function deactivation_actions() {
		$this->settings->delete();
	}

	/**
	 * Set the settings when plugin gets activated.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function activation_actions() {
		// Check if legacy plugin was installed by checking legacy a`wsblc_options` option.
		// If installed first time enable V2, else V1.
		$legacy_option = new Option( array( 'name' => 'wsblc_options' ) );

		if ( empty( $legacy_option->get() ) || ( empty( $this->settings->get( 'use_legacy_blc_version' ) ) && ! empty( $this->settings->get( 'schedule' ) ) && ! empty( $this->settings->get( 'schedule' )['active'] ) ) ) {
			\WPMUDEV_BLC\App\Scheduled_Events\Scan\Controller::instance()->set_scan_schedule();
		}

		if ( ! empty( get_option( 'blc_settings' ) ) ) {
			return;
		}

		if ( empty( $legacy_option->get() ) ) {
			//$this->settings->set( array( 'use_legacy_blc_version' => false ) );
			if ( Utilities::site_connected() ) {
				$this->settings->set( array( 'use_legacy_blc_version' => false ) );
			} else {
				$this->settings->set( array( 'use_legacy_blc_version' => true ) );
			}
		} else {
			$this->settings->set( array( 'use_legacy_blc_version' => true ) );
		}

		// If installed from Hub no need to show plugins modal.
		if ( isset( $_GET['wpmudev-hub'] ) ) {
			$this->settings->set( array( 'activation_modal_shown' => true ) );
		}

		$this->settings->set( array( 'installation_timestamp' => time() ) );
		$this->settings->save();
	}

	/**
	 * Gets triggerred once a user gets removed from current subsite on a multsite network.
	 *
	 * @param int|null $user_id
	 * @param int|null $user_id_reassign
	 *
	 * @return void
	 */
	public function remove_user_from_blog( int $user_id = null, int $user_id_reassign = null ) {
		$this->adapt_schedule_recipients( $user_id );
	}

	/**
	 * Adapts schedule recipients when a user is deleted from admin users.
	 *
	 * @param int|null $user_id
	 * @param int|null $user_id_reassign
	 *
	 * @return void
	 */
	public function adapt_schedule_recipients( int $user_id = null, int $user_id_reassign = null ) {
		if ( empty( $user_id ) ) {
			return;
		}

		$schedule = $this->settings->get( 'schedule' );

		if ( ! empty( $schedule['recipients'] ) ) {
			if ( in_array( $user_id, $schedule['recipients'] ) ) {
				$user_key = array_search(
					$user_id,
					$schedule['recipients']
				);

				if ( intval( $schedule['recipients'][ $user_key ] ) === $user_id ) {
					unset( $schedule['recipients'][ $user_key ] );
					$schedule['recipients'] = array_values( $schedule['recipients'] );

					unset( $schedule['registered_recipients_data'][ $user_id ] );

					$this->settings->set( array( 'schedule' => $schedule ) );
					$this->settings->save();
				}
			}
		}
	}
}
