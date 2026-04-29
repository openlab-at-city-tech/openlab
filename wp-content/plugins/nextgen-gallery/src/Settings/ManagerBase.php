<?php

namespace Imagely\NGG\Settings;

/**
 * Base class for settings managers
 *
 * Provides common functionality for managing settings with dynamic option handlers,
 * default values, and array access capabilities.
 */
abstract class ManagerBase implements \ArrayAccess {

	/**
	 * The option name used for storing settings
	 *
	 * @var string
	 */
	protected static $option_name = 'ngg_options';

	/**
	 * Array of settings options
	 *
	 * @var array
	 */
	protected $_options = [];

	/**
	 * Array of default values for settings
	 *
	 * @var array
	 */
	protected $_defaults = [];

	/**
	 * Array of option handlers for dynamic options
	 *
	 * @var array
	 */
	protected $_option_handlers = [];

	/**
	 * Saves the settings
	 *
	 * @return void
	 */
	abstract public function save();

	/**
	 * Destroys the settings
	 *
	 * @return void
	 */
	abstract public function destroy();

	/**
	 * Loads the settings
	 *
	 * @return void
	 */
	abstract public function load();

	/**
	 * Constructor
	 */
	protected function __construct() {
		$this->load();
	}

	/**
	 * Adds a class to handle dynamic options
	 *
	 * @param string $klass The class name to handle the option.
	 * @param array  $options Array of option names to be handled by this class.
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
	 * @param string $option_name The option name to get the handler for.
	 * @param string $method The method to check for in the handler.
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
	 * @param string $key The setting key to retrieve.
	 * @param mixed  $default_value The default value if the setting is not found.
	 * @return mixed
	 */
	public function get( $key, $default_value = null ) {
		$retval = $default_value;

		$handler = $this->_get_option_handler( $key, 'get' );
		if ( $handler ) {
			$retval = $handler->get( $key, $default_value );
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
	 * @param string $key The setting key or an array of key-value pairs.
	 * @param mixed  $value The value to set.
	 * @param bool   $skip_handlers Whether to skip option handlers.
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
		} else {
			$handler = ! $skip_handlers ? $this->_get_option_handler( $key, 'set' ) : false;
			if ( $handler ) {
				$handler->set( $key, $value );
			} else {
				$this->_options[ $key ] = $value;
			}
		}

		return $this;
	}

	/**
	 * Deletes a setting
	 *
	 * @param string $key The setting key to delete.
	 */
	public function delete( $key ) {
		$handler = $this->_get_option_handler( $key, 'delete' );
		if ( $handler ) {
			$handler->delete( $key );
		} else {
			unset( $this->_options[ $key ] );
		}
	}

	/**
	 * Determines if a setting exists or not
	 *
	 * @param string $key The setting key to check.
	 * @return bool
	 */
	public function is_set( $key ) {
		return array_key_exists( $key, $this->_options );
	}

	/**
	 * Alias to is_set()
	 *
	 * @param string $key The setting key to check.
	 * @return bool
	 */
	public function exists( $key ) {
		return $this->is_set( $key );
	}

	/**
	 * Checks if a setting does not exist
	 *
	 * @param string $key The setting key to check.
	 * @return bool
	 */
	public function does_not_exist( $key ) {
		return ! $this->exists( $key );
	}

	/**
	 * Resets all settings and defaults
	 *
	 * @return void
	 */
	public function reset() {
		$this->_options  = [];
		$this->_defaults = [];
	}

	/**
	 * This function does two things:
	 * a) If a value hasn't been set for the specified key, or it's been set to a previously set
	 *    default value, then set this key to the value specified
	 * b) Sets a new default value for this key
	 *
	 * @param string $key The setting key.
	 * @param mixed  $default_value The default value to set.
	 * @return mixed
	 */
	public function set_default_value( $key, $default_value ) {
		if ( ! isset( $this->_defaults[ $key ] ) ) {
			$this->_defaults[ $key ] = $default_value;
		}

		if ( is_null( $this->get( $key, null ) ) || $this->get( $key ) == $this->_defaults[ $key ] ) {
			$this->set( $key, $default_value );
		}

		$this->_defaults[ $key ] = $default_value;

		return $this->get( $key );
	}

	/**
	 * Checks if an offset exists (ArrayAccess implementation)
	 *
	 * @param string $key The offset to check.
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $key ) {
		return $this->is_set( $key );
	}

	/**
	 * Gets an offset (ArrayAccess implementation)
	 *
	 * @param string $key The offset to retrieve.
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $key ) {
		return $this->get( $key );
	}

	/**
	 * Sets an offset (ArrayAccess implementation)
	 *
	 * @param string $key The offset to set.
	 * @param mixed  $value The value to set.
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $key, $value ) {
		return $this->set( $key, $value );
	}

	/**
	 * Unsets an offset (ArrayAccess implementation)
	 *
	 * @param string $key The offset to unset.
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $key ) {
		$this->delete( $key );
	}

	/**
	 * Magic method to get a setting
	 *
	 * @param string $key The setting key.
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * Magic method to set a setting
	 *
	 * @param string $key The setting key.
	 * @param mixed  $value The value to set.
	 * @return mixed
	 */
	public function __set( $key, $value ) {
		return $this->set( $key, $value );
	}

	/**
	 * Magic method to check if a setting is set
	 *
	 * @param string $key The setting key.
	 * @return bool
	 */
	public function __isset( $key ) {
		return $this->is_set( $key );
	}

	/**
	 * Converts the settings to a JSON string
	 *
	 * @return string
	 */
	public function __toString() {
		return wp_json_encode( $this->_options );
	}

	/**
	 * Converts the settings to an array
	 *
	 * @return array
	 */
	public function to_array() {
		ksort( $this->_options );
		return $this->_options;
	}

	/**
	 * Converts the settings to a JSON string
	 *
	 * @return string
	 */
	public function to_json() {
		return wp_json_encode( $this->_options );
	}

	/**
	 * Loads settings from a JSON string
	 *
	 * @param string $json The JSON string to load.
	 * @return void
	 */
	public function from_json( $json ) {
		$this->_options = (array) json_decode( $json );
	}
}
