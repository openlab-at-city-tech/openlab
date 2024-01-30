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
		$api = self::get_dashboard_api();

		if ( $api ) {
			$result = null;

			if ( is_callable( array( $api, 'get_membership_status' ) ) ) {
				$result = $api->get_membership_status();
			} elseif ( is_callable( array( $api, 'get_membership_type' ) ) ) {
				$result = $api->get_membership_type();
			}

			if ( ! \is_null( $result ) ) {
				return strval( apply_filters( 'wpmudev_blc_dashboard_membership_type', $result ) );
			}
		}

		return null;
	}

	/**
	 * Returns a boolean. True if site is connected to hub or false if not.
	 *
	 * @return bool
	 */
	public static function site_connected(): bool {
		return apply_filters( 'wpmudev_blc_dashboard_site_connected', ! empty( self::get_membership_type() ) );

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

		return null;
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
}
