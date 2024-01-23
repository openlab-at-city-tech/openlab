<?php
namespace Bookly\Lib\Slots;

class Range
{
    const AVAILABLE            = 1;
    const PARTIALLY_BOOKED     = 2;
    const FULLY_BOOKED         = 3;
    const WAITING_LIST_STARTED = 4;

    /** @var IPoint */
    protected $start;

    /** @var IPoint */
    protected $end;

    /** @var RangeData */
    protected $data;

    /**
     * Constructor.
     *
     * @param IPoint $start
     * @param IPoint $end
     * @param RangeData $data
     */
    public function __construct( IPoint $start, IPoint $end, RangeData $data = null )
    {
        $this->start = $start;
        $this->end   = $end;
        $this->data  = $data;
    }

    /**
     * Create Range object from date strings.
     *
     * @param string $start  Format Y-m-d H:i[:s]
     * @param string $end    Format Y-m-d H:i[:s]
     * @param RangeData $data
     * @return self
     */
    public static function fromDates( $start, $end, RangeData $data = null )
    {
        return new static( DatePoint::fromStr( $start ), DatePoint::fromStr( $end ), $data );
    }

    /**
     * Create Range object from time strings.
     *
     * @param string $start  Format H:i[:s]
     * @param string $end    Format H:i[:s]
     * @param RangeData $data
     * @return self
     */
    public static function fromTimes( $start, $end, RangeData $data = null )
    {
        return new static( TimePoint::fromStr( $start ), TimePoint::fromStr( $end ), $data );
    }

    /**
     * Get range start.
     *
     * @return IPoint
     */
    public function start()
    {
        return $this->start;
    }

    /**
     * Ger range end.
     *
     * @return IPoint
     */
    public function end()
    {
        return $this->end;
    }

    /**
     * Get range data.
     *
     * @return RangeData
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Get range length.
     *
     * @return int
     */
    public function length()
    {
        return $this->end->diff( $this->start );
    }

    /**
     * Tells whether range is valid (start point is less then end point).
     *
     * @return bool
     */
    public function valid()
    {
        return $this->start->lte( $this->end );
    }

    /**
     * Tells whether range contains specific point.
     *
     * @param IPoint $point
     * @return bool
     */
    public function contains( IPoint $point )
    {
        return $this->start->lte( $point ) && $this->end->gte( $point );
    }

    /**
     * Tells whether two ranges are equal.
     *
     * @param self $range
     * @return bool
     */
    public function equals( self $range )
    {
        return $this->start->eq( $range->start() ) && $this->end->eq( $range->end() );
    }

    /**
     * Tells whether two ranges overlap.
     *
     * @param self $range
     * @return bool
     */
    public function overlaps( self $range )
    {
        return $this->start->lt( $range->end() ) && $this->end->gt( $range->start() );
    }

    /**
     * Tells whether range contains all points of another range.
     *
     * @param Range $range
     * @return bool
     */
    public function wraps( self $range )
    {
        return $this->start->lte( $range->start() ) && $this->end->gte( $range->end() );
    }

    /**
     * Computes the intersection between two ranges.
     *
     * @param self $range
     * @return self|null
     */
    public function intersect( self $range )
    {
        return $this->overlaps( $range )
            ? new static( self::_max( $this->start, $range->start() ), self::_min( $this->end, $range->end() ), $this->data )
            : null;
    }

    /**
     * Computes the result of subtraction of two ranges.
     *
     * @param self $range
     * @param self $removed
     * @return RangeCollection
     */
    public function subtract( self $range, self &$removed = null )
    {
        $collection = new RangeCollection();

        $removed = $this->intersect( $range );

        if ( $this->start->lt( $range->start() ) ) {
            $collection->push( new static( $this->start, self::_min( $this->end, $range->start() ), $this->data ) );
        }

        if ( $range->end()->lt( $this->end ) ) {
            $collection->push( new static( self::_max( $this->start, $range->end() ), $this->end, $this->data ) );
        }

        return $collection;
    }

    /**
     * Split range into smaller ranges.
     *
     * @param mixed $length
     * @return RangeCollection
     */
    public function split( $length )
    {
        $collection = new RangeCollection();

        $frame = $this->resize( $length );

        while ( $range = $this->intersect( $frame ) ) {
            $collection->push( $range );
            $frame = $frame->transform( $length, $length );
        }

        return $collection;
    }

    /**
     * Computes the result of modifying the edge points according to given values.
     *
     * @param mixed $modify_start
     * @param mixed $modify_end
     * @return self
     */
    public function transform( $modify_start, $modify_end )
    {
        return new static( $this->start->modify( $modify_start ), $this->end->modify( $modify_end ), $this->data );
    }

    /**
     * Computes the result of moving the end point to given length from the start point.
     *
     * @param mixed $length
     * @return self
     */
    public function resize( $length )
    {
        return new static( $this->start, $this->start->modify( $length ), $this->data );
    }

