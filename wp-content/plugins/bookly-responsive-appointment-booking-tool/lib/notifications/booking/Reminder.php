<?php
namespace Bookly\Lib\Notifications\Booking;

use Bookly\Lib\DataHolders\Booking\Item;
use Bookly\Lib\DataHolders\Booking\Order;
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Notifications\Assets\Item\Attachments;
use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Base;
use Bookly\Lib\Notifications\WPML;

/**
 * Class Reminder
 * @package Bookly\Lib\Notifications\Booking
 */
abstract class Reminder extends Base\Reminder
{
    /**
     * Send booking/appointment notifications.
     *
     * @param Notification $notification
     * @param Item $item
     * @return bool
     */
    public static function send( Notification $notification, Item $item )
    {
        $order = Order::createFromItem( $item );
        $codes = new Codes( $order );
        $attachments = new Attachments( $codes );

        $result = false;

        if ( $item->getCA()->getLocale() ) {
            WPML::switchLang( $item->getCA()->getLocale() );
        } else {
            WPML::switchToDefaultLang();
        }
        $codes->prepareForItem( $item, 'client' );

        // Notify client.
        if ( static::sendToClient( $order->getCustomer(), $notification, $codes, $attachments ) ) {
            $result = true;
        }

        WPML::switchToDefaultLang();
        foreach ( $item->getItems() as $i ) {
            $attachments->clear();
            $codes->prepareForItem( $i, 'staff' );

            // Reply to customer.
            $reply_to = null;
            if ( get_option( 'bookly_email_reply_to_customers' ) ) {
                $customer = $order->getCustomer();
                $reply_to = array( 'email' => $customer->getEmail(), 'name' => $customer->getFullName() );
            }

            // Notify staff.
            if ( static::sendToStaff( $i->getStaff(), $notification, $codes, $attachments, $reply_to ) ) {
                $result = true;
            }

            // Notify admins.
            if ( static::sendToAdmins( $notification, $codes, $attachments, $reply_to ) ) {
                $result = true;
            }

            // Notify customs.
            if ( static::sendToCustom( $notification, $codes, $attachments, $reply_to ) ) {
                $result = true;
            }
        }
        WPML::restoreLang();

        $attachments->clear();

        return $result;
    }
}