<?php
namespace Bookly\Lib\Slots;

/**
 * Class RangeData
 * @package Bookly\Lib\Slots
 */
class RangeData
{
    /** @var int */
    protected $service_id;
    /** @var int */
    protected $staff_id;
    /** @var int */
    protected $location_id;
    /** @var int */
    protected $state;
    /** @var int */
    protected $on_waiting_list;
    /** @var int */
    protected $capacity;
    /** @var int */
    protected $nop;
    /** @var Range */
    protected $next_slot;
    /** @var Range */
    protected $alt_slot;
    /** @var Range */
    protected $prev_alt_slot;  // for creating doubly linked list
    /** @var int */
    protected $next_connection;

    /**
     * Constructor.
     *
     * @param int $service_id
     * @param int $staff_id
     * @param int $location_id
     * @param int $state
     * @param int $on_waiting_list
     * @param int $capacity
     * @param int $nop
     * @param Range|null $next_slot
     * @param Range|null $alt_slot
     * @param Range|null $prev_alt_slot
     * @param int $next_connection
     */
    public function __construct(
        $service_id,
        $staff_id,
        $location_id = 0,
        $state = Range::AVAILABLE,
        $on_waiting_list = 0,
        $capacity = 1,
        $nop = 0,
        $next_slot = null,
        $alt_slot = null,
        $prev_alt_slot = null,
        $next_connection = Generator::CONNECTION_CONSECUTIVE
    )
    {
        $this->service_id      = $service_id;
        $this->staff_id        = $staff_id;
        $this->location_id     = $location_id;
        $this->state           = $state;
        $this->on_waiting_list = $on_waiting_list;
        $this->capacity        = $capacity;
        $this->nop             = $nop;
        $this->next_slot       = $next_slot;
        $this->alt_slot        = $alt_slot;
        $this->prev_alt_slot   = $prev_alt_slot;
        $this->next_connection = $next_connection;
    }

    /**
     * Get service ID.
     *
     * @return int
     */
    public function serviceId()
    {
        return $this->service_id;
    }

    /**
     * Get staff ID.
     *
     * @return int
     */
    public function staffId()
    {
        return $this->staff_id;
    }

    /**
     * Get location ID.
     *
     * @return int
     */
    public function locationId()
    {
        return $this->location_id;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function state()
    {
        return $this->state;
    }

    /**
     * Get capacity.
     *
     * @return int
     */
    public function capacity()
    {
        return $this->capacity;
    }

    /**
     * Get nop.
     *
     * @return int
     */
    public function nop()
    {
        return $this->nop;
    }

    /**
     * Get number of persons on waiting list.
     *
     * @return int
     */
    public function onWaitingList()
    {
        return $this->on_waiting_list;
    }

    /**
     * Get next slot.
     *
     * @return Range
     */
    public function nextSlot()
    {
        return $this->next_slot;
    }

    /**
     * Get alternative slot.
     *
     * @return Range
     */
    public function altSlot()
    {
        return $this->alt_slot;
    }

    /**
     * Get previous alternative slot.
     *
     * @return Range
     */
    public function prevAltSlot()
    {
        return $this->prev_alt_slot;
    }

    /**
     * Get connection type with the next slot (CONSECUTIVE or PARALLEL).
     *
     * @return int
     */
    public function nextConnection()
    {
        return $this->next_connection;
    }

    /**
     * Check whether next slot is set.
     *
     * @return bool
     */
    public function hasNextSlot()
    {
        return $this->next_slot != null;
    }

    /**
     * Check whether alternative slot is set.
     *
     * @return bool
     */
    public function hasAltSlot()
    {
        return $this->alt_slot != null;
    }

    /**
     * Check whether previous alternative slot is set.
     *
     * @return bool
     */
    public function hasPrevAltSlot()
    {
        return $this->prev_alt_slot != null;
    }

    /**
     * Create a copy of the data with new staff ID.
     *
     * @param int $new_staff_id
     * @return static
     */
    public function replaceStaffId( $new_staff_id )
    {
        return new static( $this->service_id, $new_staff_id, $this->location_id, $this->state, $this->on_waiting_list, $this->capacity, $this->nop, $this->next_slot, $this->alt_slot, $this->prev_alt_slot, $this->next_connection );
    }

    /**
     * Create a copy of the data with new state.
     *
     * @param int $new_state
     * @return static
     */
    public function replaceState( $new_state )
    {
        return new static( $this->service_id, $this->staff_id, $this->location_id, $new_state, $this->on_waiting_list, $this->capacity, $this->nop, $this->next_slot, $this->alt_slot, $this->prev_alt_slot, $this->next_connection );
    }

    /**
     * Create a copy of the data with new on waiting list number.
     *
     * @param int $new_on_waiting_list
     * @return static
     */
    public function replaceOnWaitingList( $new_on_waiting_list )
    {
        return new static( $this->service_id, $this->staff_id, $this->location_id, $this->state, $new_on_waiting_list, $this->capacity, $this->nop, $this->next_slot, $this->alt_slot, $this->prev_alt_slot, $this->next_connection );
    }

    /**
     * Create a copy of the data with new capacity.
     *
     * @param int $new_capacity
     * @return static
     */
    public function replaceCapacity( $new_capacity )
    {
        return new static( $this->service_id, $this->staff_id, $this->location_id, $this->state, $this->on_waiting_list, $new_capacity, $this->nop, $this->next_slot, $this->alt_slot, $this->prev_alt_slot, $this->next_connection );
    }

    /**
     * Create a copy of the data with new nop.
     *
     * @param int $new_nop
     * @return static
     */
    public function replaceNop( $new_nop )
    {
        return new static( $this->service_id, $this->staff_id, $this->location_id, $this->state, $this->on_waiting_list, $this->capacity, $new_nop, $this->next_slot, $this->alt_slot, $this->prev_alt_slot, $this->next_connection );
    }

    /**
     * Create a copy of the data with new next slot.
     *
     * @param Range|null $new_next_slot
     * @param int|null $next_connection
     * @return static
     */
    public function replaceNextSlot( $new_next_slot, $next_connection = null )
    {
        return new static( $this->service_id, $this->staff_id, $this->location_id, $this->state, $this->on_waiting_list, $this->capacity, $this->nop, $new_next_slot, $this->alt_slot, $this->prev_alt_slot, $next_connection ?: $this->next_connection );
    }

    /**
     * Create a copy of the data with new alternative slot.
     *
     * @param Range|null $new_alt_slot
     * @return static
     */
    public function replaceAltSlot( $new_alt_slot )
    {
        return new static( $this->service_id, $this->staff_id, $this->location_id, $this->state, $this->on_waiting_list, $this->capacity, $this->nop, $this->next_slot, $new_alt_slot, $this->prev_alt_slot, $this->next_connection );
    }

    /**
     * Create a copy of the data with new previous alternative slot.
     *
     * @param Range|null $new_prev_alt_slot
     * @return static
     */
    public function replacePrevAltSlot( $new_prev_alt_slot )
    {
        return new static( $this->service_id, $this->staff_id, $this->location_id, $this->state, $this->on_waiting_list, $this->capacity, $this->nop, $this->next_slot, $this->alt_slot, $new_prev_alt_slot, $this->next_connection );
    }
}