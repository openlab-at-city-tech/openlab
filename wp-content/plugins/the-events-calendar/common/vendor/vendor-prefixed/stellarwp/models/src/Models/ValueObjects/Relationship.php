<?php

namespace TEC\Common\StellarWP\Models\ValueObjects;

use TEC\Common\StellarWP\Models\Config;

/**
 * Model Relationship Value Object
 *
 * @since 2.19.6
 *
 * @method static self HAS_ONE() Create a HAS_ONE relationship.
 * @method static self HAS_MANY() Create a HAS_MANY relationship.
 * @method static self MANY_TO_MANY() Create a MANY_TO_MANY relationship.
 * @method static self BELONGS_TO() Create a BELONGS_TO relationship.
 * @method static self BELONGS_TO_MANY() Create a BELONGS_TO_MANY relationship.
 * @method bool isHasOne() Check if the relationship is a HAS_ONE type.
 * @method bool isHasMany() Check if the relationship is a HAS_MANY type.
 * @method bool isManyToMany() Check if the relationship is a MANY_TO_MANY type.
 * @method bool isBelongsTo() Check if the relationship is a BELONGS_TO type.
 * @method bool isBelongsToMany() Check if the relationship is a BELONGS_TO_MANY type.
 */
class Relationship {
	const HAS_ONE         = 'has-one';
	const HAS_MANY        = 'has-many';
	const MANY_TO_MANY    = 'many-to-many';
	const BELONGS_TO      = 'belongs-to';
	const BELONGS_TO_MANY = 'belongs-to-many';

	/**
	 * Cached instances of relationship types.
	 *
	 * @var array<string, self>
	 */
	private static array $instances = [];

	/**
	 * The relationship type value.
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * Private constructor to enforce factory method usage.
	 *
	 * @param string $value The relationship type value.
	 */
	private function __construct( string $value ) {
		$this->value = $value;
	}

	/**
	 * Create or retrieve a relationship instance.
	 *
	 * @param string $value The relationship type value.
	 *
	 * @return self
	 */
	private static function getInstance( string $value ): self {
		if ( ! self::isValid( $value ) ) {
			Config::throwInvalidArgumentException( "Invalid relationship type: {$value}" );
		}

		if ( ! isset( self::$instances[ $value ] ) ) {
			self::$instances[ $value ] = new self( $value );
		}

		return self::$instances[ $value ];
	}

	/**
	 * Create a relationship from a string value.
	 *
	 * @param string $value The relationship type value.
	 *
	 * @return self
	 */
	public static function from( string $value ): self {
		return self::getInstance( $value );
	}

	/**
	 * Magic method to handle factory methods for relationship types.
	 *
	 * @param string $name The method name (should match a constant).
	 * @param array<mixed> $arguments The method arguments (none expected).
	 *
	 * @return self
	 */
	public static function __callStatic( string $name, array $arguments ): self {
		$value = strtolower( str_replace( '_', '-', $name ) );
		return self::getInstance( $value );
	}

	/**
	 * Get the relationship type value.
	 *
	 * @return string
	 */
	public function getValue(): string {
		return $this->value;
	}

	/**
	 * Magic method to handle "is" methods for relationship type checking.
	 *
	 * @param string $name The method name (should be "is" + constant name in camelCase).
	 * @param array<mixed> $arguments The method arguments (none expected).
	 *
	 * @return bool
	 */
	public function __call( string $name, array $arguments ): bool {
		if ( strpos( $name, 'is' ) === 0 ) {
			// Convert "isHasOne" to "has-one"
			$type = substr( $name, 2 ); // Remove "is" prefix
			$converted = preg_replace( '/([a-z])([A-Z])/', '$1-$2', $type );
			$value = strtolower( $converted !== null ? $converted : $type );

			if ( self::isValid( $value ) ) {
				return $this->value === $value;
			}
		}

		Config::throwInvalidArgumentException( "Method {$name} does not exist on Relationship." );
	}

	/**
	 * Check if the relationship returns a single model.
	 *
	 * @return bool
	 */
	public function isSingle(): bool {
		return in_array( $this->value, [
			self::HAS_ONE,
			self::BELONGS_TO,
		] );
	}

	/**
	 * Check if the relationship returns multiple models.
	 *
	 * @return bool
	 */
	public function isMultiple(): bool {
		return in_array( $this->value, [
			self::HAS_MANY,
			self::BELONGS_TO_MANY,
			self::MANY_TO_MANY,
		] );
	}

	/**
	 * Check if a value is a valid relationship type.
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public static function isValid( string $value ): bool {
		return in_array( $value, [
			self::HAS_ONE,
			self::HAS_MANY,
			self::MANY_TO_MANY,
			self::BELONGS_TO,
			self::BELONGS_TO_MANY,
		] );
	}

	/**
	 * Get all relationship type instances.
	 *
	 * @return self[]
	 */
	public static function all(): array {
		return [
			self::HAS_ONE(),
			self::HAS_MANY(),
			self::MANY_TO_MANY(),
			self::BELONGS_TO(),
			self::BELONGS_TO_MANY(),
		];
	}

	/**
	 * Convert to string.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return $this->value;
	}
}
