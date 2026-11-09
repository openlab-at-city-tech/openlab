<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Licensing\Repositories;

use TEC\Common\LiquidWeb\Harbor\Licensing\Product_Collection;
use TEC\Common\LiquidWeb\Harbor\Licensing\Validation_State;
use TEC\Common\LiquidWeb\Harbor\Utils\Sanitize;
use WP_Error;

/**
 * Handles all persistence for the unified licensing subsystem.
 *
 * Covers two storage layers:
 *   - WordPress options: the unified license key (single key per site).
 *   - WordPress options: the license state (last successful products, fetch
 *     timestamp, last error). Stored without a TTL so product data survives
 *     indefinitely even when the licensing server is unreachable.
 *
 * This class is a pure data-access layer — it only reads from and writes to
 * WordPress storage. It never calls external APIs or applies business logic.
 * Use License_Manager for orchestrated fetching and key discovery.
 *
 * On multisite, get_key() checks the network option first and falls back
 * to the site option. Callers control the storage level explicitly
 * via the $network parameter on store_key() and delete_key().
 *
 * @since 1.0.0
 */
final class License_Repository {

	/**
	 * The option name used to store the unified license key.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const KEY_OPTION_NAME = 'lw_harbor_unified_license_key';

	/**
	 * Option name for the license state envelope.
	 *
	 * Stores an associative array with four keys:
	 *   - collection      (array|null)     Product_Collection::to_array() from the last
	 *                                     successful API fetch, or null if never fetched.
	 *   - last_success_at (int|null)      Unix timestamp of the last successful fetch.
	 *   - last_failure_at (int|null)      Unix timestamp of the most recent failed fetch,
	 *                                     or null if no failure has occurred.
	 *   - last_error      (WP_Error|null) Error from the most recent failed attempt, or
	 *                                     null when the last fetch succeeded.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const PRODUCTS_STATE_OPTION_NAME = 'lw_harbor_licensing_products_state';

	/**
	 * State envelope key for the serialized product collection array.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const STATE_KEY_COLLECTION = 'collection';

	/**
	 * State envelope key for the last successful fetch timestamp.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const STATE_KEY_LAST_SUCCESS_AT = 'last_success_at';

	/**
	 * State envelope key for the last failed fetch timestamp.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const STATE_KEY_LAST_FAILURE_AT = 'last_failure_at';

	/**
	 * State envelope key for the last fetch error.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const STATE_KEY_LAST_ERROR = 'last_error';

	/**
	 * Option name for the map of per-product last-active timestamps.
	 *
	 * Stored as an associative array keyed by product slug.
	 *
	 *  TODO: Decide where to store this data. See discussion in https://github.com/lw-harbor/pull/162/changes#r2906722318
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const PRODUCTS_LAST_ACTIVE_DATES_OPTION_NAME = 'lw_harbor_licensing_products_last_active_dates';

	/**
	 * Option name for the validate_and_store rate-limit state.
	 *
	 * Backs a Validation_State envelope holding the per-key cached failures
	 * and the rolling-window failure timestamps used by the layered
	 * validation throttle. Distinct from PRODUCTS_STATE_OPTION_NAME so that
	 * user-submitted failures do not arm the background-fetch throttle that
	 * get_products() relies on.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public const VALIDATION_STATE_OPTION_NAME = 'lw_harbor_licensing_validation_state';

	/**
	 * Get the stored unified license key.
	 *
	 * On multisite, the network-level key takes precedence over a
	 * site-level key. Returns null if no key exists at either level.
	 *
	 * @since 1.0.0
	 *
	 * @return ?string The license key, or null if not set.
	 */
	public function get_key(): ?string {
		if ( is_multisite() ) {
			/** @var string $key */
			$key = get_network_option( null, self::KEY_OPTION_NAME, '' );

			if ( ! empty( $key ) ) {
				return $key;
			}
		}

		/** @var string $key */
		$key = get_option( self::KEY_OPTION_NAME, '' );

		return empty( $key ) ? null : $key;
	}

