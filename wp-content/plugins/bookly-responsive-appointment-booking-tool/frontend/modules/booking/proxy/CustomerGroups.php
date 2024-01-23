<?php
namespace Bookly\Frontend\Modules\Booking\Proxy;

use Bookly\Lib as BooklyLib;

/**
 * @method static void renderCartDiscountRow( array $table, string $layout )
 * @method static bool allowedGateway( string $gateway, BooklyLib\UserBookingData $userData ) Check if gateway allowed for current customer
 * @method static bool getSkipPayment( BooklyLib\Entities\Customer $customer ) Check if customer should skip payment step.
 * @method static float prepareCartTotalPrice( float $total, BooklyLib\UserBookingData $userData ) Prepare total price depending on group discount.
 */
abstract class CustomerGroups extends BooklyLib\Base\Proxy
{
}