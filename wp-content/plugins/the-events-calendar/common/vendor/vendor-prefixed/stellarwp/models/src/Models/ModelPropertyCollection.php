<?php

declare(strict_types=1);

namespace TEC\Common\StellarWP\Models;

use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * A collection of properties for a model.
 *
 * Some philosophical notes:
 * 		* The collection is immutable. Once created, the collection cannot be changed.
 *
 * @implements IteratorAggregate<string,ModelProperty>
 */
class ModelPropertyCollection implements Countable, IteratorAggregate {
	/**
	 * The properties.
	 *
	 * @var array<string,ModelProperty>
	 */
	private array $properties = [];

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 * @param array<string,ModelProperty> $properties
	 */
	public function __construct( array $properties = [] ) {
		foreach ( $properties as $key => $property ) {
			if ( ! is_string( $key ) ) {
				throw new InvalidArgumentException( 'Property key must be a string.' );
			}

			if ( ! $property instanceof ModelProperty ) {
				throw new InvalidArgumentException( 'Property must be an instance of ModelProperty.' );
			}

			$this->properties[$key] = $property;
		}
	}

	/**
	 * Reset the properties so the original value matches the current value.
	 *
	 * @since 2.0.0
	 */
	public function commitChangedProperties(): void {
		$this->tap( fn( ModelProperty $property ) => $property->isDirty() ? $property->commitChanges() : null );
	}

	/**
	 * Count the number of properties.
	 *
	 * @since 2.0.0
	 */
	public function count(): int {
		return count($this->properties);
	}

	/**
	 * Returns a new collection with the properties that match the callback.
	 *
	 * @since 2.0.0
	 *
	 * @param callable $callback
	 * @param int $mode The mode as used in array_filter() which determines the arguments passed to the callback.
	 */
	public function filter( callable $callback, $mode = 0 ): ModelPropertyCollection {
		return new self( array_filter( $this->properties, $callback, $mode ) );
	}

	/**
	 * Create a new ModelPropertyCollection from an array of ModelPropertyDefinitions.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string,ModelPropertyDefinition> $propertyDefinitions
	 * @param array<string,mixed> $initialValues
	 * @return ModelPropertyCollection
	 */
	public static function fromPropertyDefinitions( array $propertyDefinitions, array $initialValues = [] ): ModelPropertyCollection {
		$properties = [];

		foreach ( $propertyDefinitions as $key => $definition ) {
			if ( ! is_string( $key ) ) {
				throw new InvalidArgumentException( 'Property key must be a string.' );
			}

			if ( ! $definition instanceof ModelPropertyDefinition ) {
				throw new InvalidArgumentException( 'Property definition must be an instance of ModelPropertyDefinition.' );
			}

			if ( isset( $initialValues[$key] ) ) {
				$properties[$key] = new ModelProperty( $key, $definition, $initialValues[$key] );
			} else {
				$properties[$key] = new ModelProperty( $key, $definition );
			}
		}

		return new ModelPropertyCollection( $properties );
	}

