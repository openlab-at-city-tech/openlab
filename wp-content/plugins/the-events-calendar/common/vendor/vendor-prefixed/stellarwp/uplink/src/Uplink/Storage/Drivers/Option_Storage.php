<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Storage\Drivers;

use Closure;
use InvalidArgumentException;
use TEC\Common\StellarWP\Uplink\Storage\Contracts\Storage;
use TEC\Common\StellarWP\Uplink\Storage\Exceptions\Invalid_Key_Exception;
use TEC\Common\StellarWP\Uplink\Storage\Traits\With_Key_Formatter;

class Option_Storage implements Storage {

	use With_Key_Formatter;

	/**
	 * The option name to store in the wp_options table.
	 *
	 * @see Config::set_hook_prefix()
	 *
	 * @var string
	 */
	protected $option_name;

	/**
	 * @param  string  $option_name  The option name as set via Config::set_token_auth_prefix().
	 */
	public function __construct( string $option_name ) {
		if ( ! $option_name ) {
			throw new InvalidArgumentException(
				__( 'You must set a token prefix with StellarWP\Uplink\Config::set_hook_prefix() before using Option Storage.',
					'%TEXTDOMAIN%' )
			);
		}

		$this->option_name = $option_name;
	}

	/**
	 * Put a value in storage.
	 *
	 * @param  string|int|float|mixed[]|object  $key     The storage key. Accepts any variable that can be json encoded.
	 * @param  mixed                            $value   The value to store.
	 * @param  int                              $expire  The storage lifespan in seconds.
	 *
	 * @throws Invalid_Key_Exception If passed an invalid storage key.
	 */
	public function set( $key, $value, int $expire = 0 ): bool {
		$data = (array) get_option( $this->option_name, [] );

		$data[ $this->key( $key ) ] = [
			'value'      => $value,
			'expiration' => $expire > 0 ? time() + $expire : 0,
		];

		return update_option( $this->option_name, $data );
	}

	/**
	 * Get a value from storage.
	 *
	 * @param  string|int|float|mixed[]|object  $key  The storage key. Accepts any variable that can be json encoded.
	 *
	 * @throws Invalid_Key_Exception If passed an invalid storage key.
	 *
	 * @return null|mixed Returns null if we can't find the storage value.
	 */
	public function get( $key ) {
		$data      = (array) get_option( $this->option_name, [] );
		$transient = $data[ $this->key( $key ) ] ?? [];

		if ( isset( $transient['expiration'] ) && $transient['expiration'] > 0 && $transient['expiration'] < time() ) {
			$this->delete( $key );

			return null;
		}

		return $transient['value'] ?? null;
	}

	/**
	 * Delete a value from storage.
	 *
	 * @param  string|int|float|mixed[]|object  $key  The storage key.
	 *
	 * @throws Invalid_Key_Exception If passed an invalid storage key.
	 */
	public function delete( $key ): bool {
		$data = (array) get_option( $this->option_name, [] );

		unset( $data[ $this->key( $key ) ] );

		return update_option( $this->option_name, $data );
	}

	/**
	 * Get an item from storage, or execute the given Closure and store the result.
	 *
	 * @param  string|int|float|mixed[]|object  $key       The storage key.
	 * @param  Closure                          $callback  The callback used to generate and store the value.
	 * @param  int                              $expire    The storage lifespan in seconds.
	 *
	 * @throws Invalid_Key_Exception If passed an invalid storage key.
	 *
	 * @return mixed The storage value.
	 */
	public function remember( $key, Closure $callback, int $expire = 0 ) {
		$value = $this->get( $key );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		$value = $callback();

		$this->set( $key, $value, $expire );

		return $value;
	}

	/**
	 * Retrieve an item from storage and delete it.
	 *
	 * @param  string|int|float|mixed[]|object  $key  The storage key.
	 *
	 * @throws Invalid_Key_Exception If passed an invalid storage key.
	 */
	public function pull( $key ) {
		$value = $this->get( $key );

		$this->delete( $key );

		return $value;
	}

}
