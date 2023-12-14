<?php
namespace Bookly\Lib\Notifications\Assets\Item;

use Bookly\Lib;
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Notifications\Assets\Order;

/**
 * @property Codes $codes
 */
class Attachments extends Order\Attachments
{
    /**
     * @inheritDoc
     */
    public function createFor( Notification $notification, $recipient = 'client' )
    {
        $result = array();

        if ( $notification->getAttachIcs() ) {
            if ( ! isset( $this->files['ics'] ) ) {
                // ICS.
                if ( $this->codes instanceof \BooklyPro\Lib\Notifications\Assets\Combined\Codes ) {
                    $ics = Proxy\Pro::createICS( $this->codes, $recipient );
                } elseif ( $this->codes->getItem()->isSeries() && Lib\Config::recurringAppointmentsActive() ) {
                    $ics = Proxy\RecurringAppointments::createICS( $this->codes, $recipient );
                } else {
                    $ics = new ICS( $this->codes, $recipient );
                }
                $file = $ics->create();
                if ( $file ) {
                    $this->files['ics'] = $file;
                }
            }
            $result = isset( $this->files['ics'] ) ? array( $this->files['ics'] ) : array();
        }

        return array_merge( parent::createFor( $notification ), $result );
    }
}