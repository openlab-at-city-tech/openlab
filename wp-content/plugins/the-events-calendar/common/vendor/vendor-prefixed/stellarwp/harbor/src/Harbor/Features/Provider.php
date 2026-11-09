<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features;

use TEC\Common\LiquidWeb\Harbor\Contracts\Abstract_Provider;
use TEC\Common\LiquidWeb\Harbor\Features\Strategy\Strategy_Factory;

/**
 * Registers the Features subsystem in the DI container and hooks.
 *
 * @since 1.0.0
 */
class Provider extends Abstract_Provider {

	/**
	 * Registers singletons and hooks for the Features subsystem.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton( Strategy_Factory::class );

		$this->container->singleton( Resolve_Feature_Collection::class );
		$this->container->singleton( Feature_Repository::class );
		$this->container->singleton( Feature_Collection::class );
		$this->container->singleton( Manager::class );

		$this->container->singleton( Update\Provider::class );
		$this->container->get( Update\Provider::class )->register();
	}
}
