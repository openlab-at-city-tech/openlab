<?php
namespace Bookly\Lib\Slots;

use Bookly\Lib\Entities;
use Bookly\Lib\Proxy;

class Staff
{
    /** @var Schedule[] */
    protected $schedule;
    /** @var Booking[] */
    protected $bookings;
    /** @var Service[] */
    protected $services;
    /** @var array */
    protected $workload;
    /** @var int */
    protected $working_time_limit;
    /** @var string|null */
    protected $timezone;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->schedule = array( new Schedule() );
        $this->bookings = array();
        $this->services = array();
    }

    /**
     * Get schedule.
     *
     * @param int $location_id
     * @return Schedule
     */
    public function getSchedule( $location_id = 0 )
    {
        return isset ( $this->schedule[ $location_id ] )
            ? $this->schedule[ $location_id ]
            : $this->schedule[0];
    }

    /**
     * @param Schedule $schedule
     * @param int $location_id
     *
     * @return $this
     */
    public function setSchedule( $schedule, $location_id = 0 )
    {
        $this->schedule[ $location_id ] = $schedule;

        return $this;
    }

    /**
     * @return array
     */
    public function getScheduleLocations()
    {
        return array_keys( $this->schedule );
    }

    /**
     * Add holiday.
     *
     * @param string $date Format Y-m[-d]
     * @return $this
     */
    public function addHoliday( $date )
    {
        foreach ( $this->schedule as $schedule ) {
            $schedule->addHoliday( $date );
        }

        return $this;
    }

    /**
     * @param array $day
     * @return $this
     */
    public function addSpecialDay( $day )
    {
        $location_id = $day['location_id'] ?: 0;
        if ( ! in_array( $location_id, $this->getScheduleLocations() ) ) {
            $this->setSchedule( new Schedule(), $location_id );
        }
        $schedule = $this->getSchedule( $location_id );

        if ( ! $schedule->hasSpecialDay( $day['date'] ) ) {
            $schedule->addSpecialDay( $day['date'], $day['start_time'], $day['end_time'] );
        }
        if ( $day['break_start'] ) {
            $schedule->addSpecialBreak( $day['date'], $day['break_start'], $day['break_end'] );
        }

        return $this;
    }

    /**
     * Add booking.
     *
     * @param Booking $booking
     * @return $this
     */
    public function addBooking( Booking $booking )
    {
        $this->bookings[] = $booking;

        $date = $booking->range()->start()->format( 'Y-m-d' );
        if ( ! isset ( $this->workload[ $date ] ) ) {
            $this->workload[ $date ] = 0;
        }
        $this->workload[ $date ] += $booking->range()->length();

        return $this;
    }

    /**
     * Get bookings.
     *
     * @return Booking[]
     */
    public function getBookings()
    {
        return $this->bookings;
    }

    /**
     * Add service.
     *
     * @param int $service_id
     * @param int $location_id
     * @param double $price
     * @param int $capacity_min
     * @param int $capacity_max
     * @param int $waiting_list_capacity
     * @param string $staff_preference_rule
     * @param array $staff_preference_settings
     * @param int $staff_preference_order
     * @return $this
     */
    public function addService(
        $service_id,
        $location_id,
        $price,
        $capacity_min,
        $capacity_max,
        $waiting_list_capacity,
        $staff_preference_rule,
        $staff_preference_settings,
        $staff_preference_order
    ) {
        $this->services[ $service_id ][ $location_id ] = new Service(
            $price,
            $capacity_min,
            $capacity_max,
            $waiting_list_capacity,
            $staff_preference_rule,
            $staff_preference_settings,
            $staff_preference_order
        );

        return $this;
    }

    /**
     * Set working_time_limit
     *
     * @param int $working_time_limit
     * @return $this
     */
    public function setWorkingTimeLimit( $working_time_limit )
    {
        $this->working_time_limit = $working_time_limit;

        return $this;
    }

    /**
     * Get working_time_limit
     *
     * @return int
     */
    public function getWorkingTimeLimit()
    {
        return $this->working_time_limit;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return $this
     */
    public function setTimeZone( $timezone )
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timezone;
    }

    /**
     * Tells whether staff provides given service.
     *
     * @param int $service_id
     * @param int $location_id
     * @return bool
     */
    public function providesService( $service_id, $location_id )
    {
        return isset ( $this->services[ $service_id ][ $location_id ] );
    }

    /**
     * Get service by ID.
     *
     * @param int $service_id
     * @param int $location_id
     * @return Service
     */
    public function getService( $service_id, $location_id )
    {
        return isset ( $this->services[ $service_id ][ $location_id ] )
            ? $this->services[ $service_id ][ $location_id ]
            : $this->services[ $service_id ][0];
    }

    /**
     * Get workload for given date.
     *
     * @param $date
     * @return int
     */
    public function getWorkload( $date )
    {
        if ( isset ( $this->workload[ $date ] ) ) {
            return $this->workload[ $date ];
        }

        return 0;
    }

    /**
     * Get workload for given period.
     *
     * @param DatePoint $from
     * @param DatePoint $to
     * @return int
     */
    public function getWorkloadForPeriod( DatePoint $from, DatePoint $to )
    {
        $result = 0;

        for ( $dp = $from; $dp->lte( $to ); $dp = $dp->modify( '+1 day' ) ) {
            $date = $dp->format( 'Y-m-d' );
            if ( isset ( $this->workload[ $date ] ) ) {
                $result += $this->workload[ $date ];
            }
        }

        return $result;
    }

    /**
     * Check whether this staff if more preferable than the given one for given time slot.
     *
     * @param Staff $staff
     * @param Range $slot
     * @return bool
     */
    public function morePreferableThan( Staff $staff, Range $slot )
    {
        $service_id = $slot->serviceId();
        $location_id = Proxy\Locations::servicesPerLocationAllowed() ? $slot->locationId() : 0;
        $service = $this->getService( $service_id, $location_id );
        $settings = $service->getStaffPreferenceSettings();

        switch ( $service->getStaffPreferenceRule() ) {
            case Entities\Service::PREFERRED_ORDER:
                $value1 = $service->getStaffPreferenceOrder();
                $value2 = $staff->getService( $service_id, $location_id )->getStaffPreferenceOrder();
                break;
            case Entities\Service::PREFERRED_LEAST_OCCUPIED:
                $date = $slot->start()->value()->format( 'Y-m-d' );
                $value1 = $this->getWorkload( $date );
                $value2 = $staff->getWorkload( $date );
                break;
            case Entities\Service::PREFERRED_MOST_OCCUPIED:
                $date = $slot->start()->value()->format( 'Y-m-d' );
                $value1 = $staff->getWorkload( $date );
                $value2 = $this->getWorkload( $date );
                break;
            case Entities\Service::PREFERRED_LEAST_OCCUPIED_FOR_PERIOD:
                $from = $slot->start()->modify( sprintf( '-%d days', $settings['period']['before'] ) );
                $to = $slot->start()->modify( sprintf( '+%d days', $settings['period']['after'] ) );
                $value1 = $this->getWorkloadForPeriod( $from, $to );
                $value2 = $staff->getWorkloadForPeriod( $from, $to );
                break;
            case Entities\Service::PREFERRED_MOST_OCCUPIED_FOR_PERIOD:
                $from = $slot->start()->modify( sprintf( '-%d days', $settings['period']['before'] ) );
                $to = $slot->start()->modify( sprintf( '+%d days', $settings['period']['after'] ) );
                $value1 = $staff->getWorkloadForPeriod( $from, $to );
                $value2 = $this->getWorkloadForPeriod( $from, $to );
                break;
            case Entities\Service::PREFERRED_LEAST_EXPENSIVE:
                $value1 = $service->price();
                $value2 = $staff->getService( $service_id, $location_id )->price();
                break;
            case Entities\Service::PREFERRED_MOST_EXPENSIVE:
            default:
                $value1 = $staff->getService( $service_id, $location_id )->price();
                $value2 = $service->price();
                break;
        }

        return ( $value1 == $value2 && isset ( $settings['random'] ) && $settings['random'] ) ? rand( 0, 1 ) == 1 : $value1 < $value2;
    }
}