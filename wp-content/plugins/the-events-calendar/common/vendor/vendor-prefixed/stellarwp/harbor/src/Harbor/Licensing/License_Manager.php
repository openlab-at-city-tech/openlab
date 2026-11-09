<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Licensing;

use TEC\Common\LiquidWeb\Harbor\Utils\License_Key;
use TEC\Common\LiquidWeb\Harbor\Licensing\Registry\Product_Registry;
use TEC\Common\LiquidWeb\Harbor\Licensing\Repositories\License_Repository;
use TEC\Common\LiquidWeb\Harbor\Licensing\Results\Product_Entry;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Error_Throttle;
use TEC\Common\LiquidWeb\LicensingApiClient\Contracts\LicensingClientInterface;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\NotFoundException;
use WP_Error;

/**
 * Orchestrates unified license key discovery, persistence, and product catalog fetching.
 *
 * All keys must begin with the LWSW- prefix (see License_Key::is_valid_format()).
 * store() returns false and does not write to the repository when the
 * key fails this check.
 *
 * Priority order for get():
 *   1. Stored key (License_Repository) — always wins.
 *   2. Embedded key discovered from a bundled LWSW_KEY.php file — used when
 *      no key is stored; the first active plugin with this file wins and its
 *      key is auto-stored for subsequent requests.
 *
 * Any method that would call the remote API first checks whether a recent
 * failure is within the error throttle TTL window. When throttled, the
 * cached WP_Error is returned immediately without hitting the upstream
 * service. The throttle resets automatically on the next successful call.
 *
 * @since 1.0.0
 */
class License_Manager {

	use With_Debugging;
	use With_Error_Throttle;

	/**
	 * Window (in seconds) during which a repeat submission of the same
	 * invalid key returns the cached error without re-calling the API.
	 *
	 * @since 1.5.0
	 *
	 * @var int
	 */
	private const VALIDATION_PER_KEY_THROTTLE_TTL = MINUTE_IN_SECONDS;

	/**
	 * Sliding window (in seconds) for the site-wide failure counter that
	 * protects against bots cycling through bad keys.
	 *
	 * @since 1.5.0
	 *
	 * @var int
	 */
	private const VALIDATION_FAILURE_WINDOW = MINUTE_IN_SECONDS;

	/**
	 * Number of failed validate_and_store attempts within
	 * VALIDATION_FAILURE_WINDOW that triggers the rate-limit error.
	 *
	 * @since 1.5.0
	 *
	 * @var int
	 */
	private const VALIDATION_FAILURE_THRESHOLD = 5;

	/**
	 * @since 1.0.0
	 *
	 * @var License_Repository
	 */
	private License_Repository $repository;

	/**
	 * @since 1.0.0
	 *
	 * @var Product_Registry
	 */
	private Product_Registry $registry;

	/**
	 * @since 1.0.0
	 *
	 * @var LicensingClientInterface
	 */
	private LicensingClientInterface $client;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param License_Repository       $repository The license repository.
	 * @param Product_Registry         $registry   The product registry.
	 * @param LicensingClientInterface $client     The licensing API client.
	 */
	public function __construct(
		License_Repository $repository,
		Product_Registry $registry,
		LicensingClientInterface $client
	) {
		$this->repository = $repository;
		$this->registry   = $registry;
		$this->client     = $client;
	}

	/**
	 * Get the unified license key.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null The license key, or null if none exists.
	 */
	public function get_key(): ?string {
		return $this->repository->get_key();
	}

	/**
	 * Store the unified license key.
	 *
	 * Returns false without writing if the key does not begin with LWSW-.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     The license key to store.
	 * @param bool   $network Whether to store at the network level (multisite only).
	 *
	 * @return bool Whether the key was successfully stored.
	 */
	public function store_key( string $key, bool $network = false ): bool {
		if ( ! License_Key::is_valid_format( $key ) ) {
			static::debug_log( 'Rejected license key: invalid format.' );

			return false;
		}

		static::debug_log(
			sprintf(
				'Storing license key (network: %s).',
				$network ? 'yes' : 'no'
			)
		);

		return $this->repository->store_key( $key, $network );
	}

