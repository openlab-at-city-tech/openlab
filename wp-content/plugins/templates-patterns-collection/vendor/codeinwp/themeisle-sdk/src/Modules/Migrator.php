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
 * Allows products to ship PHP migration files that run automatically on
 * admin page loads. Each product opts in by registering its migrations
 * directory via the `{product_slug}_sdk_migrations_path` filter.
 */
class Migrator extends Abstract_Module {
	/**
	 * Option key suffix used to store the list of ran migrations.
	 */
	const OPTION_SUFFIX = '_ran_migrations';

	/**
	 * Check if we should load the module for this product.
	 *
	 * Always returns true — the actual path check happens lazily at admin_init.
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
		add_action( 'admin_init', array( $this, 'run_pending' ) );
		add_action( 'themeisle_sdk_rollback_migration_' . $product->get_slug(), array( $this, 'rollback' ) );
		return $this;
	}

	/**
	 * Discover and run any pending migrations for the product.
	 *
	 * Only runs when a version upgrade was detected during this request, indicated
	 * by the themeisle_sdk_update_{slug} action having fired.
	 *
	 * @return void
	 */
	public function run_pending() {
		if ( ! did_action( 'themeisle_sdk_update_' . $this->product->get_slug() ) ) {
			return;
		}

		$path = $this->get_migrations_path();

		if ( empty( $path ) || ! is_dir( $path ) ) {
			return;
		}

		$files = glob( trailingslashit( $path ) . '*.php' );

		if ( empty( $files ) ) {
			return;
		}

		sort( $files ); // Alphabetical order = chronological order given timestamp naming.

		$option_key = $this->product->get_key() . self::OPTION_SUFFIX;
		$ran        = get_option( $option_key, array() );

		foreach ( $files as $file ) {
			$name = basename( $file, '.php' );

			if ( in_array( $name, $ran, true ) ) {
				continue;
			}

			try {
				$migration = require $file; // Migration files return an anonymous class instance.

				if ( ! ( $migration instanceof Abstract_Migration ) ) {
					continue;
				}

				if ( ! $migration->should_run() ) {
					continue;
				}

				$migration->up();
				$ran[] = $name;
				update_option( $option_key, $ran );
			} catch ( \Throwable $e ) {
				// Log and stop — leave the migration unrecorded so it retries next load.
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'ThemeIsle SDK Migrator: failed to run ' . $name . ': ' . $e->getMessage() );
				break;
			}
		}
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
