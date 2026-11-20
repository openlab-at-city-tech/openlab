<?php
/**
 * The migrator module for ThemeIsle SDK.
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2024, Themeisle
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       3.3.50
 */

namespace ThemeisleSDK\Modules;

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Product;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Migrator module for ThemeIsle SDK.
 *
 * Allows products to ship PHP migration files that run automatically on the
 * first complete request for each version. Each product opts in by registering
 * its migrations directory via the `{product_slug}_sdk_migrations_path` filter.
 */
class Migrator extends Abstract_Module {
	/**
	 * Option key suffix used to store the list of ran migrations.
	 */
	const OPTION_SUFFIX = '_ran_migrations';

	/**
	 * Option key suffix used to store the last fully migrated product version.
	 */
	const VERSION_OPTION_SUFFIX = '_migrated_version';

	/**
	 * Option/cache key suffix used while migrations are running.
	 */
	const LOCK_SUFFIX = '_migration_lock';

	/**
	 * Cache group used for migration locks.
	 */
	const LOCK_CACHE_GROUP = 'themeisle_sdk_migrations';

	/**
	 * Maximum lock lifetime in seconds.
	 */
	const LOCK_TTL = 300;

	/**
	 * Token identifying the lock owned by this instance.
	 *
	 * @var string
	 */
	private $lock_token = '';

	/**
	 * Serialized value used by the database lock.
	 *
	 * @var string
	 */
	private $lock_value = '';

	/**
	 * Lock backend used by this instance.
	 *
	 * @var string
	 */
	private $lock_driver = '';

	/**
	 * Check if we should load the module for this product.
	 *
	 * Always returns true — the actual path check happens lazily at wp_loaded.
	 *
	 * @param Product $product Product to load the module for.
	 *
	 * @return bool
	 */
	public function can_load( $product ) {
		return apply_filters( $product->get_slug() . '_sdk_enable_migrator', true );
	}

	/**
	 * Load module logic.
	 *
	 * @param Product $product Product to load.
	 *
	 * @return Migrator
	 */
	public function load( $product ) {
		$this->product = $product;
		add_action( 'wp_loaded', array( $this, 'run_pending' ) );
		add_action( 'themeisle_sdk_rollback_migration_' . $product->get_slug(), array( $this, 'rollback' ) );
		return $this;
	}

	/**
	 * Discover and run any pending migrations for the product.
	 *
	 * Runs on the first complete request for each product version. The migrated
	 * version is recorded only after all pending migrations finish successfully,
	 * so interrupted or failed runs are retried on the next request.
	 *
	 * @return void
	 */
	public function run_pending() {
		$path = $this->get_migrations_path();

		if ( empty( $path ) || ! is_dir( $path ) ) {
			return;
		}

		$version_key     = $this->product->get_key() . self::VERSION_OPTION_SUFFIX;
		$current_version = $this->product->get_version();

		if ( get_option( $version_key, '' ) === $current_version ) {
			return;
		}

		if ( ! $this->acquire_lock() ) {
			return;
		}

		// Another request may have completed while this request waited for the lock.
		if ( get_option( $version_key, '' ) === $current_version ) {
			$this->release_lock();
			return;
		}

		if ( $this->execute_pending( $path ) ) {
			update_option( $version_key, $current_version );
		}

		$this->release_lock();
	}

	/**
	 * Execute all pending migration files.
	 *
	 * @param string $path Absolute migrations directory path.
	 *
	 * @return bool True when every migration was handled successfully.
	 */
	private function execute_pending( $path ) {
		$files = glob( trailingslashit( $path ) . '*.php' );

		if ( false === $files ) {
			$this->log_error( 'discovery', 'failed to read the migrations directory' );
			return false;
		}

		if ( empty( $files ) ) {
			return true;
		}

		sort( $files ); // Alphabetical order = chronological order given timestamp naming.

		$option_key = $this->product->get_key() . self::OPTION_SUFFIX;
		$ran        = get_option( $option_key, array() );
		$ran        = is_array( $ran ) ? $ran : array();

		foreach ( $files as $file ) {
			$name = basename( $file, '.php' );

			if ( in_array( $name, $ran, true ) ) {
				continue;
			}

			try {
				$migration = require $file; // Migration files return an anonymous class instance.

				if ( ! ( $migration instanceof Abstract_Migration ) ) {
					$this->log_error( $name, 'migration file must return an Abstract_Migration instance' );
					return false;
				}

				if ( ! $migration->should_run() ) {
					continue;
				}

				$migration->up();
				$ran[] = $name;
				update_option( $option_key, $ran );
			} catch ( \Throwable $e ) {
				// Stop and leave the product version incomplete so the next request retries.
				$this->log_error( $name, $e->getMessage() );
				return false;
			}
		}

		return true;
	}

