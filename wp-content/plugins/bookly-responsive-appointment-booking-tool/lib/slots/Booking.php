<?php
namespace Bookly\Lib\Slots;

/**
 * Class Booking
 *
 * @package Bookly\Lib\Slots
 */
class Booking
{
    /** @var int */
    protected $location_id;
    /** @var int */
    protected $service_id;
    /** @var int */
    protected $nop;
    /** @var int */
    protected $on_waiting_list;
    /** @var Range */
    protected $range;
    /** @var Range */
    protected $range_with_padding;
    /** @var int */
    protected $extras_duration;
    /** @var bool */
    protected $one_booking_per_slot;
    /** @var bool */
    protected $external;

    /**
     * Constructor.
     *
     * @param int $service_id
     * @param int $location_id
     * @param int $nop
     * @param int $on_waiting_list
     * @param string $start Format Y-m-d H:i[:s]
     * @param string $end Format Y-m-d H:i[:s]
     * @param int $padding_left
     * @param int $padding_right
     * @param int $extras_duration
     * @param bool $one_booking_per_slot
     * @param bool $external
     */
    public function __construct( $location_id, $service_id, $nop, $on_waiting_list, $start, $end, $padding_left, $padding_right, $extras_duration, $one_booking_per_slot, $external )
    {
        $this->location_id = (int) $location_id;
        $this->service_id = (int) $service_id;
        $this->nop = (int) $nop;
        $this->on_waiting_list = (int) $on_waiting_list;
        $this->range = Range::fromDates( $start, $end );
        $this->range_with_padding = $this->range->transform( -(int) $padding_left, (int) $padding_right );
        $this->extras_duration = (int) $extras_duration;
        $this->one_booking_per_slot = (bool) $one_booking_per_slot;
        $this->external = (bool) $external;
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
     * Get service ID.
     *
     * @return int
     */
    public function serviceId()
    {
        return $this->service_id;
    }

    /**
     * Get number of persons.
     *
     * @return int
     */
    public function nop()
    {
        return $this->nop;
    }

    /**
     * Increase number of persons by given value.
     *
     * @param int $value
     * @return static
     */
    public function incNop( $value )
    {
        $this->nop += $value;

        return $this;
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
     * Get range.
     *
     * @return Range
     */
    public function range()
    {
        return $this->range;
    }

    /**
     * Get range with padding.
     *
     * @return Range
     */
    public function rangeWithPadding()
    {
        return $this->range_with_padding;
    }

    /**
     * Get extras duration.
     *
     * @return int
     */
    public function extrasDuration()
    {
        return $this->extras_duration;
    }

    /**
     * Get one_booking_pre_slot.
     *
     * @return int
     */
    public function oneBookingPerSlot()
    {
        return $this->one_booking_per_slot;
    }

    /**
     * Check if booking is from external calendar (e.g. Google Calendar).
     *
     * @return bool
     */
    public function external()
    {
        return $this->external;
    }
}