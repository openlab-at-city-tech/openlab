<?php

namespace TEC\Common\StellarWP\Models;

use JsonSerializable;
use RuntimeException;
use TEC\Common\StellarWP\Models\Contracts\Arrayable;
use TEC\Common\StellarWP\Models\Contracts\Model as ModelInterface;
use TEC\Common\StellarWP\Models\ValueObjects\Relationship;

abstract class Model implements ModelInterface, Arrayable, JsonSerializable {
	/**
	 * The model's attributes.
	 *
	 * @var array<string,mixed>
	 */
	protected $attributes = [];

	/**
	 * The model attribute's original state.
	 *
	 * @var array<string,mixed>
	 */
	protected $original = [];

	/**
	 * The model properties assigned to their types.
	 *
	 * @var array<string,string>
	 */
	protected $properties = [];

	/**
	 * The model relationships assigned to their relationship types.
	 *
	 * @var array<string,string>
	 */
	protected $relationships = [];

	/**
	 * Relationships that have already been loaded and don't need to be loaded again.
	 *
	 * @var Model[]
	 */
	private $cachedRelations = [];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $attributes Attributes.
	 */
	public function __construct( array $attributes = [] ) {
		$this->fill( array_merge( $this->getPropertyDefaults(), $attributes ) );

		$this->syncOriginal();
	}

	/**
	 * Fills the model with an array of attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $attributes Attributes.
	 *
	 * @return ModelInterface
	 */
	public function fill( array $attributes ) : ModelInterface {
		foreach ( $attributes as $key => $value ) {
			$this->setAttribute( $key, $value );
		}

		return $this;
	}

	/**
	 * Returns an attribute from the model.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     Attribute name.
	 * @param mixed  $default Default value. Default is null.
	 *
	 * @return mixed
	 *
	 * @throws RuntimeException
	 */
	public function getAttribute( string $key, $default = null ) {
		$this->validatePropertyExists( $key );

		return $this->attributes[ $key ] ?? $default;
	}

	/**
	 * Returns the attributes that have been changed since last sync.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string,mixed>
	 */
	public function getDirty() : array {
		$dirty = [];

		foreach ( $this->attributes as $key => $value ) {
			if ( ! array_key_exists( $key, $this->original ) || $value !== $this->original[ $key ] ) {
				$dirty[ $key ] = $value;
			}
		}

		return $dirty;
	}

	/**
	 * Returns the model's original attribute values.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $key Attribute name.
	 *
	 * @return mixed|array
	 */
	public function getOriginal( string $key = null ) {
		return $key ? $this->original[ $key ] : $this->original;
	}

	/**
	 * Whether the property is set or not. This is different from isset() because this considers a `null` value as
	 * being set. Defaults are considered set as well.
	 *
	 * @since 1.2.2
	 *
	 * @return boolean
	 */
	public function isSet( string $key ): bool {
		return array_key_exists( $key, $this->attributes ) || $this->hasDefault( $key );
	}

	/**
	 * Check if there is a default value for a property.
	 *
	 * @since 1.2.2
	 *
	 * @param string $key Property name.
	 *
	 * @return bool
	 */
	protected function hasDefault( string $key ): bool {
		return is_array( $this->properties[ $key ] ) && array_key_exists( 1, $this->properties[ $key ] );
	}

	/**
	 * Returns the default value for a property if one is provided, otherwise null.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Property name.
	 *
	 * @return mixed|null
	 */
	protected function getPropertyDefault( string $key ) {
		if ( $this->hasDefault( $key ) ) {
			return $this->properties[ $key ][1];
		}

		return null;
	}

	/**
	 * Returns the defaults for all the properties. If a default is omitted it defaults to null.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string,mixed>
	 */
	protected function getPropertyDefaults() : array {
		$defaults = [];
		foreach ( array_keys( $this->properties ) as $property ) {
			if ( $this->hasDefault( $property ) ) {
				$defaults[ $property ] = $this->getPropertyDefault( $property );
			}
		}

		return $defaults;
	}

	/**
	 * Get the property type
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Property name.
	 *
	 * @return string
	 */
	protected function getPropertyType( string $key ) : string {
		$type = is_array( $this->properties[ $key ] ) ? $this->properties[ $key ][0] : $this->properties[ $key ];

		return strtolower( trim( $type ) );
	}

	/**
	 * Returns a relationship.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Relationship name.
	 *
	 * @return Model|Model[]
	 */
	protected function getRelationship( string $key ) {
		if ( ! is_callable( [ $this, $key ] ) ) {
			$exception = Config::getInvalidArgumentException();
			throw new $exception( "$key() does not exist." );
		}

		if ( $this->hasCachedRelationship( $key ) ) {
			return $this->cachedRelations[ $key ];
		}

		$relationship = $this->relationships[ $key ];

		switch ( $relationship ) {
			case Relationship::BELONGS_TO:
			case Relationship::HAS_ONE:
				return $this->cachedRelations[ $key ] = $this->$key()->get();
			case Relationship::HAS_MANY:
			case Relationship::BELONGS_TO_MANY:
			case Relationship::MANY_TO_MANY:
				return $this->cachedRelations[ $key ] = $this->$key()->getAll();
		}

		return null;
	}

