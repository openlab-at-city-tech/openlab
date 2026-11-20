<?php
/**
 * Failed page password attempts per IP.
 *
 * @package Imagely\NGG\PrivateGalleries
 */

namespace Imagely\NGG\PrivateGalleries;

/**
 * 5 failed passwords per IP in 10 minutes lock the password form for 10 minutes. Keyed on
 * REMOTE_ADDR only: forwarded headers are set by the client. An IPv6 visitor counts per /64.
 */
class RateLimiter {

	/**
	 * Failures allowed in one window.
	 *
	 * @var int
	 */
	const MAX_FAILURES = 5;

	/**
	 * Window length, in seconds.
	 *
	 * @var int
	 */
	const WINDOW = 600;

	/**
	 * Lock length, in seconds.
	 *
	 * @var int
	 */
	const LOCK = 600;

	/**
	 * Seconds a password check waits for another check from the same address.
	 *
	 * @var int
	 */
	const MUTEX_WAIT = 2;

	/**
	 * Seconds after which a held check lock counts as left by a request that died.
	 *
	 * @var int
	 */
	const MUTEX_STALE = 30;

	/**
	 * Whether the current IP is locked out.
	 *
	 * @return bool
	 */
	public static function is_locked() {
		$state = get_transient( self::key() );

		return is_array( $state ) && ! empty( $state['locked_until'] ) && (int) $state['locked_until'] > time();
	}

	/**
	 * Counts a failed password and locks the IP at the limit.
	 *
	 * @return bool Whether the IP is now locked.
	 */
	public static function record_failure() {
		$now   = time();
		$key   = self::key();
		$state = get_transient( $key );

		if ( ! is_array( $state ) || ! isset( $state['start'], $state['count'] ) || ( $now - (int) $state['start'] ) >= self::WINDOW ) {
			$state = [
				'start'        => $now,
				'count'        => 0,
				'locked_until' => 0,
			];
		}

		++$state['count'];

		if ( $state['count'] >= self::MAX_FAILURES ) {
			$state['locked_until'] = $now + self::LOCK;
			$ttl                   = self::LOCK;
		} else {
			// Fixed close time; re-deriving it from now would slide the window forever.
			$ttl = max( 1, self::WINDOW - ( $now - (int) $state['start'] ) );
		}

		set_transient( $key, $state, $ttl );

		return $state['count'] >= self::MAX_FAILURES;
	}

	/**
	 * Takes the current address's check lock, so parallel guesses cannot all pass is_locked()
	 * before one failure is counted. False when another check held it for MUTEX_WAIT seconds.
	 *
	 * @return bool
	 */
	public static function acquire() {
		global $wpdb;

		$name     = self::mutex_name();
		$deadline = microtime( true ) + self::MUTEX_WAIT;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- An atomic lock row, like WP_Upgrader::create_lock(); the options cache must not answer.
		do {
			$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->options} ( option_name, option_value, autoload ) VALUES ( %s, %s, 'no' )", $name, (string) time() ) );

			if ( 1 === (int) $wpdb->rows_affected ) {
				return true;
			}

			$since = (int) $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $name ) );

			if ( $since && time() - $since > self::MUTEX_STALE ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", $name, (string) $since ) );
				continue;
			}

			usleep( 50000 );
		} while ( microtime( true ) < $deadline );
		// phpcs:enable

		return false;
	}

	/**
	 * Releases the current address's check lock.
	 *
	 * @return void
	 */
	public static function release() {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The lock row from acquire().
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = %s", self::mutex_name() ) );
	}

	/**
	 * Clears the current IP's record (tests).
	 *
	 * @return void
	 */
	public static function clear() {
		delete_transient( self::key() );
		self::release();
	}

	/**
	 * Transient key for the current IP.
	 *
	 * @return string
	 */
	private static function key() {
		$ip     = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$packed = filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ? inet_pton( $ip ) : false;

		if ( is_string( $packed ) && 16 === strlen( $packed ) ) {
			// An IPv4-mapped address is that IPv4 address; any other IPv6 host usually owns its /64.
			if ( str_repeat( "\0", 10 ) . "\xff\xff" === substr( $packed, 0, 12 ) ) {
				$ip = (string) inet_ntop( substr( $packed, 12 ) );
			} else {
				$ip = bin2hex( substr( $packed, 0, 8 ) ) . '/64';
			}
		}

		return 'ngg_pg_rl_' . md5( $ip );
	}

	/**
	 * Option name of the current address's check lock.
	 *
	 * @return string
	 */
	private static function mutex_name() {
		return self::key() . '_lock';
	}
}
