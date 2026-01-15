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
     * @param string $staff_name
     * @param string $staff_email
     * @param string $description
     * @param int $location_id
     * @return $this
     */
    public function addEvent( $start_date, $end_date, $summary, $staff_name, $staff_email, $description, $location_id )
    {
        $event = new Event();
        $event
            ->setStartDate( $start_date )
            ->setEndDate( $end_date )
            ->setStaffName( $staff_name )
            ->setStaffEmail( $staff_email )
            ->setLocationId( $location_id )
            ->setSummary( $summary )
            ->setDescription( $description );
        $this->events[] = $event;

        return $this;
    }

    /**
     * Create ICS file.
     *
     * @return bool|string
     */
    public function create()
    {
        $body = $this->render();
        if ( $body ) {
            $path = tempnam( get_temp_dir(), 'Bookly_' );

            if ( $path ) {
                $info = pathinfo( $path );
                $new_path = sprintf( '%s%s%s.ics', $info['dirname'], DIRECTORY_SEPARATOR, $info['filename'] );
                if ( rename( $path, $new_path ) ) {
                    $path = $new_path;
                } else {
                    $new_path = sprintf( '%s%s%s.ics', $info['dirname'], DIRECTORY_SEPARATOR, $info['basename'] );
                    if ( rename( $path, $new_path ) ) {
                        $path = $new_path;
                    }
                }
                Lib\Utils\Common::getFilesystem()->put_contents( $path, $body );

                return $path;
            }
        }

        return false;
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
            foreach ( $order->getItems() as $data ) {
                if ( $data['type'] === 'appointment' ) {
                    $description_codes = Lib\Utils\Codes::getICSCodes( $data['item'] );
                    $staff = Lib\Entities\Staff::find( $data['item']->getAppointment()->getStaffId() );

                    $ics->addEvent(
                        $data['item']->getAppointment()->getStartDate(),
                        $data['item']->getAppointment()->getEndDate(),
                        $data['title'],
                        $staff->getFullName(),
                        $staff->getEmail(),
                        Lib\Utils\Codes::replace( $description_template, $description_codes, false ),
                        $data['item']->getAppointment()->getLocationId()
                    );
                }
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
        $items = $order->getItems();
        if ( $items && $items[0]['type'] === 'event' ) {
            $ics = Lib\Proxy\Events::getIcsFromOrder( $order );
        } else {
            // Generate ICS feed.
            $ics = new self();
            $description_template = Lib\Utils\Codes::getICSDescriptionTemplate();
            foreach ( $order->getItems() as $data ) {
                $description_codes = Lib\Utils\Codes::getICSCodes( $data['item'] );
                $staff = Lib\Entities\Staff::find( $data['item']->getAppointment()->getStaffId() );

                $ics->addEvent(
                    $data['item']->getAppointment()->getStartDate(),
                    $data['item']->getAppointment()->getEndDate(),
                    $data['title'],
                    $staff->getFullName(),
                    $staff->getEmail(),
                    Lib\Utils\Codes::replace( $description_template, $description_codes, false ),
                    $data['item']->getAppointment()->getLocationId()
                );
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