<?php
namespace Bookly\Frontend\Modules\Booking\Proxy;

use Bookly\Lib;

/**
 * Class Coupons
 * @package Bookly\Frontend\Modules\Booking\Proxy
 *
 * @method static void renderPaymentStep( Lib\UserBookingData $userData ) Render coupons block for Payment step.
 * @method static \BooklyCoupons\Lib\Entities\Coupon findOneByCode( string $code ) Return coupon entity.
 */
abstract class Coupons extends Lib\Base\Proxy
{

}