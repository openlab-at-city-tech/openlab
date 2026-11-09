<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Traits;

use WP_Error;

/**
 * Throttles outbound API calls after a failure.
 *
 * When a remote call fails, subsequent calls are short-circuited and
 * return the cached WP_Error until the TTL window expires. This prevents
 * hammering a degraded upstream service.
 *
 * Classes using this trait must implement two methods that supply the
 * failure timestamp and cached error from their own storage layer.
 *
 * @since 1.0.0
 */
trait With_Error_Throttle {

	/**
	 * Return the Unix timestamp of the last API failure, or null if none.
	 *
	 * @since 1.0.0
	 *
	 * @return int|null
	 */
	abstract protected function get_last_failure_at(): ?int;

	/**
	 * Return the cached WP_Error from the last API failure, or null.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Error|null
	 */
	abstract protected function get_last_error(): ?WP_Error;

	/**
	 * How long (in seconds) to suppress outbound API calls after a failure.
	 *
	 * Override in the consuming class to change the window.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	protected function get_error_throttle_ttl(): int {
		return 60;
	}

	/**
	 * Returns the cached WP_Error if a recent API failure is within the
	 * throttle TTL window, or null if the call should proceed.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Error|null
	 */
	protected function get_throttled_error(): ?WP_Error {
		$failure_at = $this->get_last_failure_at();

		if ( $failure_at === null ) {
			return null;
		}

		if ( ( time() - $failure_at ) > $this->get_error_throttle_ttl() ) {
			return null;
		}

		return $this->get_last_error();
	}
}
