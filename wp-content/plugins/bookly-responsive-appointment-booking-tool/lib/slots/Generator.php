<?php
namespace Bookly\Lib\Slots;

use Bookly\Lib\Proxy\Locations as LocationsProxy;
use Bookly\Lib\Proxy\Pro as ProProxy;

/**
 * Class Generator
 *
 * @package Bookly\Lib\Slots
 */
class Generator implements \Iterator
{
    const CONNECTION_CONSECUTIVE = 1;
    const CONNECTION_PARALLEL    = 2;

    /** @var Staff[] */
    protected $staff_members;
    /** @var Schedule[] */
    protected $staff_schedule;
    /** @var int */
    protected $slot_length;
    /** @var DatePoint */
    protected $dp;
    /** @var int */
    protected $location_id;
    /** @var int */
    protected $srv_id;
    /** @var int */
    protected $srv_duration;
    /** @var int */
    protected $srv_duration_days;
    /** @var int */
    protected $srv_padding_left;
    /** @var int */
    protected $srv_padding_right;
    /** @var int */
    protected $nop;
    /** @var int */
    protected $extras_duration;
    /** @var int */
    protected $full_duration;
    /** @var Range|null  Requested time range (null means no limit) */
    protected $time_limit;
    /** @var int */
    protected $spare_time;
    /** @var bool */
    protected $waiting_list_enabled;
    /** @var static */
    protected $next_generator;
    /** @var int */
    protected $next_connection;
    /** @var RangeCollection */
    protected $next_slots;
    /** @var RangeCollection[] */
    protected $past_slots;

    /**
     * Constructor.
     *
     * @param Staff[] $staff_members Array of Staff objects indexed by staff ID
     * @param Schedule|null $service_schedule
     * @param int $slot_length
     * @param int $location_id
     * @param int $service_id
     * @param int $service_duration
     * @param int $service_padding_left
     * @param int $service_padding_right
     * @param int $nop Number of persons
     * @param int $extras_duration
     * @param DatePoint $start_dp
     * @param string|null $time_from Limit results by start time (null - no limit)
     * @param string|null $time_to Limit results by end time (null - no limit)
     * @param int $spare_time Spare time next to service
     * @param bool $waiting_list_enabled
     * @param self|null $next_generator
     * @param int $next_connection
     */
    public function __construct(
        array $staff_members,
        $service_schedule,
        $slot_length,
        $location_id,
        $service_id,
        $service_duration,
        $service_padding_left,
        $service_padding_right,
        $nop,
        $extras_duration,
        DatePoint $start_dp,
        $time_from,
        $time_to,
        $spare_time,
        $waiting_list_enabled,
        $next_generator,
        $next_connection
    ) {
        $this->staff_members = array();
        $this->staff_schedule = array();
        $this->dp = $start_dp->modify( 'midnight' );
        $this->location_id = (int) $location_id;
        $this->srv_id = (int) $service_id ?: null;
        $this->srv_duration = (int) min( $service_duration, DAY_IN_SECONDS );
        $this->srv_duration_days = (int) ( $service_duration / DAY_IN_SECONDS );
        $this->srv_padding_left = (int) $service_padding_left;
        $this->srv_padding_right = (int) $service_padding_right;
        $this->slot_length = (int) ( $this->srv_duration_days ? DAY_IN_SECONDS : min( $slot_length, DAY_IN_SECONDS ) );
        $this->nop = (int) $nop;
        $this->extras_duration = (int) ( $this->srv_duration_days < 1 ? $extras_duration : 0 );
        $this->full_duration = $this->srv_duration + $this->extras_duration;
        $this->time_limit = $time_from && $time_to ? Range::fromTimes( $time_from, $time_to ) : null;
        $this->spare_time = (int) $spare_time;
        $this->waiting_list_enabled = (bool) $waiting_list_enabled;
        $this->next_generator = $next_generator;
        $this->next_connection = $next_connection;

        // Pick only those staff members who provides the service
        // and who can serve the requested number of persons.
        foreach ( $staff_members as $staff_id => $staff ) {
            // Check that staff provides the service.
            if ( $staff->providesService( $this->srv_id, $this->location_id ) ) {
                // Check that requested number of persons meets service capacity.
                $service = $staff->getService( $this->srv_id, $this->location_id );
                if ( $service->capacityMax() >= $this->nop && $service->capacityMin() <= $this->nop ) {
                    $location_id = LocationsProxy::servicesPerLocationAllowed() ? $this->location_id : 0;
                    $this->staff_members[ $staff_id ] = $staff;
                    // Prepare staff schedule.
                    $schedule = $staff->getSchedule( $location_id );
                    if ( $service_schedule ) {
                        $schedule = $schedule->intersect( $service_schedule );
                    }
                    $this->staff_schedule[ $staff_id ] = $schedule;
                }
            }
        }

        // Init next generator.
        if ( $this->next_generator ) {
            $this->next_slots = new RangeCollection();
            $this->next_generator->rewind();
        }
    }

