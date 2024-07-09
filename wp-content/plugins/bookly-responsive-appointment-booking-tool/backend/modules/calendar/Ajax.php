<?php
namespace Bookly\Backend\Modules\Calendar;

use Bookly\Lib;
use Bookly\Lib\Config;
use Bookly\Lib\Entities\Staff;
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Utils\Common;

class Ajax extends Page
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => array( 'staff', 'supervisor' ) );
    }

    /**
     * Get data for Event Calendar
     */
    public static function getStaffAppointments()
    {
        $result = array();
        $one_day = new \DateInterval( 'P1D' );
        $start_date = new \DateTime( self::parameter( 'start' ) );
        $end_date = new \DateTime( self::parameter( 'end' ) );
        $location_ids = Config::locationsActive() && self::parameter( 'location_ids' ) !== '' ? explode( ',', self::parameter( 'location_ids', '' ) ) : array();

        // Determine display time zone
        $display_tz = Common::getCurrentUserTimeZone();

        // Due to possibly different time zones of staff members expand start and end dates
        // to provide 100% coverage of the requested date range
        $start_date->sub( $one_day );
        $end_date->add( $one_day );

        // Load staff members
        $query = Staff::query()->whereNot( 'visibility', 'archive' );
        if ( Config::proActive() ) {
            if ( Common::isCurrentUserSupervisor() ) {
                $query->whereIn( 'id', explode( ',', self::parameter( 'staff_ids' ) ) );
            } else {
                $query->where( 'wp_user_id', get_current_user_id() );
            }
        } else {
            $query->limit( 1 );
        }
        /** @var Staff[] $staff_members */
        $staff_members = $query->find();

        if ( ! empty ( $staff_members ) ) {
            $query = self::getAppointmentsQueryForCalendar( $staff_members, $start_date, $end_date, $location_ids );
            $appointments = self::buildAppointmentsForCalendar( $query, $display_tz );
            $result = array_merge( $result, $appointments );
            $schedule = self::buildStaffSchedule( $staff_members, $start_date, $end_date, $location_ids );
            $result = array_merge( $result, $schedule );
        }

        wp_send_json( $result );
    }

    /**
     * Update calendar refresh rate.
     */
    public static function updateCalendarRefreshRate()
    {
        $rate = (int) self::parameter( 'rate', 0 );
        update_user_meta( get_current_user_id(), 'bookly_calendar_refresh_rate', $rate );

        wp_send_json_success();
    }

    /**
     * Get appointments query for Event Calendar
     *
     * @param array $staff_members
     * @param \DateTime $start_date
     * @param \DateTime $end_date
     * @param array|null $location_ids
     * @return Lib\Query
     */
    public static function getAppointmentsQueryForCalendar( $staff_members, \DateTime $start_date, \DateTime $end_date, $location_ids )
    {
        $staff_ids = array_map( function ( $staff ) {
            return $staff->getId();
        }, $staff_members );

        $query = Lib\Entities\Appointment::query( 'a' )
            ->whereIn( 'st.id', $staff_ids )
            ->whereLt( 'a.start_date', $end_date->format( 'Y-m-d H:i:s' ) )
            ->whereRaw( 'DATE_ADD(a.end_date, INTERVAL a.extras_duration SECOND) >= \'%s\'', array( $start_date->format( 'Y-m-d H:i:s' ) ) );

        $service_ids = array_filter( explode( ',', self::parameter( 'service_ids' ) ) );

        if ( ! empty( $service_ids ) && ! in_array( 'all', $service_ids ) ) {
            $raw_where = array();
            if ( in_array( 'custom', $service_ids ) ) {
                $raw_where[] = 'a.service_id IS NULL';
            }

            $service_ids = array_filter( $service_ids, 'is_numeric' );
            if ( ! empty( $service_ids ) ) {
                $raw_where[] = 'a.service_id IN (' . implode( ',', $service_ids ) . ')';
            }

            if ( $raw_where ) {
                $query->whereRaw( implode( ' OR ', $raw_where ), array() );
            }
        }

        Proxy\Shared::prepareAppointmentsQueryForCalendar( $query, $start_date, $end_date, $location_ids );

        return $query;
    }

    /**
     * @param Staff[] $staff_members
     * @param $start_date
     * @param $end_date
     * @param $location_ids
     * @return array
     */
    public static function buildStaffSchedule( $staff_members, $start_date, $end_date, $location_ids )
    {
        $one_day = new \DateInterval( 'P1D' );

        // Determine display time zone
        $display_tz = Common::getCurrentUserTimeZone();

        $result = array();

        // Load special days.
        $special_days = array();
        $staff_ids = array_map( function ( $staff ) {
            return $staff->getId();
        }, $staff_members );
        $schedule = Lib\Proxy\SpecialDays::getSchedule( $staff_ids, $start_date, $end_date ) ?: array();
        foreach ( $schedule as $day ) {
            $staff_location_ids = is_array( $location_ids )
                ? array_unique(
                    array_map( function( $l ) use ( $day ) {
                        return Lib\Proxy\Locations::prepareStaffSpecialDaysLocationId( $l, $day['staff_id'] ) ?: null;
                    }, $location_ids )
                )
                : array();
            if ( ! $location_ids
                || in_array( 'all', $location_ids, false )
                || in_array( Lib\Proxy\Locations::prepareStaffSpecialDaysLocationId( $day['location_id'], $day['staff_id'] ) ?: null, $staff_location_ids, true ) )
            {
                $special_days[ $day['staff_id'] ][ $day['date'] ][] = $day;
            }
        }

        foreach ( $staff_members as $staff ) {
            // Schedule
            $schedule = array();
            $items = $staff->getScheduleItems();
            $day = clone $start_date;
            // Find previous day end time.
            $last_end = clone $day;
            $last_end->sub( $one_day );
            $end_time = $items[ (int) $last_end->format( 'w' ) + 1 ]->getEndTime();
            if ( $end_time !== null ) {
                $end_time = explode( ':', $end_time );
                $last_end->setTime( $end_time[0], $end_time[1] );
            } else {
                $last_end->setTime( 24, 0 );
            }
            // Do the loop.
            while ( $day < $end_date ) {
                $start = $last_end->format( 'Y-m-d H:i:s' );
                // Check if $day is Special Day for current staff.
                if ( isset ( $special_days[ $staff->getId() ][ $day->format( 'Y-m-d' ) ] ) ) {
                    $sp_days = $special_days[ $staff->getId() ][ $day->format( 'Y-m-d' ) ];
                    $end = $sp_days[0]['date'] . ' ' . $sp_days[0]['start_time'];
                    if ( $start < $end ) {
                        $schedule[] = compact( 'start', 'end' );
                    }
                    // Breaks.
                    foreach ( $sp_days as $sp_day ) {
                        if ( $sp_day['break_start'] ) {
                            $break_start = date(
                                'Y-m-d H:i:s',
                                strtotime( $sp_day['date'] ) + DateTime::timeToSeconds( $sp_day['break_start'] )
                            );
                            $break_end = date(
                                'Y-m-d H:i:s',
                                strtotime( $sp_day['date'] ) + DateTime::timeToSeconds( $sp_day['break_end'] )
                            );
                            $schedule[] = array(
                                'start' => $break_start,
                                'end' => $break_end,
                            );
                        }
                    }
                    $end_time = explode( ':', $sp_days[0]['end_time'] );
                    $last_end = clone $day;
                    $last_end->setTime( $end_time[0], $end_time[1] );
                } else {
                    $item = $items[ (int) $day->format( 'w' ) + 1 ];
                    if ( $item->getStartTime() && ! $staff->isOnHoliday( $day ) ) {
                        $end = $day->format( 'Y-m-d ' . $item->getStartTime() );
                        if ( $start < $end ) {
                            $schedule[] = compact( 'start', 'end' );
                        }
                        $last_end = clone $day;
                        $end_time = explode( ':', $item->getEndTime() );
                        $last_end->setTime( $end_time[0], $end_time[1] );

                        // Breaks.
                        foreach ( $item->getBreaksList() as $break ) {
                            $break_start = date(
                                'Y-m-d H:i:s',
                                $day->getTimestamp() + DateTime::timeToSeconds( $break['start_time'] )
                            );
                            $break_end = date(
                                'Y-m-d H:i:s',
                                $day->getTimestamp() + DateTime::timeToSeconds( $break['end_time'] )
                            );
                            $schedule[] = array(
                                'start' => $break_start,
                                'end' => $break_end,
                            );
                        }
                    }
                }

                $day->add( $one_day );
            }

            if ( $last_end->format( 'Ymd' ) !== $day->format( 'Ymd' ) ) {
                $schedule[] = array(
                    'start' => $last_end->format( 'Y-m-d H:i:s' ),
                    'end' => $day->format( 'Y-m-d 24:00:00' ),
                );
            }

            // Add schedule to result,
            // with appropriate time zone shift if needed
            $staff_tz = $staff->getTimeZone();
            $convert_tz = $staff_tz && $staff_tz !== $display_tz;
            foreach ( $schedule as $item ) {
                if ( $convert_tz ) {
                    $item['start'] = DateTime::convertTimeZone( $item['start'], $staff_tz, $display_tz );
                    $item['end'] = DateTime::convertTimeZone( $item['end'], $staff_tz, $display_tz );
                }
                $result[] = array(
                    'start' => $item['start'],
                    'end' => $item['end'],
                    'display' => 'background',
                    'resourceId' => $staff->getId(),
                );
            }
        }

        return $result;
    }
}