    /**
     * Computes the result of aligning the edge points to a grid
     * made up by the start point of given range and given step.
     *
     * @param Range $range
     * @param mixed $step
     * @param mixed $precursor  Length of possible left sibling
     * @return self
     */
    public function align( self $range, $step, $precursor )
    {
        $step = $range->resize( $step )->length();
        $precursor = $range->resize( $precursor )->length();

        $start = $this->start;
        $end   = $this->end;

        $mod = $precursor % $step;
        if ( $mod ) {
            $start = $start->modify( $step - $mod );
        }

        $mod_left  = $start->diff( $range->start() ) % $step;
        $mod_right = $end->diff( $range->start() ) % $step;
        if ( $mod_left ) {
            $start = $start->modify( - $mod_left );
        }
        if ( $mod_right ) {
            $end = $end->modify( $step - $mod_right );
        }

        return new static( $start, $end, $this->data );
    }

    /**
     * Create a copy of the range with new data.
     *
     * @param RangeData $new_data
     * @return self
     */
    public function replaceData( RangeData $new_data )
    {
        return new static( $this->start, $this->end, $new_data );
    }

    /**
     * Get max point.
     *
     * @param IPoint $x
     * @param IPoint $y
     * @return IPoint
     */
    private static function _max( IPoint $x, IPoint $y )
    {
        return $x->gte( $y ) ? $x : $y;
    }

    /**
     * Get min point.
     *
     * @param IPoint $x
     * @param IPoint $y
     * @return IPoint
     */
    private static function _min( IPoint $x, IPoint $y )
    {
        return $x->lte( $y ) ? $x : $y;
    }

    /******************************************************************************************************************
     * RangeData related methods.                                                                                     *
     ******************************************************************************************************************/

    /**
     * Get service ID.
     *
     * @return int
     */
    public function serviceId()
    {
        return $this->data->serviceId();
    }

    /**
     * Get staff ID.
     *
     * @return int
     */
    public function staffId()
    {
        return $this->data->staffId();
    }