	/**
	 * Acquire a cross-request migration lock.
	 *
	 * Persistent object caches provide an atomic add operation. Sites without
	 * one use an atomic database insert/compare-and-swap fallback.
	 *
	 * @return bool True when the lock was acquired.
	 */
	private function acquire_lock() {
		$this->lock_token = function_exists( 'wp_generate_uuid4' ) ? wp_generate_uuid4() : uniqid( '', true );

		if ( function_exists( 'wp_using_ext_object_cache' ) && wp_using_ext_object_cache() ) {
			$acquired = wp_cache_add(
				$this->get_lock_key(),
				$this->lock_token,
				self::LOCK_CACHE_GROUP,
				self::LOCK_TTL
			);

			if ( $acquired ) {
				$this->lock_driver = 'cache';
			}

			return $acquired;
		}

		return $this->acquire_database_lock();
	}

	/**
	 * Acquire the database lock used without a persistent object cache.
	 *
	 * @return bool True when the lock was acquired.
	 */
	private function acquire_database_lock() {
		global $wpdb;

		$key              = $this->get_lock_key();
		$this->lock_value = wp_json_encode(
			array(
				'token'   => $this->lock_token,
				'expires' => time() + self::LOCK_TTL,
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$inserted = $wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, %s)",
				$key,
				$this->lock_value,
				'no'
			)
		);

		if ( 1 === (int) $inserted ) {
			$this->lock_driver = 'database';
			return true;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$existing = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $key ) );
		$lock     = json_decode( (string) $existing, true );

		if ( is_array( $lock ) && ! empty( $lock['expires'] ) && (int) $lock['expires'] >= time() ) {
			return false;
		}

		// Replace an expired lock only if it has not changed since it was read.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$updated = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value = %s",
				$this->lock_value,
				$key,
				$existing
			)
		);

		if ( 1 === (int) $updated ) {
			$this->lock_driver = 'database';
			return true;
		}

		return false;
	}

	/**
	 * Release the lock owned by this instance.
	 *
	 * @return void
	 */
	private function release_lock() {
		if ( 'cache' === $this->lock_driver ) {
			$current_token = wp_cache_get( $this->get_lock_key(), self::LOCK_CACHE_GROUP );

			if ( $current_token === $this->lock_token ) {
				wp_cache_delete( $this->get_lock_key(), self::LOCK_CACHE_GROUP );
			}
		}

		if ( 'database' === $this->lock_driver ) {
			global $wpdb;

			// Delete only the lock value created by this instance.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s",
					$this->get_lock_key(),
					$this->lock_value
				)
			);
		}

		$this->lock_token  = '';
		$this->lock_value  = '';
		$this->lock_driver = '';
	}

	/**
	 * Get the migration lock key.
	 *
	 * @return string Lock key.
	 */
	private function get_lock_key() {
		return $this->product->get_key() . self::LOCK_SUFFIX;
	}

	/**
	 * Log a migration failure.
	 *
	 * @param string $name    Migration name or operation.
	 * @param string $message Error message.
	 *
	 * @return void
	 */
	private function log_error( $name, $message ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'ThemeIsle SDK Migrator: failed to run ' . $name . ': ' . $message );
	}

	/**
	 * Roll back a single migration by name.
	 *
	 * Calls down() on the migration and removes it from the ran list so it will
	 * be picked up again on the next upgrade. This method is never called
	 * automatically — products invoke it explicitly when needed.
	 *
	 * @param string $migration_name Migration basename without .php extension.
	 *
	 * @return bool True if rolled back successfully, false if not found or not previously run.
	 */
	public function rollback( $migration_name ) {
		$option_key = $this->product->get_key() . self::OPTION_SUFFIX;
		$ran        = get_option( $option_key, array() );

		if ( ! in_array( $migration_name, $ran, true ) ) {
			return false;
		}

		$path = $this->get_migrations_path();
		$file = trailingslashit( $path ) . $migration_name . '.php';

		if ( ! is_file( $file ) ) {
			return false;
		}

		try {
			$migration = require $file;

			if ( ! ( $migration instanceof Abstract_Migration ) ) {
				return false;
			}

			$migration->down();
			update_option( $option_key, array_values( array_diff( $ran, array( $migration_name ) ) ) );

			return true;
		} catch ( \Throwable $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'ThemeIsle SDK Migrator: failed to roll back ' . $migration_name . ': ' . $e->getMessage() );

			return false;
		}
	}

	/**
	 * Get the migrations directory path for the current product.
	 *
	 * Products register their path via the `{slug}_sdk_migrations_path` filter.
	 *
	 * @return string Absolute path to the migrations directory, or empty string.
	 */
	private function get_migrations_path() {
		return (string) apply_filters( $this->product->get_slug() . '_sdk_migrations_path', '' );
	}
}
