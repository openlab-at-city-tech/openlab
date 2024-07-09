<?php
namespace Bookly\Lib\Notifications\Cart\Proxy;

use Bookly\Lib;
use Bookly\Lib\DataHolders\Booking\Order;
use Bookly\Backend\Components\Dialogs\Queue\NotificationList;

/**
 * @method static Order sendCombinedToClient( Order $order, NotificationList|null $queue = null ) Send combined notifications to client.
 */
abstract class Pro extends Lib\Base\Proxy
{

}