	/**
	 * Get an iterator for the properties.
	 *
	 * @since 2.0.0
	 */
	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->properties);
	}

	/**
	 * Get a property by key and return null if it does not exist.
	 *
	 * @since 2.0.0
	 */
	public function get( string $key ): ?ModelProperty {
		return $this->properties[$key] ?? null;
	}

	/**
	 * Get the dirty properties.
	 *
	 * @since 2.0.0
	 */
	public function getDirtyProperties(): ModelPropertyCollection {
		return $this->filter( fn( ModelProperty $property ) => $property->isDirty() );
	}

	/**
	 * Get the dirty values of the properties.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string,mixed>
	 */
	public function getDirtyValues(): array {
		return $this->getDirtyProperties()->map( fn( ModelProperty $property ) => $property->getValue() );
	}

	/**
	 * Get a property by key. If the property does not exist, throw an exception.
	 *
	 * @since 2.0.0
	 */
	public function getOrFail( string $key ): ModelProperty {
		if ( ! $this->has( $key ) ) {
			throw new InvalidArgumentException( 'Property ' . $key . ' does not exist.' );
		}

		return $this->properties[$key];
	}

	/**
	 * Get the original values of the properties.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string,mixed>
	 */
	public function getOriginalValues(): array {
		return $this->map( fn( ModelProperty $property ) => $property->getOriginalValue() );
	}

	/**
	 * Get the properties that are required for instantiation.
	 *
	 * @since 2.0.0
	 */
	public function getRequiredProperties(): ModelPropertyCollection {
		return $this->filter( fn( ModelProperty $property ) => $property->getDefinition()->isRequired() );
	}

	/**
	 * Get the properties that are required on save.
	 *
	 * @since 2.0.0
	 */
	public function getRequiredOnSaveProperties(): ModelPropertyCollection {
		return $this->filter( fn( ModelProperty $property ) => $property->getDefinition()->isRequiredOnSave() );
	}

	/**
	 * Get the values of the properties.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string,mixed>
	 */
	public function getValues(): array {
		return $this->map( fn( ModelProperty $property ) => $property->getValue() );
	}

	/**
	 * Check if the collection has a property with the given key.
	 *
	 * @since 2.0.0
	 */
	public function has( string $key ): bool {
		return isset( $this->properties[$key] );
	}

	/**
	 * Check if the collection has any dirty properties.
	 *
	 * @since 2.0.0
	 */
	public function isDirty(): bool {
		return $this->getDirtyProperties()->count() > 0;
	}

	/**
	 * Check if the collection is empty (has no properties).
	 *
	 * @since 2.0.0
	 */
	public function isEmpty(): bool {
		return empty( $this->properties );
	}

	/**
	 * Check if the property is set.
	 *
	 * @since 2.0.0
	 */
	public function isSet( string $key ): bool {
		return $this->getOrFail( $key )->isSet();
	}

	/**
	 * Map the properties. This does not use array_map because we want to preserve the keys.
	 *
	 * @since 2.0.0
	 *
	 * @template TMapValue
	 * @param callable(ModelProperty):TMapValue $callback
	 * @return array<string,TMapValue>
	 */
	public function map( callable $callback ) {
		$reducer = static function( array $carry, ModelProperty $property ) use ( $callback ): array {
			$carry[ $property->getKey() ] = $callback( $property );

			return $carry;
		};

		/** @var array<string,TMapValue> */
		return $this->reduce( $reducer, [] );
	}

	/**
	 * Reduce the properties.
	 *
	 * @since 2.0.0
	 *
	 * @template TReduceInitial
	 * @template TReduceResult
	 * @param callable(TReduceInitial|TReduceResult,ModelProperty):(TReduceInitial|TReduceResult) $callback
	 * @param TReduceInitial $initial
	 * @return TReduceResult|TReduceInitial
	 */
	public function reduce( callable $callback, $initial = null ) {
		return array_reduce( $this->properties, $callback, $initial );
	}

	/**
	 * Revert the changes to the properties so the original value matches the current value.
	 *
	 * @since 2.0.0
	 */
	public function revertChangedProperties(): void {
		$this->tap( fn( ModelProperty $property ) => $property->isDirty() ? $property->revertChanges() : null );
	}

	/**
	 * Revert the changes to the property so the original value matches the current value.
	 *
	 * @since 2.0.0
	 */
	public function revertProperty( string $key ): void {
		$this->getOrFail( $key )->revertChanges();
	}

	/**
	 * Set the values of the properties.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string,mixed> $values
	 */
	public function setValues( array $values ): void {
		foreach ( $values as $key => $value ) {
			if ( ! $this->has( $key ) ) {
				throw new InvalidArgumentException( 'Property ' . $key . ' does not exist.' );
			}

			$this->properties[$key]->setValue( $value );
		}
	}

	/**
	 * Execute a callback on each property and return the collection.
	 *
	 * @since 2.0.0
	 *
	 * @param callable(ModelProperty):void $callback
	 */
	public function tap( callable $callback ): self {
		foreach ( $this->properties as $property ) {
			$callback( $property );
		}

		return $this;
	}

	/**
	 * Unset a property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key
	 */
	public function unsetProperty( $key ): void {
		$this->getOrFail( $key )->unset();
	}
}
