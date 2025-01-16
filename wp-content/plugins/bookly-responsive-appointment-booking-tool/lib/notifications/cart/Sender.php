<?php
namespace Bookly\Lib\Notifications\Cart;

use Bookly\Lib\Config;
use Bookly\Lib\DataHolders\Booking\Order;
use BooklyPackages\Lib\DataHolders\Booking\Package;
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Booking;
use Bookly\Lib\Proxy As BooklyProxy;

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
            Proxy\Pro::sendCombinedToClient( $order );
        }

        $codes = new Codes( $order );

        $notifications = static::getNotifications( Notification::TYPE_NEW_BOOKING );
        $notifications_recurring = static::getNotifications( Notification::TYPE_NEW_BOOKING_RECURRING );

        foreach ( $order->getItems() as $item ) {
            if ( $item->isSeries() ) {
                // Notify client.
                static::notifyClient( $notifications_recurring['client'], $item, $order, $codes );

                // Notify staff and admins.
                static::notifyStaffAndAdmins( $notifications_recurring['staff'], $item, $order, $codes );
            } elseif ( $item->isPackage() ) {
                /** @var Package $item */
                BooklyProxy\Packages::sendNotifications( $item );
            } elseif ( $item->isGiftCard() ) {
                // ok
            } else {
                // Notify client.
                static::notifyClient( $notifications['client'], $item, $order, $codes );

                // Notify staff and admins.
                static::notifyStaffAndAdmins( $notifications['staff'], $item, $order, $codes );
            }
        }
    }
}