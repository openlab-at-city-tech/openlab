<?php

declare(strict_types=1);

namespace TEC\Common\StellarWP\Models;

use InvalidArgumentException;
use TEC\Common\StellarWP\Models\Exceptions\ReadOnlyPropertyException;

class ModelProperty {
	private const NO_INITIAL_VALUE = '__NO_STELLARWP_MODELS_INITIAL_VALUE__';

	/**
	 * The property definition.
	 */
	private ModelPropertyDefinition $definition;

	/**
	 * Whether the property is dirty.
	 */
	private bool $isDirty = false;

	/**
	 * Whether the original value has been set.
	 */
	private bool $isOriginalValueSet = false;

	/**
	 * Whether the value has been set.
	 */
	private bool $isValueSet = false;

	/**
	 * The key of the property.
	 */
	private string $key;

	/**
	 * The original value of the property.
	 *
	 * @var mixed
	 */
	private $originalValue;

	/**
	 * The property value.
	 *
	 * @var mixed
	 */
	private $value;

	/**
	 * @since 2.0.0
	 *
	 * @param mixed $initialValue The optional, initial value of the property, which takes precedence over the definition's default value.
	 */
	public function __construct( string $key, ModelPropertyDefinition $definition, $initialValue = self::NO_INITIAL_VALUE ) {
		$this->key = $key;
		$this->definition = $definition->lock();

		if ( $initialValue === self::NO_INITIAL_VALUE && $this->definition->hasDefault() ) {
			$initialValue = $this->definition->getDefault();
		}

		if ( $initialValue !== self::NO_INITIAL_VALUE ) {
			if ( ! $this->definition->isValidValue( $initialValue ) ) {
				throw new \InvalidArgumentException( 'Default value is not valid for the property.' );
			}

			$this->value = $initialValue;
			$this->originalValue = $this->value;
			$this->isValueSet = true;
			$this->isOriginalValueSet = true;
		}
	}

	/**
	 * Get the definition of the property.
	 *
	 * @since 2.0.0
	 */
	public function getDefinition(): ModelPropertyDefinition {
		return $this->definition;
	}

	/**
	 * Get the key of the property.
	 *
	 * @since 2.0.0
	 */
	public function getKey(): string {
		return $this->key;
	}

	/**
	 * Get the original value of the property.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function getOriginalValue() {
		return $this->originalValue;
	}

	/**
	 * Get the value of the property.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function getValue() {
		return $this->isValueSet ? $this->value : null;
	}

	/**
	 * Returns whether the property has not changed.
	 *
	 * @since 2.0.0
	 */
	public function isClean(): bool {
		return !$this->isDirty;
	}

	/**
	 * Returns whether the property has changed.
	 *
	 * @since 2.0.0
	 */
	public function isDirty(): bool {
		return $this->isDirty;
	}

	/**
	 * Returns whether the property value has been set.
	 *
	 * @since 2.0.0
	 */
	public function isSet(): bool {
		return $this->isValueSet;
	}

	/**
	 * Reverts the changes to the property — restoring the original value and clearing the dirty flag.
	 *
	 * @since 2.0.0
	 */
	public function revertChanges(): void {
		if ( $this->isOriginalValueSet ) {
			$this->value = $this->originalValue;
			$this->isValueSet = true;
		} else {
			$this->isValueSet = false;
		}

		$this->isDirty = false;
	}

	/**
	 * Commits the changes to the property — syncing the original value with the current and resetting the dirty flag.
	 *
	 * @since 2.0.0
	 */
	public function commitChanges(): void {
		$this->originalValue = $this->value;
		$this->isOriginalValueSet = $this->isValueSet;
		$this->isDirty = false;
	}

	/**
	 * Sets the value of the property.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $value
	 *
	 * @throws InvalidArgumentException When the value is invalid.
	 * @throws ReadOnlyPropertyException When attempting to modify a readonly property.
	 */
	public function setValue( $value ): self {
		if ( $this->definition->isReadonly() ) {
			Config::throwReadOnlyPropertyException( $this, sprintf( 'Cannot modify readonly property "%s".', $this->key ) );
		}

		if ( ! $this->definition->isValidValue( $value ) ) {
			throw new InvalidArgumentException( 'Value is not valid for the property.' );
		}

		$this->value = $value;
		$this->isValueSet = true;
		$this->isDirty = ! $this->isOriginalValueSet || $value !== $this->originalValue;

		return $this;
	}

	/**
	 * Unsets the value of the property.
	 *
	 * @since 2.0.0
	 *
	 * @throws ReadOnlyPropertyException When attempting to unset a readonly property.
	 */
	public function unset(): void {
		if ( $this->definition->isReadonly() ) {
			Config::throwReadOnlyPropertyException( $this, sprintf( 'Cannot unset readonly property "%s".', $this->key ) );
		}

		// Mark the value as unset
		$this->isValueSet = false;

		// If the orginal value had a value we have now deviated
		$this->isDirty = $this->isOriginalValueSet;
	}
}
