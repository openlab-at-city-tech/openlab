<?php
namespace Bookly\Frontend\Modules\Booking\Proxy;

use Bookly\Lib as BooklyLib;

/**
 * Class CustomerGroups
 * @package Bookly\Frontend\Modules\Booking\Proxy
 *
 * @method static void renderCartDiscountRow( array $table, string $layout )
 * @method static bool allowedGateway( string $gateway, BooklyLib\UserBookingData $userData ) Check if gateway allowed for current customer
 */
abstract class CustomerGroups extends BooklyLib\Base\Proxy
{
}