	/**
	 * Verify a license key is recognized by the remote API and store it.
	 *
	 * Fetches the product catalog to confirm the key exists, then persists it.
	 * Does not activate any products or consume seats.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     The license key to validate and store.
	 * @param string $domain  The site domain sent to the licensing API.
	 * @param bool   $network Whether to store at the network level (multisite only).
	 *
	 * @return Product_Collection|WP_Error The product collection on success, WP_Error on failure.
	 */
	public function validate_and_store( string $key, string $domain, bool $network = false ) {
		static::debug_log(
			sprintf(
				'Validating and storing license key for domain "%s".',
				$domain
			)
		);

		if ( ! License_Key::is_valid_format( $key ) ) {
			static::debug_log( 'Validate-and-store rejected: invalid key format.' );

			return new WP_Error(
				Error_Code::INVALID_KEY,
				__( 'The license key format is invalid.', 'tribe-common' ),
				[ 'status' => 400 ]
			);
		}

		/**
		 * Per-key throttle. The same bad key inside the TTL window returns
		 * the cached error without re-calling the API. A different key has
		 * no entry and proceeds.
		 */
		$per_key_error = $this->repository->get_per_key_failure( $key, self::VALIDATION_PER_KEY_THROTTLE_TTL );

		if ( $per_key_error !== null ) {
			static::debug_log(
				sprintf(
					'Validate-and-store per-key throttled: %s',
					$per_key_error->get_error_message()
				)
			);

			return $per_key_error;
		}

		/**
		 * Rolling-window counter. Site-wide rate-limit on the count of
		 * distinct failed attempts inside the failure window. Submissions
		 * pass through normally while the count stays below the threshold,
		 * so a user typing a few wrong keys (typos, format mistakes, etc.)
		 * keeps getting through. Once the threshold is reached the next
		 * attempt is rejected, and rejections continue until enough
		 * of the earliest recorded failures age out of the window.
		 */
		if ( $this->repository->get_recent_failure_count( self::VALIDATION_FAILURE_WINDOW ) >= self::VALIDATION_FAILURE_THRESHOLD ) {
			static::debug_log( 'Validate-and-store rate-limited by rolling-window counter.' );

			return new WP_Error(
				Error_Code::TOO_MANY_ATTEMPTS,
				__( 'Too many failed license activation attempts. Please wait a moment and try again.', 'tribe-common' ),
				[ 'status' => Error_Code::http_status( Error_Code::TOO_MANY_ATTEMPTS ) ]
			);
		}

		$result = $this->call_products_api( $key, $domain );

		if ( is_wp_error( $result ) ) {
			static::debug_log(
				sprintf(
					'Validate-and-store API failed: [%s] %s',
					$result->get_error_code(),
					$result->get_error_message()
				)
			);

			$data = $result->get_error_data();

			if ( ! is_array( $data ) || empty( $data['status'] ) ) {
				$result->add_data( [ 'status' => 500 ] );
			}

			$this->repository->record_validation_failure( $key, $result, $this->get_validation_retention_seconds() );
			$this->record_products_error( $key, $result );

			return $result;
		}

		static::debug_log(
			sprintf(
				'License validated, %d product(s) returned.',
				count( $result )
			)
		);

		$collection = Product_Collection::from_array( $result );

		// A previously-bad-then-corrected key should not stay throttled.
		$this->repository->clear_validation_failure_for_key( $key );

		// Store the key before the products. store_key() fires the
		// unified_license_key_changed action, which deletes cached
		// products and catalog data. Storing products after ensures
		// the fresh collection is not immediately wiped.
		if ( ! $this->repository->store_key( $key, $network ) ) {
			static::debug_log( 'Failed to persist license key to repository.' );

			return new WP_Error(
				Error_Code::STORE_FAILED,
				__( 'The license key could not be stored.', 'tribe-common' ),
				[ 'status' => 500 ]
			);
		}

		$this->repository->set_products( $collection );

		static::debug_log( 'License key validated and stored successfully.' );

		return $collection;
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
		static::debug_log(
			sprintf(
				'Deleting license key (network: %s).',
				$network ? 'yes' : 'no'
			)
		);

		return $this->repository->delete_key( $network );
	}

