<?php
/**
 * Useful utilities.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Utils;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Traits\Dashboard_API;

use DateTime;
use DateTimeZone;
use WPMUDEV_Dashboard;
use WPMUDEV_Dashboard_Api;
use function array_keys;
use function array_values;
use function call_user_func;
use function date;
use function dirname;
use function error_log;
use function fclose;
use function file_exists;
use function fopen;
use function is_callable;
use function is_dir;
use function is_int;
use function is_multisite;
use function is_null;
use function mkdir;
use function path_join;
use function round;
use function wp_normalize_path;

/**
 * Class WPMUDEV_BLC
 *
 * @package WPMUDEV_BLC\Core
 */
final class Utilities {
	use Dashboard_API;

	/**
	 * Array that holds variable values that can be re-used in several classes.
	 *
	 * @since 2.0.0
	 * @var array $value_provider
	 */
	public static $value_provider = array();

	/**
	 * Checks if current request is on multisite for the network administrative interface.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public static function is_network_admin() {
		if ( ! is_multisite() ) {
			return false;
		}

		/**
		 * Filter to change network admin flag.
		 *
		 * @since 2.0.0
		 *
		 * @param bool Is network.
		 */
		return apply_filters( 'wpmudev_blc_is_network_admin', is_network_admin() || self::is_ajax_network_admin() );
	}

	/**
	 *  Check if network admin.
	 *
	 * The is_network_admin() check does not work in AJAX calls.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function is_ajax_network_admin() {
		if ( ! is_multisite() ) {
			return false;
		}

		return defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_SERVER['HTTP_REFERER'] ) && preg_match( '#^' . network_admin_url() . '#i', wp_unslash( $_SERVER['HTTP_REFERER'] ) );
	}

	/**
	 * Checks if current request is on a subsite admin of a multisite installation. If we are on main site or on
	 * network admin, it returns false.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public static function is_subsite() {
		if ( ! is_multisite() ) {
			return false;
		}

		/**
		 * Filter to change subsite admin flag.
		 *
		 * @since 2.0.0
		 *
		 * @param bool Is subsite.
		 */
		return apply_filters(
			'wpmudev_blc_is_subsite',
			! ( self::is_network_admin() || is_main_site() )
		);
	}

	/**
	 * Gives the subsite id based on url.
	 *
	 * @return int|null
	 */
	public static function subsite_id_from_url( string $url = '' ) {
		if ( empty( $url ) || ! is_multisite() ) {
			return null;
		}

		$url_parts = wp_parse_url( $url );
		$domain    = $url_parts['host'] ?? null;
		$path      = null;

		if ( defined( 'SUBDOMAIN_INSTALL' ) ) {
			if ( SUBDOMAIN_INSTALL ) {
				$path = '/';
			} else {
				$path = $url_parts['path'] ?? null;
			}
		}

		if ( ! empty( $domain ) && ! empty( $path ) ) {
			return get_blog_id_from_url( $domain, $path );
		}

		return null;
	}

	/**
	 * Checks if subsite id is valid.
	 * @param int|null $id
	 *
	 * @return false|void
	 */
	public static function valid_subsite_id( int $id = null ) {
		if ( empty( $id ) || ! is_multisite() ) {
			return false;
		}

		return in_array( $id, get_sites( array( 'fields' => "ids" ) ) );
	}

	/**
	 * Checks if given url belongs to author and returns author WP_User object else false.
	 *
	 * @since 2.1.0
	 *
	 * @param string $url
	 *
	 * @return WP_User|bool
	 */
	public static function is_author_url( string $url = '' ) {
		$site_url = site_url();

		if ( substr( $url, 0, strlen( $site_url ) ) !== $site_url ) {
			return false;
		}

		global $wp_rewrite;

		$parsed_url = wp_parse_url( $url );
		$user       = null;

		// Check url when it has plain permalink structure.
		if ( ! empty( $parsed_url['query'] ) ) {
			$parsed_query = array();
			parse_str( $parsed_url['query'], $parsed_query );

			if ( ! empty( $parsed_query[ $wp_rewrite->author_base ] ) ) {
				$user_key = is_numeric( $parsed_query[ $wp_rewrite->author_base ] ) ? 'id' : 'login';
				$user     = get_user_by( $user_key, sanitize_user( $parsed_query[ $wp_rewrite->author_base ] ) );
			}
		} else if ( ! empty( $parsed_url['path'] ) ) {
			// Check url with pretty permalink structure.
			$path        = trim( $parsed_url['path'], '/\\' );
			$author_base = "{$wp_rewrite->author_base}/";
			$user_name   = sanitize_user( str_replace( $author_base, '', $path ) );

			$user = get_user_by( 'login', $user_name );
		}

		return $user;
	}

	/**
	 * Get collation of a table's column.
	 * 
	 * @since 2.2.2
	 *
	 * @param string $table The table name
	 * @param string $column The table's column name
	 * @return null|string
	 */
	public static function get_table_col_collation( string $table = '', string $column = '' ) {
		if ( empty( $table ) || empty( $column ) ) {
			return null;
		}

		$table_parts = explode( '.', $table );
		$table       = ! empty( $table_parts[1] ) ? $table_parts[1] : $table;
		$col_key     = strtolower( "{$table}_{$column}" );

		static $tables_collates = array();

		if ( ! isset( $tables_collates[ $col_key ] ) ) {
			global $wpdb;

			$tables_collates[ $col_key ] = null;
			$table_status                = null;

			// Alternatively in order to check only for wp core tables $wpdb->tables() could be used.
			$tables_like_table = $wpdb->get_results( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) );

			if ( ! empty( $tables_like_table ) ) {
				$table_status = $wpdb->get_row(
					$wpdb->prepare(
						"SHOW FULL COLUMNS FROM {$table} WHERE field = '%s'",
						$column
					)
				);
			}

			if ( ! empty( $table_status ) && ! empty( $table_status->Collation ) ) {
				$tables_collates[ $col_key ] = $table_status->Collation;
			}
		}

		return $tables_collates[ $col_key ];
	}

	/**
	 * Get charset of a table's column.
	 * 
	 * @since 2.2.2
	 *
	 * @param string $table The table name
	 * @param string $column The table's column name
	 * @return null|string
	 */
	public static function get_table_col_charset( string $table = '', string $column = '' ) {
		if ( empty( $table ) || empty( $column ) ) {
			return null;
		}

		$collation = self::get_table_col_collation( $table, $column );

		if ( empty( $collation ) ) {
			return null;
		}

		list( $charset ) = explode( '_', $collation );

		return $charset;
	}

	/**
	 * Generate random unique id. Useful for creating element ids in scripts
	 *
	 * @since 2.0.0
	 *
	 * @param string $prefix Optional. A prefix.
	 *
	 * @return string Generate unique id.
	 */
	public static function get_unique_id( $prefix = null ): string {
		if ( is_null( $prefix ) ) {
			$prefix = uniqid() . '_';
		}

		return wp_unique_id( $prefix );
	}

	/**
	 * Checks if WPMU DEV Dashboard plugin is installed.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function dash_plugin_installed() {
		return apply_filters(
			'wpmudev_blc_is_dash_installed',
			file_exists( WP_PLUGIN_DIR . '/wpmudev-updates/update-notifications.php' )
		);
	}

	/**
	 * Checks if WPMU DEV Dashboard plugin is active.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function dash_plugin_active() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$is_active = is_multisite() ?
			is_plugin_active_for_network( 'wpmudev-updates/update-notifications.php' ) :
			is_plugin_active( 'wpmudev-updates/update-notifications.php' );

		return apply_filters( 'wpmudev_blc_is_dash_active', $is_active );
	}

	/**
	 * Returns a boolean. True if site is connected to hub or false if not.
	 *
	 * @return bool
	 */
	public static function is_site_connected() {
		return self::site_connected();
	}

	/**
	 * Returns a boolean. True if site is connected to hub or false if not.
	 *
	 * @return bool
	 */
	public static function membership_expired() {
		return 'expired' === self::get_membership_type();
	}

	/**
	 *  Check if user is active member.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function is_member() {
		$is_member = false;

		if ( class_exists( '\WPMUDEV_Dashboard' ) ) {
			if ( method_exists( '\WPMUDEV_Dashboard_Api', 'get_membership_projects' ) && method_exists( '\WPMUDEV_Dashboard_Api', 'get_membership_type' ) ) {
				$type      = WPMUDEV_Dashboard::$api->get_membership_type();
				$is_member = ! empty( $type );

				if ( ! $is_member && function_exists( 'is_wpmudev_member' ) ) {
					$is_member = is_wpmudev_member();
				}
			}
		}

		return apply_filters( 'wpmudev_blc_is_member', $is_member );
	}

	/**
	 * Returns the hub's start scan url.
	 *
	 * @return string
	 */
	public static function hub_scan_url() {
		$url = null;

		if ( self::get_dashboard_api() instanceof WPMUDEV_Dashboard_Api ) {
			$site_id = self::get_site_id();

			$url = apply_filters( 'wpmudev_blc_hub_scan_url', self::hub_home_url() . "?start-scan=1" );
		}

		return $url;
	}

	/**
	 * Returns the hub's url.
	 *
	 * @return string
	 */
	public static function hub_home_url() {
		$url = null;

		if ( self::get_dashboard_api() instanceof WPMUDEV_Dashboard_Api ) {
			$site_id = self::get_site_id();

			$url = apply_filters( 'wpmudev_blc_hub_home_url', untrailingslashit( self::wpmudev_base_url() . "hub2/site/{$site_id}/blc" ) );
		}

		return $url;
	}

	/**
	 * Returns WPMUDEV API Server URL
	 *
	 * @return string
	 */
	public static function wpmudev_base_url() {
		$api_server_url = defined( 'WPMUDEV_CUSTOM_API_SERVER' ) && WPMUDEV_CUSTOM_API_SERVER
			? trailingslashit( WPMUDEV_CUSTOM_API_SERVER )
			: 'https://wpmudev.com/';

		return $api_server_url;
	}

	/**
	 * Returns the hub's signup url.
	 *
	 * @return string
	 */
	public static function hub_signup_url() {
		return apply_filters( 'wpmudev_blc_hub_signup_url', self::wpmudev_base_url() . 'register/?signup=blc&blc_url=' . site_url() );
	}

	/**
	 * Returns the hub's connect url.
	 *
	 * @return string
	 */
	public static function hub_connect_url() {
		return apply_filters( 'wpmudev_blc_hub_connect_url', self::hub_base_url() . 'connect/choose-method/' );
	}

	/**
	 * Returns the signup url.
	 * If Dashboard plugin is active the signup url returned will be the Dashboard signup page. Else Hub signup page.
	 *
	 * @return string
	 */
	public static function signup_url() {
		if ( self::get_dashboard_api() instanceof WPMUDEV_Dashboard_Api ) {

			return add_query_arg( array(
				'page' => 'wpmudev',
			), is_multisite() ? network_admin_url() : get_admin_url() );
		}

		return self::hub_signup_url();
	}

	/**
	 * Returns the hub's url.
	 *
	 * @return string
	 */
	public static function hub_base_url() {
		return apply_filters( 'wpmudev_blc_hub_base_url', self::wpmudev_base_url() . 'hub2/' );
	}

	/**
	 * Returns the hub's account url.
	 *
	 * @return string
	 */
	public static function hub_account_url() {
		return apply_filters( 'wpmudev_blc_hub_account_url', self::hub_base_url() . 'account/' );
	}

	/**
	 * Returns the hub's start scan page url.
	 *
	 * @return string|null
	 */
	public static function hub_api_scan_url() {
		$url = null;

		if ( self::get_dashboard_api() instanceof WPMUDEV_Dashboard_Api ) {
			$url = add_query_arg(
				array(
					'domain'  => untrailingslashit( self::schemaless_url() ),
					'site_id' => self::site_id()
				),
				self::wpmudev_base_url() . "api/blc/v1/scan"
			);
		}

		return apply_filters( 'wpmudev_blc_api_scan_url', $url );
	}

	/**
	 * Returns url without schema (^http(s)?://).
	 *
	 * @param string $url .
	 *
	 * @return mixed
	 */
	public static function schemaless_url( string $url = '' ) {
		if ( empty( $url ) ) {
			$url = site_url();
		}

		$parsed_url = wp_parse_url( $url );

		$host = $parsed_url['host'];
		$path = $parsed_url['path'] ?? '';

		// Path includes port if it exists in url.
		return apply_filters( 'wpmudev_blc_schemaless_url', $host . $path, $url );
	}

	/**
	 * Returns the site id from hub.
	 *
	 * @return int
	 */
	public static function site_id() {
		return apply_filters( 'wpmudev_blc_site_id', intval( self::get_site_id() ) );
	}

	/**
	 * Returns the hub's start scan page url.
	 *
	 * @return string|null
	 */
	public static function hub_api_sync_url() {
		$url = null;

		if ( self::get_dashboard_api() instanceof WPMUDEV_Dashboard_Api ) {
			$url = add_query_arg(
				array(
					'domain'  => untrailingslashit( self::schemaless_url() ),
					'site_id' => self::site_id()
				),
				self::wpmudev_base_url() . "api/blc/v1/result"
			);
		}

		return apply_filters( 'result_api_sync_url', $url );
	}

	/**
	 * Returns the hub url to send edit link results when edit link queue gets completed.
	 *
	 * @return string
	 */
	public static function hub_edit_link_completed() {
		return apply_filters( 'hub_edit_link_completed', self::wpmudev_base_url() . "api/blc/v1/edit-link-completed" );
	}

	public static function make_link_relative( string $url = '' ) {
		$site_url       = site_url();
		$site_url_parts = wp_parse_url( $site_url );
		$url_parts      = wp_parse_url( $url );

		if ( ! empty( $url_parts['host'] ) && $site_url_parts['host'] !== $url_parts['host'] ) {
			return $url;
		}

		// Check if missing url scheme.
		// It is not unusual to have urls starting with two slashes.
		// Relative urls starting with 2 slashes replace everything from the hostname onward.
		if ( substr( $url, 0, 2 ) === "//" ) {
			$url = wp_parse_url( $site_url, PHP_URL_SCHEME ) . ':' . $url;
		}

		// If string is internal (starts with same url) we need to get the relative url.
		$link_substring = substr( $url, 0, strlen( $site_url ) );

		if ( strcasecmp( $link_substring, $site_url ) == 0 ) {
			return wp_make_link_relative( $url );
		}

		return $url;
	}

	/**
	 * Returns true if given screen(s) is the current admin screen.
	 *
	 * @since 2.0.0
	 *
	 * @param string|array $screen .
	 * @param bool $strict .
	 *
	 * @return bool
	 */
	public static function is_admin_screen( $screen = null, $strict = false ) {
		if ( is_null( $screen ) || ! is_callable( '\get_current_screen' ) || ! isset( get_current_screen()->id ) ) {
			return false;
		}

		if ( $strict ) {
			return is_array( $screen ) ?
				in_array( get_current_screen()->id, $screen, true ) :
				get_current_screen()->id === $screen;
		}

		return is_array( $screen ) ?
			! empty(
			array_filter(
				$screen,
				function ( $page ) {
					return strpos( get_current_screen()->id, $page ) !== false;
				}
			)
			) : strpos( get_current_screen()->id, $screen ) !== false;
	}

	/**
	 * Returns site time zone string as done in WP settings ( Not like `wp_timezone_string()` )
	 *
	 * @since 2.0.0
	 *
	 * @param bool $plain .
	 *
	 * @return string
	 */
	public static function get_timezone_string( bool $plain = true ) {
		$current_offset = get_option( 'gmt_offset' );
		$tzstring       = get_option( 'timezone_string' );

		// Remove old Etc mappings. Fallback to gmt_offset.
		if ( false !== strpos( $tzstring, 'Etc/GMT' ) ) {
			$tzstring = '';
		}

		if ( empty( $tzstring ) ) {
			// Create a UTC+- zone if no timezone string exists.
			if ( $plain ) {
				$tzstring = $current_offset < 0 ? $current_offset : "+{$current_offset}";
			} else {
				$tzstring = $current_offset < 0 ? "UTC{$current_offset}" : "UTC+{$current_offset}";
			}
		}

		return $tzstring;
	}

	/**
	 * Returns array with hour list 0...23. Taken from Snapshot4.
	 *
	 * @return array
	 */
	public static function get_hour_list() {
		$dt = new DateTime();
		$dt->setTimezone( new DateTimeZone( 'UTC' ) );
		$dt->setTimestamp( 0 );

		$result      = array();
		$time_format = self::get_time_format();
		foreach ( range( 0, 23 ) as $hour ) {
			$dt->setTime( $hour, 0, 0 );

			$key   = $dt->format( 'H:i' );
			$value = $dt->format( $time_format );

			$result[ $key ] = $value;
		}

		return $result;
	}

	/**
	 * Returns time format
	 *
	 * @return string
	 */
	public static function get_time_format() {
		$time_format = get_option( 'time_format' );

		$supported_formats = array(
			'g:i a',
			'g:i A',
			'g:i:s a',
			'g:i:s A',
			'g:i,',
			'H:i',
		);

		if ( ! in_array( $time_format, $supported_formats, true ) ) {
			$time_format = 'H:i'; // Fallback to default format.
		}

		return $time_format;
	}

	/**
	 * Returns an array with weekdays.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public static function get_week_days() {
		global $wp_locale;
		$week_days = array();

		for ( $day_index = 0; $day_index <= 6; $day_index ++ ) {
			$week_days[] = array(
				'key'   => $day_index,
				'value' => $wp_locale->get_weekday( $day_index ),
			);
		}

		return $week_days;
	}

	/**
	 * Returns the value of a given key from a query string.
	 *
	 * @param string $query_string .
	 * @param string $key .
	 *
	 * @return mixed
	 */
	public static function get_query_var( string $query_string = '', string $key = '' ) {
		if ( '' === $query_string || '' === $key ) {
			return false;
		}

		$query_vars = array();

		wp_parse_str( wp_parse_url( $query_string, PHP_URL_QUERY ), $query_vars );

		return $query_vars[ $key ] ?? false;
	}

	/**
	 * Returns user avatar by user id.
	 *
	 * @param int $id .
	 * @param array $args .
	 *
	 * @return array|bool|string|null
	 */
	public static function avatar_by_id( int $id = 0, array $args = array() ) {
		return self::avatar_data( $id, 'id', $args );
	}

	/**
	 * Retruns avatar dat.
	 *
	 * @param string $input .
	 * @param string $input_type .
	 * @param array $args .
	 *
	 * @return bool|null|array|string
	 */
	public static function avatar_data( string $input = '', string $input_type = 'id', array $args = array() ) {
		$response = null;

		switch ( $input_type ) {
			case 'id':
				$input = intval( $input );
				break;
			case 'email':
				$input = filter_var( $input, FILTER_VALIDATE_EMAIL );
				break;
		}

		$args = wp_parse_args(
			$args,
			array(
				'size'           => 96,
				'height'         => null,
				'width'          => null,
				'default'        => get_option( 'avatar_default', 'mystery' ),
				'force_default'  => false,
				'rating'         => get_option( 'avatar_rating' ),
				'scheme'         => null,
				'processed_args' => null, // If used, should be a reference.
				'extra_attr'     => '',
				'return'         => 'raw', // Allowed values, array raw, bool found_avatar, string url.
			)
		);

		$avatar_data = get_avatar_data( $input, $args );

		switch ( $args['return'] ) {
			case 'raw':
				$response = $avatar_data;
				break;
			case 'found_avatar':
				$response = isset( $avatar_data['found_avatar'] ) && (bool) $avatar_data['found_avatar'];
				break;
			case 'url':
				$response = isset( $avatar_data['url'] ) ? esc_html( $avatar_data['url'] ) : false;
				break;
		}

		return $response;
	}

	/**
	 * Returns avatar by a given email address.
	 *
	 * @param string $email .
	 * @param array $args .
	 *
	 * @return array|bool|string|null
	 */
	public static function avatar_by_email( string $email = '', array $args = array() ) {
		return self::avatar_data( $email, 'email', $args );
	}

	/**
	 * Returns user's role titles
	 *
	 * @param int $user_id The user's id to get roles for.
	 *
	 * @return array
	 */
	public static function user_role_names( int $user_id = 0 ) {
		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		$editable_roles = get_editable_roles();

		return array_map(
			function ( $item ) use ( $editable_roles ) {
				return isset( $editable_roles[ $item ] ) ? $editable_roles[ $item ]['name'] : $item;
			},
			get_userdata( $user_id )->roles
		);
	}

	/**
	 * Returns names of user roles
	 *
	 * @param array $capabilities Optional. An array with capabilities. Fetch roles per capability.
	 *
	 * @return array
	 */
	public static function roles_names( array $capabilities = array() ) {
		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		$roles = get_editable_roles();

		if ( ! empty( $capabilities ) ) {
			$roles = array_filter(
				$roles,
				function ( $role ) use ( $capabilities ) {
					foreach ( $capabilities as $capability ) {
						if ( array_key_exists( $capability, $role['capabilities'] ) ) {
							return true;
						}
					}

					return false;
				}
			);

		}

		return wp_list_pluck( $roles, 'name' );
	}

	/**
	 * Converts an int number of seconds to hours, minutes and seconds format.
	 *
	 * @param float $seconds A float holding number of seconds.
	 *
	 * @return string
	 */
	public static function normalize_seconds_format( float $seconds = 0 ) {
		if ( 0 >= $seconds ) {
			return $seconds;
		}

		$seconds   = round( $seconds );
		$hours_str = '';

		if ( $seconds >= HOUR_IN_SECONDS ) {
			$hours_str = sprintf(
				//translators: 1. The Hours of the datetime.
				_n(
					'%d h ',
					'%d h ',
					gmdate( 'H', $seconds ),
					'broken-link-checker'
				),
				gmdate( 'H', $seconds )
			);
		}

		$minutes_str = sprintf(
			//translators: 1. The Minutes of the datetime.
			_n(
				'%d min ',
				'%d min ',
				gmdate( 'i', $seconds ),
				'broken-link-checker'
			),
			gmdate( 'i', $seconds )
		);

		$seconds_str = ( $seconds % MINUTE_IN_SECONDS ) > 0 ? sprintf(
			//translators: 1. The Seconds of the datetime.
			_n(
				'%d s ',
				'%d s ',
				gmdate( 's', $seconds ),
				'broken-link-checker'
			),
			gmdate( 's', $seconds )
		) : '';

		return $hours_str . $minutes_str . $seconds_str;
	}

	/**
	 * Formats a timestamp into a date and time string based on site format.
	 *
	 * @param int $timestamp The timestamp to format.
	 *
	 * @return string|null
	 */
	public static function timestamp_to_formatted_date( int $timestamp = null, bool $gmt_to_local = false ) {
		$timezone = null;

		if ( ! $gmt_to_local ) {
			$timezone = new DateTimeZone( 'UTC' );
		}

		return self::format_date( $timestamp, null, $timezone );
	}

	/**
	 * Format a time/date
	 *
	 * @param integer $timestamp Timestamp.
	 * @param string|null $format Date/time format.
	 * @param DateTimeZone|null $timezone Timezone.
	 *
	 * @return string
	 * @todo Rename method to be datetime specific.
	 *
	 */
	public static function format_date( $timestamp, $format = null, $timezone = null ) {
		if ( is_null( $format ) ) {
			$format = self::get_format();
		}
		if ( is_null( $timezone ) ) {
			$timezone = self::get_timezone();
		}
		$dt = new DateTime();
		$dt->setTimestamp( $timestamp );
		$dt->setTimezone( $timezone );

		return $dt->format( $format );
	}

	/**
	 * Returns datetime format
	 *
	 * @return string
	 * @todo Rename method to be datetime specific.
	 *
	 */
	public static function get_format() {
		$format = self::get_date_format();
		$format .= _x( ' ', 'date time sep', 'broken-link-checker' );
		$format .= self::get_time_format();

		return $format;
	}

	/**
	 * Returns date format
	 *
	 * @return string
	 */
	public static function get_date_format() {
		return get_option( 'date_format' );
	}

	/**
	 * Returns user's timezone
	 *
	 * @return DateTimeZone
	 */
	public static function get_timezone() {
		return wp_timezone();
	}

	/**
	 * Converts a timestamp from UTC to site's local timezone.
	 *
	 * @param int|null $timestamp
	 *
	 * @return false|int
	 */
	public static function timestamp_from_UTC( int $timestamp = null ) {
		$timestamp = is_null( $timestamp ) ? time() : $timestamp;
		$timestamp = strlen( $timestamp ) === 13 ? $timestamp / 1000 : $timestamp;
		$date_time = DateTime::createFromFormat( 'U', $timestamp, new DateTimeZone('UTC') );

		$date_time->setTimezone( new DateTimeZone( wp_timezone_string() ) );

		return strtotime( $date_time->format('Y-m-d H:i:s') );
	}

	/**
	 * Converts microtime to date.
	 *
	 * @param int|float $microtime Microtime. Int when sent from API, float from PHP.
	 * @param string $form Output form. Accepted values 'full_date', 'date', 'day', 'month', 'year', 'time'.
	 * @param bool $gmt_to_local Convert from GMT to site local timezone. By default, it's false.
	 *
	 * @return string|null
	 */
	public static function microtime_to_date( $microtime = 0, string $form = 'full_date', bool $gmt_to_local = false
	) {
		if ( ! in_array( $form, array( 'full_date', 'date', 'day', 'month', 'year', 'time' ), true ) ) {
			return null;
		}

		$microtime = ! is_float( $microtime ) ? ( strlen( $microtime ) === 13 ? $microtime / 1000 : $microtime ) : $microtime;
		$date_time = null;

		if ( strlen( $microtime ) === 10 ) {
			$date_time = DateTime::createFromFormat( 'U', $microtime );
		} elseif ( strlen( $microtime ) === 13 ) {
			$date_time = DateTime::createFromFormat( 'U.u', $microtime );
		} else {
			return null;
		}

		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		$output      = null;

		if ( $gmt_to_local ) {
			$date_time->setTimezone( new DateTimeZone( wp_timezone_string() ) );
		}

		switch ( $form ) {
			case 'full_date':
				$output = $date_time->format( "{$date_format} {$time_format}" );
				break;
			case 'date':
				$output = $date_time->format( $date_format );
				break;
			case 'day':
				$output = $date_time->format( 'd' );
				break;
			case 'month':
				$output = $date_time->format( 'm' );
				break;
			case 'year':
				$output = $date_time->format( 'Y' );
				break;
			case 'time':
				$output = $date_time->format( $time_format );
				break;
		}

		return $output;
	}

	/**
	 * Calculates the diff of microtime.
	 *
	 * @param int|float|null $micro_start .
	 * @param int|float|null $micro_end .
	 * @param string $format .
	 *
	 * @return false|float|int|mixed|null
	 */
	public static function microtimediff( $micro_start = null, $micro_end = null, string $format = 'SEC' ) {
		if ( is_null( $micro_start ) ) {
			return false;
		}

		if ( is_null( $micro_end ) ) {
			/*
			 * Multiplying with 1000 so php microtime matches format sent from api.
			 */
			$micro_end = intval( round( microtime( true ) * 1000 ) );
		}

		/**
		 * From APIs the microtime is usually a float.
		 * The previous condition, if reached, will give an integer, so there is no risk of multiplying by 1000 twice.
		 */
		$micro_start = is_float( $micro_start ) ? round( $micro_start * 1000 ) : $micro_start;
		$micro_end   = is_float( $micro_end ) ? round( $micro_end * 1000 ) : $micro_end;

		/*
		 * Microtime has 13 digits as sent from api side.
		 */
		if ( strlen( $micro_start ) !== 13 || strlen( $micro_end ) !== 13 ) {
			return false;
		}

		$diff = number_format( $micro_end - $micro_start, 2, '.', '' );

		switch ( $format ) {
			case 'MIN':
				$diff = $diff / 1000 / MINUTE_IN_SECONDS;
				break;
			case 'SEC':
				$diff = $diff / 1000;
				break;
			default:
			case 'MICRO':
				break;
		}

		return $diff;
	}

	/**
	 * Returns true if plain permalinks iare used.
	 *
	 * @return bool
	 */
	public static function plain_permalinks_mode() {
		return empty( get_option( 'permalink_structure' ) );
	}

	/**
	 * Checks if site is hosted on localhost.
	 *
	 * @return bool
	 */
	public static function is_localhost() {
		return ! wp_doing_cron() &&
		       isset( $_SERVER['REMOTE_ADDR'] ) &&
		       in_array( $_SERVER['REMOTE_ADDR'], array( '127.0.0.1', '::1' ), true ) &&
		       ! ( defined( 'WPMUDEV_DEVELOPMENT_MODE' ) && WPMUDEV_DEVELOPMENT_MODE );
	}

	/**
	 * Returns an array with all the callbacks for a cron's hook.
	 *
	 * @param string $hook .
	 *
	 * @return array
	 */
	public static function get_scheduled_event_callbacks( string $hook = '' ) {
		return self::get_hook_callbacks( $hook );
	}

	/**
	 * Returns hook callbacks. Taken from WP Crontrol plugin.
	 *
	 * @param string $hook .
	 *
	 * @return array
	 */
	public static function get_hook_callbacks( string $hook = '' ) {
		global $wp_filter;

		$actions = array();

		if ( isset( $wp_filter[ $hook ] ) ) {
			// See http://core.trac.wordpress.org/ticket/17817.
			$action = $wp_filter[ $hook ];

			foreach ( $action as $priority => $callbacks ) {
				foreach ( $callbacks as $callback ) {
					$actions[] = array(
						'priority' => $priority,
						'callback' => $callback,
					);
				}
			}
		}

		return $actions;
	}

	/**
	 * Returns replaced value based on array keys and callbacks/values from a given mapped array.
	 *
	 * @param string $content The string that contains the content we need to replace the mapped values.
	 * @param array $map An array mapping keys and values/callbacks.
	 * @param string $context An optional string holding a context. Used in filter to help in specificity.
	 * @param array $keys Optional. An array of keys to map in given mapped array. If empty all $map's array keys will be used.
	 *
	 * @return string
	 */
	public static function replace_mapped_values( string $content = '', array $map = array(), string $context = null, array $keys = array() ) {
		if ( empty( $map ) ) {
			return $content;
		}

		if ( empty( $keys ) ) {
			$keys = array_keys( $map );
		}

		$mapped_values = array_fill_keys( $keys, null );

		foreach ( $keys as $key ) {
			$mapped_values[ $key ] = self::get_mapped_value( $map[ $key ], $context );
		}

		return str_replace( array_keys( $mapped_values ), array_values( $mapped_values ), $content );
	}

	/**
	 * Returns replaced value based on array keys and callbacks/values from a given mapped array.
	 *
	 * @param mixed $input An array mapping keys and values/callbacks.
	 * @param string $context An optional string holding a context. Used in filter to help in specificity.
	 */
	public static function get_mapped_value( $input = null, string $context = null ) {
		if ( is_null( $input ) ) {
			return null;
		}

		return apply_filters(
			'wpmudev_blc_replace_mapped_value',
			is_callable( $input ) ? call_user_func( $input ) : $input,
			$input,
			$context
		);
	}

	/**
	 * Verifies is input code is a valid http response code.
	 *
	 * @param int $code The code to verify.
	 *
	 * @return bool
	 */
	public static function valid_http_response_code( int $code = 0 ) {
		// HTTP Response codes should be between 100 and 599: https://developer.mozilla.org/en-US/docs/Web/HTTP/Status.
		return is_int( $code ) && $code >= 100 && $code <= 599;
	}

	/**
	 * Logs messages in either `wp-content/debug.log` by default or to a given file path. Requires `WP_DEBUG_LOG` to be set to true.
	 *
	 * @param string $message The message to be logged.
	 * @param string $file Optional. Log file path. If not set `wp-content/debug.log` will be used. File path has to be relative to WP_CONTENT_DIR.
	 *
	 * @return void
	 */
	public static function log( string $message = '', string $file_path = '' ) {
		if ( ! defined( 'WPMUDEV_BLC_LOG' ) || ! WPMUDEV_BLC_LOG ) {
			return;
		}

		$month            = date( 'm' );
		$year             = date( 'Y' );
		$default_log_file = "/blc-logs/debug-{$year}-{$month}.log";

		if ( empty( $file_path ) ) {
			$file_path = $default_log_file;
		}

		$dt_string = date( self::get_date_format() . ' ' . self::get_time_format() );
		$file_path = wp_normalize_path( path_join( WP_CONTENT_DIR, ltrim( $file_path, '/' ) ) );

		if ( ! is_dir( dirname( $file_path ) ) ) {
			mkdir( dirname( $file_path ), 0755, true );
		}

		if ( ! file_exists( $file_path ) ) {
			$new_file = fopen( $file_path, 'w' );

			if ( ! $new_file ) {
				return;
			}

			fclose( $new_file );
		}

		error_log( "[{$dt_string}] $message \n", 3, $file_path );
	}

	/**
	 * Provides a flag that determines if plugin should go through links more extensively.
	 *
	 * @param $param
	 *
	 * @return bool
	 */
	public static function process_extensive( $param = null ) {
		return apply_filters( 'wpmudev_blc_process_extensive', false, $param );
	}
}