	/**
	 * Store the unified license key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     The license key to store.
	 * @param bool   $network Whether to store at the network level (multisite only).
	 *
	 * @return bool Whether the key was successfully stored.
	 */
	public function store_key( string $key, bool $network = false ): bool {
		$key = Sanitize::key( $key );

		if ( $network && is_multisite() ) {
			/** @var string $current */
			$current = get_network_option( null, self::KEY_OPTION_NAME, '' );

			// update_network_option() returns false when the value hasn't changed.
			if ( $current === $key ) {
				return true;
			}

			$result = (bool) update_network_option( null, self::KEY_OPTION_NAME, $key );

			if ( $result ) {
				/**
				 * Fires when the unified license key is changed.
				 *
				 * @since 1.0.0
				 *
				 * @param string $new_key The new license key.
				 * @param string $old_key The previous license key.
				 */
				do_action( 'lw-harbor/unified_license_key_changed', $key, $current );
			}

			return $result;
		}

		/** @var string $current */
		$current = get_option( self::KEY_OPTION_NAME, '' );

		// update_option() returns false when the value hasn't changed.
		if ( $current === $key ) {
			return true;
		}

		$result = (bool) update_option( self::KEY_OPTION_NAME, $key, false );

		if ( $result ) {
			/**
			 * Fires when the unified license key is changed.
			 *
			 * @since 1.0.0
			 *
			 * @param string $new_key The new license key.
			 * @param string $old_key The previous license key.
			 */
			do_action( 'lw-harbor/unified_license_key_changed', $key, $current );
		}

		return $result;
	}

	/**
	 * Delete the stored unified license key.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $network Whether to delete from the network level (multisite only).
	 *
	 * @return bool Whether the key was successfully deleted.
	 */
	public function delete_key( bool $network = false ): bool {
		$old_key = $this->get_key() ?? '';

		if ( $network && is_multisite() ) {
			$result = delete_network_option( null, self::KEY_OPTION_NAME );
		} else {
			$result = delete_option( self::KEY_OPTION_NAME );
		}

		if ( $result && $old_key !== '' ) {
			/**
			 * Fires when the unified license key is changed.
			 *
			 * @since 1.0.0
			 *
			 * @param string $new_key The new license key.
			 * @param string $old_key The previous license key.
			 */
			do_action( 'lw-harbor/unified_license_key_changed', '', $old_key );
		}

		return $result;
	}

	/**
	 * Check whether a unified license key is stored.
	 *
	 * Follows the same precedence as get_key(): network-level on multisite,
	 * then site-level.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether a license key exists.
	 */
	public function key_exists(): bool {
		return $this->get_key() !== null;
	}

	/**
	 * Read the stored license state and return the products portion.
	 *
	 * When a successful product catalog has been stored, it is returned even if
	 * a subsequent fetch failed. When no catalog exists but a previous error was
	 * recorded, that WP_Error is returned so callers can surface it. Returns
	 * null when nothing has been stored yet.
	 *
	 * Use License_Manager::get_products() for a call that will fetch from the
	 * API on a miss.
	 *
	 * @since 1.0.0
	 *
	 * @return Product_Collection|WP_Error|null
	 */
	public function get_products() {
		$state = $this->read_products_state();

		if ( is_array( $state[ self::STATE_KEY_COLLECTION ] ) ) {
			return Product_Collection::from_array( $state[ self::STATE_KEY_COLLECTION ] );
		}

		if ( $state[ self::STATE_KEY_LAST_ERROR ] instanceof WP_Error ) {
			return $state[ self::STATE_KEY_LAST_ERROR ];
		}

		return null;
	}

