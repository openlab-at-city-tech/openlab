<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Update;

use TEC\Common\LiquidWeb\Harbor\Contracts\Abstract_Provider;
use TEC\Common\LiquidWeb\Harbor\Utils\Version;

/**
 * Registers the feature update pathway in the DI container and hooks.
 *
 * @since 1.0.0
 */
class Provider extends Abstract_Provider {

	/**
	 * Registers singletons and defers hook registration to init.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton( Resolve_Update_Data::class );

		$this->container->singleton( Plugin_Handler::class );
		$this->container->singleton( Theme_Handler::class );

		add_action( 'init', [ $this, 'register_hooks' ] );
	}

	/**
	 * Registers the feature update filters if this is the highest Harbor version.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		if ( ! Version::should_handle( 'feature_updates' ) ) {
			return;
		}

		$plugin_handler = $this->container->get( Plugin_Handler::class );

		// Priority 15 to run after the plugins_api filter in the Plugins_Page class.
		add_filter( 'plugins_api', [ $plugin_handler, 'filter_plugins_api' ], 15, 3 );
		add_filter( 'pre_set_site_transient_update_plugins', [ $plugin_handler, 'filter_update_check' ], 15, 1 );
		add_filter( 'site_transient_update_plugins', [ $plugin_handler, 'filter_update_check' ], 15, 1 );

		$theme_handler = $this->container->get( Theme_Handler::class );

		add_filter( 'themes_api', [ $theme_handler, 'filter_themes_api' ], 15, 3 );
		add_filter( 'pre_set_site_transient_update_themes', [ $theme_handler, 'filter_update_check' ], 15, 1 );
		add_filter( 'site_transient_update_themes', [ $theme_handler, 'filter_update_check' ], 15, 1 );
	}
}
