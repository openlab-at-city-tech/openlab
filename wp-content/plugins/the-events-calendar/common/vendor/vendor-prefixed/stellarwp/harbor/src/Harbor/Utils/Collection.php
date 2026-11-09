<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Utils;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * A generic keyed collection.
 *
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements ArrayAccess<string, TValue>
 * @implements IteratorAggregate<string, TValue>
 */
class Collection implements ArrayAccess, IteratorAggregate, Countable {

	/**
	 * The collection items.
	 *
	 * @since 1.0.0
	 *
	 * @var array<string, TValue>
	 */
	protected array $items;

	/**
	 * Constructor for a keyed collection.
	 *
	 * @since 1.0.0
	 *
	 * @param Traversable<string, TValue>|array<string, TValue> $items An array or traversable of items.
	 *
	 * @return void
	 */
	public function __construct( $items = [] ) {
		if ( $items instanceof Traversable ) {
			/** @var array<string, TValue> $items */
			$items = iterator_to_array( $items );
		}

		$this->items = $items;
	}

	/**
	 * Retrieves an item by key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $offset The item key.
	 *
	 * @return TValue|null
	 */
	public function get( $offset ) {
		return $this->offsetGet( $offset );
	}

	/**
	 * Checks whether an item exists at the given key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $offset The item key.
	 *
	 * @return bool
	 */
	public function offsetExists( $offset ): bool {
		return array_key_exists( $offset, $this->items );
	}

	/**
	 * Retrieves an item by its key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $offset The item key.
	 *
	 * @return TValue|null
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return $this->items[ $offset ] ?? null;
	}

	/**
	 * Sets an item at the given key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $offset The item key.
	 * @param TValue $value  The item value.
	 *
	 * @return void
	 */
	public function offsetSet( $offset, $value ): void {
		$this->items[ $offset ] = $value;
	}

	/**
	 * Removes an item at the given key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $offset The item key.
	 *
	 * @return void
	 */
	public function offsetUnset( $offset ): void {
		unset( $this->items[ $offset ] );
	}

	/**
	 * Removes an item from the collection.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The item key.
	 *
	 * @return void
	 */
	public function remove( string $key ): void {
		$this->offsetUnset( $key );
	}

	/**
	 * @inheritDoc
	 */
	public function count(): int {
		return count( $this->items );
	}

	/**
	 * Returns a fresh iterator over the collection items.
	 *
	 * A new ArrayIterator is created on each call so that nested or
	 * re-entrant foreach loops over the same Collection instance cannot
	 * corrupt each other's cursor position.
	 *
	 * @since 1.0.0
	 *
	 * @return ArrayIterator<string, TValue>
	 */
	public function getIterator(): ArrayIterator {
		return new ArrayIterator( $this->items );
	}
}
