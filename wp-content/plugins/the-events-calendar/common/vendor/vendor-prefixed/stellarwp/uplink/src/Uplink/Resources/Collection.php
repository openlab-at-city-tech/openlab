<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Resources;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;

class Collection implements ArrayAccess, Iterator, Countable {

	/**
	 * Collection of resources.
	 *
	 * @var array<string, Resource>
	 */
	private $resources;

	/**
	 * The original Iterator, for memoization.
	 *
	 * @var Iterator<string, Resource>|null
	 */
	private $iterator;

	/**
	 * @param Iterator<string, Resource>|array<string, Resource> $resources An array or iterator of Resources.
	 */
	public function __construct( $resources = [] ) {
		if ( $resources instanceof Iterator ) {
			$this->iterator = $resources;
			$resources      = iterator_to_array( $resources );
		}

		$this->resources = $resources;
	}

	/**
	 * Adds a resource to the collection.
	 *
	 * @since 1.0.0
	 *
	 * @param Resource $resource Resource instance.
	 *
	 * @return Resource
	 */
	public function add( Resource $resource ): Resource {
		if ( ! $this->offsetExists( $resource->get_slug() ) ) {
			$this->offsetSet( $resource->get_slug(), $resource );
		}

		return $this->offsetGet( $resource->get_slug() );
	}

	/**
	 * @return Resource
	 */
	#[\ReturnTypeWillChange]
	public function current(): Resource {
		return current( $this->resources );
	}

	/**
	 * Alias of offsetGet().
	 *
	 * @return Resource|null
	 */
	#[\ReturnTypeWillChange]
	public function get( $offset ): ?Resource {
		return $this->offsetGet( $offset );
	}

	/**
	 * Gets the resource with the given path.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path Path to filter collection by.
	 * @param Iterator<string, Resource>|null  $iterator Optional. Iterator to filter.
	 *
	 * @return self
	 */
	public function get_by_path( string $path, ?Iterator $iterator = null ): self {
		$results = new Filters\Path_FilterIterator( $iterator ?: $this->getIterator(), [ $path ] );

		return new self( $results );
	}

	/**
	 * Gets the resource with the given paths.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string> $paths Paths to filter collection by.
	 * @param Iterator<string, Resource>|null  $iterator Optional. Iterator to filter.
	 *
	 * @return self
	 */
	public function get_by_paths( array $paths, ?Iterator $iterator = null ): self {
		$results = new Filters\Path_FilterIterator( $iterator ?: $this->getIterator(), $paths );

		return new self( $results );
	}

	/**
	 * Gets the plugin resources.
	 *
	 * @since 1.0.0
	 *
	 * @param  Iterator<string, Resource>|null  $iterator Optional. Iterator to filter.
	 *
	 * @return self
	 */
	public function get_plugins( ?Iterator $iterator = null ): self {
		$results = new Filters\Plugin_FilterIterator( $iterator ?: $this->getIterator() );

		return new self( $results );
	}

	/**
	 * Gets the service resources.
	 *
	 * @since 1.0.0
	 *
	 * @param  Iterator<string, Resource>|null  $iterator Optional. Iterator to filter.
	 *
	 * @return self
	 */
	public function get_services( ?Iterator $iterator = null ): self {
		$results = new Filters\Service_FilterIterator( $iterator ?: $this->getIterator() );

		return new self( $results );
	}

	/**
	 * @return array-key|null
	 */
	#[\ReturnTypeWillChange]
	public function key() {
		return key( $this->resources );
	}

	/**
	 * @inheritDoc
	 */
	public function next(): void {
		next( $this->resources );
	}

	/**
	 * @inheritDoc
	 */
	public function offsetExists( $offset ): bool {
		return array_key_exists( $offset, $this->resources );
	}

	/**
	 * @return Resource|null
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ): ?Resource {
		return $this->resources[ $offset ] ?? null;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet( $offset, $value ): void {
		$this->resources[ $offset ] = $value;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset( $offset ): void {
		unset( $this->resources[ $offset ] );
	}

	/**
	 * Helper function for removing a resource from the collection.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Resource slug.
	 *
	 * @return void
	 */
	public function remove( string $slug ): void {
		$this->offsetUnset( $slug );
	}

	/**
	 * @inheritDoc
	 */
	public function rewind(): void {
		reset( $this->resources );
	}

	/**
	 * Sets a resource in the collection.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Resource slug.
	 * @param Resource $resource Resource instance.
	 *
	 * @return Resource|null
	 */
	public function set( string $slug, Resource $resource ): ?Resource {
		$this->offsetSet( $slug, $resource );

		return $this->offsetGet( $slug );
	}

	/**
	 * @inheritDoc
	 */
	public function valid(): bool {
		return key( $this->resources ) !== null;
	}

	/**
	 * @inheritDoc
	 */
	public function count(): int {
		return count( $this->resources );
	}

	/**
	 * Returns a clone of the underlying iterator.
	 *
	 * @return Iterator<string, Resource>
	 */
	public function getIterator(): Iterator {
		if ( isset( $this->iterator ) ) {
			return $this->iterator;
		}

		return $this->iterator = new ArrayIterator( $this->resources );
	}

}
