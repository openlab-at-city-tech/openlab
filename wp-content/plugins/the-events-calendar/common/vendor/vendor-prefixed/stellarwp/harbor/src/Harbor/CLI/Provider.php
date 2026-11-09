<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\CLI;

use TEC\Common\LiquidWeb\Harbor\CLI\Commands\Catalog;
use TEC\Common\LiquidWeb\Harbor\CLI\Commands\Feature;
use TEC\Common\LiquidWeb\Harbor\CLI\Commands\License;
use TEC\Common\LiquidWeb\Harbor\Contracts\Abstract_Provider;
use TEC\Common\LiquidWeb\Harbor\Utils\Version;
use WP_CLI;

/**
 * Registers WP-CLI commands for the Harbor library.
 *
 * Early-returns when WP-CLI is not present, so command classes are never
 * instantiated during normal web requests.
 *
 * @since 1.0.0
 */
final class Provider extends Abstract_Provider {

	/**
	 * @inheritDoc
	 */
	public function register(): void {
		if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { // @phpstan-ignore booleanNot.alwaysFalse, booleanOr.alwaysFalse (WP_CLI is only defined in CLI context)
			return;
		}

		$this->container->singleton( Feature::class );
		$this->container->singleton( License::class );
		$this->container->singleton( Catalog::class );

		WP_CLI::add_hook( 'after_wp_load', [ $this, 'register_commands' ] );
	}

	/**
	 * Registers all WP-CLI commands.
	 *
	 * Uses Version::should_handle() to prevent duplicate registration
	 * across vendor-prefixed copies of Harbor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_commands(): void {
		if ( ! Version::should_handle( 'cli_commands' ) ) {
			return;
		}

		WP_CLI::add_command( 'harbor feature', $this->container->get( Feature::class ) );
		WP_CLI::add_command( 'harbor license', $this->container->get( License::class ) );
		WP_CLI::add_command( 'harbor catalog', $this->container->get( Catalog::class ) );
	}
}
