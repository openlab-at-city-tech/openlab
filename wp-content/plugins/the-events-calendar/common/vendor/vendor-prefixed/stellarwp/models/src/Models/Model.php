<?php

namespace TEC\Common\StellarWP\Models;

use InvalidArgumentException;
use JsonSerializable;
use TEC\Common\StellarWP\Models\Contracts\Arrayable;
use TEC\Common\StellarWP\Models\Contracts\Model as ModelInterface;

abstract class Model implements ModelInterface, Arrayable, JsonSerializable {
	// Define modes as powers of two so we can use them for bitwise operations.
	public const BUILD_MODE_STRICT = 0;
	public const BUILD_MODE_IGNORE_MISSING = 1;
	public const BUILD_MODE_IGNORE_EXTRA = 2;

	/**
	 * The model's properties.
	 *
	 * @var ModelPropertyCollection
	 */
	protected ModelPropertyCollection $propertyCollection;

	/**
	 * The model properties assigned to their types.
	 *
	 * @var array<string,string|array{0:string,1:mixed}>
	 */
	protected static array $properties = [];

	/**
	 * The model relationships assigned to their relationship types.
	 *
	 * @var array<string,string>
	 */
	protected static array $relationships = [];

	/**
	 * Cached property definitions per class.
	 *
	 * @var array<class-string<Model>,array<string,ModelPropertyDefinition>>
	 */
	private static array $cachedDefinitions = [];

	/**
	 * The model's relationships.
	 *
	 * @var ModelRelationshipCollection
	 */
	protected ModelRelationshipCollection $relationshipCollection;

	/**
	 * Cached relationship definitions per class.
	 *
	 * @var array<class-string<Model>,array<string,ModelRelationshipDefinition>>
	 */
	private static array $cachedRelationshipDefinitions = [];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $attributes Attributes.
	 */
	final public function __construct( array $attributes = [] ) {
		$this->propertyCollection = ModelPropertyCollection::fromPropertyDefinitions( static::getPropertyDefinitions(), $attributes );
		$this->relationshipCollection = ModelRelationshipCollection::fromRelationshipDefinitions( static::getRelationshipDefinitions() );
		$this->afterConstruct();
	}

	/**
	 * This method is meant to be overridden by the model to perform actions after the model is constructed.
	 *
	 * @since 2.0.0
	 */
	protected function afterConstruct(): void {
		return;
	}

