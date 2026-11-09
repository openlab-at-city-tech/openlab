<?php

namespace TEC\Common\Your\Namespace;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;

// If you are including PHP-DI container using Strauss (recommended), then:
use TEC\Common\Your\Namespace\DI\Container as PHPDIContainer;

// If you are including the PHP-DI container directly, then you'd want to do:
//use DI\Container as PHPDIContainer;

class Container implements ContainerInterface {
	protected $container;

	/**
	 * Container constructor.
	 */
	public function __construct() {
		$this->container = new PHPDIContainer();
	}

	/**
	 * @inheritDoc
	 */
	public function bind( string $id, $implementation = null ) {
		$this->container->set( $id, $implementation );
	}

	/**
	 * @inheritDoc
	 */
	public function get( string $id ) {
		return $this->container->get( $id );
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
	public function singleton( string $id, $implementation = null ) {
		$this->container->set( $id, $implementation );
	}
}
