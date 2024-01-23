<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\Installer;

use TEC\Common\StellarWP\Installer\Installer;

class Assets {
	/**
	 * Has the installer script been enqueued?
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $has_enqueued = false;

	/**
	 * Get the URL for the JS.
	 *
	 * @since 1.1.0
	 *
	 * @param string $file The file to get the URL for.
	 *
	 * @return string
	 */
	public function get_url( string $file ): string {
		$path     = dirname( __DIR__ );
		// @phpstan-ignore-next-line
		$base_url = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $path );

		if ( is_ssl() ) {
			$base_url = str_replace( 'http://', 'https://', $base_url );
		}

		return $base_url . '/' . $file;
	}

	/**
	 * Enqueues a script.
	 *
	 * @since 1.1.0
	 *
	 * @param string $handle The script handle.
	 * @param array  $data   The data to pass to the script.
	 *
	 * @return void
	 */
	public function enqueue_script( string $handle, array $data = [] ): void {
		$script_handle = $this->get_script_handle( $handle );

		// If the script has already been enqueued from elsewhere, bail.
		if ( wp_script_is( $script_handle, 'enqueued' ) ) {
			return;
		}

		add_filter( 'script_loader_tag', static function( $tag, $handle ) use ( $script_handle, $data ) {
			if ( $handle !== $script_handle ) {
				return $tag;
			}

			$namespace_key = Installer::get()->get_js_object_key();
			$data_encoded = wp_json_encode( $data );

			$replacement = "<script data-stellarwp-namespace='{$namespace_key}' data-stellarwp-data='{$data_encoded}' ";
			return str_replace( '<script ', $replacement, $tag );
		}, 50, 2 );

		wp_enqueue_script( $script_handle );
	}

	/**
	 * Enqueues the installer script.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		if ( $this->has_enqueued() ) {
			return;
		}

		$this->register_script( 'stellarwp-installer', 'assets/js/installer.js', [ 'jquery', 'wp-hooks' ], true );

		$this->enqueue_script( 'stellarwp-installer', [
			'ajaxurl'   => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
			'selectors' => Installer::get()->get_js_selectors(),
			'busyClass' => Installer::get()->get_busy_class(),
		] );

		$this->has_enqueued = true;
	}

	public function get_script_handle( string $slug ): string {
		return implode( '-', [ $slug, Config::get_hook_prefix() ] );
	}

	/**
	 * Has the installer script been enqueued?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_enqueued(): bool {
		return $this->has_enqueued;
	}

	/**
	 * Register the JS.
	 *
	 * @since 1.1.0
	 *
	 * @param string           $handle    Name of the script. Should be unique.
	 * @param string|false     $src       Full URL of the script, or path of the script relative to the WordPress root directory.
	 *                                    If source is set to false, script is an alias of other scripts it depends on.
	 * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param bool             $in_footer Optional. Whether to enqueue the script before `</body>` instead of in the `<head>`.
	 *                                    Default 'false'.
	 *
	 * @return bool Whether the script has been registered. True on success, false on failure.
	 */
	public function register_script( $handle, $src, $deps, $in_footer ): bool {
		$script_handle = $this->get_script_handle( $handle );

		if ( wp_script_is( $script_handle, 'registered' ) ) {
			return true;
		}

		$registered = wp_register_script( $script_handle, $this->get_url( $src ), $deps, Installer::VERSION, $in_footer );

		return $registered;
	}
}
