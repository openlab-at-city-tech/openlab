<?php
namespace Bookly\Lib\Notifications\Cart\Proxy;

use Bookly\Lib;
use Bookly\Lib\DataHolders\Booking\Order;

/**
 * Class Pro
 * @package Bookly\Lib\Notifications\Cart\Proxy
 *
 * @method static array|bool sendCombinedToClient( array|bool $queue, Order $order ) Send combined notifications to client.
 */
abstract class Pro extends Lib\Base\Proxy
{

}