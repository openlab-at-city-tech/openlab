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
		$enabled         = isset( $onboarding_data['_usage_tracking'] ) ? $onboarding_data['_usage_tracking'] : false;
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
	 * Get the raw database server version string.
	 *
	 * The db_version() helper reports MariaDB as a 5.5.5 compatibility version, which makes MariaDB
	 * and MySQL indistinguishable -- and they differ on exactly the behaviour being measured, since
	 * only MariaDB 10.4+ falls back to a hash index for an over-length UNIQUE key. db_server_info()
	 * returns the unmangled string ("10.11.6-MariaDB-log"), so prefer it and keep db_version() only
	 * as a fallback.
	 *
	 * @since 4.4.0
	 *
	 * @return string
	 */
	private function get_db_server_info() {
		global $wpdb;

		$server_info = method_exists( $wpdb, 'db_server_info' ) ? $wpdb->db_server_info() : $wpdb->db_version();

		// The receiving end stores each check-in value in a VARCHAR(100) column.
		return substr( (string) $server_info, 0, 100 );
	}

	/**
	 * Get the schema facts that decide whether the duplicate-image guard can exist at all.
	 *
	 * The UNIQUE KEY on (galleryid, filename) is what stops duplicate images (see #781), but on some
	 * engine/charset combinations it cannot be created: filename is VARCHAR(255), which at utf8mb4 is
	 * 1020 bytes -- past MyISAM's 1000-byte total key limit, and past InnoDB's 767-byte per-column
	 * limit under ROW_FORMAT=COMPACT. dbDelta() never checks for the error, so such a site runs
	 * indefinitely with no guard and no symptom beyond duplicates accumulating.
	 *
	 * unique_key is therefore the field that matters: it reports the outcome directly instead of
	 * asking the receiving end to infer it. The engine facts explain why the key is missing when it
	 * is, and row_format has to be reported rather than derived from the server version, because it
	 * is stored per table and survives every server upgrade -- a table created on MySQL 5.5 is still
	 * COMPACT on MySQL 8 today.
	 *
	 * unique_key alone stopped being enough once the key could be created as a filename prefix.
	 * It is decided on index *name*, so a site that has the guard at a 191-character prefix and a
	 * site that has always had the full-column key both report '1' -- which would collapse the
	 * previously-broken and always-fine populations into one number and make the measurement this
	 * function exists for impossible to take. unique_key_prefix separates them: 'full' for the
	 * whole column, the prefix length for a prefixed key, 'none' when there is no key at all.
	 *
	 * @since 4.4.0
	 * @since 4.4.2 Added unique_key_prefix.
	 *
	 * @return array
	 */
	private function get_pictures_table_info() {
		global $wpdb;

		// 'unknown' rather than an empty string, so "we could not look this up" stays distinguishable
		// from a table that exists and genuinely has no unique key.
		$info = [
			'engine'            => 'unknown',
			'row_format'        => 'unknown',
			'charset'           => 'unknown',
			'unique_key'        => 'unknown',
			'unique_key_prefix' => 'unknown',
		];

		$table = $wpdb->prefix . 'ngg_pictures';

		// information_schema is queried directly because no WordPress API exposes a table's engine,
		// row format, or index list. Both queries are scoped to one table and run on the weekly
		// check-in cron, never on a page load.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$table_info = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT ENGINE AS engine, ROW_FORMAT AS row_format, TABLE_COLLATION AS charset FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = %s',
				$table
			)
		);

		if ( empty( $table_info ) ) {
			// Either the table has not been created yet or information_schema is restricted for this
			// user; leaving every value 'unknown' keeps that case out of the measurement entirely.
			return $info;
		}

		$info['engine']     = ! empty( $table_info->engine ) ? $table_info->engine : 'unknown';
		$info['row_format'] = ! empty( $table_info->row_format ) ? $table_info->row_format : 'unknown';
		$info['charset']    = ! empty( $table_info->charset ) ? $table_info->charset : 'unknown';

		// SUB_PART is NULL for a column indexed in full and the prefix length for a prefixed one,
		// so MAX() over the index's rows reports the filename prefix without needing to name the
		// column: 0 means every column is indexed in full, anything else is the prefix length.
		$key_info = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT COUNT(*) AS columns_indexed, MAX( COALESCE( SUB_PART, 0 ) ) AS filename_prefix
				FROM information_schema.statistics
				WHERE table_schema = DATABASE() AND table_name = %s AND index_name = %s',
				$table,
				'unique_gallery_filename'
			)
		);
		// phpcs:enable

		if ( ! empty( $key_info ) && null !== $key_info->columns_indexed ) {
			$info['unique_key'] = $key_info->columns_indexed ? '1' : '0';

			if ( ! $key_info->columns_indexed ) {
				$info['unique_key_prefix'] = 'none';
			} elseif ( empty( $key_info->filename_prefix ) ) {
				$info['unique_key_prefix'] = 'full';
			} else {
				$info['unique_key_prefix'] = (string) (int) $key_info->filename_prefix;
			}
		}

		return $info;
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

		// These ride in the settings payload rather than as top-level check-in fields because the
		// receiving end whitelists top-level parameters -- anything it does not name explicitly is
		// discarded -- while it stores every settings key/value pair as it arrives. Sending them here
		// means the data starts landing without a matching change to the usage-tracking service.
		//
		// Assigning after get_settings() also keeps them clear of its empty() filter, which matters:
		// 'pictures_unique_key' => '0' is the single most important value to report, and '0' is falsy.
		$pictures_info = $this->get_pictures_table_info();

		$settings['db_server_info']      = $this->get_db_server_info();
		$settings['pictures_engine']     = $pictures_info['engine'];
		$settings['pictures_row_format'] = $pictures_info['row_format'];
		$settings['pictures_charset']    = $pictures_info['charset'];
		$settings['pictures_unique_key'] = $pictures_info['unique_key'];
		// Without this the prefix is computed on every check-in and thrown away, and a prefixed
		// key reports 'pictures_unique_key' => '1' identically to a full-column one -- the exact
		// collapse get_pictures_table_info()'s docblock says makes #941's measurement impossible.
		$settings['pictures_unique_key_prefix'] = $pictures_info['unique_key_prefix'];
		// The population the installer deliberately did NOT migrate: set on an existing table
		// that has no guard, carrying that table's approximate row count. #941 sizes what was
		// left behind from this rather than by inspecting sites by hand. '' when nothing was
		// deferred, so the deferred and never-examined populations stay distinguishable.
		$settings['pictures_guard_deferred'] = (string) get_option( 'ngg_pictures_guard_deferred', '' );

		$data['nextgen_version'] = NGG_PLUGIN_VERSION;
		$data['ng_type']         = 'lite';

		$data['php_version']    = phpversion();
		$data['wp_version']     = get_bloginfo( 'version' );
		$data['server']         = isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : 'CLI'; // phpcs:ignore
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
		$ignore_last_checkin = $ignore_last_checkin || ( defined( 'DOING_CRON' ) && DOING_CRON );

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
			'display'  => __( 'Once Weekly', 'nggallery' ),
		];

		return $schedules;
	}
}
