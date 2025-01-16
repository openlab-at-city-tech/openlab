<?php
/**
 * WPMUDEV Dashboard API methods.
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
trait Dashboard_API {
	/**
	 * Returns WPMUDEV Dashboard API object
	 *
	 * @return \WPMUDEV_Dashboard_Api|null
	 */
	public static function get_dashboard_api(): ?\WPMUDEV_Dashboard_Api {
		if ( class_exists( 'WPMUDEV_Dashboard' ) && ! empty( \WPMUDEV_Dashboard::$api ) ) {
			return \WPMUDEV_Dashboard::$api;
		}
		return null;
	}

	/**
	 * Returns WPMU DEV membership type
	 *
	 * Possible return values:
	 * 'free'    - Free hub membership.
	 * 'single'  - Single membership (i.e. only 1 project is licensed)
	 * 'unit'    - One or more projects licensed
	 * 'full'    - Full membership, no restrictions.
	 * 'paused'  - Membership access is paused.
	 * 'expired' - Expired membership.
	 * ''        - (empty string) If user is not logged in or with an unknown type.
	 *
	 * @return string
	 */
	public static function get_membership_type(): ?string {
		static $result = null;

		if ( ! is_null( $result ) ) {
			return $result;
		}

		$api = self::get_dashboard_api();

		if ( $api ) {
			$result = null;

			if ( is_callable( array( $api, 'get_membership_status' ) ) ) {
				$result = $api->get_membership_status();
			} elseif ( is_callable( array( $api, 'get_membership_type' ) ) ) {
				$result = $api->get_membership_type();
			}
		} else {
			$result = \WPMUDEV\Hub\Connector\Data::get()->membership_type();
		}

		return strval( apply_filters( 'wpmudev_blc_dashboard_membership_type', $result ) );
	}

	/**
	 * Returns a boolean. True if site is connected to hub or false if not.
	 *
	 * @return bool
	 */
	public static function site_connected(): bool {
		static $site_connected = null;

		if ( ! is_bool( $site_connected ) ) {
			$site_connected = class_exists( 'WPMUDEV_Dashboard' ) ? ! empty( self::get_membership_type() ) : self::hub_connector_logged_in();
			//$site_connected = apply_filters( 'wpmudev_blc_dashboard_site_connected', ! empty( self::get_membership_type() ) );
		}

		/**
		 * Filters site connected result.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'wpmudev_blc_dashboard_site_connected', boolval( $site_connected ) );

		// phpcs:disable
		/*
		 * Until we get BLC project ID we will keep using Dashboard's get_membership_status().
		 */

		/*
		$has_access = false;

		if ( class_exists( 'WPMUDEV_Dashboard' ) && method_exists( WPMUDEV_Dashboard::$upgrader, 'user_can_install' ) ) {
			$has_access = WPMUDEV_Dashboard::$upgrader->user_can_install( XXXX, true );
		}

		return apply_filters( 'wpmudev_blc_dashboard_site_connected', $has_access );
		*/
		// phpcs:enable
	}

	/**
	 * Returns the Hub site id.
	 *
	 * @return int|null
	 */
	public static function get_site_id(): ?int {
		$api = self::get_dashboard_api();

		if ( $api instanceof \WPMUDEV_Dashboard_Api && is_numeric( $api->get_site_id() ) ) {
			return intval( $api->get_site_id() );
		}

		return \WPMUDEV\Hub\Connector\Data::get()->hub_site_id();
	}

	/**
	 * Returns an array with WPMUDEV Dashboard users.
	 *
	 * @return array|bool
	 */
	public static function get_dash_users() {
		if ( ! class_exists( 'WPMUDEV_Dashboard' ) ) {
			return array();
		}

		return \WPMUDEV_Dashboard::$site->get_allowed_users( true );
	}

	/**
	 * Returns email or WP user that connected Dash plugin to HUB. If nothing found it returns false.
	 *
	 * @param bool $wp_user Defines if method should return the WP_User if found. Else it returns the email.
	 *
	 * @return false|string|WP_User
	 */
	public static function get_auth_user( bool $wp_user = false ) {
		if ( ! class_exists( 'WPMUDEV_Dashboard' ) ) {
			return false;
		}

		$auth_user = \WPMUDEV_Dashboard::$settings->get( 'auth_user', 'general' );

		if ( $wp_user && ! empty( $auth_user ) ) {
			return \get_user_by( 'email', $auth_user );
		}

		return $auth_user;
	}

	/**
	 * Checks if White Labeling is active on Dashboard plugin.
	 *
	 * @return bool
	 */
	public static function white_label_active(): bool {
		static $is_whitelabel_enabled = null;

		if ( is_null( $is_whitelabel_enabled ) ) {
			$is_whitelabel_enabled = class_exists( 'WPMUDEV_Dashboard' ) ? \WPMUDEV_Dashboard::$whitelabel->is_whitelabel_enabled() : false;
		}

		return $is_whitelabel_enabled;
	}

	/**
	 * Checks if Hub Connector is active. If Dash plugin is not installed Hub connector can take over.
	 *
	 * @return bool
	 */
	public static function hub_connector_active(): bool {
		/**
		 * Filters if HUB Connector is active (Dash plugin not installed).
		 *
		 * @since 2.3.0
		 */
		return apply_filters( 'wpmudev_blc_dashboard_hub_connector_active', ! file_exists( trailingslashit( WP_PLUGIN_DIR ) . 'wpmudev-updates/update-notifications.php' ) );
	}

	/**
	 * Checks if Hub Connector is logged in.
	 *
	 * @return bool
	 */
	public static function hub_connector_logged_in(): bool {
		/**
		 * Filters if HUB Connector is logged in.
		 *
		 * @since 2.3.0
		 */
		return apply_filters( 'wpmudev_blc_dashboard_hub_connector_logged_in', boolval( \WPMUDEV\Hub\Connector\API::get()->is_logged_in() ) );
	}

	/**
	 * Checks if Hub Connector is connected. If Dash plugin is not installed Hub connector can take over.
	 *
	 * @return bool
	 */
	public static function hub_connector_connected(): bool {
		static $connected = null;

		if ( ! is_bool( $connected ) ) {
			if ( class_exists( 'WPMUDEV_Dashboard' ) ) {
				$connected = self::get_dashboard_api()->has_key();
			} else {
				$connected = self::hub_connector_logged_in();
			}
		}

		/**
		 * Filters if HUB Connector is connected.
		 *
		 * @since 2.3.0
		 */
		return apply_filters( 'wpmudev_blc_dashboard_hub_connector_connected', boolval( $connected ) );
	}

	/**
	 * Indicates if Hub Connector UI should be used. It should be used when Connector is active (Dash not installed) and when user not logged in.
	 *
	 * @return boolean
	 */
	public static function load_hub_connector_ui(): bool {
		/**
		 * Filters if HUB Connector should be used.
		 *
		 * @since 2.3.0
		 */

		return apply_filters( 'wpmudev_blc_dashboard_use_hub_connector', ! class_exists( 'WPMUDEV_Dashboard' ) );
	}

	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	public static function get_profile_data(): array {
		if ( class_exists( 'WPMUDEV_Dashboard' ) ) {
			$profile_data = self::get_dashboard_api()->get_profile();
			if( ! empty( $profile_data['profile'] ) ) { 
				return $profile_data['profile'];
			} 
		 } elseif ( self::hub_connector_logged_in() ) {
			return \WPMUDEV\Hub\Connector\Data::get()->profile_data();
		}

		return array();
	}

	/**
	 * Returns the api key.
	 *
	 * @return string
	 */
	public static function get_api_key(): string {
		static $api_key = null;

		if ( ! is_null( $api_key ) ) {
			return $api_key;
		}

		if ( class_exists( 'WPMUDEV_Dashboard' ) ) {
			$api_key = \WPMUDEV_Dashboard::$api->get_key();
		}else {
			$api_key = \WPMUDEV\Hub\Connector\API::get()->get_api_key();
		}

		return apply_filters( 'wpmudev_blc_dashboard_api_key', $api_key );
	}
}
