<?php
/**
 * Kadence\Container class
 *
 * @package kadence
 */

namespace Kadence;

use Kadence\lucatume\DI52\Container as DI52_Container;
use Kadence\StellarWP\ContainerContract\ContainerInterface;

/**
 * The theme's service container.
 *
 * Code outside the service providers reaches it through `Theme::instance()->container()`
 * and sees only the container contract.
 *
 * @since 1.6.0
 */
final class Container implements ContainerInterface {

	/**
	 * The wrapped container.
	 *
	 * @since 1.6.0
	 *
	 * @var DI52_Container
	 */
	private DI52_Container $container;

	/**
	 * Builds the container and binds it to its own ids.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {
		$this->container = new DI52_Container();

		$this->container->singleton( ContainerInterface::class, $this );
		$this->container->singleton( self::class, $this );
	}

	/**
	 * Binds an id to an implementation that is built on every request for it.
	 *
	 * @since 1.6.0
	 *
	 * @param string $id             Class, interface or slug.
	 * @param mixed  $implementation Class name, object or closure; null to build the id itself.
	 */
	public function bind( string $id, $implementation = null ): void {
		$this->container->bind( $id, $implementation );
	}

	/**
	 * Returns the entry for an id, building it when needed.
	 *
	 * @since 1.6.0
	 *
	 * @param string $id Class, interface or slug.
	 * @return mixed The entry.
	 */
	public function get( string $id ) {
		return $this->container->get( $id );
	}

	/**
	 * Whether the container can return an entry for an id.
	 *
	 * @since 1.6.0
	 *
	 * @param string $id Class, interface or slug.
	 * @return bool True when it can.
	 */
	public function has( string $id ): bool {
		return $this->container->has( $id );
	}

	/**
	 * Binds an id to an implementation that is built once and then shared.
	 *
	 * @since 1.6.0
	 *
	 * @param string $id             Class, interface or slug.
	 * @param mixed  $implementation Class name, object or closure; null to build the id itself.
	 */
	public function singleton( string $id, $implementation = null ): void {
		$this->container->singleton( $id, $implementation );
	}

	/**
	 * Registers a service provider, which binds its services and adds its hooks.
	 *
	 * @since 1.6.0
	 *
	 * @param string $provider_class Class name of a Service_Provider subclass.
	 *
	 * @phpstan-param class-string<Service_Provider> $provider_class
	 */
	public function register( string $provider_class ): void {
		$this->container->register( $provider_class );
	}
}