    /**
     * @inheritDoc
     * @return RangeCollection
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        $result = new RangeCollection();

        // Loop through all staff members.
        foreach ( $this->staff_members as $staff_id => $staff ) {
            $schedule = $this->staff_schedule[ $staff_id ];
            // Check that staff is not off.
            if ( ! $schedule->isDayOff( $this->dp ) ) {
                // Create ranges from staff schedule.
                $ranges = $this->srv_duration_days
                    ? $schedule->getAllDayRange( $this->dp, $this->srv_id, $staff_id, $this->location_id )
                    : $schedule->getRanges( $this->dp, $this->srv_id, $staff_id, $this->location_id, $staff->getTimeZone() );

                // Create booked ranges from staff bookings.
                $ranges = $this->_mapStaffBookings( $ranges, $staff );

                // Remove ranges with hours limit
                $ranges = ProProxy::prepareGeneratorRanges( $ranges, $staff, $this->full_duration );

                // Find slots.
                $max_capacity = $staff->getService( $this->srv_id, $this->location_id )->capacityMax();
                foreach ( $ranges->all() as $range ) {
                    $range = $range->replaceCapacity( $max_capacity );
                    // With available ranges we need to adjust their length.
                    if ( $range->state() == Range::AVAILABLE ) {
                        // Shorten range by service and extras duration.
                        $range = $range->transform( null, -$this->full_duration );
                        if ( ! $range->valid() ) {
                            // If range is not valid skip it.
                            continue;
                        }
                        // Enlarge range by slot length.
                        $range = $range->transform( null, $this->slot_length );
                    }
                    // Split range into slots.
                    foreach ( $range->split( $this->slot_length )->all() as $slot ) {
                        if ( $slot->length() < $this->slot_length ) {
                            // Skip slots with not enough length.
                            continue;
                        }
                        if ( $this->time_limit !== null && ! $this->srv_duration_days ) {
                            // Convert slot start date into time in seconds from current datepoint
                            $start_time = ( $slot->start()->value()->format( 'G' ) * 60 + $slot->start()->value()->format( 'i' ) ) * 60;
                            // Apply date offset.
                            $start_time += DAY_IN_SECONDS * ( ( 8 + $slot->start()->value()->format( 'w' ) - $this->dp->format( 'w' ) ) % 7 - 1 );
                            if ( ! $this->time_limit->wraps( new Range( new TimePoint( $start_time ), new TimePoint( $start_time + $this->full_duration ) ) ) ) {
                                // Skip slots outside customers time limit.
                                continue;
                            }
                        }
                        // For consecutive/parallel bookings try to find a next slot.
                        if ( $this->next_generator && $slot->notFullyBooked() ) {
                            $candidate_slot = $this->_tryFindNextSlot( $slot );
                            if ( ! $candidate_slot ) {
                                // If no next slot was found then skip this slot
                                // (for multi-day services we continue search, i.e. try to find past slot).
                                if ( $this->srv_duration_days <= 1 ) {
                                    continue;
                                }
                            } else {
                                $slot = $candidate_slot;
                            }
                        }
                        // For multi-day services try to find available day in the past.
                        if ( $this->srv_duration_days > 1 && ( $slot = $this->_tryFindPastSlot( $slot ) ) === false ) {
                            continue;
                        }
                        // Decide whether to add slot or skip it.
                        $timestamp = $slot->start()->value()->getTimestamp();
                        $ex_slot = null;
                        if ( $result->has( $timestamp ) ) {
                            // If result already has this timestamp...
                            if ( $slot->fullyBooked() ) {
                                // Skip the slot if it is fully booked.
                                continue;
                            } else {
                                $ex_slot = $result->get( $timestamp );
                                if ( $ex_slot->notFullyBooked() && $slot->waitingListStarted() && $ex_slot->noWaitingListStarted() ) {
                                    // Skip the slot if it has waiting list started but the existing one does not.
                                    continue;
                                }
                            }
                        }
                        // Decide which slot to add.
                        if ( $ex_slot && $ex_slot->notFullyBooked() && ( $slot->waitingListStarted() || $ex_slot->noWaitingListStarted() ) ) {
                            $slot = $this->_findPreferableSlot( $slot, $ex_slot );
                        }
                        // Add slot to result.
                        $result->put( $timestamp, $slot );
                    }
                }
            }
        }

        return $result->ksort();
    }

    /**
     * Create fully/partially booked ranges from staff bookings.
     *
     * @param RangeCollection $ranges
     * @param Staff $staff
     * @return RangeCollection
     */
    private function _mapStaffBookings( RangeCollection $ranges, $staff )
    {
        $waiting_list_despite_capacity = get_option( 'bookly_waiting_list_despite_capacity' );
        if ( $ranges->isNotEmpty() ) {
            $service = $staff->getService( $this->srv_id, $this->location_id );
            $max_capacity = $service->capacityMax();
            $max_waiting_list_capacity = $service->waitingListCapacity();
            foreach ( $staff->getBookings() as $booking ) {
                // Take in account booking and service padding.
                $range_to_remove = $booking->rangeWithPadding()->transform( -$this->srv_padding_right, $this->srv_padding_left );
                // Remove booking from ranges.
                $new_ranges = new RangeCollection();
                $removed = new RangeCollection();
                foreach ( $ranges->all() as $r ) {
                    if ( $r->available() && $r->overlaps( $range_to_remove ) ) {
                        $new_ranges = $new_ranges->merge( $r->subtract(
                        // Make sure that removed range will have length of a multiple of slot length.
                            $range_to_remove->align( $r, $this->slot_length, $this->full_duration ),
                            $removed_range
                        ) );
                        /** @var Range $removed_range */
                        if ( $removed_range ) {
                            $removed->push( $removed_range );
                            // Find range that should be marked as fully booked and add it to results.
                            $r = $r->transform( null, -$this->full_duration );
                            if ( $r->valid() ) {
                                $r = $r->transform( null, $this->slot_length );
                                $new_ranges->push(
                                    $r->intersect(
                                        $removed_range->transform(
                                            -$this->full_duration + $this->slot_length,
                                            null
                                        // Align without considering precursor here.
                                        )->align( $r, $this->slot_length, $this->slot_length )
                                    )->replaceState( Range::FULLY_BOOKED )
                                );
                            }
                        }
                    } else {
                        $new_ranges->push( $r );
                    }
                }
                $ranges = $new_ranges;
                // If some ranges were removed check whether we need to add them back with appropriate state.
                if (
                    $removed->isNotEmpty() &&
                    ( ! $booking->locationId() || ! $this->location_id || $booking->locationId() == $this->location_id ) &&
                    $booking->serviceId() == $this->srv_id &&
                    $booking->range()->length() - $booking->extrasDuration() == ( $this->srv_duration_days > 1 ? $this->srv_duration_days * DAY_IN_SECONDS : $this->srv_duration ) &&
                    $booking->extrasDuration() >= $this->extras_duration
                ) {
                    // Handle partially booked appointments (when number of persons is less than max capacity).
                    if ( $booking->nop() - ( $waiting_list_despite_capacity ? 0 : $booking->onWaitingList() ) <= $max_capacity - $this->nop && ! $booking->oneBookingPerSlot() ) {
                        $booking_range = $booking->range();
                        foreach ( $removed->all() as $range ) {
                            // Find range which contains booking start point.
                            if ( $range->contains( $booking_range->start() ) ) {
                                $data = $range->data()->replaceState( Range::PARTIALLY_BOOKED )->replaceNop( $booking->nop() );
                                // Create partially booked range and add it to collection.
                                $ranges->push( $booking_range->resize( $this->slot_length )->replaceData( $data ) );
                                break;
                            }
                        }
                    } // Handle waiting list.
                    elseif ( $this->waiting_list_enabled ) {
                        $booking_range = $booking->range();
                        foreach ( $removed->all() as $range ) {
                            // Find range which contains booking start point.
                            if ( ( $max_waiting_list_capacity === null || ( $booking->nop() <= $max_capacity + $max_waiting_list_capacity - $this->nop ) ) && $range->contains( $booking_range->start() ) ) {
                                $data = $range->data()->replaceState( Range::WAITING_LIST_STARTED )->replaceNop( $booking->nop() );
                                if ( $booking->onWaitingList() ) {
                                    $data = $data->replaceOnWaitingList( $booking->onWaitingList() );
                                }
                                // Create partially booked range and add it to collection.
                                $ranges->push( $booking_range->resize( $this->slot_length )->replaceData( $data ) );
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $ranges;
    }

    /**
     * Try to find next slot for consecutive bookings.
     *
     * @param Range $slot
     * @return Range|false
     */
    private function _tryFindNextSlot( Range $slot )
    {
        if ( $this->next_connection == self::CONNECTION_CONSECUTIVE ) {
            $padding = $this->srv_padding_right + $this->next_generator->srv_padding_left;
            $next_start = $slot->start()->modify( max( $this->full_duration + $this->spare_time, $this->slot_length - $padding ) );
            // There are 2 possible options:
            // 1. next service is done by another staff, then do not take into account padding
            // 2. next service is done by the same staff, then count padding
            $next_slot = $this->_findNextSlot( $next_start );
            if (
                $next_slot == false ||
                $next_slot->fullyBooked() ||
                $padding != 0 && $next_slot->staffId() == $slot->staffId()
            ) {
                $next_slot = $this->_findNextSlot( $next_start->modify( $padding ) );
                if ( $next_slot && (
                        $next_slot->fullyBooked() ||
                        $next_slot->staffId() != $slot->staffId()
                    ) ) {
                    $next_slot = false;
                }
            }
        } else {
            $next_slot = $this->_findNextSlot( $slot->start() );
            if ( $next_slot ) {
                if ( $next_slot->fullyBooked() ) {
                    $next_slot = false;
                } else {
                    // Make sure that all parallel slots have unique staff ids
                    $next_slot = $next_slot->mayBeAltSlot( array( $slot->staffId() ) );
                }
            }
        }

        if ( $next_slot ) {
            // Connect slots with each other.
            $slot = $slot->replaceNextSlot( $next_slot, $this->next_connection );
        } else {
            // If no next slot was found then return false.
            return false;
        }

        return $slot;
    }

    /**
     * Store slot for further reference in _tryFindPastSlot().
     *
     * @param Range $slot
     * @return void
     */
    private function _storePastSlot( Range $slot )
    {
        if ( ! isset ( $this->past_slots[ $slot->staffId() ] ) ) {
            $this->past_slots[ $slot->staffId() ] = new RangeCollection();
        }

        // @todo In theory we can hold just $this->srv_duration_days slots in the past.
        $timestamp = $slot->start()->value()->getTimestamp();
        $this->past_slots[ $slot->staffId() ]->put( $timestamp, $slot );
    }

    /**
     * Try to find a valid slot in the past for multi-day services.
     *
     * @param Range $slot
     * @return Range|bool
     */
    private function _tryFindPastSlot( Range $slot )
    {
        $this->_storePastSlot( $slot );

        // Find past slot
        $timestamp = $slot->start()->modify( sprintf( '-%s days', $this->srv_duration_days - 1 ) )->value()->getTimestamp();
        $past_slot = $this->past_slots[ $slot->staffId() ]->get( $timestamp );
        if ( ! $past_slot ) {
            return false;
        }

        // If there is a next generator, then we want to pass only fully booked slots
        // or slots for which the next slot was found
        if ( $this->next_generator ) {
            if ( $this->next_connection == self::CONNECTION_CONSECUTIVE ) {
                // For consecutive booking the current slot must have a next slot
                if ( ! $slot->hasNextSlot() && $past_slot->notFullyBooked() ) {
                    return false;
                }
                // Replace next slot with the one from the current slot
                $past_slot = $past_slot->replaceNextSlot( $slot->nextSlot() );
            } else {
                // For parallel booking the past slot must have a next slot
                if ( ! $past_slot->hasNextSlot() && $past_slot->notFullyBooked() ) {
                    return false;
                }
            }
        }

        // If past slot is partially booked or has waiting list started then it is good to go,
        // otherwise we need to check days from past to current slot
        if ( $past_slot->notPartiallyBooked() && $past_slot->noWaitingListStarted() ) {
            // Check if there are enough valid days for service duration in the past.
            $day = $slot->start();
            for ( $d = 0; $d < $this->srv_duration_days; ++ $d ) {
                $timestamp = $day->value()->getTimestamp();
                $day_slot = $this->past_slots[ $slot->staffId() ]->get( $timestamp );
                if ( ! $day_slot || $day_slot->fullyBooked() && $past_slot->notFullyBooked() ) {
                    // If day slot is fully booked and past slot is not then we exit here
                    return false;
                }
                $day = $day->modify( '-1 day' );
            }
        }

        return $past_slot;
    }

    /**
     * Find next slot for consecutive bookings.
     *
     * @param IPoint $start
     * @return Range|false
     */
    private function _findNextSlot( IPoint $start )
    {
        while (
            $this->next_generator->valid() &&
            // Do search only while next generator is producing slots earlier than the requested point.
            // +1 day because of with staff timezone $start can be in a day before next generator dp
            $start->modify( ( $this->next_generator->srv_duration_days + 1 ) . ' days' )->gt( $this->next_generator->key() )
        ) {
            $this->next_slots = $this->next_slots->union( $this->next_generator->current() );
            $this->next_generator->next();
        }

        return $this->next_slots->get( $start->value()->getTimestamp() );
    }

    /**
     * Find more preferable slot and store the other one as alternative.
     *
     * @param Range $slot
     * @param Range $ex_slot
     * @return Range
     */
    private function _findPreferableSlot( $slot, $ex_slot )
    {
        // Find which staff is more preferable.
        $staff = $this->staff_members[ $slot->staffId() ];
        $ex_staff = $this->staff_members[ $ex_slot->staffId() ];
        if ( $staff->morePreferableThan( $ex_staff, $slot ) ) {
            $slot = $slot->replaceAltSlot( $ex_slot->replacePrevAltSlot( $slot ) );
        } else {
            if ( $ex_slot->hasAltSlot() ) {
                $slot = $this->_findPreferableSlot( $slot, $ex_slot->altSlot() );
            }
            $slot = $ex_slot->replaceAltSlot( $slot->replacePrevAltSlot( $ex_slot ) );
        }

        return $slot;
    }

    /**
     * Get service duration in days.
     *
     * @return int
     */
    public function serviceDurationInDays()
    {
        return $this->srv_duration_days;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        // Start one day earlier to cover night shifts.
        $this->dp = $this->dp->modify( '-1 day midnight' );
    }

    /**
     * @inheritDoc
     * @return DatePoint
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->dp;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->dp = $this->dp->modify( '+1 day midnight' );
    }

    /**
     * Infinite search.
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return true;
    }
}