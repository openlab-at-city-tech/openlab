<?php

namespace Imagely\NGG\DataMapper;

abstract class Model {

	use Validation;

	// This attribute is no longer used, but serialized objects created before the POPE -> namespace transition will
	// still retain this attribute and generate a warning with PHP 8.0 when hydrating the object.
	public $__defaults_set;

	public function __construct( \stdClass $object = null ) {
		if ( $object ) {
			foreach ( get_object_vars( $object ) as $key => $value ) {
				$this->$key = $value;
			}
		}

		$this->set_defaults();
	}

	abstract function get_mapper();

	/**
	 * This should be removed when POPE compat v1 is reached in Pro
	 *
	 * @deprecated
	 * @return bool|array
	 */
	public function validate() {
		return $this->validation();
	}

	public function validation() {
		return true;
	}

	public function set_defaults() {
		$mapper = $this->get_mapper();
		if ( method_exists( $mapper, 'set_defaults' ) ) {
			$mapper->set_defaults( $this );
		}
	}

	/**
	 * @return bool
	 */
	public function is_new() {
		return ! $this->id();
	}

	public function get_primary_key_column() {
		return 'id';
	}

	/**
	 * @param null|int|string $value (optional)
	 * @return mixed
	 */
	public function id( $value = null ) {
		$key = $this->get_primary_key_column();

		if ( $value ) {
			$this->$key = $value;
		}

		return $this->$key;
	}

	/**
	 * This should be removed when POPE compat v1 is reached in Pro
	 *
	 * @deprecated
	 * @return array
	 */
	public function get_errors() {
		return $this->validation();
	}

	/**
	 * Necessary for compatibility with some WP-Admin pages.
	 *
	 * @deprecated
	 */
	public function clear_errors() {
		return true;
	}

	/**
	 * Determines if a particular field for the object has errors
	 *
	 * @param string $property
	 * @return bool
	 */
	public function is_valid( $property = null ) {
		$errors = $this->validation();
		return ! ( is_array( $errors ) && isset( $errors[ $property ] ) );
	}

	/**
	 * @param array $updated_attributes
	 * @return int|bool Object ID or false upon failure
	 */
	public function save( $updated_attributes = [] ) {
		foreach ( $updated_attributes as $key => $value ) {
			$this->$key = $value;
		}

		return $this->get_mapper()->save( $this );
	}
}
