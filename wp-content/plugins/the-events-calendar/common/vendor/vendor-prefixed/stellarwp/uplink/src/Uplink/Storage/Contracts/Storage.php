<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Storage\Contracts;

use Closure;
use TEC\Common\StellarWP\Uplink\Storage\Exceptions\Invalid_Key_Exception;

interface Storage {

	/**
	 * Put a value in storage.
	 *
	 * @param string|int|float|mixed[]|object $key The storage key. Accepts any variable that can be json encoded.
	 * @param mixed                           $value The value to store.
	 * @param int                             $expire The storage lifespan in seconds.
	 *
	 * @throws Invalid_Key_Exception If passed an invalid storage key.
	 */
	public function set( $key, $value, int $expire = 0 ): bool;

	/**
	 * Get a value from storage.
	 *
	 * @param string|int|float|mixed[]|object $key The storage key. Accepts any variable that can be json encoded.
	 *
	 * @throws Invalid_Key_Exception If passed an invalid storage key.
	 *
	 * @return null|mixed Returns null if we can't find the storage value.
	 */
	public function get( $key );

	/**
	 * Delete a value from storage.
	 *
	 * @param string|int|float|mixed[]|object $key The storage key.
	 *
	 * @throws Invalid_Key_Exception If passed an invalid storage key.
	 */
	public function delete( $key ): bool;

	/**
	 * Get an item from storage, or execute the given Closure and store the result.
	 *
	 * @param string|int|float|mixed[]|object $key      The storage key.
	 * @param Closure                         $callback The callback used to generate and store the value.
	 * @param int                             $expire   The storage lifespan in seconds.
	 *
	 * @throws Invalid_Key_Exception If passed an invalid storage key.
	 *
	 * @return mixed The storage value.
	 */
	public function remember( $key, Closure $callback, int $expire = 0 );

	/**
	 * Retrieve an item from storage and delete it.
	 *
	 * @param string|int|float|mixed[]|object $key The storage key.
	 *
	 * @throws Invalid_Key_Exception If passed an invalid storage key.
	 */
	public function pull( $key );

}
