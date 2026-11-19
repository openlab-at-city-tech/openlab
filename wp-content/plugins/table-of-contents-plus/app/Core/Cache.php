<?php
namespace AIOSEO\TableOfContents\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles our cache.
 *
 * @since 1.0.0
 */
class Cache {
	/**
	 * The name of our cache table.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $table = 'aioseo_table_of_contents_cache';

	/**
	 * Our cache.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $cache = [];

	/**
	 * Prefix for this cache.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $prefix = 'aioseo_table_of_contents_';

	/**
	 * Whether to use transients as fallback.
	 *
	 * @since 1.0.0
	 *
	 * @var bool|null
	 */
	private $useTransientFallback = null;

	/**
	 * Class constructor.
	 *
	 * @since 4.7.8
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'checkIfTableExists' ] ); // This needs to run on init because the DB
		// class gets instantiated along with the cache class.
	}

	/**
	 * Checks if the cache table exists and creates it if it doesn't.
	 *
	 * @since 4.7.8
	 *
	 * @return void
	 */
	public function checkIfTableExists() {
		if ( ! aioseoTableOfContents()->core->db->tableExists( $this->table ) ) {
			aioseoTableOfContents()->preUpdates->createCacheTable();
		}

		// Table is guaranteed to exist now. Skip future re-checks this request
		// and refresh the transient so isCacheTableAvailable() won't query next request either.
		$this->useTransientFallback = false;
		set_transient( 'aioseo_plugin_cache_table_exists', 1, WEEK_IN_SECONDS );
	}

	/**
	 * Returns the cache value if it exists and isn't expired.
	 *
	 * @since   1.0.0
	 * @version 202607 Renamed key to name
	 *
	 * @param  string $key The key of the cache. Use a '%' for a LIKE query.
	 * @return mixed       The value or null if the cache does not exist.
	 */
	public function get( $key ) {
		$key = $this->prepareKey( $key );

		// Check if we're supposed to do a LIKE get.
		$isLikeGet = preg_match( '/%/', (string) $key );

		// Check static cache first (only for non-LIKE queries)
		if ( ! $isLikeGet && isset( self::$cache[ $key ] ) ) {
			return self::$cache[ $key ];
		}

		// Check if we should use transients
		if ( ! $this->isCacheTableAvailable() ) {
			if ( $isLikeGet ) {
				// Use custom query for LIKE patterns
				return $this->getTransientLike( $key );
			}

			$value = $this->getTransient( $key );
			if ( null !== $value ) {
				self::$cache[ $key ] = $value;
			}

			return $value;
		}

		$result = aioseoTableOfContents()->core->db
			->start( $this->table )
			->select( '`name`, `value`, `is_object`' )
			->whereRaw( '( `expiration` IS NULL OR `expiration` > \'' . aioseoTableOfContents()->helpers->timeToMysql( time() ) . '\' )' );

		if ( $isLikeGet ) {
			$key = esc_sql( aioseoTableOfContents()->core->db->db->esc_like( $key ) );
			$result->whereRaw( '`name` LIKE \'' . $key . '\'' );
		} else {
			$key = esc_sql( $key );
			$result->where( 'name', $key );
		}

		$result->output( ARRAY_A )->run();

		// If we have nothing in the cache, let's return null.
		$values = $result->nullSet() ? null : $result->result();

		// If we have something, let's normalize it.
		if ( $values ) {
			foreach ( $values as &$value ) {
				// Use is_object flag to determine decode type: if 0 (false) decode to array, if 1 (true) decode to object.
				$value['value'] = json_decode( $value['value'], empty( $value['is_object'] ) );
			}
			// Return only the single cache value.
			if ( ! $isLikeGet ) {
				$values = $values[0]['value'];
			}
		}

		// Return values without a static cache.
		// This is here because clearing the LIKE cache is not simple.
		if ( $isLikeGet ) {
			return $values;
		}

		self::$cache[ $key ] = $values;

		return self::$cache[ $key ];
	}

