<?php
namespace Bookly\Lib\Utils\Ics;

use Bookly\Lib\DataHolders\Booking\Simple;
use Bookly\Lib\UserBookingData;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib;

/**
 * Class Feed
 *
 * @package Bookly\Lib\Utils\Ics
 */
class Feed
{
    /** @var Event[] */
    protected $events = array();

    /**
     * @return string
     */
    public function render()
    {
        $content = '';
        foreach ( $this->events as $event ) {
            $content .= $event->render();
        }

        return "BEGIN:VCALENDAR\r\n"
            . "VERSION:2.0\r\n"
            . "PRODID:-//Bookly\r\n"
            . "CALSCALE:GREGORIAN\r\n"
            . $content
            . 'END:VCALENDAR';
    }

    /**
     * @param string $start_date
     * @param string $end_date
     * @param string $summary
     * @param string $description
     * @param int $location_id
     * @return $this
     */
    public function addEvent( $start_date, $end_date, $summary, $description, $location_id = null )
    {
        $event = new Event();
        $event
            ->setStartDate( $start_date )
            ->setEndDate( $end_date )
            ->setLocationId( $location_id )
            ->setSummary( $summary )
            ->setDescription( $description );
        $this->events[] = $event;

        return $this;
    }

    /**
     * @param UserBookingData $userData
     * @return Feed
     */
    public static function createFromBookingData( UserBookingData $userData )
    {
        // Generate ICS feed.
        $ics = new self();

        if ( $userData->load() && $userData->getOrderId() ) {
            $query = CustomerAppointment::query( 'ca' )
                ->select( 'ca.id as ca_id, COALESCE(ca.compound_service_id,ca.collaborative_service_id,a.service_id) AS service_id, a.custom_service_name, a.location_id, s.title AS service_title, a.start_date, a.end_date, st.full_name AS staff_name' )
                ->where( 'ca.order_id', $userData->getOrderId() )
                ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
                ->leftJoin( 'Service', 's', 's.id = COALESCE(ca.compound_service_id,ca.collaborative_service_id,a.service_id)' )
                ->leftJoin( 'Staff', 'st', 'st.id = a.staff_id' )
                ->groupBy( 'COALESCE(ca.compound_token, ca.collaborative_token, ca.id)' )
                ->groupBy( 'a.id' );
            $description_template = Lib\Utils\Codes::getICSDescriptionTemplate();
            foreach ( $query->fetchArray() as $appointment ) {
                $item = Simple::create( CustomerAppointment::find( $appointment['ca_id'] ) );
                $description_codes = Lib\Utils\Codes::getICSCodes( $item );
                if ( $appointment['service_id'] === null ) {
                    $service_name = $appointment['custom_service_name'];
                } else {
                    $service_name = Common::getTranslatedString( 'service_' . $appointment['service_id'], $appointment['service_title'] );
                }

                $ics->addEvent( $appointment['start_date'], $appointment['end_date'], $service_name, Lib\Utils\Codes::replace( $description_template, $description_codes, false ), $appointment['location_id'] );
            }
        }

        return $ics;
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }
}