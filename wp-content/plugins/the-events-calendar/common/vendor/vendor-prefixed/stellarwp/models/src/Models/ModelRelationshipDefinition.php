<?php

declare(strict_types=1);

namespace TEC\Common\StellarWP\Models;

use InvalidArgumentException;
use RuntimeException;
use TEC\Common\StellarWP\Models\ValueObjects\Relationship;

/**
 * Defines a model relationship with its type and configuration.
 *
 * @since 2.0.0
 */
class ModelRelationshipDefinition {
	/**
	 * Whether to cache the relationship result.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	private bool $cachingEnabled = true;

	/**
	 * The callable to hydrate the relationship with.
	 *
	 * @since 2.0.0
	 *
	 * @var ?callable
	 */
	private $hydrateWith = null;

	/**
	 * The callable to validate and sanitize the relationship with.
	 *
	 * @since 2.0.0
	 *
	 * @var ?callable
	 */
	private $validateSanitizeRelationshipWith = null;

	/**
	 * Whether the definition is locked. Once locked, the definition cannot be changed.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	private bool $locked = false;

	/**
	 * The key/name of the relationship.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	private string $key;

	/**
	 * The relationship type (has-one, has-many, belongs-to, etc.).
	 *
	 * @since 2.0.0
	 *
	 * @var Relationship
	 */
	private Relationship $type;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key The relationship key/name.
	 * @param string|Relationship|null $type The relationship type (optional, can be set later via fluent methods).
	 */
	public function __construct( string $key, $type = null ) {
		$this->key = $key;

		if ( $type !== null ) {
			$this->type = is_string( $type ) ? Relationship::from( $type ) : $type;
		}
	}

	/**
	 * Set the relationship as belongs-to.
	 *
	 * @since 2.0.0
	 */
	public function belongsTo(): self {
		$this->checkLock();

		$this->type = Relationship::BELONGS_TO();

		return $this;
	}

	/**
	 * Set the callable to hydrate the relationship with.
	 *
	 * @since 2.0.0
	 *
	 * @param callable $hydrateWith The callable to hydrate the relationship with.
	 */
	public function setHydrateWith( callable $hydrateWith ): self {
		$this->checkLock();

		$this->hydrateWith = $hydrateWith;

		return $this;
	}

	/**
	 * Get the callable to hydrate the relationship with.
	 *
	 * @since 2.0.0
	 *
	 * @return callable( mixed $value ): ( Model )
	 */
	public function getHydrateWith(): callable {
		// By default, it returns whats given.
		return $this->hydrateWith ?? static fn( $value ) => $value;
	}

	/**
	 * Set the callable to validate the relationship with.
	 *
	 * @since 2.0.0
	 *
	 * @param callable $validateSanitizeRelationshipWith The callable to validate the relationship with.
	 */
	public function setValidateSanitizeRelationshipWith( callable $validateSanitizeRelationshipWith ): self {
		$this->checkLock();

		$this->validateSanitizeRelationshipWith = $validateSanitizeRelationshipWith;

		return $this;
	}

	/**
	 * Get the callable to validate the relationship with.
	 *
	 * @since 2.0.0
	 *
	 * @return callable( mixed $thing ): ( Model | null )
	 */

	public function getValidateSanitizeRelationshipWith(): callable {
		return $this->validateSanitizeRelationshipWith ?? static function( $thing ): ?Model {
			if ( null !== $thing && ! $thing instanceof Model ) {
				throw new InvalidArgumentException( 'Relationship value must be a valid value.' );
			}

			return $thing;
		};
	}

	/**
	 * Set the relationship as belongs-to-many.
	 *
	 * @since 2.0.0
	 */
	public function belongsToMany(): self {
		$this->checkLock();

		$this->type = Relationship::BELONGS_TO_MANY();

		return $this;
	}

	/**
	 * Enable caching for this relationship.
	 *
	 * @since 2.0.0
	 */
	public function enableCaching(): self {
		$this->checkLock();

		$this->cachingEnabled = true;

		return $this;
	}

	/**
	 * Disable caching for this relationship.
	 *
	 * @since 2.0.0
	 */
	public function disableCaching(): self {
		$this->checkLock();

		$this->cachingEnabled = false;

		return $this;
	}

	/**
	 * Check if the relationship is locked and throw an exception if it is.
	 *
	 * @since 2.0.0
	 *
	 * @throws RuntimeException When the relationship is locked.
	 */
	private function checkLock(): void {
		if ( $this->locked ) {
			throw new RuntimeException( 'Relationship is locked' );
		}
	}

	/**
	 * Create a relationship definition from a shorthand string.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key The relationship key/name.
	 * @param string $type The relationship type shorthand.
	 *
	 * @throws InvalidArgumentException When the type is invalid.
	 */
	public static function fromShorthand( string $key, string $type ): self {
		return new self( $key, $type );
	}

	/**
	 * Get the relationship key/name.
	 *
	 * @since 2.0.0
	 */
	public function getKey(): string {
		return $this->key;
	}

	/**
	 * Get the relationship type.
	 *
	 * @since 2.0.0
	 */
	public function getType(): Relationship {
		return $this->type;
	}

	/**
	 * Set the relationship as has-many.
	 *
	 * @since 2.0.0
	 */
	public function hasMany(): self {
		$this->checkLock();

		$this->type = Relationship::HAS_MANY();

		return $this;
	}

	/**
	 * Set the relationship as has-one.
	 *
	 * @since 2.0.0
	 */
	public function hasOne(): self {
		$this->checkLock();

		$this->type = Relationship::HAS_ONE();

		return $this;
	}

	/**
	 * Whether the relationship result should be cached.
	 *
	 * @since 2.0.0
	 */
	public function hasCachingEnabled(): bool {
		return $this->cachingEnabled;
	}

	/**
	 * Whether the relationship is locked.
	 *
	 * @since 2.0.0
	 */
	public function isLocked(): bool {
		return $this->locked;
	}

	/**
	 * Whether the relationship returns multiple models.
	 *
	 * @since 2.0.0
	 */
	public function isMultiple(): bool {
		return $this->type->isMultiple();
	}

	/**
	 * Whether the relationship returns a single model.
	 *
	 * @since 2.0.0
	 */
	public function isSingle(): bool {
		return $this->type->isSingle();
	}

	/**
	 * Locks the relationship so it cannot be changed.
	 * Note that once locked the relationship cannot be unlocked.
	 *
	 * @since 2.0.0
	 */
	public function lock(): self {
		$this->locked = true;

		return $this;
	}

	/**
	 * Set the relationship as many-to-many.
	 *
	 * @since 2.0.0
	 */
	public function manyToMany(): self {
		$this->checkLock();

		$this->type = Relationship::MANY_TO_MANY();

		return $this;
	}
}