	/**
	 * Persist the product catalog or a fetch error to the license state option.
	 *
	 * On success (Product_Collection): updates collection and last_success_at,
	 * clears last_error.
	 *
	 * On failure (WP_Error): stores last_error only. The existing collection and
	 * last_success_at are preserved so callers can still use the last known-good
	 * catalog.
	 *
	 * @since 1.0.0
	 *
	 * @param Product_Collection|WP_Error $data The product catalog or fetch error to store.
	 *
	 * @return void
	 */
	public function set_products( $data ): void {
		if ( $data instanceof Product_Collection ) {
			$state                                    = $this->read_products_state();
			$state[ self::STATE_KEY_COLLECTION ]      = $data->to_array();
			$state[ self::STATE_KEY_LAST_SUCCESS_AT ] = time();
			$state[ self::STATE_KEY_LAST_ERROR ]      = null;
			$state[ self::STATE_KEY_LAST_FAILURE_AT ] = null;
			update_option( self::PRODUCTS_STATE_OPTION_NAME, $state, false );

			return;
		}

		if ( is_wp_error( $data ) ) {
			$state                                    = $this->read_products_state();
			$state[ self::STATE_KEY_LAST_ERROR ]      = $data;
			$state[ self::STATE_KEY_LAST_FAILURE_AT ] = time();
			update_option( self::PRODUCTS_STATE_OPTION_NAME, $state, false );
		}
	}

	/**
	 * Discard rejected purchases while preserving the fetch history.
	 *
	 * @return void
	 */
	public function invalidate_products(): void {
		$state                               = $this->read_products_state();
		$state[ self::STATE_KEY_COLLECTION ] = null;
		update_option( self::PRODUCTS_STATE_OPTION_NAME, $state, false );
	}

	/**
	 * Delete the entire license state option.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function delete_products(): void {
		delete_option( self::PRODUCTS_STATE_OPTION_NAME );
	}

	/**
	 * Unix timestamp of the last successful products fetch, or null if never fetched.
	 *
	 * @since 1.0.0
	 *
	 * @return int|null
	 */
	public function get_products_last_success_at(): ?int {
		$value = $this->read_products_state()[ self::STATE_KEY_LAST_SUCCESS_AT ];

		return is_int( $value ) ? $value : null;
	}

	/**
	 * Unix timestamp of the most recent failed products fetch, or null if no
	 * failure has occurred.
	 *
	 * @since 1.0.0
	 *
	 * @return int|null
	 */
	public function get_products_last_failure_at(): ?int {
		$value = $this->read_products_state()[ self::STATE_KEY_LAST_FAILURE_AT ];

		return is_int( $value ) ? $value : null;
	}

	/**
	 * WP_Error from the most recent failed fetch attempt, or null if the last
	 * fetch was successful (or no fetch has occurred).
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Error|null
	 */
	public function get_products_last_error(): ?WP_Error {
		$error = $this->read_products_state()[ self::STATE_KEY_LAST_ERROR ];

		return $error instanceof WP_Error ? $error : null;
	}

	/**
	 * Read the raw license state array from the option, returning a zeroed
	 * default when nothing has been stored.
	 *
	 * @since 1.0.0
	 *
	 * @return array{collection: array<array<string,mixed>>|null, last_success_at: int|null, last_failure_at: int|null, last_error: WP_Error|null}
	 */
	private function read_products_state(): array {
		$raw = get_option( self::PRODUCTS_STATE_OPTION_NAME, null );

		if ( ! is_array( $raw ) ) {
			return [
				self::STATE_KEY_COLLECTION      => null,
				self::STATE_KEY_LAST_SUCCESS_AT => null,
				self::STATE_KEY_LAST_FAILURE_AT => null,
				self::STATE_KEY_LAST_ERROR      => null,
			];
		}

		$collection = null;
		if ( isset( $raw[ self::STATE_KEY_COLLECTION ] ) && is_array( $raw[ self::STATE_KEY_COLLECTION ] ) ) {
			/** @var array<array<string, mixed>> $collection */
			$collection = $raw[ self::STATE_KEY_COLLECTION ];
		}

		$last_success_at = isset( $raw[ self::STATE_KEY_LAST_SUCCESS_AT ] ) && is_int( $raw[ self::STATE_KEY_LAST_SUCCESS_AT ] ) ? $raw[ self::STATE_KEY_LAST_SUCCESS_AT ] : null;
		$last_failure_at = isset( $raw[ self::STATE_KEY_LAST_FAILURE_AT ] ) && is_int( $raw[ self::STATE_KEY_LAST_FAILURE_AT ] ) ? $raw[ self::STATE_KEY_LAST_FAILURE_AT ] : null;
		$last_error      = isset( $raw[ self::STATE_KEY_LAST_ERROR ] ) && $raw[ self::STATE_KEY_LAST_ERROR ] instanceof WP_Error ? $raw[ self::STATE_KEY_LAST_ERROR ] : null;

		return [
			self::STATE_KEY_COLLECTION      => $collection,
			self::STATE_KEY_LAST_SUCCESS_AT => $last_success_at,
			self::STATE_KEY_LAST_FAILURE_AT => $last_failure_at,
			self::STATE_KEY_LAST_ERROR      => $last_error,
		];
	}

