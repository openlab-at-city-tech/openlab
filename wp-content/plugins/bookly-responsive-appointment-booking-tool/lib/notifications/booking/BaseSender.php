<?php
namespace Bookly\Lib\Notifications\Booking;

use Bookly\Lib\DataHolders\Booking\Item;
use Bookly\Lib\DataHolders\Booking\Order;
use Bookly\Lib\DataHolders\Booking\Series;
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Notifications\Assets\Item\Attachments;
use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Base;
use Bookly\Lib\Notifications\WPML;
use Bookly\Lib\Proxy;
use Bookly\Backend\Components\Dialogs\Queue\NotificationList;

abstract class BaseSender extends Base\Sender
{
    /**
     * Notify client.
     *
     * @param Notification[] $notifications
     * @param Item $item
     * @param Order $order
     * @param Codes $codes
     * @param NotificationList|null $queue
     */
    protected static function notifyClient( array $notifications, Item $item, Order $order, Codes $codes, $queue = null )
    {
        if ( $item->getCA()->getLocale() ) {
            WPML::switchLang( $item->getCA()->getLocale() );
        } else {
            WPML::switchToDefaultLang();
        }

        $codes->prepareForItem( $item, 'client' );
        $attachments = new Attachments( $codes );

        foreach ( $notifications as $notification ) {
            if ( $notification->matchesItemForClient( $item ) ) {
                static::sendToClient( $order->getCustomer(), $notification, $codes, $attachments, $queue );
            }
        }

        if ( $queue === null ) {
            $attachments->clear();
        }

        WPML::restoreLang();
    }

    /**
     * Notify staff and/or administrators.
     *
     * @param Notification[] $notifications
     * @param Item $item
     * @param Order $order
     * @param Codes $codes
     * @param NotificationList|null $queue
     */
    protected static function notifyStaffAndAdmins( array $notifications, Item $item, Order $order, Codes $codes, $queue = null )
    {
        WPML::switchToDefaultLang();

        // Reply to customer.
        $reply_to = null;
        if ( get_option( 'bookly_email_reply_to_customers' ) ) {
            $customer = $order->getCustomer();
            $reply_to = array( 'email' => $customer->getEmail(), 'name' => $customer->getFullName() );
        }

        /** @var Series $item */
        $sub_items = $item->isSeries() ? $item->getFirstItem()->getItems() : $item->getItems();

        foreach ( $sub_items as $sub_item ) {
            $codes->prepareForItem( $sub_item, 'staff' );
            $attachments = new Attachments( $codes );
            foreach ( $notifications as $notification ) {
                switch ( $notification->getType() ) {
                    case Notification::TYPE_NEW_BOOKING_RECURRING:
                    case Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING:
                        $send = $notification
                            ->getSettingsObject()
                            ->allowedServiceWithStatus( $sub_item->getService(), $sub_item->getCA()->getStatus() );
                        break;
                    default:
                        $send = $notification->matchesItemForStaff( $sub_item, $sub_item->getService() );
                        break;
                }
                if ( $send ) {
                    if ( ! Proxy\RecurringAppointments::notifyStaffAndAdmins( false, $sub_item->getStaff(), $notification, $codes, $attachments, $reply_to, $queue ) ) {
                        static::sendToStaff( $sub_item->getStaff(), $notification, $codes, $attachments, $reply_to, $queue );
                        static::sendToAdmins( $notification, $codes, $attachments, $reply_to, $queue );
                        static::sendToCustom( $notification, $codes, $attachments, $reply_to, $queue );
                    }
                }
            }
            if ( $queue === null ) {
                $attachments->clear();
            }
        }

        WPML::restoreLang();
    }
}