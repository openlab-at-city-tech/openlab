<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Licensing;

use WP_Error;

/**
 * Internal value object for the validate_and_store throttle.
 *
 * Wraps the per-license-key failure cache and the rolling-window list of
 * failure timestamps used by the layered throttle. License_Repository owns
 * the persistence; this class owns the state shape and the throttle
 * semantics (freshness checks, window counting, pruning).
 *
 * @since 1.5.0
 */
final class Validation_State {

	/**
	 * Map of license-key hashes to the most recent failure for that key.
	 *
	 * @since 1.5.0
	 *
	 * @var array<string, array{failed_at: int, error: WP_Error}>
	 */
	private array $per_key = [];

	/**
	 * Sliding list of failure timestamps used to throttle abusive traffic.
	 *
	 * @since 1.5.0
	 *
	 * @var int[]
	 */
	private array $failure_timestamps = [];

	/**
	 * The cached WP_Error from the most recent failed attempt for the given
	 * key hash, or null if there is no in-window entry for it.
	 *
	 * @since 1.5.0
	 *
	 * @param string $key_hash The hashed license key.
	 * @param int    $ttl      How long (seconds) a cached failure remains valid.
	 * @param int    $now      Current Unix timestamp.
	 *
	 * @return WP_Error|null
	 */
	public function get_failure_for( string $key_hash, int $ttl, int $now ): ?WP_Error {
		$entry = $this->per_key[ $key_hash ] ?? null;

		if ( ! is_array( $entry ) ) {
			return null;
		}

		if ( ( $now - $entry['failed_at'] ) > $ttl ) {
			return null;
		}

		return $entry['error'];
	}

	/**
	 * Record a failure for the given key hash and append it to the
	 * rolling-window failure list.
	 *
	 * @since 1.5.0
	 *
	 * @param string   $key_hash The hashed license key.
	 * @param WP_Error $error    The error returned by the upstream API.
	 * @param int      $now      Current Unix timestamp.
	 *
	 * @return void
	 */
	public function record_failure( string $key_hash, WP_Error $error, int $now ): void {
		$this->per_key[ $key_hash ] = [
			'failed_at' => $now,
			'error'     => $error,
		];

		$this->failure_timestamps[] = $now;
	}

	/**
	 * Remove the per-key entry for the given key hash, if any.
	 *
	 * Called on the success path so an otherwise-valid key that was previously
	 * cached as a failure (for example because the licensing server was
	 * temporarily unhealthy or returned an error for it) does not stay
	 * throttled once it next validates successfully.
	 *
	 * The rolling-window failure list is intentionally not touched: a
	 * legitimate success should not erase evidence of abusive traffic.
	 *
	 * @since 1.5.0
	 *
	 * @param string $key_hash The hashed license key.
	 *
	 * @return void
	 */
	public function clear_failure_for( string $key_hash ): void {
		unset( $this->per_key[ $key_hash ] );
	}

	/**
	 * Number of recorded failures whose timestamp falls within `$window`
	 * seconds of `$now`.
	 *
	 * @since 1.5.0
	 *
	 * @param int $window Length of the rolling window in seconds.
	 * @param int $now    Current Unix timestamp.
	 *
	 * @return int
	 */
	public function count_recent_failures( int $window, int $now ): int {
		$cutoff = $now - $window;
		$count  = 0;

		foreach ( $this->failure_timestamps as $timestamp ) {
			if ( $timestamp >= $cutoff ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Drop per-key entries and rolling-window timestamps older than
	 * `$retention` seconds relative to `$now`.
	 *
	 * @since 1.5.0
	 *
	 * @param int $retention How long (seconds) to keep entries.
	 * @param int $now       Current Unix timestamp.
	 *
	 * @return void
	 */
	public function prune( int $retention, int $now ): void {
		$cutoff = $now - $retention;

		foreach ( $this->per_key as $hash => $entry ) {
			if ( $entry['failed_at'] < $cutoff ) {
				unset( $this->per_key[ $hash ] );
			}
		}

		$this->failure_timestamps = array_values(
			array_filter(
				$this->failure_timestamps,
				static function ( int $timestamp ) use ( $cutoff ): bool {
					return $timestamp >= $cutoff;
				}
			)
		);
	}

	/**
	 * Serialize the state to a plain array for option storage.
	 *
	 * @since 1.5.0
	 *
	 * @return array{per_key: array<string, array{failed_at: int, error: WP_Error}>, failure_timestamps: int[]}
	 */
	public function to_array(): array {
		return [
			'per_key'            => $this->per_key,
			'failure_timestamps' => $this->failure_timestamps,
		];
	}

	/**
	 * Hydrate from a stored option array.
	 *
	 * Defensively skips malformed entries so a corrupted option cannot
	 * break the throttle.
	 *
	 * @since 1.5.0
	 *
	 * @param array<string, mixed> $raw Raw option value.
	 *
	 * @return self
	 */
	public static function from_array( array $raw ): self {
		$instance = new self();

		if ( isset( $raw['per_key'] ) && is_array( $raw['per_key'] ) ) {
			foreach ( $raw['per_key'] as $hash => $entry ) {
				if ( ! is_string( $hash ) || ! is_array( $entry ) ) {
					continue;
				}

				$failed_at = $entry['failed_at'] ?? null;
				$error     = $entry['error'] ?? null;

				if ( ! is_int( $failed_at ) || ! ( $error instanceof WP_Error ) ) {
					continue;
				}

				$instance->per_key[ $hash ] = [
					'failed_at' => $failed_at,
					'error'     => $error,
				];
			}
		}

		if ( isset( $raw['failure_timestamps'] ) && is_array( $raw['failure_timestamps'] ) ) {
			foreach ( $raw['failure_timestamps'] as $timestamp ) {
				if ( is_int( $timestamp ) ) {
					$instance->failure_timestamps[] = $timestamp;
				}
			}
		}

		return $instance;
	}
}
