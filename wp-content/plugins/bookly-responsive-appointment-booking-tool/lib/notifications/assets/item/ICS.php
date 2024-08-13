<?php
namespace Bookly\Lib\Notifications\Assets\Item;

use Bookly\Lib;

class ICS extends Lib\Utils\Ics\Base
{
    protected $data;

    /**
     * Constructor.
     *
     * @param Codes $codes
     * @param string $recipient
     */
    public function __construct( Codes $codes, $recipient = 'client' )
    {
        /** @var Lib\DataHolders\Booking\Simple $item */
        $item = $codes->getItem();
        if ( $item->getAppointment()->getStartDate() ) {
            $this->empty = false;
            $description_template = $this->getDescriptionTemplate( $recipient );
            $this->data = sprintf(
                "BEGIN:VCALENDAR\n"
                . "VERSION:2.0\n"
                . "PRODID:-//Bookly\n"
                . "CALSCALE:GREGORIAN\n"
                . "BEGIN:VEVENT\n"
                . "ORGANIZER;%s\n"
                . "DTSTAMP:%s\n"
                . "DTSTART:%s\n"
                . "DTEND:%s\n"
                . "SUMMARY:%s\n"
                . "DESCRIPTION:%s\n"
                . "LOCATION:%s\n"
                . "END:VEVENT\n"
                . "END:VCALENDAR",
                $this->escape( sprintf( 'CN=%s:mailto:%s', $codes->staff_name, $codes->staff_email ) ),
                $this->formatDateTime( $item->getAppointment()->getStartDate() ),
                $this->formatDateTime( $item->getAppointment()->getStartDate() ),
                $this->formatDateTime( $item->getAppointment()->getEndDate() ),
                $this->escape( $codes->service_name ),
                $this->escape( $codes->replace( $description_template ) ),
                $this->escape( $codes->location_name )
            );
        }
    }
}