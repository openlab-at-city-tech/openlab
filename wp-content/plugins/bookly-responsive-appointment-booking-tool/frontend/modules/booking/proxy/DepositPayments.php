<?php
namespace Bookly\Frontend\Modules\Booking\Proxy;

use Bookly\Lib as BooklyLib;
use Bookly\Lib\CartInfo;

/**
 * @method static void renderPayNowRow( CartInfo $cart_info, array $table, string $layout ) Render "Pay now" row on a Cart step
 * @method static void renderPaymentStep( BooklyLib\UserBookingData $userData ) Render payment step selector deposit/full payment
 */
abstract class DepositPayments extends BooklyLib\Base\Proxy
{

}