	/**
	 * Updates the given cache or creates it if it doesn't exist.
	 *
	 * @since   1.0.0
	 * @version 202607 Renamed key to name
	 *
	 * @param  string $key        The name of the cache.
	 * @param  mixed  $value      The value.
	 * @param  int    $expiration The expiration time in seconds. Defaults to 24 hours. 0 to no expiration.
	 * @return void
	 */
	public function update( $key, $value, $expiration = DAY_IN_SECONDS ) {
		$key = $this->prepareKey( $key );

		// If the value is null we'll convert it and give it a shorter expiration.
		if ( null === $value ) {
			$value      = false;
			$expiration = 10 * MINUTE_IN_SECONDS;
		}

		// Check if we should use transients
		if ( ! $this->isCacheTableAvailable() ) {
			$this->updateTransient( $key, $value, $expiration );
			$this->updateStatic( $key, $value );

			return;
		}

		$isObject   = is_object( $value );
		$jsonValue  = wp_json_encode( $value );
		$expiration = 0 < $expiration ? aioseoTableOfContents()->helpers->timeToMysql( time() + $expiration ) : null;

		// Handle JSON encoding errors.
		if ( false === $jsonValue && JSON_ERROR_NONE !== json_last_error() ) {
			if ( aioseoTableOfContents()->helpers->isDev() ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'AIOSEO Plugin Cache: JSON encode failed for name "' . $key . '" - ' . json_last_error_msg() );
			}

			return;
		}

		aioseoTableOfContents()->core->db->insert( $this->table )
			->set( [
				'name'       => $this->prepareKey( $key ),
				'value'      => $jsonValue,
				'is_object'  => $isObject,
				'expiration' => $expiration,
				'created'    => aioseoTableOfContents()->helpers->timeToMysql( time() ),
				'updated'    => aioseoTableOfContents()->helpers->timeToMysql( time() )
			] )->onDuplicate( [
				'value'      => $jsonValue,
				'is_object'  => $isObject,
				'expiration' => $expiration,
				'updated'    => aioseoTableOfContents()->helpers->timeToMysql( time() )
			] )
			->run();