	/**
	 * Whether any entry for a product slug exists in the cached catalog.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The product slug to check.
	 *
	 * @return bool
	 */
	public function has_product( string $slug ): bool {
		$products = $this->get_products();

		if ( ! $products instanceof Product_Collection ) {
			return false;
		}

		return count( $products->get_all_by_slug( $slug ) ) > 0;
	}

	/**
	 * Whether any entry for a product slug has a valid license status.
	 *
	 * Returns true when at least one tier entry for the slug is valid, meaning
	 * the product is activated on the current domain and the entitlement is active.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The product slug to check.
	 *
	 * @return bool
	 */
	public function is_product_valid( string $slug ): bool {
		$products = $this->get_products();

		if ( ! $products instanceof Product_Collection ) {
			return false;
		}

		foreach ( $products->get_all_by_slug( $slug ) as $entry ) {
			if ( $entry->is_valid() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether a product has a valid, active license.
	 *
	 * Returns true when the cached catalog shows the product as valid, or when
	 * the product is within the grace period after its last confirmed active date.
	 * This prevents platform fees from being charged immediately after a license
	 * expires or when a network issue prevents reaching the licensing server.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The product slug to retrieve.
	 *
	 * @return bool
	 */
	public function is_product_active( string $slug ): bool {
		if ( $this->is_product_valid( $slug ) ) {
			return true;
		}

		return $this->is_in_grace_period( $slug );
	}

	/**
	 * Get the timestamp of the last time a product was confirmed active.
	 *
	 * Returns null when no active date has been recorded for the slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The product slug to retrieve.
	 *
	 * @return int|null Unix timestamp (UTC), or null if never recorded.
	 */
	public function get_last_active_date( string $slug ): ?int {
		$raw_dates = get_option( self::PRODUCTS_LAST_ACTIVE_DATES_OPTION_NAME, [] );

		/** @var array<string, int> $dates */
		$dates = is_array( $raw_dates ) ? $raw_dates : [];

		return $dates[ $slug ] ?? null;
	}

	/**
	 * Record the current time as the last active date for a product.
	 *
	 * Called whenever a fetch confirms the product has a valid license so that
	 * the grace period is anchored to the most recent known-good state.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug      The product slug to record.
	 * @param int    $timestamp Unix timestamp (UTC) to record.
	 *
	 * @return void
	 */
	public function set_last_active_date( string $slug, int $timestamp ): void {
		$raw_dates = get_option( self::PRODUCTS_LAST_ACTIVE_DATES_OPTION_NAME, [] );

		/** @var array<string, int> $dates */
		$dates = is_array( $raw_dates ) ? $raw_dates : [];

		$dates[ $slug ] = $timestamp;
		update_option( self::PRODUCTS_LAST_ACTIVE_DATES_OPTION_NAME, $dates, false );
	}

	/**
	 * The grace period in seconds after the last active date during which
	 * a product is still considered active.
	 *
	 * 30 days gives customers and the licensing server reasonable time to
	 * recover from network issues or an unintentional lapse in renewal.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	private function get_grace_period_in_seconds(): int {
		return 30 * DAY_IN_SECONDS;
	}

	/**
	 * Whether a product is within the grace period after its last active date.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The product slug to check.
	 *
	 * @return bool
	 */
	private function is_in_grace_period( string $slug ): bool {
		$last_active = $this->get_last_active_date( $slug );

		if ( $last_active === null ) {
			return false;
		}

		$current_time = time();

		return $current_time <= $last_active + $this->get_grace_period_in_seconds();
	}

	/**
	 * Get the cached WP_Error from the most recent failed validation attempt
	 * for the given license key if it is still within the supplied TTL.
	 *
	 * Used by the per-key throttle in License_Manager::validate_and_store() so
	 * that a different key submitted right after a failed attempt is never
	 * blocked by the previous key's error.
	 *
	 * @since 1.5.0
	 *
	 * @param string $key The license key being submitted.
	 * @param int    $ttl How long (seconds) a cached failure remains valid.
	 *
	 * @return WP_Error|null The cached WP_Error, or null if there is no
	 *                       in-window entry for this key.
	 */
	public function get_per_key_failure( string $key, int $ttl ): ?WP_Error {
		return $this->read_validation_state()->get_failure_for(
			$this->hash_key( $key ),
			$ttl,
			time()
		);
	}

	/**
	 * Record a failed validation attempt for the given license key.
	 *
	 * Persists both the per-key entry (read by get_per_key_failure) and a
	 * timestamp in the rolling window (read by get_recent_failure_count),
	 * pruning anything outside `$retention_seconds` so the option stays bounded.
	 *
	 * @since 1.5.0
	 *
	 * @param string   $key               The license key whose validation failed.
	 * @param WP_Error $error             The error returned by the upstream API.
	 * @param int      $retention_seconds How long (seconds) the state should
	 *                                    retain entries before they are pruned.
	 *
	 * @return void
	 */
	public function record_validation_failure( string $key, WP_Error $error, int $retention_seconds ): void {
		$state = $this->read_validation_state();
		$now   = time();

		$state->record_failure( $this->hash_key( $key ), $error, $now );
		$state->prune( $retention_seconds, $now );

		$this->write_validation_state( $state );
	}

	/**
	 * Clear the per-key throttle entry for a single license key.
	 *
	 * Called after a successful validation so the same key can be re-submitted
	 * without hitting the cached error. The rolling-window failure counter is
	 * intentionally not cleared because legitimate successes should not erase
	 * evidence of abusive traffic.
	 *
	 * @since 1.5.0
	 *
	 * @param string $key The license key to clear.
	 *
	 * @return void
	 */
	public function clear_validation_failure_for_key( string $key ): void {
		$state = $this->read_validation_state();
		$state->clear_failure_for( $this->hash_key( $key ) );

		$this->write_validation_state( $state );
	}

	/**
	 * Number of failed validate_and_store attempts within the given window.
	 *
	 * @since 1.5.0
	 *
	 * @param int $window Length of the rolling window in seconds.
	 *
	 * @return int
	 */
	public function get_recent_failure_count( int $window ): int {
		return $this->read_validation_state()->count_recent_failures( $window, time() );
	}

	/**
	 * Delete the entire validation throttle state.
	 *
	 * Wired up to lw-harbor/unified_license_key_changed so that rotating the
	 * stored license key wipes per-key entries that are no longer relevant.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function delete_validation_state(): void {
		delete_option( self::VALIDATION_STATE_OPTION_NAME );
	}

	/**
	 * Hydrate a Validation_State from the backing option, or a fresh one on miss.
	 *
	 * @since 1.5.0
	 *
	 * @return Validation_State
	 */
	private function read_validation_state(): Validation_State {
		$raw = get_option( self::VALIDATION_STATE_OPTION_NAME, null );

		if ( ! is_array( $raw ) ) {
			return new Validation_State();
		}

		$string_keyed = [];
		foreach ( $raw as $key => $value ) {
			if ( is_string( $key ) ) {
				$string_keyed[ $key ] = $value;
			}
		}

		return Validation_State::from_array( $string_keyed );
	}

	/**
	 * Persist a Validation_State to the backing option.
	 *
	 * @since 1.5.0
	 *
	 * @param Validation_State $state The state to persist.
	 *
	 * @return void
	 */
	private function write_validation_state( Validation_State $state ): void {
		update_option( self::VALIDATION_STATE_OPTION_NAME, $state->to_array(), false );
	}

	/**
	 * Hash a license key for use as a map key in the per-key throttle.
	 *
	 * @since 1.5.0
	 *
	 * @param string $key The license key.
	 *
	 * @return string
	 */
	private function hash_key( string $key ): string {
		return hash( 'sha256', $key );
	}
}
