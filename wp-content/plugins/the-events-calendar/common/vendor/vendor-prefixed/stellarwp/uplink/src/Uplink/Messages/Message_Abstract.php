<?php

namespace TEC\Common\StellarWP\Uplink\Messages;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Uplink\Config;

abstract class Message_Abstract {
	/**
	 * Container instance.
	 *
	 * @since 1.0.0
	 *
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param ContainerInterface|null $container Container instance.
	 */
	public function __construct( $container = null ) {
		$this->container = $container ?: Config::get_container();
	}

	/**
	 * Gets the fully built message.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	abstract public function get(): string;

	/**
	 * Returns the message as a string.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function __toString(): string {
		return $this->get();
	}
}
