<?php
namespace Bookly\Frontend\Modules\Booking\Proxy;

use Bookly\Lib as BooklyLib;

/**
 * Class Packages
 *
 * @package Bookly\Frontend\Modules\Booking\Proxy
 * @method static BooklyLib\DataHolders\Booking\Order createPackage( BooklyLib\DataHolders\Booking\Order $order, BooklyLib\CartItem $cart_item, int $item_key ) Create package
 */
abstract class Packages extends BooklyLib\Base\Proxy
{

}