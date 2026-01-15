<?php
/**
 * Class Utils
 *
 * @package GravityView\TrustedLogin\Client
 *
 * @copyright 2024 Katz Web Services, Inc.
 */

namespace TEC\Common\TrustedLogin;

/**
 * Class Utils
 */
class Utils {

	/**
	 * Wrapper around {@see get_transient()}. Transient is stored as an option {@see self::set_transient()} in order to avoid object caching issues.
	 * Raw SQL query (taken from WordPress core) is used in order to avoid object caching issues, such as with the Redis Object Cache plugin.
	 *
	 * @since 1.7.0
	 *
	 * @param string $transient Transient name.
	 *
	 * @return mixed|false Transient value or false if not set.
	 */
	public static function get_transient( $transient ) {
		global $wpdb;

		if ( ! is_string( $transient ) ) {
			return false;
		}

		if ( ! is_object( $wpdb ) ) {
			return false;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$pre = apply_filters( "pre_transient_{$transient}", false, $transient );

		if ( false !== $pre ) {
			return $pre;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM `$wpdb->options` WHERE option_name = %s LIMIT 1", $transient ) );

		if ( ! is_object( $row ) ) {
			return false;
		}

		$data = maybe_unserialize( $row->option_value );

		$value = self::retrieve_value_and_maybe_expire_transient( $transient, $data );

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		return apply_filters( "transient_{$transient}", $value, $transient );
	}

	/**
	 * Wrapper around {@see set_transient()}. Transient is stored as an option in order to avoid object caching issues.
	 * Raw SQL query (taken from WordPress core) is used in order to avoid object caching issues, such as with the Redis Object Cache plugin.
	 *
	 * @since 1.7.0
	 *
	 * @param string $transient  Transient name.
	 * @param mixed  $value      Transient value.
	 * @param int    $expiration (optional) Time until expiration in seconds. Default: 0 (no expiration).
	 *
	 * @return bool True if the value was set, false otherwise.
	 */
	public static function set_transient( $transient, $value, $expiration = 0 ) {
		global $wpdb;

		if ( ! is_string( $transient ) ) {
			return false;
		}

		if ( ! is_object( $wpdb ) ) {
			return false;
		}

		wp_protect_special_option( $transient );

		$expiration = (int) $expiration;

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$value = apply_filters( "pre_set_transient_{$transient}", $value, $expiration, $transient );

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$expiration = apply_filters( "expiration_of_transient_{$transient}", $expiration, $value, $transient );

		$data = self::format_transient_data( $value, $expiration );

		// Insert or update the option.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO `$wpdb->options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)", $transient, maybe_serialize( $data ), true ) );

		if ( $result ) {
			do_action( "set_transient_{$transient}", $data['value'], $data['expiration'], $transient ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( 'setted_transient', $transient, $data['value'], $data['expiration'] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		}

		return $result;
	}

	/**
	 * Retrieves a value from the transient data and conditionally deletes transient if expired.
	 *
	 * @since 1.7.0
	 *
	 * @param string $transient      Transient name.
	 * @param mixed  $transient_data Transient data as stored in the database (unserialized).
	 *
	 * @return false|mixed
	 */
	private static function retrieve_value_and_maybe_expire_transient( $transient, $transient_data ) {

		if ( ! is_array( $transient_data ) ) {
			return false;
		}

		// If the transient lacks an expiration time or value, it's not a valid transient.
		if ( ! array_key_exists( 'expiration', $transient_data ) || ! array_key_exists( 'value', $transient_data ) ) {
			return false;
		}

		// If the transient has a non-zero expiration and has expired, delete it and return false.
		if ( 0 !== ( isset( $transient_data['expiration'] ) ? $transient_data['expiration'] : 0 ) && time() > $transient_data['expiration'] ) {
			delete_option( $transient );

			return false;
		}

		return $transient_data['value'];
	}

	/**
	 * Formats transient data for storage in the database.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $value      Transient value.
	 * @param int   $expiration (optional) Time until expiration in seconds. Default: 0 (no expiration).
	 *
	 * @return array
	 */
	private static function format_transient_data( $value, $expiration = 0 ) {
		return array(
			'expiration' => 0 === $expiration ? $expiration : time() + $expiration,
			'value'      => $value,
		);
	}

	/**
	 * Returns the HTTP user agent, sanitized.
	 *
	 * @param int $max_length The maximum length of the returned user agent string.
	 *
	 * @return string The user agent string, sanitized. Truncated at $max_length, if set.
	 */
	public static function get_user_agent( $max_length = 0 ) {

		if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return '';
		}

		$user_agent = wp_unslash( $_SERVER['HTTP_USER_AGENT'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$user_agent = sanitize_text_field( $user_agent );

		if ( ! $max_length ) {
			return $user_agent;
		}

		return substr( esc_attr( $user_agent ), 0, (int) $max_length );
	}

	/**
	 * Returns the IP address of the requester
	 *
	 * @since 1.7.1 Moved from SecurityChecks class to Utils class.
	 *
	 * @return null|string Returns null if REMOTE_ADDR isn't set, string IP address otherwise.
	 */
	public static function get_ip() {

		if ( ! isset( $_SERVER['REMOTE_ADDR'] ) ) {
			return null;
		}

		$ip = wp_unslash( $_SERVER['REMOTE_ADDR'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$ip = trim( $ip );

		$ip = sanitize_text_field( $ip );

		if ( ! defined( 'TL_DOING_TESTS' ) ) {
			$ip = filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE );
		}

		return (string) $ip;
	}

	/**
	 * Sanitizes text. If the text is already sanitized, it is returned as is.
	 *
	 * This is faster alternative to {@see sanitize_title_with_dashes()}. We're not concerned with whether the text
	 * begins or ends with a dash, or if it has multiple dashes in a row. We're only concerned with whether the text
	 * contains characters that are not a-z, 0-9, a hyphen, or an underscore.
	 *
	 * @since 1.8.0
	 *
	 * @param string $text The text to sanitize.
	 *
	 * @return string The sanitized role name. Sanitized with {@uses sanitize_title_with_dashes}.
	 */
	public static function sanitize_with_dashes( $text ) {

		preg_match( '/[^a-z0-9_-]/', $text, $matches );

		// If the text is already sanitized, return it. This saves a minor amount of processing.
		if ( empty( $matches ) ) {
			return $text;
		}

		return sanitize_title_with_dashes( $text );
	}

	/**
	 * Retrieves and optionally sanitizes a parameter from $_POST or $_GET.
	 *
	 * Use this instead of $_REQUEST to avoid potential security issues related to $_REQUEST including $_COOKIE data.
	 *
	 * @since 1.8.0
	 *
	 * @param string $param The parameter to retrieve.
	 * @param bool   $sanitize Whether to sanitize the parameter using {@see sanitize_text_field}. Default: true.
	 *
	 * @return string|array|null The parameter value or null if not found.
	 */
	public static function get_request_param( $param, $sanitize = true ) {
		$value = null;

		//phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( isset( $_POST[ $param ] ) ) {
			$value = wp_unslash( $_POST[ $param ] );
		} elseif ( isset( $_GET[ $param ] ) ) {
			$value = wp_unslash( $_GET[ $param ] );
		}
		//phpcs:enable WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! $sanitize || null === $value ) {
			return $value;
		}

		if ( is_string( $value ) ) {
			return sanitize_text_field( $value );
		}

		// Handle arrays.
		return map_deep( $value, 'sanitize_text_field' );
	}
}
