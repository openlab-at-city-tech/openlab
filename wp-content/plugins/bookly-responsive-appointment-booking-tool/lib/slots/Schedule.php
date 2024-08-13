<?php
namespace Bookly\Lib\Slots;

class Schedule
{
    /** @var RangeCollection[] */
    protected $days = array();
    /** @var RangeCollection[] */
    protected $special_days = array();
    /** @var array */
    protected $holidays = array();

    /**
     * Add schedule for a day of the week.
     *
     * @param int $day_of_week 0(Sun)-6(Sat)
     * @param string $start Format H:i[:s]
     * @param string $end Format H:i[:s]
     * @return $this
     */
    public function addDay( $day_of_week, $start, $end )
    {
        $collection = new RangeCollection();

        $this->days[ $day_of_week ] = $collection->push( Range::fromTimes( $start, $end ) );

        return $this;
    }

    /**
     * Check whether specific weekday has been set.
     *
     * @param int $day_of_week
     * @return bool
     */
    public function hasDay( $day_of_week )
    {
        return isset ( $this->days[ $day_of_week ] );
    }

    /**
     * Add break.
     *
     * @param integer $day_of_week 0(Sun)-6(Sat)
     * @param string $start Format H:i[:s]
     * @param string $end Format H:i[:s]
     * @return $this
     */
    public function addBreak( $day_of_week, $start, $end )
    {
        $this->days[ $day_of_week ] = $this->days[ $day_of_week ]->subtract( Range::fromTimes( $start, $end ) );

        return $this;
    }

    /**
     * Add holiday.
     *
     * @param string $date Format Y-m[-d]
     * @return $this
     */
    public function addHoliday( $date )
    {
        $this->holidays[ $date ] = true;

        return $this;
    }

    /**
     * Add schedule for special day.
     *
     * @param string $date Format Y-m-d
     * @param string $start Format H:i[:s]
     * @param string $end Format H:i[:s]
     * @return $this
     */
    public function addSpecialDay( $date, $start, $end )
    {
        $collection = new RangeCollection();

        if ( $start !== null ) {
            // Add special day if it is not OFF.
            $collection->push( Range::fromTimes( $start, $end ) );
        }

        $this->special_days[ $date ] = $collection;

        return $this;
    }

    /**
     * Check whether special day has been set for specific date.
     *
     * @param string $date
     * @return bool
     */
    public function hasSpecialDay( $date )
    {
        return isset ( $this->special_days[ $date ] );
    }

    /**
     * Add special day break.
     *
     * @param string $date Format Y-m-d
     * @param string $start Format H:i[:s]
     * @param string $end Format H:i[:s]
     * @return $this
     */
    public function addSpecialBreak( $date, $start, $end )
    {
        $this->special_days[ $date ] = $this->special_days[ $date ]->subtract( Range::fromTimes( $start, $end ) );

        return $this;
    }

    /**
     * Get schedule for given date (the date must not be day off).
     *
     * @param DatePoint $dp
     * @param int $service_id
     * @param int $staff_id
     * @param int $location_id
     * @param string $staff_timezone
     *
     * @return RangeCollection
     */
    public function getRanges( DatePoint $dp, $service_id, $staff_id, $location_id, $staff_timezone )
    {
        $date_Ymd = $dp->format( 'Y-m-d' );

        if ( $staff_timezone ) {
            // Handle staff time zone
            // Convert date point to staff time zone
            $dp = DatePoint::fromStrInTz( $date_Ymd, $staff_timezone );
        }

        // Check for special day.
        if ( isset ( $this->special_days[ $date_Ymd ] ) ) {
            // Return special day schedule.
            $collection = $this->special_days[ $date_Ymd ];
        } else {
            // Return weekday schedule.
            $collection = $this->days[ $dp->format( 'w' ) ];
        }

        $range_data = new RangeData( $service_id, $staff_id, $location_id );

        return $collection
            // Convert to date ranges.
            ->map( function( Range $range ) use ( $dp, $range_data ) {
                return new Range(
                    $dp->modify( $range->start()->value() )->toWpTz(),
                    $dp->modify( $range->end()->value() )->toWpTz(),
                    $range_data
                );
            } );
    }

