<?php

namespace Imagely\NGG\DataMapper;

/**
 * Abstract base model class.
 *
 * Provides common functionality for all data models in the NextGEN Gallery system.
 */
#[\AllowDynamicProperties]
abstract class Model {

	use Validation;

	/**
	 * Legacy attribute for serialized objects compatibility.
	 *
	 * This attribute is no longer used, but serialized objects created before the POPE -> namespace transition will
	 * still retain this attribute and generate a warning with PHP 8.0 when hydrating the object.
	 *
	 * @var mixed
	 */
	public $__defaults_set;

	/**
	 * Constructor.
	 *
	 * Initializes the model with optional data from a stdClass object.
	 *
	 * @param \stdClass|null $obj Optional object data to populate the model.
	 */
	// phpcs:ignore PHPCompatibility.FunctionDeclarations.NewNullableTypes -- Explicit nullable required for PHP 8.4.
	public function __construct( ?\stdClass $obj = null ) {
		if ( $obj ) {
			foreach ( get_object_vars( $obj ) as $key => $value ) {
					$this->$key = $value;
			}
		}

		$this->set_defaults();
	}

	/**
	 * Gets the mapper instance for this model.
	 *
	 * @return mixed The mapper instance.
	 */
	abstract public function get_mapper();

	/**
	 * Legacy validation method for POPE compatibility.
	 *
	 * This should be removed when POPE compat v1 is reached in Pro.
	 *
	 * @deprecated
	 * @return bool|array Validation result.
	 */
	public function validate() {
		return $this->validation();
	}

	/**
	 * Validates the model data.
	 *
	 * @return bool|array True if valid, array of errors if invalid.
	 */
	public function validation() {
		return true;
	}

	/**
	 * Sets default values for the model.
	 */
	public function set_defaults() {
		$mapper = $this->get_mapper();
		if ( method_exists( $mapper, 'set_defaults' ) ) {
			$mapper->set_defaults( $this );
		}
	}

	/**
	 * Checks if this is a new model instance.
	 *
	 * @return bool True if the model is new (has no ID), false otherwise.
	 */
	public function is_new() {
		return ! $this->id();
	}

	/**
	 * Gets the primary key column name for this model.
	 *
	 * @return string The primary key column name.
	 */
	public function get_primary_key_column() {
		return 'id';
	}

	/**
	 * Gets or sets the model's ID.
	 *
	 * @param null|int|string $value Optional value to set as the ID.
	 * @return mixed The model's ID value.
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
	 * Determines if a particular field for the object has errors.
	 *
	 * @param string|null $property The property name to check.
	 * @return bool True if valid, false if has errors.
	 */
	public function is_valid( $property = null ) {
		$errors = $this->validation();
		return ! ( is_array( $errors ) && isset( $errors[ $property ] ) );
	}

	/**
	 * Saves the model with optional updated attributes.
	 *
	 * @param array $updated_attributes Optional array of attributes to update before saving.
	 * @return int|bool Object ID or false upon failure.
	 */
	public function save( $updated_attributes = [] ) {
		foreach ( $updated_attributes as $key => $value ) {
			$this->$key = $value;
		}

		return $this->get_mapper()->save( $this );
	}
}
