<?php
namespace Bookly\Frontend\Modules\Booking\Proxy;

use Bookly\Lib;

/**
 * Class Discounts
 * @package Bookly\Frontend\Modules\Booking\Proxy
 *
 * @method static void renderCartDiscountRow( array $table, string $layout, Lib\UserBookingData $userData )
 * @method static void renderCartItemInfo( Lib\UserBookingData $userData, $cart_key, $positions, $desktop ) Render extra info for cart item at Cart step.
 * @method static float prepareCartTotalPrice( float $total, Lib\UserBookingData $userData ) Prepare cart total price.
 */
abstract class Discounts extends Lib\Base\Proxy
{
}