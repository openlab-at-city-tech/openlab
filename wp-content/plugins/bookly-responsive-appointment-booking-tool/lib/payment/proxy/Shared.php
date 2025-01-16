<?php
namespace Bookly\Lib\Payment\Proxy;

use Bookly\Lib;
use Bookly\Frontend\Modules\Payment;

/**
 * @method static Lib\CartInfo applyGateway( Lib\CartInfo $cart_info, string $gateway ) Set gateway.
 * @method static array  prepareOutdatedUnpaidPayments( array $payments ) Get list of outdated unpaid payments ids.
 * @method static Lib\Base\Gateway getGatewayByName( string $gateway, Payment\Request $request )
 * @method static Lib\Base\Gateway getGatewayForRefund( $gateway, Lib\Entities\Payment $payment ) Get payment system gateway
 * @method static int create( int $item_key, Lib\DataHolders\Booking\Order $order, Lib\CartItem $cart_item, Lib\UserBookingData $userData )
 * @method static void complete( Lib\DataHolders\Booking\Item $item )
 * @method static string getTranslatedTitle( $default, Lib\CartItem $cart_item )
 * @method static \Bookly\Lib\DataHolders\Details\Base paymentCreateDetailsFromItem( $details, Lib\DataHolders\Booking\Item $item )
 * @method static \Bookly\Lib\DataHolders\Details\Base paymentCreateDetailsByType( $details, string $type )
 * @method static bool paymentSpecificPriceExists( string $gateway ) Check whether specific price exists for given gateway.
 * @method static bool showPaymentSpecificPrices( bool $show ) Whether to show specific price for each payment system.
 * @method static Lib\Entities\Payment rollbackPayment( Lib\Entities\Payment $payment ) Rollback payment.
 */
abstract class Shared extends Lib\Base\Proxy
{
}