	/**
	 * Casts the value for the type, used when constructing a model from query data. If the model needs to support
	 * additional types, especially class types, this method can be overridden.
	 *
	 * Note: Type casting is performed at runtime based on property definitions. PHPStan cannot statically verify
	 * the resulting types, so we suppress type-checking errors for the cast operations.
	 *
	 * @since 2.0.0 changed to static
	 *
	 * @param ModelPropertyDefinition $definition The property definition.
	 * @param mixed  $value The query data value to cast, probably a string.
	 * @param string $property The property being casted.
	 *
	 * @return mixed
	 */
	protected static function castValueForProperty( ModelPropertyDefinition $definition, $value, string $property ) {
		if ( $definition->isValidValue( $value ) || $value === null ) {
			return $value;
		}

		if ( $definition->canCast() ) {
			return $definition->cast( $value );
		}

		$type = $definition->getType();
		if ( count( $type ) !== 1 ) {
			throw new InvalidArgumentException( "Property '$property' has multiple types: " . implode( ', ', $type ) . ". To support additional types, implement a custom castValueForProperty() method." );
		}

		// Runtime type casting based on property definition - PHPStan cannot verify this statically
		switch ( $type[0] ) {
			case 'int':
				return (int) $value; // @phpstan-ignore-line
			case 'string':
				return (string) $value; // @phpstan-ignore-line
			case 'bool':
				return (bool) filter_var( $value, FILTER_VALIDATE_BOOLEAN );
			case 'array':
				return (array) $value;
			case 'float':
				return (float) filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION );
			default:
				Config::throwInvalidArgumentException( "Unexpected type: '{$type[0]}'. To support additional types, overload this method or use Definition casting." );
		}
	}

	/**
	 * Commit the changes to the properties.
	 *
	 * @since 2.0.0
	 */
	public function commitChanges(): void {
		$this->propertyCollection->commitChangedProperties();
	}

	/**
	 * Revert the changes to a specific property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key Property name.
	 */
	public function revertChange( string $key ): void {
		$this->propertyCollection->getOrFail( $key )->revertChanges();
	}

	/**
	 * Discard the changes to the properties.
	 *
	 * @since 2.0.0
	 */
	public function revertChanges(): void {
		$this->propertyCollection->revertChangedProperties();
	}

	/**
	 * A more robust, alternative way to define properties for the model than static::$properties.
	 *
	 * @return array<string,ModelPropertyDefinition>
	 */
	protected static function properties(): array {
		return [];
	}

	/**
	 * A more robust, alternative way to define relationships for the model than static::$relationships.
	 *
	 * @return array<string,ModelRelationshipDefinition>
	 */
	protected static function relationships(): array {
		return [];
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
		$this->propertyCollection->setValues( $attributes );

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
	 */
	public function getAttribute( string $key, $default = null ) {
		$property = $this->propertyCollection->getOrFail( $key );

		return $property->isSet() ? $property->getValue() : $default;
	}

	/**
	 * Returns the attributes that have been changed since last sync.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string,mixed>
	 */
	public function getDirty() : array {
		return $this->propertyCollection->getDirtyValues();
	}

	public static function getPropertyDefinition( string $key ): ModelPropertyDefinition {
		$definitions = static::getPropertyDefinitions();

		if ( ! isset( $definitions[ $key ] ) ) {
			throw new InvalidArgumentException( 'Property ' . $key . ' does not exist.' );
		}

		return $definitions[ $key ];
	}

	/**
	 * Generates the property definitions for the model.
	 *
	 * This method processes the raw property definitions from static::$properties and static::properties(),
	 * converting shorthand notation to ModelPropertyDefinition instances and locking them.
	 *
	 * Child classes can override this method to customize how property definitions are generated,
	 * either by completely replacing the logic or by calling parent::generatePropertyDefinitions()
	 * and modifying the results.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string,ModelPropertyDefinition>
	 */
	protected static function generatePropertyDefinitions(): array {
		$definitions = array_merge( static::$properties, static::properties() );
		/** @var array<string,ModelPropertyDefinition> $processedDefinitions */
		$processedDefinitions = [];

		foreach ( $definitions as $key => $definition ) {
			if ( ! is_string( $key ) ) {
				throw new InvalidArgumentException( 'Property key must be a string.' );
			}

			if ( ! $definition instanceof ModelPropertyDefinition ) {
				$definition = ModelPropertyDefinition::fromShorthand( $definition );
			}

			$processedDefinitions[ $key ] = $definition->lock();
		}

		return $processedDefinitions;
	}

	/**
	 * Returns the parsed property definitions for the model.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string,ModelPropertyDefinition>
	 */
	public static function getPropertyDefinitions(): array {
		$class = static::class;

		if ( ! isset( self::$cachedDefinitions[ $class ] ) ) {
			self::$cachedDefinitions[ $class ] = static::generatePropertyDefinitions();
		}

		return self::$cachedDefinitions[ $class ];
	}

	/**
	 * Generates the relationship definitions for the model.
	 *
	 * This method processes the raw relationship definitions from static::$relationships and static::relationships(),
	 * converting shorthand notation to ModelRelationshipDefinition instances and locking them.
	 *
	 * Child classes can override this method to customize how relationship definitions are generated,
	 * either by completely replacing the logic or by calling parent::generateRelationshipDefinitions()
	 * and modifying the results.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string,ModelRelationshipDefinition>
	 */
	protected static function generateRelationshipDefinitions(): array {
		$definitions = array_merge( static::$relationships, static::relationships() );
		/** @var array<string,ModelRelationshipDefinition> $processedDefinitions */
		$processedDefinitions = [];

		foreach ( $definitions as $key => $definition ) {
			if ( ! is_string( $key ) ) {
				throw new InvalidArgumentException( 'Relationship key must be a string.' );
			}

			if ( ! $definition instanceof ModelRelationshipDefinition ) {
				$definition = ModelRelationshipDefinition::fromShorthand( $key, $definition );
			}

			$processedDefinitions[ $key ] = $definition->lock();
		}

		return $processedDefinitions;
	}

	/**
	 * Returns the parsed relationship definitions for the model.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string,ModelRelationshipDefinition>
	 */
	public static function getRelationshipDefinitions(): array {
		$class = static::class;

		if ( ! isset( self::$cachedRelationshipDefinitions[ $class ] ) ) {
			self::$cachedRelationshipDefinitions[ $class ] = static::generateRelationshipDefinitions();
		}

		return self::$cachedRelationshipDefinitions[ $class ];
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
	public function getOriginal( ?string $key = null ) {
		return $key ? $this->propertyCollection->getOrFail( $key )->getOriginalValue() : $this->propertyCollection->getOriginalValues();
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
		return $this->propertyCollection->isSet( $key );
	}

	/**
	 * Checks if a method exists that returns a ModelQueryBuilder instance.
	 *
	 * @since 2.0.0
	 *
	 * @param string $method Method name.
	 *
	 * @return bool
	 */
	protected function hasRelationshipMethod( string $method ): bool {
		if ( ! method_exists( $this, $method ) ) {
			return false;
		}

		try {
			$reflectionMethod = new \ReflectionMethod( $this, $method );
			$returnType = $reflectionMethod->getReturnType();

			if ( ! $returnType instanceof \ReflectionNamedType ) {
				return false;
			}

			$typeName = $returnType->getName();

			return $typeName === ModelQueryBuilder::class || is_subclass_of( $typeName, ModelQueryBuilder::class );
		} catch ( \ReflectionException $e ) {
			return false;
		}
	}

	/**
	 * Fetches a relationship from the database.
	 *
	 * This method can be overridden by subclasses to customize how relationships are loaded.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key Relationship name.
	 *
	 * @return Model|list<Model>|null
	 */
	protected function fetchRelationship( string $key ) {
		if ( ! $this->hasRelationshipMethod( $key ) ) {
			$exception = Config::getInvalidArgumentException();
			throw new $exception( "$key() does not exist." );
		}

		/** @var ModelQueryBuilder<Model> $queryBuilder */
		$queryBuilder = $this->$key();
		$definition = $this->relationshipCollection->getOrFail( $key )->getDefinition();

		if ( $definition->isMultiple() ) {
			$result = $queryBuilder->getAll();
			/** @var list<Model>|null $result */
			return $result;
		}

		$result = $queryBuilder->get();
		/** @var Model|null $result */
		return $result;
	}

	/**
	 * Returns a relationship.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Relationship name.
	 *
	 * @return Model|list<Model>|null
	 */
	protected function getRelationship( string $key ) {
		return $this->relationshipCollection->getOrFail( $key )->getValue( fn() => $this->fetchRelationship( $key ) );
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
		return $this->propertyCollection->has( $key );
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
		return $this->relationshipCollection->has( $key ) && $this->relationshipCollection->isLoaded( $key );
	}

	/**
	 * Purges the entire relationship cache.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function purgeRelationshipCache(): void {
		$this->relationshipCollection->purgeAll();
	}

	/**
	 * Purges a specific relationship from the cache.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key Relationship name.
	 *
	 * @return void
	 */
	protected function purgeRelationship( string $key ): void {
		if ( ! $this->relationshipCollection->has( $key ) ) {
			Config::throwInvalidArgumentException( "Relationship '$key' is not defined on this model." );
		}

		$this->relationshipCollection->purge( $key );
	}

	/**
	 * Updates the cached value for a given relationship.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key Relationship name.
	 * @param Model|list<Model>|null $value The relationship value to cache.
	 *
	 * @return void
	 */
	public function setCachedRelationship( string $key, $value ): void {
		$relationship = $this->relationshipCollection->get( $key );

		if ( ! $relationship ) {
			Config::throwInvalidArgumentException( "Relationship '$key' is not defined on this model." );
		}

		$relationship->setValue( $value );
	}

	/**
	 * Determines if the model has the given property.
	 *
	 * @since 2.0.0 changed to static
	 * @since 1.0.0
	 *
	 * @param string $key Property name.
	 *
	 * @return bool
	 */
	public static function hasProperty( string $key ) : bool {
		return isset( static::getPropertyDefinitions()[ $key ] );
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
	public function isClean( ?string $attribute = null ) : bool {
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
	public function isDirty( ?string $attribute = null ) : bool {
		if ( ! $attribute ) {
			return $this->propertyCollection->isDirty();
		}

		return $this->propertyCollection->getOrFail( $attribute )->isDirty();
	}

	/**
	 * Validates an attribute to a PHP type.
	 *
	 * @since 2.0.0
	 * @since 1.0.0
	 *
	 * @param string $key   Property name.
	 * @param mixed  $value Property value.
	 *
	 * @return bool
	 */
	public static function isPropertyTypeValid( string $key, $value ) : bool {
		return static::getPropertyDefinition( $key )->isValidValue( $value );
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
	 * Constructs a model instance from database query data.
	 *
	 * @param array<string,mixed>|object $data
	 * @param int $mode The level of strictness to take when constructing the object, by default it will ignore extra keys but error on missing keys.
	 * @return static
	 */
	public static function fromData($data, $mode = self::BUILD_MODE_IGNORE_EXTRA) {
		if ( ! is_object( $data ) && ! is_array( $data ) ) {
			Config::throwInvalidArgumentException( 'Query data must be an object or array' );
		}

		$data = (array) $data;

		$properties = array_merge( static::$properties, static::properties() );

		// If we're not ignoring extra keys, check for them and throw an exception if any are found.
		if ( ! ($mode & self::BUILD_MODE_IGNORE_EXTRA) ) {
			$extraKeys = array_diff_key( (array) $data, $properties );
			if ( ! empty( $extraKeys ) ) {
				Config::throwInvalidArgumentException( 'Query data contains extra keys: ' . implode( ', ', array_keys( $extraKeys ) ) );
			}
		}

		if ( ! ($mode & self::BUILD_MODE_IGNORE_MISSING) ) {
			$missingKeys = array_diff_key( $properties, (array) $data );
			if ( ! empty( $missingKeys ) ) {
				Config::throwInvalidArgumentException( 'Query data is missing keys: ' . implode( ', ', array_keys( $missingKeys ) ) );
			}
		}

		$initialValues = [];

		foreach ( $properties as $key => $_ ) {
			if ( ! array_key_exists( $key, $data ) ) {
				// Skip missing properties when BUILD_MODE_IGNORE_MISSING is set
				if ( $mode & self::BUILD_MODE_IGNORE_MISSING ) {
					continue;
				}
				Config::throwInvalidArgumentException( "Property '$key' does not exist." );
			}

			$initialValues[ $key ] = static::castValueForProperty( static::getPropertyDefinition( $key ), $data[ $key ], $key );
		}

		return new static( $initialValues );
	}

	/**
	 * Returns the property keys.
	 *
	 * @since 1.0.0
	 *
	 * @return list<string>
	 */
	public static function propertyKeys() : array {
		return array_keys( array_merge( static::$properties, static::properties() ) );
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
		$this->propertyCollection->getOrFail( $key )->setValue( $value );

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
	 * This is considered an alias of `commitChanges()` and is here for backwards compatibility.
	 *
	 * @since 1.0.0
	 *
	 * @return ModelInterface
	 */
	public function syncOriginal() : ModelInterface {
		$this->commitChanges();

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
		return $this->propertyCollection->getValues();
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
		if ( $this->relationshipCollection->has( $key ) ) {
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
		return $this->propertyCollection->isSet( $key );
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

	/**
	 * Unset a property.
	 *
	 * @since 2.0.0
	 */
	public function __unset( string $key ) {
		$this->propertyCollection->unsetProperty( $key );
	}
}