	/**
	 * Whether a unified license key is stored or discoverable via the registry.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function key_exists(): bool {
		return $this->get_key() !== null;
	}

	/**
	 * Get the licensed product catalog for the stored key.
	 *
	 * Returns the cached catalog if available; otherwise fetches from the
	 * licensing API and primes the cache.
	 *
	 * @since 1.0.0
	 *
	 * @param string $domain Site domain.
	 *
	 * @return Product_Collection|WP_Error WP_Error if no key is stored or the API call fails.
	 */
	public function get_products( string $domain ) {
		$key = $this->get_key();

		if ( $key === null ) {
			return new WP_Error(
				Error_Code::INVALID_KEY,
				__( 'No license key is stored.', 'tribe-common' ),
				[ 'status' => 422 ]
			);
		}

		$cached = $this->repository->get_products();

		if ( $cached instanceof Product_Collection ) {
			return $cached;
		}

		$throttled = $this->get_throttled_error();

		if ( $throttled !== null ) {
			static::debug_log(
				sprintf(
					'Products fetch throttled: %s',
					$throttled->get_error_message()
				)
			);

			return $throttled;
		}

		return $this->fetch_and_cache( $key, $domain );
	}

	/**
	 * Re-fetch the product catalog from the API, bypassing the cache.
	 *
	 * A successful response replaces the cached catalog. A definitive license rejection
	 * clears it; transient or ambiguous failures preserve the last known-good catalog.
	 *
	 * @since 1.0.0
	 *
	 * @param string $domain Site domain.
	 *
	 * @return Product_Collection|WP_Error WP_Error if no key is stored or the API call fails.
	 */
	public function refresh_products( string $domain ) {
		$key = $this->get_key();

		if ( $key === null ) {
			return new WP_Error(
				Error_Code::INVALID_KEY,
				__( 'No license key is stored.', 'tribe-common' ),
				[ 'status' => 422 ]
			);
		}

		return $this->fetch_and_cache( $key, $domain );
	}

	/**
	 * Look up the products for a license key without storing anything.
	 *
	 * Validates the key format, calls the remote API, and returns the
	 * product collection. Never persists the key or caches results.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key    The license key to look up.
	 * @param string $domain The site domain.
	 *
	 * @return Product_Collection|WP_Error
	 */
	public function lookup_products( string $key, string $domain ) {
		if ( ! License_Key::is_valid_format( $key ) ) {
			return new WP_Error(
				Error_Code::INVALID_KEY,
				__( 'The license key format is invalid.', 'tribe-common' ),
				[ 'status' => 400 ]
			);
		}

		$result = $this->call_products_api( $key, $domain );

		if ( is_wp_error( $result ) ) {
			$data = $result->get_error_data();

			if ( ! is_array( $data ) || empty( $data['status'] ) ) {
				$result->add_data( [ 'status' => 500 ] );
			}

			return $result;
		}

		return Product_Collection::from_array( $result );
	}

	/**
	 * Unix timestamp of the most recent failed API call, or null if no failure
	 * has occurred since the last successful call.
	 *
	 * @since 1.0.0
	 *
	 * @return int|null
	 */
	public function get_last_failure_at(): ?int {
		return $this->repository->get_products_last_failure_at();
	}

	/**
	 * WP_Error from the most recent failed API call, or null if the last call
	 * was successful (or no call has occurred).
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Error|null
	 */
	public function get_last_error(): ?WP_Error {
		return $this->repository->get_products_last_error();
	}

	/**
	 * Fetch the product catalog from the API and cache the result.
	 *
	 * After a successful fetch, the last active date is updated for every
	 * product that reports a valid license. This anchors the grace period
	 * to the most recent confirmed-good state.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key    License key.
	 * @param string $domain Site domain.
	 *
	 * @return Product_Collection|WP_Error
	 */
	private function fetch_and_cache( string $key, string $domain ) {
		/** @var Product_Entry[]|WP_Error $result */
		$result = $this->call_products_api( $key, $domain );

		if ( is_wp_error( $result ) ) {
			static::debug_log(
				sprintf(
					'Products fetch failed: [%s] %s',
					$result->get_error_code(),
					$result->get_error_message()
				)
			);

			$this->record_products_error( $key, $result );

			return $result;
		}

		$collection = Product_Collection::from_array( $result );

		$this->repository->set_products( $collection );

		$current_time = time();

		foreach ( $collection as $product ) {
			/** @var Product_Entry $product */
			if ( $product->is_valid() ) {
				$this->repository->set_last_active_date( $product->get_product_slug(), $current_time );
			}
		}

		return $collection;
	}