		$this->updateStatic( $key, $value );
	}

	/**
	 * Deletes the cache record with the given key.
	 *
	 * @since   1.0.0
	 *
	 * @param  string $key The name of the cache.
	 * @return void
	 */
	public function delete( $key ) {
		$key = $this->prepareKey( $key );

		// Check if we should use transients
		if ( ! $this->isCacheTableAvailable() ) {
			$this->deleteTransient( $key );
			$this->clearStatic( $key );

			return;
		}

		aioseoTableOfContents()->core->db->delete( $this->table )
			->where( 'name', $key )
			->run();

		$this->clearStatic( $key );
	}

	/**
	 * Prepares the key before using the cache.
	 *
	 * @since   1.0.0
	 *
	 * @param  string $key The name of the cache to prepare.
	 * @return string      The prepared key.
	 */
	private function prepareKey( $key ) {
		$key = trim( (string) $key );
		$key = $this->prefix && 0 !== strpos( $key, $this->prefix ) ? $this->prefix . $key : $key;

		if ( aioseoTableOfContents()->helpers->isDev() && 80 < mb_strlen( $key, 'UTF-8' ) ) {
			throw new \Exception( 'You are using a cache name that is too large, shorten your name and try again: [' . esc_html( $key ) . ']' );
		}

		return $key;
	}

	/**
	 * Clears all of our cache.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function clear() {
		if ( $this->prefix ) {
			$this->clearPrefix( '' );

			return;
		}

		// Check if we should use transients
		if ( ! $this->isCacheTableAvailable() ) {
			// Delete all AIOSEO plugin cache transients
			$this->deleteAllTransients();
			$this->clearStatic();

			return;
		}

		aioseoTableOfContents()->core->db->truncate( $this->table )->run();

		$this->clearStatic();
	}

	/**
	 * Clears all of our cache under a certain prefix.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $prefix A prefix to clear or empty to clear everything.
	 * @return void
	 */
	public function clearPrefix( $prefix ) {
		$prefix = $this->prepareKey( $prefix );

		// Check if we should use transients
		if ( ! $this->isCacheTableAvailable() ) {
			// Delete transients by prefix
			$this->deleteTransientsByPrefix( $prefix );
			$this->clearStaticPrefix( $prefix );

			return;
		}

		aioseoTableOfContents()->core->db->delete( $this->table )
			->whereRaw( "`name` LIKE '$prefix%'" )
			->run();

		$this->clearStaticPrefix( $prefix );
	}

	/**
	 * Clears all of our static in-memory cache of a prefix.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $prefix The prefix to clear.
	 * @return void
	 */
	private function clearStaticPrefix( $prefix ) {
		$prefix = $this->prepareKey( $prefix );
		foreach ( array_keys( self::$cache ) as $key ) {
			if ( 0 === strpos( $key, $prefix ) ) {
				unset( self::$cache[ $key ] );
			}
		}
	}

	/**
	 * Clears all of our static in-memory cache.
	 *
	 * @since   1.0.0
	 * @version 202607 Renamed key to name
	 *
	 * @param  string $key The name of the cache to clear.
	 * @return void
	 */
	private function clearStatic( $key = null ) {
		if ( empty( $key ) ) {
			self::$cache = [];

			return;
		}

		unset( self::$cache[ $this->prepareKey( $key ) ] );
	}

	/**
	 * Updates the static in-memory cache for a single given key.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key   The key to update.
	 * @param  mixed  $value The value to cache.
	 * @return void
	 */
	private function updateStatic( $key = null, $value = null ) {
		if ( empty( $key ) ) {
			$this->clearStatic( $key );

			return;
		}

		self::$cache[ $this->prepareKey( $key ) ] = $value;
	}

	/**
	 * Checks if the cache table is available for use.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if table exists and is accessible.
	 */
	private function isCacheTableAvailable() {
		if ( null !== $this->useTransientFallback ) {
			return ! $this->useTransientFallback;
		}

		// Check transient first to avoid a DB query on every request.
		if ( get_transient( 'aioseo_plugin_cache_table_exists' ) ) {
			$this->useTransientFallback = false;

			return true;
		}

		// Transient not set — check the DB directly (avoids circular dependency with Database::tableExists).
		global $wpdb;
		$tableName = $wpdb->prefix . $this->table;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$tableExists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $tableName ) );

		if ( ! $tableExists ) {
			$this->useTransientFallback = true;

			return false;
		}

		// Verify the table has the expected 'name' column. During upgrades from older versions
		// the table may still have the legacy 'key' column. If the migration hasn't run yet,
		// we must fall back to transients to avoid "Unknown column 'name'" errors.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$nameColumnExists = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COLUMN_NAME
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE TABLE_SCHEMA = DATABASE()
				AND TABLE_NAME = %s
				AND COLUMN_NAME = 'name'",
				$tableName
			)
		);

		if ( ! $nameColumnExists ) {
			$this->useTransientFallback = true;

			return false;
		}

		set_transient( 'aioseo_plugin_cache_table_exists', 1, WEEK_IN_SECONDS );
		$this->useTransientFallback = false;

		return true;
	}

	/**
	 * Gets the transient name for a cache key.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key The cache key.
	 * @return string      The transient name.
	 */
	private function getTransientName( $key ) {
		// Store the original key to maintain compatibility with LIKE queries
		return 'aioseo_plugin_cache_' . $key;
	}

	/**
	 * Gets a value from transients.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key The cache key.
	 * @return mixed       The cached value or null.
	 */
	private function getTransient( $key ) {
		$transientName = $this->getTransientName( $key );
		$data          = get_transient( $transientName );

		if ( false === $data ) {
			return null;
		}

		// Decode the wrapper structure
		$wrapper = json_decode( $data, true );
		if ( ! is_array( $wrapper ) || ! isset( $wrapper['value'] ) ) {
			return null;
		}

		// Decode JSON value using is_object flag (matching database cache behavior)
		// If is_object is 1 (true), decode to object; if 0 (false), decode to array
		$decoded = json_decode( $wrapper['value'], empty( $wrapper['is_object'] ) );

		return $decoded;
	}

	/**
	 * Gets multiple transients using a LIKE query.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $pattern The pattern to match (with % wildcards).
	 * @return mixed           Array of matching cache entries or null.
	 */
	private function getTransientLike( $pattern ) {
		global $wpdb;

		$transientPattern = $this->getTransientName( $pattern );

		// Query for non-expired transients matching the pattern
		$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT REPLACE(option_name, '_transient_', '') as `key`, option_value as `value`
				FROM {$wpdb->options}
				WHERE option_name LIKE %s
				AND option_name NOT LIKE %s
				AND (
					NOT EXISTS (
						SELECT 1 FROM {$wpdb->options} timeout
						WHERE timeout.option_name = CONCAT('_transient_timeout_', REPLACE(option_name, '_transient_', ''))
						AND CAST(timeout.option_value AS UNSIGNED) > 0
						AND CAST(timeout.option_value AS UNSIGNED) < UNIX_TIMESTAMP()
					)
				)",
				'_transient_' . implode( '%', array_map( [ $wpdb, 'esc_like' ], explode( '%', $transientPattern ) ) ),
				'_transient_timeout_%'
			),
			ARRAY_A
		);

		if ( empty( $results ) ) {
			return null;
		}

		// Decode JSON values with is_object flag
		foreach ( $results as &$result ) {
			$result['key'] = str_replace( 'aioseo_plugin_cache_', '', $result['key'] );

			// Decode the wrapper structure
			$wrapper = json_decode( $result['value'], true );
			if ( is_array( $wrapper ) && isset( $wrapper['value'] ) ) {
				// Decode JSON value using is_object flag (matching database cache behavior)
				// If is_object is 1 (true), decode to object; if 0 (false), decode to array
				$result['value'] = json_decode( $wrapper['value'], empty( $wrapper['is_object'] ) );
			} else {
				// Fallback for malformed data
				$result['value'] = null;
			}
		}

		return $results;
	}

	/**
	 * Updates a transient value.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key        The cache key.
	 * @param  mixed  $value      The value to cache.
	 * @param  int    $expiration Expiration in seconds.
	 * @return void
	 */
	private function updateTransient( $key, $value, $expiration ) {
		$transientName = $this->getTransientName( $key );

		// Detect if value is an object to match database cache behavior
		$isObject = is_object( $value );

		// Encode as JSON to match database cache behavior
		$jsonValue = wp_json_encode( $value );

		if ( false === $jsonValue && JSON_ERROR_NONE !== json_last_error() ) {
			if ( aioseoTableOfContents()->helpers->isDev() ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'AIOSEO Plugin Cache: JSON encode failed for key "' . $key . '" - ' . json_last_error_msg() );
			}

			return;
		}

		// Wrap the value with metadata including is_object flag
		$wrapper = [
			'value'     => $jsonValue,
			'is_object' => $isObject
		];

		$wrappedValue = wp_json_encode( $wrapper );

		if ( false === $wrappedValue ) {
			if ( aioseoTableOfContents()->helpers->isDev() ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'AIOSEO Plugin Cache: JSON encode failed for wrapper on key "' . $key . '"' );
			}

			return;
		}

		set_transient( $transientName, $wrappedValue, $expiration );
	}

	/**
	 * Deletes a transient.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key The cache key.
	 * @return void
	 */
	private function deleteTransient( $key ) {
		$transientName = $this->getTransientName( $key );
		delete_transient( $transientName );
	}

	/**
	 * Deletes all transients matching a prefix.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $prefix The prefix to match.
	 * @return void
	 */
	private function deleteTransientsByPrefix( $prefix ) {
		global $wpdb;

		$escapedPrefix = $wpdb->esc_like( $this->getTransientName( $prefix ) );

		// Delete both the transient and its timeout
		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options}
				WHERE option_name LIKE %s
				OR option_name LIKE %s",
				'_transient_' . $escapedPrefix . '%',
				'_transient_timeout_' . $escapedPrefix . '%'
			)
		);
	}

	/**
	 * Deletes all AIOSEO plugin cache transients.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function deleteAllTransients() {
		global $wpdb;

		$prefix = 'aioseo_plugin_cache_%';

		// Delete both transients and their timeouts
		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options}
				WHERE option_name LIKE %s
				OR option_name LIKE %s",
				'_transient_' . $wpdb->esc_like( $prefix ),
				'_transient_timeout_' . $wpdb->esc_like( $prefix )
			)
		);
	}
}