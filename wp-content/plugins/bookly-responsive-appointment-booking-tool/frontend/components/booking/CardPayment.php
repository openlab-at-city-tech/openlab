<?php
namespace Bookly\Frontend\Components\Booking;

use Bookly\Lib;

class CardPayment extends Lib\Base\Component
{
    /**
     * Render card payment form.
     */
    public static function render()
    {
        self::renderTemplate( 'card_payment' );
    }
}