	/**
	 * Call the licensing API and return the product catalog as Product_Entry objects.
	 *
	 * Converts package exceptions to WP_Error so callers can use is_wp_error().
	 *
	 * @since 1.0.0
	 *
	 * @param string $key    License key.
	 * @param string $domain Site domain.
	 *
	 * @return Product_Entry[]|WP_Error
	 */
	private function call_products_api( string $key, string $domain ) {
		try {
			$catalog = $this->client->products()->catalog( $key, $domain );
			return array_map( [ Product_Entry::class, 'from_catalog_entry' ], $catalog->products->all() );
		} catch ( NotFoundException $e ) {
			return new WP_Error(
				Error_Code::INVALID_KEY,
				sprintf(
					/* translators: 1: the license key, 2: the error message from the licensing server */
					__( 'License key "%1$s": %2$s', 'tribe-common' ),
					$key,
					$e->getMessage()
				),
				[
					'status'           => Error_Code::http_status( Error_Code::INVALID_KEY ),
					'license_rejected' => $this->is_license_rejection( $e ),
				]
			);
		} catch ( ApiErrorExceptionInterface $e ) {
			return new WP_Error(
				$e->errorCode(),
				$e->getMessage(),
				array_merge(
					$e->errorPayLoad() ?? [],
					[
						'status'           => $e->statusCode(),
						'license_rejected' => $this->is_license_rejection( $e ),
					]
				)
			);
		} catch ( \Throwable $e ) {
			static::debug_log( sprintf( 'Products API exception: %s', $e->getMessage() ) );
			return new WP_Error( Error_Code::INVALID_RESPONSE, __( 'An unexpected error occurred.', 'tribe-common' ), [ 'status' => 500 ] );
		}
	}

	/**
	 * Only structured Licensing responses can revoke the cached purchases.
	 * The client's fallback code for an unstructured HTTP 404 is also not_found,
	 * so the exception code alone is not sufficient evidence of rejection.
	 *
	 * @param ApiErrorExceptionInterface $error The decoded API error.
	 *
	 * @return bool
	 */
	private function is_license_rejection( ApiErrorExceptionInterface $error ): bool {
		$payload = $error->errorPayLoad();
		$details = $payload['error'] ?? null;

		if ( ! is_array( $details ) ) {
			return false;
		}

		$code = $details['code'] ?? null;

		if ( $error->statusCode() === 404 ) {
			return in_array( $code, [ 'not_found', 'products_not_found' ], true );
		}

		return $error->statusCode() === 403 && $code === 'license_inactive';
	}

	/**
	 * Record a fetch failure, invalidating purchases only for the stored key.
	 * A rejected replacement key must not discard the existing key's purchases.
	 *
	 * @param string   $key   The key used for this request.
	 * @param WP_Error $error The API failure.
	 *
	 * @return void
	 */
	private function record_products_error( string $key, WP_Error $error ): void {
		$data = $error->get_error_data();

		if ( $key === $this->get_key() && is_array( $data ) && ! empty( $data['license_rejected'] ) ) {
			$this->repository->invalidate_products();
		}

		$this->repository->set_products( $error );
	}

	/**
	 * How long the validation throttle state should retain entries.
	 *
	 * Each layer of the throttle reads a different slice of history:
	 * get_per_key_failure() looks back VALIDATION_PER_KEY_THROTTLE_TTL
	 * seconds, and get_recent_failure_count() looks back
	 * VALIDATION_FAILURE_WINDOW seconds. Retention is the larger of the
	 * two so neither layer has its data pruned before it can read it.
	 *
	 * As of 1.5.0 both constants hold the same value.
	 * This is intentional: this method lets either window be tuned
	 * independently in the future without silently dropping data
	 * the other layer depends on.
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	private function get_validation_retention_seconds(): int {
		return max( self::VALIDATION_PER_KEY_THROTTLE_TTL, self::VALIDATION_FAILURE_WINDOW );
	}

	/**
	 * Scan active plugins for a bundled LWSW_KEY.php and store the first valid
	 * key found. Does nothing if a key is already stored.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether a key was discovered and stored.
	 */
	public function store_embedded_key_if_present(): bool {
		if ( $this->repository->get_key() !== null ) {
			return false;
		}

		$key = $this->registry->first_with_embedded_key();

		if ( $key === null ) {
			return false;
		}

		return $this->repository->store_key( $key );
	}
}
