<?php
namespace Bookly\Lib\Notifications\Assets\Item;

use Bookly\Lib;

/**
 * Class ICS
 *
 * @package Bookly\Lib\Notifications\Assets\Item
 */
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
        $description_template = $this->getDescriptionTemplate( $recipient );
        $this->data = sprintf(
            "BEGIN:VCALENDAR\n"
            . "VERSION:2.0\n"
            . "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\n"
            . "CALSCALE:GREGORIAN\n"
            . "BEGIN:VEVENT\n"
            . "DTSTART:%s\n"
            . "DTEND:%s\n"
            . "SUMMARY:%s\n"
            . "DESCRIPTION:%s\n"
            . "LOCATION:%s\n"
            . "END:VEVENT\n"
            . "END:VCALENDAR",
            $this->formatDateTime( $codes->appointment_start ),
            $this->formatDateTime( $codes->appointment_end ),
            $this->escape( $codes->service_name ),
            $this->escape( $codes->replace( $description_template ) ),
            $this->escape( $codes->location_name )
        );
    }
}