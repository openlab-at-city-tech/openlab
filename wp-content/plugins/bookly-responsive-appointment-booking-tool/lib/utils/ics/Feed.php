<?php
namespace Bookly\Lib\Utils\Ics;

use Bookly\Lib\UserBookingData;
use Bookly\Lib;

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
            $order = new Lib\Entities\Order( array( 'id' => $userData->getOrderId() ) );
            $description_template = Lib\Utils\Codes::getICSDescriptionTemplate();
            foreach ( $order->getCaItems() as $data ) {
                $description_codes = Lib\Utils\Codes::getICSCodes( $data['item'] );
                $ics->addEvent(
                    $data['item']->getAppointment()->getStartDate(),
                    $data['item']->getAppointment()->getEndDate(), $data['title'],
                    Lib\Utils\Codes::replace( $description_template, $description_codes, false ),
                    $data['item']->getAppointment()->getLocationId()
                );
            }
        }

        return $ics;
    }

    /**
     * @param Lib\Entities\Order $order
     * @return Feed
     */
    public static function createFromOrder( Lib\Entities\Order $order )
    {
        // Generate ICS feed.
        $ics = new self();
        $description_template = Lib\Utils\Codes::getICSDescriptionTemplate();
        foreach ( $order->getCaItems() as $data ) {
            $description_codes = Lib\Utils\Codes::getICSCodes( $data['item'] );
            $ics->addEvent(
                $data['item']->getAppointment()->getStartDate(),
                $data['item']->getAppointment()->getEndDate(), $data['title'],
                Lib\Utils\Codes::replace( $description_template, $description_codes, false ),
                $data['item']->getAppointment()->getLocationId()
            );
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