    /**
     * Create all day range (no staff hours or breaks are taken in account).
     *
     * @param DatePoint $dp
     * @param int $service_id
     * @param int $staff_id
     * @param int $location_id
     * @return RangeCollection
     */
    public function getAllDayRange( DatePoint $dp, $service_id, $staff_id, $location_id )
    {
        $collection = new RangeCollection();

        $date_Ymd = $dp->format( 'Y-m-d' );

        // Check for special day OFF.
        if ( isset ( $this->special_days[ $date_Ymd ] ) && $this->special_days[ $date_Ymd ]->isEmpty() ) {
            $collection = $this->special_days[ $date_Ymd ];
        } else {
            // Return weekday schedule.
            $collection->push( new Range( $dp, $dp->modify( '+1 day' ), new RangeData( $service_id, $staff_id, $location_id ) ) );
        }

        return $collection;
    }

    /**
     * Check if given date is day off.
     *
     * @param DatePoint $dp
     * @return bool
     */
    public function isDayOff( DatePoint $dp )
    {
        $date_Ymd = $dp->format( 'Y-m-d' );

        // Check for special day.
        if ( isset ( $this->special_days[ $date_Ymd ] ) && ! $this->special_days[ $date_Ymd ]->isEmpty() ) {
            return false;
        } else {
            // Check for weekday.
            if ( isset ( $this->days[ $dp->format( 'w' ) ] ) ) {
                // Check for holiday.
                if ( ! isset ( $this->holidays[ $date_Ymd ] ) ) {
                    // Check for repeating holiday.
                    $date_md = $dp->format( 'm-d' );
                    if ( ! isset ( $this->holidays[ $date_md ] ) ) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Computes intersection between two schedules.
     *
     * @param self $schedule
     * @return static
     */
    public function intersect( self $schedule )
    {
        $new_schedule = new static();

        // Weekdays.
        foreach ( $this->days as $day_of_week => $day ) {
            if ( $schedule->hasDay( $day_of_week ) ) {
                $new_day = new RangeCollection();
                foreach ( $schedule->days[ $day_of_week ]->all() as $range ) {
                    $new_day = $new_day->merge( $day->intersect( $range ) );
                }
                $new_schedule->days[ $day_of_week ] = $new_day;
            }
        }

        // Special days (ours).
        foreach ( $this->special_days as $date => $day ) {
            $new_day = new RangeCollection();
            // Check for special day.
            if ( $schedule->hasSpecialDay( $date ) ) {
                foreach ( $schedule->special_days[ $date ]->all() as $range ) {
                    $new_day = $new_day->merge( $day->intersect( $range ) );
                }
            } else {
                do {
                    $dp = DatePoint::fromStr( $date );
                    $day_of_week = $dp->format( 'w' );
                    // Check for weekday.
                    if ( $schedule->hasDay( $day_of_week ) ) {
                        // Check for holiday.
                        if ( ! isset ( $schedule->holidays[ $date ] ) ) {
                            // Check for repeating holiday.
                            $date_md = $dp->format( 'm-d' );
                            if ( ! isset ( $schedule->holidays[ $date_md ] ) ) {
                                foreach ( $schedule->days[ $day_of_week ]->all() as $range ) {
                                    $new_day = $new_day->merge( $day->intersect( $range ) );
                                }
                                break;
                            }
                        }
                    }
                    continue;
                } while ( false );
            }
            $new_schedule->special_days[ $date ] = $new_day;
        }

        // Special days (theirs).
        foreach ( $schedule->special_days as $date => $day ) {
            $new_day = new RangeCollection();
            // Check for special day.
            if ( $this->hasSpecialDay( $date ) ) {
                foreach ( $this->special_days[ $date ]->all() as $range ) {
                    $new_day = $new_day->merge( $day->intersect( $range ) );
                }
            } else {
                do {
                    $dp = DatePoint::fromStr( $date );
                    $day_of_week = $dp->format( 'w' );
                    // Check for weekday.
                    if ( $this->hasDay( $day_of_week ) ) {
                        // Check for holiday.
                        if ( ! isset ( $this->holidays[ $date ] ) ) {
                            // Check for repeating holiday.
                            $date_md = $dp->format( 'm-d' );
                            if ( ! isset ( $this->holidays[ $date_md ] ) ) {
                                foreach ( $this->days[ $day_of_week ]->all() as $range ) {
                                    $new_day = $new_day->merge( $day->intersect( $range ) );
                                }
                                break;
                            }
                        }
                    }
                    continue;
                } while ( false );
            }
            $new_schedule->special_days[ $date ] = $new_day;
        }

        // Holidays.
        $new_schedule->holidays = array_merge( $this->holidays, $schedule->holidays );

        return $new_schedule;
    }
}