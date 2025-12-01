<?php

declare(strict_types=1);

namespace TEC\Common\StellarWP\Models;

use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * A collection of relationships for a model.
 *
 * Similar to ModelPropertyCollection, this collection stores ModelRelationship instances.
 *
 * @since 2.0.0
 *
 * @implements IteratorAggregate<string,ModelRelationship>
 */
class ModelRelationshipCollection implements Countable, IteratorAggregate {
	/**
	 * The relationships.
	 *
	 * @var array<string,ModelRelationship>
	 */
	private array $relationships;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 * @param array<string,ModelRelationship> $relationships
	 */
	public function __construct( array $relationships = [] ) {
		foreach ( $relationships as $key => $relationship ) {
			if ( ! is_string( $key ) ) {
				throw new InvalidArgumentException( 'Relationship key must be a string.' );
			}

			if ( ! $relationship instanceof ModelRelationship ) {
				throw new InvalidArgumentException( 'Relationship must be an instance of ModelRelationship.' );
			}
		}

		$this->relationships = $relationships;
	}

	/**
	 * Count the number of relationships.
	 *
	 * @since 2.0.0
	 */
	public function count(): int {
		return count($this->relationships);
	}

	/**
	 * Returns a new collection with the relationships that match the callback.
	 *
	 * @since 2.0.0
	 *
	 * @param callable $callback
	 * @param int $mode The mode as used in array_filter() which determines the arguments passed to the callback.
	 */
	public function filter( callable $callback, $mode = 0 ): ModelRelationshipCollection {
		return new self( array_filter( $this->relationships, $callback, $mode ) );
	}

	/**
	 * Create a new ModelRelationshipCollection from an array of relationship definitions.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string,ModelRelationshipDefinition> $relationshipDefinitions
	 * @return ModelRelationshipCollection
	 */
	public static function fromRelationshipDefinitions( array $relationshipDefinitions ): ModelRelationshipCollection {
		$relationships = [];

		foreach ( $relationshipDefinitions as $key => $definition ) {
			if ( ! is_string( $key ) ) {
				throw new InvalidArgumentException( 'Relationship key must be a string.' );
			}

			if ( ! $definition instanceof ModelRelationshipDefinition ) {
				throw new InvalidArgumentException( 'Relationship definition must be an instance of ModelRelationshipDefinition.' );
			}

			$relationships[$key] = new ModelRelationship( $key, $definition );
		}

		return new ModelRelationshipCollection( $relationships );
	}

	/**
	 * Get an iterator for the relationships.
	 *
	 * @since 2.0.0
	 */
	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->relationships);
	}

	/**
	 * Get a relationship by key and return null if it does not exist.
	 *
	 * @since 2.0.0
	 */
	public function get( string $key ): ?ModelRelationship {
		return $this->relationships[$key] ?? null;
	}

	/**
	 * Get a relationship by key. If the relationship does not exist, throw an exception.
	 *
	 * @since 2.0.0
	 */
	public function getOrFail( string $key ): ModelRelationship {
		if ( ! $this->has( $key ) ) {
			throw new InvalidArgumentException( 'Relationship ' . $key . ' does not exist.' );
		}

		return $this->relationships[$key];
	}

	/**
	 * Get all relationships as an array.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string,ModelRelationship>
	 */
	public function getAll(): array {
		return $this->relationships;
	}

	/**
	 * Check if the collection has a relationship with the given key.
	 *
	 * @since 2.0.0
	 */
	public function has( string $key ): bool {
		return isset( $this->relationships[$key] );
	}

	/**
	 * Map the relationships. This does not use array_map because we want to preserve the keys.
	 *
	 * @since 2.0.0
	 *
	 * @template TMapValue
	 * @param callable(ModelRelationship):TMapValue $callback
	 * @return array<string,TMapValue>
	 */
	public function map( callable $callback ) {
		$reducer = static function( array $carry, ModelRelationship $relationship ) use ( $callback ): array {
			$carry[ $relationship->getKey() ] = $callback( $relationship );

			return $carry;
		};

		/** @var array<string,TMapValue> */
		return $this->reduce( $reducer, [] );
	}

	/**
	 * Reduce the relationships.
	 *
	 * @since 2.0.0
	 *
	 * @template TReduceInitial
	 * @template TReduceResult
	 * @param callable(TReduceInitial|TReduceResult,ModelRelationship):(TReduceInitial|TReduceResult) $callback
	 * @param TReduceInitial $initial
	 * @return TReduceResult|TReduceInitial
	 */
	public function reduce( callable $callback, $initial = null ) {
		return array_reduce( $this->relationships, $callback, $initial );
	}

	/**
	 * Execute a callback on each relationship and return the collection.
	 *
	 * @since 2.0.0
	 *
	 * @param callable(ModelRelationship):void $callback
	 */
	public function tap( callable $callback ): self {
		foreach ( $this->relationships as $relationship ) {
			$callback( $relationship );
		}

		return $this;
	}

	/**
	 * Purge all loaded relationships.
	 *
	 * @since 2.0.0
	 */
	public function purgeAll(): void {
		$this->tap( fn( ModelRelationship $relationship ) => $relationship->purge() );
	}

	/**
	 * Purge a specific relationship.
	 *
	 * @since 2.0.0
	 */
	public function purge( string $key ): void {
		$this->getOrFail( $key )->purge();
	}

	/**
	 * Check if a relationship is loaded.
	 *
	 * @since 2.0.0
	 */
	public function isLoaded( string $key ): bool {
		return $this->getOrFail( $key )->isLoaded();
	}
}
