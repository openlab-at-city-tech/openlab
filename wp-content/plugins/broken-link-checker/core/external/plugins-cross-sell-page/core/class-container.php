<?php
/**
 * The Container class used for DI.
 *
 * @link    https://wpmudev.com/
 * @since   1.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV/Plugin_Cross_Sell
 *
 * @copyright (c) 2025, Incsub (http://incsub.com)
 */

namespace WPMUDEV\Modules\Plugin_Cross_Sell;

/**
 * Dependency Injection Container class.
 *
 * @since 1.0.0
 */
class Container {
	/**
	 * Services list.
	 *
	 * @var array
	 */
	private $services = array();

	/**
	 * Pushes a service into the container.
	 *
	 * @param string $key Service key.
	 * @param  mixed  $value Service.
	 * @return void
	 */
	public function set( string $key, $value ): void {
		$this->services[ $key ] = $value;
	}

	/**
	 * Fetches a service from the container.
	 *
	 * @param string $key Service key.
	 * @throws \InvalidArgumentException If service not found.
	 */
	public function get( string $key ) {
		if ( ! isset( $this->services[ $key ] ) ) {
			throw new \InvalidArgumentException( esc_html( "Service '{$key}' not found in container." ) );
		}

		return $this->services[ $key ];
	}

	/**
	 * Checks if a service is registered.
	 *
	 * @param string $key Service key.
	 * @return bool
	 */
	public function has( string $key ): bool {
		return isset( $this->services[ $key ] );
	}
}
