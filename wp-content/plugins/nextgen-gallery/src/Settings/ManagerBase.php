<?php

namespace Imagely\NGG\Settings;

abstract class ManagerBase implements \ArrayAccess {

	protected static $option_name = 'ngg_options';
	protected $_options           = [];
	protected $_defaults          = [];
	protected $_option_handlers   = [];

	abstract public function save();
	abstract public function destroy();
	abstract public function load();

	protected function __construct() {
		$this->load();
	}

	/**
	 * Adds a class to handle dynamic options
	 *
	 * @param string $klass
	 * @param array  $options
	 */
	public function add_option_handler( $klass, $options = [] ) {
		if ( ! is_array( $options ) ) {
			$options = [ $options ];
		}
		foreach ( $options as $option_name ) {
			$this->_option_handlers[ $option_name ] = $klass;
		}
	}

	/**
	 * Gets a handler used to provide a dynamic option
	 *
	 * @param string $option_name
	 * @return null|mixed
	 */
	protected function _get_option_handler( $option_name, $method = 'get' ) {
		$retval = null;

		if ( isset( $this->_option_handlers[ $option_name ] ) ) {
			if ( ! is_object( $this->_option_handlers[ $option_name ] ) ) {
				$klass                                  = $this->_option_handlers[ $option_name ];
				$this->_option_handlers[ $option_name ] = new $klass();
			}

			$retval = $this->_option_handlers[ $option_name ];

			if ( ! method_exists( $retval, $method ) ) {
				$retval = null;
			}
		}

		return $retval;
	}

	/**
	 * Gets the value of a particular setting
	 *
	 * @param $key
	 * @param null $default
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		$retval = $default;

		if ( ( $handler = $this->_get_option_handler( $key, 'get' ) ) ) {
			$retval = $handler->get( $key, $default );
		} elseif ( isset( $this->_options[ $key ] ) ) {
			$retval = $this->_options[ $key ];
		}

		// In case a stdObject has been passed in as a value, we want to only return scalar values or arrays.
		if ( is_object( $retval ) ) {
			$retval = (array) $retval;
		}

		return $retval;
	}

	/**
	 * Sets a setting to a particular value
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @return mixed
	 */
	public function set( $key, $value = null, $skip_handlers = false ) {
		if ( is_object( $value ) ) {
			$value = (array) $value;
		}

		if ( is_array( $key ) ) {
			foreach ( $key as $k => $v ) {
				$this->set( $k, $v );
			}
		} elseif ( ! $skip_handlers && ( $handler = $this->_get_option_handler( $key, 'set' ) ) ) {
			$handler->set( $key, $value );
		} else {
			$this->_options[ $key ] = $value;
		}

		return $this;
	}

	/**
	 * Deletes a setting
	 *
	 * @param string $key
	 */
	public function delete( $key ) {
		if ( ( $handler = $this->_get_option_handler( $key, 'delete' ) ) ) {
			$handler->delete( $key );
		} else {
			unset( $this->_options[ $key ] );
		}
	}

	/**
	 * Determines if a setting exists or not
	 *
	 * @param $key
	 * @return bool
	 */
	public function is_set( $key ) {
		return array_key_exists( $key, $this->_options );
	}

	/**
	 * Alias to is_set()
	 *
	 * @param $key
	 * @return bool
	 */
	public function exists( $key ) {
		return $this->is_set( $key );
	}

	public function does_not_exist( $key ) {
		return ! $this->exists( $key );
	}

	public function reset() {
		$this->_options  = [];
		$this->_defaults = [];
	}

	/**
	 * This function does two things:
	 * a) If a value hasn't been set for the specified key, or it's been set to a previously set
	 *    default value, then set this key to the value specified
	 * b) Sets a new default value for this key
	 */
	public function set_default_value( $key, $default ) {
		if ( ! isset( $this->_defaults[ $key ] ) ) {
			$this->_defaults[ $key ] = $default;
		}

		if ( is_null( $this->get( $key, null ) ) or $this->get( $key ) == $this->_defaults[ $key ] ) {
			$this->set( $key, $default );
		}

		$this->_defaults[ $key ] = $default;

		return $this->get( $key );
	}

	#[\ReturnTypeWillChange]
	public function offsetExists( $key ) {
		return $this->is_set( $key );
	}

	#[\ReturnTypeWillChange]
	public function offsetGet( $key ) {
		return $this->get( $key );
	}

	#[\ReturnTypeWillChange]
	public function offsetSet( $key, $value ) {
		return $this->set( $key, $value );
	}

	#[\ReturnTypeWillChange]
	public function offsetUnset( $key ) {
		$this->delete( $key );
	}

	public function __get( $key ) {
		return $this->get( $key );
	}

	public function __set( $key, $value ) {
		return $this->set( $key, $value );
	}

	public function __isset( $key ) {
		return $this->is_set( $key );
	}

	public function __toString() {
		return json_encode( $this->_options );
	}

	public function __toArray() {
		ksort( $this->_options );
		return $this->_options;
	}

	public function to_array() {
		return $this->__toArray();
	}

	public function to_json() {
		return json_encode( $this->_options );
	}

	public function from_json( $json ) {
		$this->_options = (array) json_decode( $json );
	}
}
