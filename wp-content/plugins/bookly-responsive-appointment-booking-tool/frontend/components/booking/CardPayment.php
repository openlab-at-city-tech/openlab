<?php
namespace Bookly\Frontend\Components\Booking;

use Bookly\Lib;

/**
 * Class CardPayment
 * @package Bookly\Frontend\Components\Booking
 */
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