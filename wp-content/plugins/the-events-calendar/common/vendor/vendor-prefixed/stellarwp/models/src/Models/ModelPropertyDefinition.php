<?php

declare(strict_types=1);

namespace TEC\Common\StellarWP\Models;

use Closure;
use InvalidArgumentException;
use RuntimeException;

/**
 * Defines a model property with its type, default value, and validation rules.
 *
 * @since 2.0.0
 */
class ModelPropertyDefinition {
	/**
	 * The default value of the property.
	 *
	 * @since 2.0.0
	 *
	 * @var mixed|Closure
	 */
	private $default;

	/**
	 * The method to cast the property value.
	 *
	 * @since 2.0.0
	 *
	 * @var Closure A closure that accepts the value and property instance as parameters and returns the cast value.
	 */
	private Closure $castMethod;

	/**
	 * Whether the definition is locked. Once locked, the definition cannot be changed.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	private bool $locked = false;

	/**
	 * Whether the property is nullable.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	private bool $nullable = false;

	/**
	 * Whether the property is required.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	private bool $required = false;

	/**
	 * Whether the property is required on save.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	private bool $requiredOnSave = false;

	/**
	 * Whether the property is readonly.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	private bool $readonly = false;

	/**
	 * The type of the property.
	 *
	 * @since 2.0.0
	 *
	 * @var string[]
	 */
	private array $type = ['string'];

	/**
	 * Set the default value of the property.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed|Closure $default The default value of the property.
	 */
	public function default( $default ): self {
		$this->checkLock();

		$this->default = $default;

		return $this;
	}

	/**
	 * Whether the property can cast the value.
	 *
	 * @since 2.0.0
	 */
	public function canCast(): bool {
		return isset( $this->castMethod );
	}

	/**
	 * Cast the property value.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $value The value to cast.
	 *
	 * @return mixed
	 *
	 * @throws RuntimeException When no cast method is set.
	 */
	public function cast( $value ) {
		if ( ! $this->canCast() ) {
			throw new RuntimeException( 'No cast method set' );
		}

		$castMethod = $this->castMethod;

		return $castMethod( $value, $this );
	}

	/**
	 * Provides a method to cast the property value.
	 *
	 * @since 2.0.0
	 */
	public function castWith( callable $castMethod ): self {
		$this->checkLock();

		$this->castMethod = Closure::fromCallable( $castMethod );

		return $this;
	}

	/**
	 * Check if the property is locked and throw an exception if it is.
	 *
	 * @since 2.0.0
	 *
	 * @throws RuntimeException When the property is locked.
	 */
	private function checkLock(): void {
		if ( $this->locked ) {
			throw new RuntimeException( 'Property is locked' );
		}
	}

	/**
	 * Create a property definition from a shorthand string or array.
	 *
	 * @since 2.0.0
	 *
	 * @param string|array{0:string,1:mixed} $definition The shorthand definition.
	 *
	 * @throws InvalidArgumentException When the definition is invalid.
	 */
	public static function fromShorthand( $definition ): self {
		$property = new self();

		if ( is_string( $definition ) ) {
			$property->type( $definition );
		} else if ( is_array( $definition ) && 2 === count( $definition ) ) {
			$property->type( $definition[0] );
			$property->default( $definition[1] );
		} else {
			throw new InvalidArgumentException( 'Invalid shorthand property definition' );
		}

		// Nullable for backwards compatibility
		$property->nullable();

		return $property;
	}

	/**
	 * Get the default value of the property.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function getDefault() {
		if ( $this->default instanceof Closure ) {
			$default = $this->default;

			return $default();
		}

		return $this->default;
	}

	/**
	 * Get the type of the property.
	 *
	 * @since 2.0.0
	 *
	 * @return string[]
	 */
	public function getType(): array {
		return $this->type;
	}

	/**
	 * Whether the property has a default value.
	 *
	 * @since 2.0.0
	 */
	public function hasDefault(): bool {
		return isset( $this->default );
	}

	/**
	 * Whether the property is locked.
	 *
	 * @since 2.0.0
	 */
	public function isLocked(): bool {
		return $this->locked;
	}

	/**
	 * Whether the property is nullable.
	 *
	 * @since 2.0.0
	 */
	public function isNullable(): bool {
		return $this->nullable;
	}

	/**
	 * Whether the property is required.
	 *
	 * @since 2.0.0
	 */
	public function isRequired(): bool {
		return $this->required;
	}

	/**
	 * Whether the property is required on save.
	 *
	 * @since 2.0.0
	 */
	public function isRequiredOnSave(): bool {
		return $this->requiredOnSave;
	}

	/**
	 * Whether the property is readonly.
	 *
	 * @since 2.0.0
	 */
	public function isReadonly(): bool {
		return $this->readonly;
	}

	/**
	 * Whether the property is valid for the given value.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $value The value to validate.
	 */
	public function isValidValue( $value ): bool {
		$valueType = gettype( $value );

		switch ( $valueType ) {
			case 'NULL':
				return $this->nullable;
			case 'integer':
				return $this->supportsType( 'int' );
			case 'string':
				return $this->supportsType( 'string' );
			case 'boolean':
				return $this->supportsType( 'bool' );
			case 'array':
				return $this->supportsType( 'array' );
			case 'double':
				return $this->supportsType( 'float' );
			case 'object':
				/** @var object $value */
				if ( $this->supportsType( 'object' ) ) {
					return true;
				} else {
					$class = get_class( $value );
					return $this->supportsType( $class );
				}
			default:
				return false;
		}
	}

	/**
	 * Locks the property so it cannot be changed.
	 * Note that once locked the property cannot be unlocked.
	 *
	 * @since 2.0.0
	 */
	public function lock(): self {
		$this->locked = true;

		return $this;
	}

	/**
	 * Makes the property nullable.
	 *
	 * @since 2.0.0
	 */
	public function nullable(): self {
		$this->checkLock();

		$this->nullable = true;

		return $this;
	}

	/**
	 * Makes the property required.
	 *
	 * @since 2.0.0
	 */
	public function required(): self {
		$this->checkLock();

		$this->required = true;

		return $this;
	}

	/**
	 * Makes the property required on save.
	 *
	 * @since 2.0.0
	 */
	public function requiredOnSave(): self {
		$this->checkLock();

		$this->requiredOnSave = true;

		return $this;
	}

	/**
	 * Makes the property readonly.
	 *
	 * @since 2.0.0
	 */
	public function readonly(): self {
		$this->checkLock();

		$this->readonly = true;

		return $this;
	}

	/**
	 * Whether the property supports the given type.
	 *
	 * @since 2.0.0
	 */
	public function supportsType( string $type ): bool {
		return in_array( $type, $this->type );
	}

	/**
	 * Set the type of the property.
	 *
	 * @since 2.0.0
	 *
	 * @param string ...$types The types of the property, multiple types are considered a union type.
	 */
	public function type( string ...$types ): self {
		$this->checkLock();

		$this->type = $types;

		return $this;
	}
}
