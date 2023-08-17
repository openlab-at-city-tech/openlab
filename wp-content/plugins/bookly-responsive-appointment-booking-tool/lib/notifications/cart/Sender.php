<?php
namespace Bookly\Lib\Notifications\Cart;

use Bookly\Lib\Config;
use Bookly\Lib\DataHolders\Booking\Order;
use Bookly\Lib\DataHolders\Booking\Package;
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Booking;
use Bookly\Frontend\Modules\ModernBookingForm\Proxy\Packages;

/**
 * Class Sender
 * @package Bookly\Lib\Notifications\Cart
 */
abstract class Sender extends Booking\BaseSender
{
    /**
     * Send notifications for order.
     *
     * @param Order $order
     */
    public static function send( Order $order )
    {
        if ( Config::proActive() ) {
            Proxy\Pro::sendCombinedToClient( false, $order );
        }

        $codes = new Codes( $order );

        $notifications           = static::getNotifications( Notification::TYPE_NEW_BOOKING );
        $notifications_recurring = static::getNotifications( Notification::TYPE_NEW_BOOKING_RECURRING );

        foreach ( $order->getItems() as $item ) {
            if ( $item->isSeries() ) {
                // Notify client.
                static::notifyClient( $notifications_recurring['client'], $item, $order, $codes );

                // Notify staff and admins.
                static::notifyStaffAndAdmins( $notifications_recurring['staff'], $item, $order, $codes );
            } elseif ( $item->isPackage() ) {
                /** @var Package $item */
                Packages::sendNotifications( $item->getPackage() );
            } else {
                // Notify client.
                static::notifyClient( $notifications['client'], $item, $order, $codes );

                // Notify staff and admins.
                static::notifyStaffAndAdmins( $notifications['staff'], $item, $order, $codes );
            }
        }
    }
}