	/**
	 * Returns true if an attribute exists. Otherwise, false.
	 *
	 * @since 1.1.0
	 *
	 * @param string $key Attribute name.
	 *
	 * @return bool
	 */
	protected function hasAttribute( string $key ) : bool {
		return array_key_exists( $key, $this->attributes );
	}

	/**
	 * Checks whether a relationship has already been loaded.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Relationship name.
	 *
	 * @return bool
	 */
	protected function hasCachedRelationship( string $key ) : bool {
		return array_key_exists( $key, $this->cachedRelations );
	}

	/**
	 * Determines if the model has the given property.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Property name.
	 *
	 * @return bool
	 */
	public function hasProperty( string $key ) : bool {
		return array_key_exists( $key, $this->properties );
	}

	/**
	 * Determine if a given attribute is clean.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $attribute Attribute name.
	 *
	 * @return bool
	 */
	public function isClean( string $attribute = null ) : bool {
		return ! $this->isDirty( $attribute );
	}

	/**
	 * Determine if a given attribute is dirty.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $attribute Attribute name.
	 *
	 * @return bool
	 */
	public function isDirty( string $attribute = null ) : bool {
		if ( ! $attribute ) {
			return (bool) $this->getDirty();
		}

		return array_key_exists( $attribute, $this->getDirty() );
	}

	/**
	 * Validates an attribute to a PHP type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   Property name.
	 * @param mixed  $value Property value.
	 *
	 * @return bool
	 */
	public function isPropertyTypeValid( string $key, $value ) : bool {
		if ( is_null( $value ) ) {
			return true;
		}

		$type = $this->getPropertyType( $key );

		switch ( $type ) {
			case 'int':
				return is_int( $value );
			case 'string':
				return is_string( $value );
			case 'bool':
				return is_bool( $value );
			case 'array':
				return is_array( $value );
			case 'float':
				return is_float( $value );
			case 'number':
				return is_int( $value ) || is_float( $value );
			default:
				return $value instanceof $type;
		}
	}

	/**
	 * Returns the object vars.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string,mixed>
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return get_object_vars( $this );
	}

	/**
	 * Returns the property keys.
	 *
	 * @since 1.0.0
	 *
	 * @return int[]|string[]
	 */
	public static function propertyKeys() : array {
		return array_keys( ( new static() )->properties );
	}

	/**
	 * Sets an attribute on the model.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   Attribute name.
	 * @param mixed  $value Attribute value.
	 *
	 * @return ModelInterface
	 */
	public function setAttribute( string $key, $value ) : ModelInterface {
		$this->validatePropertyExists( $key );
		$this->validatePropertyType( $key, $value );

		$validation_method = 'validate_' . $key;
		if ( method_exists( $this, $validation_method ) ) {
			$this->$validation_method( $value );
		}

		$this->attributes[ $key ] = $value;

		return $this;
	}

	/**
	 * Sets multiple attributes on the model.
	 *
	 * @since 1.2.0
	 *
	 * @param array<string,mixed> $attributes Attributes to set.
	 *
	 * @return ModelInterface
	 */
	public function setAttributes( array $attributes ) : ModelInterface {
		foreach ( $attributes as $key => $value ) {
			$this->setAttribute( $key, $value );
		}

		return $this;
	}

	/**
	 * Syncs the original attributes with the current.
	 *
	 * @since 1.0.0
	 *
	 * @return ModelInterface
	 */
	public function syncOriginal() : ModelInterface {
		$this->original = $this->attributes;

		return $this;
	}

	/**
	 * Returns attributes.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string,mixed>
	 */
	public function toArray() : array {
		return $this->attributes;
	}

	/**
	 * Validates that the given property exists
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Property name.
	 *
	 * @return void
	 */
	protected function validatePropertyExists( string $key ) {
		if ( ! $this->hasProperty( $key ) ) {
			$exception = Config::getInvalidArgumentException();
			throw new $exception( "Invalid property. '$key' does not exist." );
		}
	}

	/**
	 * Validates that the given value is a valid type for the given property.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   Property name.
	 * @param mixed  $value Property value.
	 *
	 * @return void
	 */
	protected function validatePropertyType( string $key, $value ) {
		if ( ! $this->isPropertyTypeValid( $key, $value ) ) {
			$type = $this->getPropertyType( $key );

			$exception = Config::getInvalidArgumentException();
			throw new $exception( "Invalid attribute assignment. '$key' should be of type: '$type'" );
		}
	}

	/**
	 * Dynamically retrieves attributes on the model.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Attribute name.
	 *
	 * @return mixed
	 */
	public function __get( string $key ) {
		if ( array_key_exists( $key, $this->relationships ) ) {
			return $this->getRelationship( $key );
		}

		return $this->getAttribute( $key );
	}

	/**
	 * Determines if an attribute exists on the model.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Attribute name.
	 *
	 * @return bool
	 */
	public function __isset( string $key ) {
		return isset( $this->attributes[ $key ] );
	}

	/**
	 * Dynamically sets attributes on the model.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   Attribute name.
	 * @param mixed  $value Attribute value.
	 *
	 * @return void
	 */
	public function __set( string $key, $value ) {
		$this->setAttribute( $key, $value );
	}
}
