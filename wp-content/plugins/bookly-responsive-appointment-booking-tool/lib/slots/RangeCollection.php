<?php
namespace Bookly\Lib\Slots;

class RangeCollection implements \IteratorAggregate
{
    /** @var  Range[] */
    protected $ranges = array();

    /**
     * Create collection from array of ranges.
     *
     * @param array $ranges
     * @return static
     */
    public static function fromArray( array $ranges )
    {
        $new_collection = new static();
        $new_collection->ranges = $ranges;

        return $new_collection;
    }

    /**
     * Tells whether collection is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty ( $this->ranges );
    }

    /**
     * Tells whether collection is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return ! empty ( $this->ranges );
    }

    /**
     * Push range to collection.
     *
     * @param Range $range
     * @return static
     */
    public function push( Range $range  )
    {
        $this->ranges[] = $range;

        return $this;
    }

    /**
     * Put range with the given key into the collection.
     *
     * @param mixed $key
     * @param Range $range
     * @return static
     */
    public function put( $key, Range $range  )
    {
        $this->ranges[ $key ] = $range;

        return $this;
    }

    /**
     * Determines if a given key exists in the collection.
     *
     * @param mixed $key
     * @return bool
     */
    public function has( $key )
    {
        return isset ( $this->ranges[ $key ] );
    }

    /**
     * Get range at a given key.
     *
     * @param mixed $key
     * @return Range|false
     */
    public function get( $key )
    {
        return $this->has( $key ) ? $this->ranges[ $key ] : false;
    }

    /**
     * Get all ranges.
     *
     * @return Range[]
     */
    public function all()
    {
        return $this->ranges;
    }

    /**
     * Get the number of ranges in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count( $this->ranges );
    }

    /**
     * Sort collection by keys.
     *
     * @return static
     */
    public function ksort()
    {
        ksort( $this->ranges );

        return $this;
    }

    /**
     * Computes the intersection between collection and given range.
     *
     * @param Range $range
     * @return static
     */
    public function intersect( Range $range )
    {
        $new_collection = new static();

        foreach ( $this->ranges as $r1 ) {
            $r2 = $r1->intersect( $range );
            if ( $r2 ) {
                $new_collection->push( $r2 );
            }
        }

        return $new_collection;
    }

    /**
     * Computes the subtraction between collection and given range.
     *
     * @param Range $range
     * @param self $removed
     * @return static
     */
    public function subtract( Range $range, self &$removed = null )
    {
        $new_collection = new static();

        $removed = new static();

        foreach ( $this->ranges as $r ) {
            $new_collection = $new_collection->merge( $r->subtract( $range, $removed_range ) );
            if ( $removed_range ) {
                $removed->push( $removed_range );
            }
        }

        return $new_collection;
    }

    /**
     * Computes the result by merging two collections.
     *
     * @param self $collection
     * @return static
     */
    public function merge( self $collection )
    {
        return static::fromArray( array_merge( $this->ranges, $collection->all() ) );
    }

    /**
     * Computes the union of two collections.
     *
     * @param self $collection
     * @return static
     */
    public function union( self $collection )
    {
        return static::fromArray( $this->ranges + $collection->all() );
    }

    /**
     * Computes new collection after applying filter to each item.
     *
     * @param callable $callback
     * @return static
     */
    public function filter( $callback )
    {
        return static::fromArray( array_filter( $this->ranges, $callback ) );
    }

    /**
     * Computes new collection by applying the callback to each item.
     *
     * @param callable $callback
     * @return static
     */
    public function map( $callback )
    {
        return static::fromArray( array_map( $callback, $this->ranges ) );
    }

    /**
     * @inheritDoc
     * @return \ArrayIterator
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator( $this->ranges );
    }
}