    /**
     * Get location ID.
     *
     * @return int
     */
    public function locationId()
    {
        return $this->data->locationId();
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function state()
    {
        return $this->data->state();
    }

    /**
     * Get capacity.
     *
     * @return int
     */
    public function capacity()
    {
        return $this->data->capacity();
    }

    /**
     * Get next slot.
     *
     * @return self
     */
    public function nextSlot()
    {
        return $this->data->nextSlot();
    }

    /**
     * Check whether next slot is set.
     *
     * @return bool
     */
    public function hasNextSlot()
    {
        return $this->data->hasNextSlot();
    }

    /**
     * Get alternative slot.
     *
     * @return self
     */
    public function altSlot()
    {
        return $this->data->altSlot();
    }

    /**
     * Check whether alternative slot is set.
     *
     * @return bool
     */
    public function hasAltSlot()
    {
        return $this->data->hasAltSlot();
    }

    /**
     * Get previous alternative slot.
     *
     * @return self
     */
    public function prevAltSlot()
    {
        return $this->data->prevAltSlot();
    }

    /**
     * Check whether previous alternative slot is set.
     *
     * @return bool
     */
    public function hasPrevAltSlot()
    {
        return $this->data->hasPrevAltSlot();
    }

    /**
     * Create a copy of the range with new staff ID in data.
     *
     * @param int $new_staff_id
     * @return self
     */
    public function replaceStaffId( $new_staff_id )
    {
        return $this->replaceData( $this->data->replaceStaffId( $new_staff_id ) );
    }

    /**
     * Create a copy of the range with new state in data.
     *
     * @param int $new_state
     * @return self
     */
    public function replaceState( $new_state )
    {
        return $this->replaceData( $this->data->replaceState( $new_state ) );
    }

    /**
     * Create a copy of the range with new capacity in data.
     *
     * @param int $new_capacity
     * @return self
     */
    public function replaceCapacity( $new_capacity )
    {
        return $this->replaceData( $this->data->replaceCapacity( $new_capacity ) );
    }

    /**
     * Create a copy of the range with new nop in data.
     *
     * @param int $new_nop
     * @return self
     */
    public function replaceNop( $new_nop )
    {
        return $this->replaceData( $this->data->replaceNop( $new_nop ) );
    }

    /**
     * Create a copy of the range with new next slot in data.
     *
     * @param self|null $new_next_slot
     * @param int|null $next_connection
     * @return self
     */
    public function replaceNextSlot( $new_next_slot, $next_connection = null )
    {
        return $this->replaceData( $this->data->replaceNextSlot( $new_next_slot, $next_connection ) );
    }

    /**
     * Create a copy of the range with new alternative slot in data.
     *
     * @param self|null $new_alt_slot
     * @return self
     */
    public function replaceAltSlot( $new_alt_slot )
    {
        return $this->replaceData( $this->data->replaceAltSlot( $new_alt_slot ) );
    }

    /**
     * Create a copy of the range with new previous alternative slot in data.
     *
     * @param self|null $new_prev_alt_slot
     * @return self
     */
    public function replacePrevAltSlot( $new_prev_alt_slot )
    {
        return $this->replaceData( $this->data->replacePrevAltSlot( $new_prev_alt_slot ) );
    }

    /**
     * Tells whether range's state is available.
     *
     * @return bool
     */
    public function available()
    {
        return $this->data->state() == self::AVAILABLE;
    }

    /**
     * Tells whether range's state is not available.
     *
     * @return bool
     */
    public function notAvailable()
    {
        return $this->data->state() != self::AVAILABLE;
    }

    /**
     * Tells whether range's state is partially booked.
     *
     * @return bool
     */
    public function partiallyBooked()
    {
        return $this->data->state() == self::PARTIALLY_BOOKED;
    }

    /**
     * Tells whether range's state is not partially booked.
     *
     * @return bool
     */
    public function notPartiallyBooked()
    {
        return $this->data->state() != self::PARTIALLY_BOOKED;
    }

    /**
     * Tells whether range's state is fully booked.
     *
     * @return bool
     */
    public function fullyBooked()
    {
        return $this->data->state() == self::FULLY_BOOKED;
    }

    /**
     * Tells whether range's state is not fully booked.
     *
     * @return bool
     */
    public function notFullyBooked()
    {
        return $this->data->state() != self::FULLY_BOOKED;
    }

    /**
     * Tells whether range's state is waiting list started.
     *
     * @return bool
     */
    public function waitingListStarted()
    {
        return $this->data->state() == self::WAITING_LIST_STARTED;
    }

    /**
     * Tells whether range's state is not waiting list started.
     *
     * @return bool
     */
    public function noWaitingListStarted()
    {
        return $this->data->state() != self::WAITING_LIST_STARTED;
    }

    /**
     * Build slot data.
     *
     * @return array
     */
    public function buildSlotData()
    {
        $result = array( array( $this->serviceId(), $this->staffId(), $this->start->value()->format( 'Y-m-d H:i:s' ), $this->locationId() ) );

        if ( $this->data->state() == self::WAITING_LIST_STARTED ) {
            // Mark slot as being put to waiting list.
            $result[0][] = 'w';
        }

        if ( $this->data()->hasNextSlot() ) {
            $result = array_merge( $result, $this->nextSlot()->buildSlotData() );
        }

        return $result;
    }

    /**
     * Tells whether waiting list has been started in any of the slots of the chain.
     *
     * @return bool
     */
    public function waitingListEverStarted()
    {
        $started = $this->data->state() == self::WAITING_LIST_STARTED;

        if ( ! $started && $this->data()->hasNextSlot() ) {
            $started = $this->nextSlot()->waitingListEverStarted();
        }

        return $started;
    }

    /**
     * Get maximal nop.
     *
     * @return int
     */
    public function maxNop()
    {
        $result = $this->data->nop();

        if ( $this->data()->hasNextSlot() ) {
            $result = max( $result, $this->nextSlot()->maxNop() );
        }

        return $result;
    }

    /**
     * Get maximal number of persons on waiting list.
     *
     * @return int
     */
    public function maxOnWaitingList()
    {
        $result = $this->data->onWaitingList();

        if ( $this->data()->hasNextSlot() ) {
            $result = max( $result, $this->nextSlot()->maxOnWaitingList() );
        }

        return $result;
    }

    /**
     * Find such a slot among the current and alternative slots
     * so that there are only unique staff ids in the entire chain of next slots run in parallel.
     *
     * @return Range|null
     */
    public function mayBeAltSlot( array $staff_ids )
    {
        $slot = $this;
        while ( $slot->data()->hasPrevAltSlot() ) {
            // Rewind to the very first slot in the list
            $slot = $slot->data()->prevAltSlot();
        }

        do {
            if ( ! in_array( $slot->data()->staffId(), $staff_ids ) ) {
                if ( $slot->data()->hasNextSlot() && $slot->data()->nextConnection() == Generator::CONNECTION_PARALLEL ) {
                    $next_slot = $slot->data()->nextSlot()->mayBeAltSlot( array_merge(
                        $staff_ids,
                        array( $slot->data()->staffId() )
                    ) );
                    if ( $next_slot ) {
                        if ( $next_slot !== $slot->data()->nextSlot() ) {
                            $slot = $slot->replaceNextSlot( $next_slot );
                        }
                        break;
                    }
                } else {
                    break;
                }
            }

            $slot = $slot->data()->altSlot();

        } while ( $slot );

        return $slot;
    }
}