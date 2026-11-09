<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Traits;

use Throwable;
use WP_Error;

trait With_Debugging {

	/**
	 * Determine if WP_DEBUG is enabled.
	 *
	 * @return bool
	 */
	protected function is_wp_debug(): bool {
		return defined( 'WP_DEBUG' ) && WP_DEBUG;
	}

	/**
	 * Log a debug message when WP_DEBUG is enabled.
	 *
	 * All messages are prefixed with "Harbor:" for easy filtering in the debug log.
	 *
	 * Logging is skipped entirely when the `LW_HARBOR_DISABLE_DEBUG_LOG` constant is
	 * defined and truthy, allowing site owners to silence Harbor's debug output even
	 * while `WP_DEBUG` and `WP_DEBUG_LOG` remain enabled for other purposes.
	 *
	 * @since 1.5.0 add LW_HARBOR_DISABLE_DEBUG_LOG
	 * @since 1.0.0
	 *
	 * @param string $message The message to log.
	 *
	 * @return void
	 */
	protected static function debug_log( string $message ): void {
		// Allow site owners to opt out of Harbor's debug logging without disabling WP_DEBUG.
		if ( defined( 'LW_HARBOR_DISABLE_DEBUG_LOG' ) && LW_HARBOR_DISABLE_DEBUG_LOG ) {
			return;
		}

		// Inline check for WP_DEBUG instead of using the `is_wp_debug()` method here because `is_wp_debug()` is not static and we don't want to change it because of the backwards compatibility.
		if (
			! defined( 'WP_DEBUG' )
			|| ! WP_DEBUG
		) {
			return;
		}

		if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) {
			return;
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentionally logging debug info.
		error_log( 'Harbor: ' . $message );
	}

	/**
	 * Log a Throwable with context when WP_DEBUG is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param Throwable $e       The Throwable to log.
	 * @param string    $context A short description of where the error occurred.
	 *
	 * @return void
	 */
	protected static function debug_log_throwable( Throwable $e, string $context ): void {
		static::debug_log(
			"{$context}: {$e->getMessage()} {$e->getFile()}:{$e->getLine()} {$e->getTraceAsString()}"
		);
	}

	/**
	 * Log a WP_Error with context when WP_DEBUG is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Error $error   The WP_Error to log.
	 * @param string   $context A short description of where the error occurred.
	 *
	 * @return void
	 */
	protected static function debug_log_wp_error( WP_Error $error, string $context ): void {
		static::debug_log(
			"{$context}: [{$error->get_error_code()}] {$error->get_error_message()}"
		);
	}
}
