<?php

namespace TEC\Common\StellarWP\Uplink\Admin;

use TEC\Common\StellarWP\Uplink\API;
use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Resources;
use WP_Error;
use WP_Upgrader;
use WP_Filesystem_Base;

class Package_Handler {

	/**
	 * @var WP_Upgrader
	 */
	public $upgrader;

	/**
	 * @var WP_Filesystem_Base|null
	 */
	public $filesystem;

	/**
	 * Filters the package download step to store the downloaded file with a shorter file name.
	 *
	 * @param  bool|WP_Error  $reply        Whether to bail without returning the package.
	 *                                      Default false.
	 * @param  string|null    $package      The package file name or URL.
	 * @param  WP_Upgrader    $upgrader     The WP_Upgrader instance.
	 * @param  array          $hook_extra   Extra arguments passed to hooked filters.
	 *
	 * @return string|bool|WP_Error
	 */
	public function filter_upgrader_pre_download( $reply, $package, WP_Upgrader $upgrader, $hook_extra ) {
		if ( empty( $package ) || 'invalid_license' === $package ) {
			return new WP_Error(
				'download_failed',
				__( 'Failed to update plugin. Check your license details first.', '%TEXTDOMAIN%' ),
				''
			);
		}
		if ( $this->is_uplink_package_url( $package, $hook_extra ) ) {
			$this->upgrader = $upgrader;

			return $this->download( $package );
		}

		return $reply;
	}

	/**
	 * Filters the source file location.
	 *
	 * @since  1.0.0
	 *
	 * @param array<mixed> $result   Result of the upgrader process.
	 * @param array<mixed> $extras   Extra args for the upgrader process.
	 *
	 * @return array
	 */
	public function filter_upgrader_install_package_result( $result, $extras ) {
		global $wp_filesystem;

		if ( ! isset( $extras['plugin'] ) ) {
			return $result;
		}

		$plugin = $extras['plugin'];

		// Bail if we are not dealing with a plugin we own.
		if ( ! $this->is_uplink_package( $plugin ) ) {
			return $result;
		}

		$containing_dir = dirname( $result['remote_destination'] );
		$intended_dir   = dirname( $plugin );
		$actual_dir     = basename( $result['remote_destination'] );

		// @phpstan-ignore-next-line
		$protected_directories = [ ABSPATH, WP_CONTENT_DIR, WP_PLUGIN_DIR, WP_CONTENT_DIR . '/themes' ];

		if (
			$intended_dir !== $actual_dir
			&& ! in_array( $containing_dir . '/' . $actual_dir, $protected_directories, true )
			&& ! in_array( $containing_dir . '/' . $intended_dir, $protected_directories, true )
		) {
			$wp_filesystem->move( $containing_dir . '/' . $actual_dir, $containing_dir . '/' . $intended_dir );
			$result['remote_destination'] = $containing_dir . '/' . $intended_dir;
			activate_plugin( $plugin );
		}

		return $result;
	}

	/**
	 * Whether the current package is an StellarWP product or not.
	 *
	 * @param string $plugin The plugin file relative to the plugins dir.
	 *
	 * @return bool
	 */
	protected function is_uplink_package( string $plugin ) : bool {
		if ( empty( $plugin ) ) {
			return false;
		}

		$container = Config::get_container();
		$resource  = $container->get( Resources\Collection::class )->get_by_path( $plugin );

		return (bool) $resource->count();
	}

	/**
	 * Whether the current package is an StellarWP product or not.
	 *
	 * @param string $package The package file name or URL.
	 * @param array  $hook_extra Extra arguments passed to hooked filters.
	 *
	 * @return bool
	 */
	protected function is_uplink_package_url( string $package, $hook_extra ) : bool {
		if ( empty( $hook_extra['plugin'] ) ) {
			return false;
		}

		if (
			empty( $package )
			|| ! preg_match( '!^(http|https|ftp)://!i', $package )
		) {
			return false;
		}

		$query_vars = parse_url( $package, PHP_URL_QUERY );

		if ( empty( $query_vars ) ) {
			return false;
		}

		if ( ! $this->is_uplink_package( $hook_extra['plugin'] ) ) {
			return false;
		}

		$container    = Config::get_container();
		$api_base_url = $container->get( API\Client::class )->get_api_base_url();

		return preg_match( '!^' . preg_quote( $api_base_url, '!' ) . '!i', $package );
	}

	/**
	 * A mimic of the `WP_Upgrader::download_package` method that adds a step to store the temp file with a shorter
	 * file name.
	 *
	 * @see WP_Upgrader::download_package()
	 *
	 * @param string $package The URI of the package. If this is the full path to an
	 *                        existing local file, it will be returned untouched.
	 *
	 * @return string|bool|WP_Error The full path to the downloaded package file, or a WP_Error object.
	 */
	protected function download( string $package ) {
		if ( empty( $this->filesystem ) ) {
			// try to connect
			// @phpstan-ignore-next-line
			$this->upgrader->fs_connect( [ WP_CONTENT_DIR, WP_PLUGIN_DIR ] );

			global $wp_filesystem;

			// still empty?
			if ( empty( $wp_filesystem ) ) {
				// bail
				return false;
			}

			// @phpstan-ignore-next-line
			$this->filesystem = $wp_filesystem;
		}

		$this->upgrader->skin->feedback( 'downloading_package', $package );

		$download_file = download_url( $package );

		if ( is_wp_error( $download_file ) ) {
			return new WP_Error(
				'download_failed',
				$this->upgrader->strings['download_failed'],
				$download_file->get_error_message()
			);
		}

		$file = $this->get_short_filename( $download_file );

		$moved = $this->filesystem->move( $download_file, $file );

		if ( empty( $moved ) ) {
			// We tried, we failed, we bail and let WP do its job
			return false;
		}

		return $file;
	}

	/**
	 * Returns the absolute path to a shorter filename version of the original download temp file.
	 *
	 * The path will point to the same temp dir (WP handled) but shortening the filename to a
	 * 6 chars hash to cope with OSes limiting the max number of chars in a file path.
	 * The original filename would be a sanitized version of the URL including query args.
	 *
	 * @param string $download_file The absolute path to the original download file.
	 *
	 * @return string The absolute path to a shorter name version of the downloaded file.
	 */
	protected function get_short_filename( string $download_file ) : string {
		$extension = pathinfo( $download_file, PATHINFO_EXTENSION );
		$filename  = substr( md5( $download_file ), 0, 5 );
		$file      = dirname( $download_file ) . '/' . $filename . '.' . $extension;

		return $file;
	}
}
