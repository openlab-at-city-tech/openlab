<?php
namespace Bookly\Frontend\Modules\Booking\Lib;

abstract class Errors
{
    const SESSION_ERROR               = 'session_error';
    const FORM_ID_ERROR               = 'form_id_error';
    const CART_ITEM_NOT_AVAILABLE     = 'cart_item_not_available';
    const PAY_LOCALLY_NOT_AVAILABLE   = 'pay_locally_not_available';
    const INVALID_GATEWAY             = 'invalid_gateway';
    const PAYMENT_ERROR               = 'payment_error';
    const INCORRECT_USERNAME_PASSWORD = 'incorrect_username_password';
    const ALREADY_LOGGED_IN           = 'already_logged_in';

    public static function sendSessionError()
    {
        wp_send_json( array( 'success' => false, 'error' => self::SESSION_ERROR ) );
    }
}