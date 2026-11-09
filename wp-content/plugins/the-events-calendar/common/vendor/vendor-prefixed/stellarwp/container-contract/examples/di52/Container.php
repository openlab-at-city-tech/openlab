<?php declare( strict_types=1 );

namespace TEC\Common\Your\Namespace;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;

// If you are including lucatume\DI52 using Strauss (recommended), then:
use TEC\Common\Your\Namespace\lucatume\DI52\Container as DI52Container;

// If you are including lucatume\DI52 directly, then you'd want to do:
// use TEC\Common\lucatume\DI52\Container as DI52Container;

/**
 * @method mixed getVar(string $key, mixed|null $default = null)
 * @method void register(string $serviceProviderClass, string ...$alias)
 * @method self when(string $class)
 * @method self needs(string $id)
 * @method void give(mixed $implementation)
 */
class Container implements ContainerInterface {
	/**
	 * @var DI52Container
	 */
	protected $container;

	/**
	 * Container constructor.
	 *
	 * @param object $container The container to use.
	 */
	public function __construct( $container = null ) {
		$this->container = $container ?: new DI52Container();
	}

	/**
	 * @inheritDoc
	 */
	public function bind( string $id, $implementation = null, array $afterBuildMethods = null ) {
		$this->container->bind( $id, $implementation, $afterBuildMethods );
	}

	/**
	 * @inheritDoc
	 */
	public function get( string $id ) {
		return $this->container->get( $id );
	}

	/**
	 * @return DI52Container
	 */
	public function get_container() {
		return $this->container;
	}

	/**
	 * @inheritDoc
	 */
	public function has( string $id ) {
		return $this->container->has( $id );
	}

	/**
	 * @inheritDoc
	 */
	public function singleton( string $id, $implementation = null, array $afterBuildMethods = null ) {
		$this->container->singleton( $id, $implementation, $afterBuildMethods );
	}

	/**
	 * Defer all other calls to the container object.
	 */
	public function __call( $name, $args ) {
		return $this->container->{$name}( ...$args );
	}
}
