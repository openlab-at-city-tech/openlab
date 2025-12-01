<?php

declare(strict_types=1);

namespace TEC\Common\StellarWP\Models;

use InvalidArgumentException;

/**
 * Represents a model relationship instance with its loaded value.
 *
 * Similar to ModelProperty, but for relationships.
 *
 * @since 2.0.0
 */
class ModelRelationship {
	/**
	 * The relationship definition.
	 */
	private ModelRelationshipDefinition $definition;

	/**
	 * Whether the relationship value has been loaded.
	 */
	private bool $isLoaded = false;

	/**
	 * The key of the relationship.
	 */
	private string $key;

	/**
	 * The relationship value.
	 *
	 * @var mixed
	 */
	private $value;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key The relationship key/name.
	 * @param ModelRelationshipDefinition $definition The relationship definition.
	 */
	public function __construct( string $key, ModelRelationshipDefinition $definition ) {
		$this->key = $key;
		$this->definition = $definition->lock();
	}

	/**
	 * Get the definition of the relationship.
	 *
	 * @since 2.0.0
	 */
	public function getDefinition(): ModelRelationshipDefinition {
		return $this->definition;
	}

	/**
	 * Get the key of the relationship.
	 *
	 * @since 2.0.0
	 */
	public function getKey(): string {
		return $this->key;
	}

	/**
	 * Get the value of the relationship, loading it if necessary.
	 *
	 * @since 2.0.0
	 *
	 * @param callable():( Model|list<Model>|null ) $loader A callable that loads the relationship value.
	 *
	 * @return Model|list<Model>|null
	 */
	public function getValue( callable $loader ) {
		// If caching is disabled, always load fresh
		if ( ! $this->definition->hasCachingEnabled() ) {
			return $this->hydrate( $loader() );
		}

		// If already loaded and caching is enabled, return cached value
		if ( $this->isLoaded ) {
			return $this->hydrate( $this->value );
		}

		// Load and cache the value
		$this->setValue( $loader() );
		return $this->hydrate( $this->value );
	}

	/**
	 * Get the raw value of the relationship.
	 *
	 * @since 2.0.0
	 *
	 * @param callable():( Model|list<Model>|null ) $loader A callable that loads the relationship value.
	 *
	 * @return mixed
	 */
	public function getRawValue( callable $loader ) {
		$this->getValue( $loader );
		return $this->value;
	}

	/**
	 * Hydrate the relationship value.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $value The relationship value.
	 *
	 * @return Model|list<Model>|null
	 */
	private function hydrate( $value ) {
		if ( null === $value ) {
			return null;
		}

		$hydrator = $this->definition->getHydrateWith();

		if ( is_array( $value ) ) {
			return array_values( array_map( fn( $item ) => $hydrator( $item ), $value ) );
		}

		return $hydrator( $value );
	}

	/**
	 * Returns whether the relationship has been loaded.
	 *
	 * @since 2.0.0
	 */
	public function isLoaded(): bool {
		return $this->isLoaded;
	}

	/**
	 * Purge/clear the relationship value.
	 *
	 * @since 2.0.0
	 */
	public function purge(): void {
		$this->value = null;
		$this->isLoaded = false;
	}

	/**
	 * Sets the value of the relationship.
	 *
	 * @since 2.0.0
	 *
	 * @param Model|list<Model>|null $value
	 *
	 * @throws InvalidArgumentException When the value is invalid.
	 */
	public function setValue( $value ): self {
		if ( $value !== null ) {
			if ( $this->definition->isSingle() ) {
				$value = $this->definition->getValidateSanitizeRelationshipWith()( $value );
			}

			if ( $this->definition->isMultiple() ) {
				if ( ! is_array( $value ) ) {
					Config::throwInvalidArgumentException( 'Multiple relationship value must be an array or null.' );
				}

				$sanitizer = $this->definition->getValidateSanitizeRelationshipWith();
				$value = array_map( $sanitizer, $value );
			}
		}

		$this->value = $value;
		$this->isLoaded = true;

		return $this;
	}
}
