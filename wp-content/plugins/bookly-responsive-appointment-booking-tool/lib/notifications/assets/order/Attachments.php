<?php
namespace Bookly\Lib\Notifications\Assets\Order;

use Bookly\Lib;
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Notifications\Assets\Base;

/**
 * Class Attachments
 * @package Bookly\Lib\Notifications\Assets\Order
 */
class Attachments extends Base\Attachments
{
    /** @var Codes */
    protected $codes;

    /**
     * Constructor.
     *
     * @param Codes $codes
     */
    public function __construct( Codes $codes )
    {
        $this->codes = $codes;
    }

    /**
     * @inheritDoc
     */
    public function createFor( Notification $notification, $recipient = 'client' )
    {
        if ( $notification->getAttachInvoice() ) {
            if ( ! isset ( $this->files['invoice'] ) ) {
                // Invoices.
                if ( $this->codes->getOrder()->hasPayment() ) {
                    $file = Lib\Proxy\Invoices::getInvoice( $this->codes->getOrder()->getPayment() );
                    if ( $file ) {
                        $this->files['invoice'] = $file;
                    }
                }
            }

            return isset ( $this->files['invoice'] ) ? array( $this->files['invoice'] ) : array();
        }

        return array();
    }
}