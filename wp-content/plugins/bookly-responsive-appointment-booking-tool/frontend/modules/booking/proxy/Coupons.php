<?php
namespace Bookly\Frontend\Modules\Booking\Proxy;

use Bookly\Lib;

/**
 * @method static void renderPaymentStep( Lib\UserBookingData $userData ) Render coupons block for Payment step.
 * @method static \BooklyCoupons\Lib\Entities\Coupon findOneByCode( string $code ) Return coupon entity.
 * @method static void claim( integer $id ) Claim coupon
 */
abstract class Coupons extends Lib\Base\Proxy
{

}