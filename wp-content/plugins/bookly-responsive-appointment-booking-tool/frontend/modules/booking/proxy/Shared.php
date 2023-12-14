<?php
namespace Bookly\Frontend\Modules\Booking\Proxy;

use Bookly\Lib;

/**
 * @method static array  booklyFormOptions( array $bookly_options ) Modify Bookly form options.
 * @method static array  stepOptions( array $options, string $step, Lib\UserBookingData $userData ) Modify options for given step.
 * @method static array  enqueueBookingScripts( array $depends ) Enqueue scripts for booking form. @params $depends as array of registered script handles this script depends on.
 * @method static array  prepareCartItemInfoText( array $data, Lib\CartItem $cart_item, Lib\UserBookingData $userData ) Prepare array for replacing in Cart items.
 * @method static array  prepareChainItemInfoText( array $data, Lib\ChainItem $chain_item ) Prepare array for replacing in Chain items.
 * @method static array  prepareInfoTextCodes( array $codes, array $data ) Prepare array for replacing on booking steps.
 * @method static array  preparePaymentOptions( array $options, $form_id, bool $show_price, Lib\CartInfo $cart_info, Lib\UserBookingData $userData ) Prepare payment options for Payment step.
 * @method static array  prepareSlotsData( array $slots_data ) Prepare slots data for Time step.
 * @method static void   renderCartItemInfo( Lib\UserBookingData $userData, $cart_key, $positions, $desktop ) Render extra info for cart item at Cart step.
 * @method static string renderCustomFieldsOnDetailsStep( Lib\UserBookingData $userData ) Get Custom Fields HTML for details step.
 * @method static void   renderWaitingListInfoText() Render WL info text in Time step.
 */
abstract class Shared extends Lib\Base\Proxy
{

}