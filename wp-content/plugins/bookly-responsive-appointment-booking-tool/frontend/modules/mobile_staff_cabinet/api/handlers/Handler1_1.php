<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api\Handlers;

use Bookly\Lib;
use Bookly\Lib\Entities\Service;
use Bookly\Frontend\Modules\MobileStaffCabinet\Api\Exceptions;

class Handler1_1  extends Handler
{
    public static function getVersion()
    {
        return '1.1';
    }

    /**
     * @return array
     * @throws Exceptions\ParameterException
     */
    protected function slots()
    {
        return ( get_option( 'bookly_appointments_displayed_time_slots' ) === 'appropriate' )
            ? $this->appropriateSlots()
            : $this->simpleSlots();
    }

    /**
     * @return array
     * @throws Exceptions\ParameterException
     */
    private function simpleSlots()
    {
        $service_id = $this->param( 'service_id' );
        $service = Service::find( $service_id );
        $appointments_time_delimiter = get_option( 'bookly_appointments_time_delimiter', 0 ) * MINUTE_IN_SECONDS;

        if ( ! $service ) {
            $service = new Service();
            $service->setDuration( Lib\Config::getTimeSlotLength() );
        }

        $date = $this->getDateFormattedParameter( 'date', 'Y-m-d' );
        if ( ! $appointments_time_delimiter && ( $ts_length = (int) $service->getSlotLength() ) ) {
            $time_end = max( $service->getUnitsMax() * $service->getDuration() + DAY_IN_SECONDS, DAY_IN_SECONDS * 2 );
        } else {
            $ts_length = $appointments_time_delimiter > 0 ? $appointments_time_delimiter : Lib\Config::getTimeSlotLength();
            $time_end = max( ( $service->getUnitsMax() * $service->getDuration() ) + DAY_IN_SECONDS, DAY_IN_SECONDS * 2 );
        }

        return array(
            'mode' => 'all',
            'start' => $this->generateSimpleSlots( 0, $time_end, $ts_length, $date, true ),
            'end' => $this->generateSimpleSlots( 0, $time_end, $ts_length, $date, false ),
        );
    }

    /**
     * @return array
     * @throws Exceptions\ParameterException
     */
    private function appropriateSlots()
    {
        if ( $this->role === self::ROLE_SUPERVISOR ) {
            $staff_id = $this->param( 'staff_id' );
            if ( ! $staff_id ) {
                throw new Exceptions\ParameterException( 'staff_id', $this->param( 'staff_id' ) );
            }
        } else {
            $staff_id = $this->staff->getId();
        }
        $date = $this->getDateFormattedParameter( 'date', 'Y-m-d' );

        $result = Lib\Utils\Appointment::getDaySchedule(
            array( $staff_id ),
            $this->param( 'service_id' ),
            $date,
            $this->param( 'appointment_id' ),
            $this->param( 'location_id' ),
            $this->param( 'extras', array() ),
            max( 1, $this->param( 'nop', 1 ) )
        );

        return array(
            'mode' => 'appropriate',
            'start' => $result ? $this->generateAppropriateSlots( $result['start'], $date ) : array(),
            'end' => $result ? $this->generateAppropriateSlots( $result['end'], $date ) : array(),
        );
    }

    /**
     * @param array $slots
     * @param string $date
     *
     * @return array{value: string}[]
     */
    protected function generateAppropriateSlots( array $slots, $date )
    {
        $result = array();
        foreach ( $slots as $slot ) {
            if ( ! $slot['disabled'] ) {
                $result[] = array(
                    'value' => $date . ' ' . $slot['value'] . ':00',
                );
            }
        }

        return $result;
    }

    /**
     * @param integer $time_start
     * @param integer $time_end
     * @param integer $ts_length
     * @param string $date
     * @param bool $first_day
     *
     * @return array{value: string}[]
     */
    protected function generateSimpleSlots( $time_start, $time_end, $ts_length, $date, $first_day = true )
    {
        $slots = array();
        $date_start = date_create( $date )->modify( '+' . $time_start . ' seconds' );
        $date_end = date_create( $date )->modify( '+' . $time_end . ' seconds' );
        while ( $date_start < $date_end ) {
            if ( ! $first_day || $date == $date_start->format( 'Y-m-d' ) ) {
                $slots[] = array(
                    'value' => $date_start->format( 'Y-m-d H:i:s' ),
                );
            }
            $date_start->modify( '+' . $ts_length . ' seconds' );
        }

        return $slots;
    }
}