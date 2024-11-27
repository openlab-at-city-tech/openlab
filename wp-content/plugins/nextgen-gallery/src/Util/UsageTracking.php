<?php
/**
 * This file contains the UsageTracking class
 *
 * @package NextGEN Gallery
 */

namespace Imagely\NGG\Util;

use Imagely\NGG\Admin\Onboarding_Wizard;

/**
 * Class UsageTracking
 *
 * This class is responsible for tracking usage of the NextGen plugin and sending check-in data.
 */
class UsageTracking {

	/**
	 * The endpoint to send the checkin data to.
	 *
	 * @var string
	 */
	protected $endpoint = '';

	/**
	 * The user agent to send with the request.
	 *
	 * @var string
	 */
	protected $user_agent = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->user_agent = 'NextGen/' . NGG_PLUGIN_VERSION . '; ' . get_bloginfo( 'url' );
		$this->endpoint   = 'https://evusage.enviragallery.com/v1/nextgen-checkin/';
	}

	/**
	 * Register hooks.
	 *
	 * @since 3.59.5
	 *
	 * @return void
	 */
	public function hooks() {
		$onboarding_data = get_option( 'ngg_onboarding_data', [] );
		$enabled         = $onboarding_data['_usage_tracking'] ?? false;
		$enabled         = filter_var( $enabled, FILTER_VALIDATE_BOOLEAN );

		// Check the license type.
		$type = ( new Onboarding_Wizard() )->get_license_type();

		if ( ! $enabled || 'lite' !== $type ) {
			return; // Return early if usage tracking is disabled or the license type is not lite.
		}

		add_action( 'admin_init', [ $this, 'schedule_send' ] );
		add_filter( 'cron_schedules', [ $this, 'add_schedules' ], 99 );
		add_action( 'nextgen_usage_tracking_cron', [ $this, 'send_checkin' ] );
	}

	/**
	 * Get the settings to send.
	 *
	 * @since 3.59.5
	 * @return array
	 */
	protected function get_settings() {
		$settings         = get_option( 'ngg_options', [] );
		$settings_to_send = [];
		foreach ( $settings as $key => $value ) {
			if ( empty( $value ) || ( false !== strpos( $key, 'stripe' ) ) ) {
				continue;
			}

			$settings_to_send[ $key ] = $value;
		}

		return $settings_to_send;
	}

	/**
	 * Get the data to send
	 *
	 * @since 3.59.5
	 *
	 * @return array
	 */
	private function get_data() {
		$data = [];

		// Retrieve current theme info.
		$theme_data = wp_get_theme();

		$sites_count = 1;
		if ( is_multisite() ) {
			if ( function_exists( 'get_blog_count' ) ) {
				$sites_count = get_blog_count();
			} else {
				$sites_count = 'Not Set';
			}
		}

		$settings = $this->get_settings();

		$data['nextgen_version'] = NGG_PLUGIN_VERSION;
		$data['ng_type']         = 'lite';

		$data['php_version']    = phpversion();
		$data['wp_version']     = get_bloginfo( 'version' );
		$data['server']         = $_SERVER['SERVER_SOFTWARE'] ?? 'CLI'; // phpcs:ignore
		$data['over_time']      = get_option( 'nextgen_over_time', [] );
		$data['multisite']      = is_multisite();
		$data['url']            = home_url();
		$data['themename']      = $theme_data->get( 'Name' );
		$data['themeversion']   = $theme_data->get( 'Version' );
		$data['email']          = get_bloginfo( 'admin_email' );
		$data['settings']       = $settings;
		$data['pro']            = false;
		$data['sites']          = $sites_count;
		$data['usagetracking']  = false;
		$data['usercount']      = function_exists( 'get_user_count' ) ? get_user_count() : 'Not Set';
		$data['timezoneoffset'] = wp_date( 'P' );

		// Not used on sol.
		$data['tracking_mode'] = '';
		$data['events_mode']   = '';
		$data['usesauth']      = '';
		$data['autoupdate']    = false;

		// Retrieve current plugin information.
		if ( ! function_exists( 'get_plugins' ) ) {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugins        = array_keys( get_plugins() );
		$active_plugins = get_option( 'active_plugins', [] );

		foreach ( $plugins as $key => $plugin ) {
			if ( in_array( $plugin, $active_plugins, true ) ) {
				// Remove active plugins from list so we can show active and inactive separately.
				unset( $plugins[ $key ] );
			}
		}

		$data['active_plugins']   = $active_plugins;
		$data['inactive_plugins'] = $plugins;
		$data['locale']           = get_locale();

		return $data;
	}

	/**
	 * Send the checkin
	 *
	 * @since 3.59.5
	 *
	 * @return bool
	 */
	public function send_checkin( $ignore_last_checkin = false ) {
		$ignore_last_checkin = $ignore_last_checkin || defined( DOING_CRON ) && DOING_CRON;

		$home_url = trailingslashit( home_url() );
		if ( strpos( $home_url, 'imagely.com' ) !== false ) {
			return false;
		}

		// Send a maximum of once per week.
		$last_send = get_option( 'nextgen_usage_tracking_last_checkin' );
		if ( is_numeric( $last_send ) && $last_send > strtotime( '-1 week' ) && ! $ignore_last_checkin ) {
			return false;
		}

		$request = wp_remote_post(
			$this->endpoint,
			[
				'method'      => 'POST',
				'timeout'     => 5,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking'    => false,
				'body'        => $this->get_data(),
				'user-agent'  => $this->user_agent,
			]
		);

		// If we have completed successfully, recheck in 1 week.
		update_option( 'nextgen_usage_tracking_last_checkin', time() );

		return true;
	}

	/**
	 * Schedule the checkin
	 *
	 * @since 3.59.5
	 * @return void
	 */
	public function schedule_send() {
		if ( wp_next_scheduled( 'nextgen_usage_tracking_cron' ) ) {
			return;
		}

		$tracking            = [];
		$tracking['day']     = wp_rand( 0, 6 );
		$tracking['hour']    = wp_rand( 0, 23 );
		$tracking['minute']  = wp_rand( 0, 59 );
		$tracking['second']  = wp_rand( 0, 59 );
		$tracking['offset']  = ( $tracking['day'] * DAY_IN_SECONDS );
		$tracking['offset'] += ( $tracking['hour'] * HOUR_IN_SECONDS );
		$tracking['offset'] += ( $tracking['minute'] * MINUTE_IN_SECONDS );
		$tracking['offset'] += $tracking['second'];

		$tracking['initsend'] = strtotime( 'next sunday' ) + $tracking['offset'];

		wp_schedule_event( $tracking['initsend'], 'weekly', 'nextgen_usage_tracking_cron' );
		update_option( 'nextgen_usage_tracking_config', wp_json_encode( $tracking ) );
	}

	/**
	 * Add weekly schedule
	 *
	 * @since 3.59.5
	 *
	 * @param array $schedules Array of schedules.
	 *
	 * @return array
	 */
	public function add_schedules( $schedules = [] ) {
		if ( isset( $schedules['weekly'] ) ) {
			return $schedules;
		}

		$schedules['weekly'] = [
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'nextgen-gallery' ),
		];

		return $schedules;
	}
}
