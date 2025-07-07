<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Admin;

use TEC\Common\StellarWP\Uplink\Config;

class Asset_Manager {
	/**
	 * @var string
	 */
	protected $handle = '';

	/**
	 * @var string
	 */
	protected $assets_path = '';

	public function __construct( string $assets_path ) {
		$this->assets_path = $assets_path;
		$this->handle      = sprintf( 'stellarwp-uplink-license-admin-%s', Config::get_hook_prefix() );
	}

	/**
	 * @return void
	 */
	public function register_assets(): void {
		/**
		 * Filters the JS source for the admin.
		 *
		 * @since 2.0.0
		 *
		 * @param string $js_src The JS source.
		 */
		$js_src = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/admin_js_source', $this->assets_path . '/js/key-admin.js' );

		wp_register_script( $this->handle, $js_src, [ 'jquery' ], '1.0.0', true );

		$action_postfix = Config::get_hook_prefix_underscored();
		wp_localize_script( $this->handle, sprintf( 'stellarwp_config_%s', $action_postfix ), [ 'action' => sprintf( 'pue-validate-key-uplink-%s', $action_postfix ) ] );

		/**
		 * Filters the CSS source for the admin.
		 *
		 * @since 2.0.0
		 *
		 * @param string $css_src The CSS source.
		 */
		$css_src = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/admin_css_source', $this->assets_path . '/css/main.css' );

		wp_register_style( $this->handle, $css_src );
	}

	/**
	 * Enqueue the registered scripts and styles, only when rendering fields.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		wp_enqueue_script( $this->handle );
		wp_enqueue_style( $this->handle );
	}
}
