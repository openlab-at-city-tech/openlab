<?php
namespace Bookly\Frontend\Modules\ModernBookingForm\Proxy;

use Bookly\Lib;
use BooklyPro\Frontend\Modules\ModernBookingForm\Lib\Request;

/**
 * Class Shared
 *
 * @package Bookly\Frontend\Modules\ModernBookingForm\Proxy
 * @method static array prepareFormOptions( array $bookly_options ) Modify form options.
 * @method static array prepareAppearance( array $bookly_options ) Modify form options.
 * @method static array prepareAppearanceData( array $bookly_options ) Modify appearance data.
 * @method static void  renderForm( string $form_id ) Render form.
 * @method static void  validate( $request ) Validate request.
 * @method static string getCheckoutUrl( string $checkout_url, Request $request, string $response_url ) Get the url for payment.
 * @method static string preparePaymentStatus( string $status, Lib\Entities\Payment $payment ) Get only the payment status from the payment system.
 * @method static string retrieveStatus( string $status, Lib\Entities\Payment $payment ) Get the payment status from the payment system.
 */
abstract class Shared extends Lib\Base\Proxy
{

}