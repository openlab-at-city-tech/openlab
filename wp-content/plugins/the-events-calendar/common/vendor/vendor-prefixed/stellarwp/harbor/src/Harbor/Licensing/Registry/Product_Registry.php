<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Licensing\Registry;

use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;
use TEC\Common\LiquidWeb\Harbor\Utils\Cast;
use TEC\Common\LiquidWeb\Harbor\Utils\License_Key;

/**
 * Discovers products that have opted in to unified licensing by scanning
 * active plugins for a bundled LWSW_KEY.php file.
 *
 * Each LWSW_KEY.php file must return a single string containing a valid
 * LWSW- prefixed license key:
 *
 *   <?php return 'LWSW-xxxx-xxxx-xxxx-xxxx';
 *
 * The presence of this file signals that the product belongs to the unified
 * Harbor licensing system and is not managed by StellarWP Uplink v2.
 *
 * @since 1.0.0
 */
final class Product_Registry {

	use With_Debugging;

	/**
	 * The filename Harbor looks for inside each active plugin's root directory.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const KEY_FILE = 'LWSW_KEY.php';

	/**
	 * Plugin root directories to scan. When null, auto-discovered from
	 * the active_plugins WordPress option at scan time.
	 *
	 * @since 1.0.0
	 *
	 * @var string[]|null
	 */
	private ?array $plugin_dirs;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string[]|null $plugin_dirs Plugin root directories to scan.
	 *                                   Null (default) auto-discovers from active plugins.
	 */
	public function __construct( ?array $plugin_dirs = null ) {
		$this->plugin_dirs = $plugin_dirs;
	}

	/**
	 * Find the first embedded license key from a bundled LWSW_KEY.php file.
	 *
	 * Scans each active plugin's root directory for an LWSW_KEY.php file.
	 * Returns the first valid LWSW- prefixed key found, or null if none exists.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function first_with_embedded_key(): ?string {
		$dirs = $this->resolve_plugin_dirs();

		self::debug_log(
			sprintf( 'Scanning %d active plugin(s) for embedded license key.', count( $dirs ) )
		);

		foreach ( $dirs as $dir ) {
			$key_file = $dir . '/' . self::KEY_FILE;

			if ( ! is_readable( $key_file ) ) {
				continue;
			}

			self::debug_log(
				sprintf( 'Found %s in %s — loading.', self::KEY_FILE, $dir )
			);

			$key = include $key_file;

			if ( is_string( $key ) && License_Key::is_valid_format( $key ) ) {
				self::debug_log(
					sprintf( 'Valid embedded license key found in %s: %s', $dir, $key )
				);

				return $key;
			}

			self::debug_log(
				sprintf(
					'%s in %s did not return a valid LWSW- key. Got: %s',
					self::KEY_FILE,
					$dir,
					Cast::to_string( $key )
				)
			);
		}

		self::debug_log( 'No embedded license key found in any active plugin.' );

		return null;
	}

	/**
	 * Returns the plugin root directories to scan.
	 *
	 * Uses the injected dirs if provided; otherwise builds the list from
	 * the active_plugins WordPress option.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	private function resolve_plugin_dirs(): array {
		if ( $this->plugin_dirs !== null ) {
			return $this->plugin_dirs;
		}

		$active = (array) get_option( 'active_plugins', [] );
		$dirs   = [];

		foreach ( $active as $plugin_file ) {
			if ( ! is_string( $plugin_file ) ) {
				continue;
			}

			$dir = WP_PLUGIN_DIR . '/' . dirname( $plugin_file );

			if ( is_dir( $dir ) ) {
				$dirs[] = $dir;
			}
		}

		return $dirs;
	}
}
