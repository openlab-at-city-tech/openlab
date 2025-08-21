<?php

namespace TEC\Common\StellarWP\Uplink\Contracts;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Uplink\Config;

abstract class Abstract_Provider implements Provider_Interface {

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Constructor for the class.
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct( $container = null ) {
		$this->container = $container ?: Config::get_container();
	}

}
