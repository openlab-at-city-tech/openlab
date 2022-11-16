<?php
namespace Bookly\Lib\Notifications\Booking;

use Bookly\Lib\Config;
use Bookly\Lib\DataHolders\Booking\Item;
use Bookly\Lib\DataHolders\Booking\Simple;
use Bookly\Lib\DataHolders\Booking\Order;
use Bookly\Lib\Entities\Appointment;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Cart\Proxy;

/**
 * Class Sender
 * @package Bookly\Lib\Notifications\Instant\Backend
 */
abstract class Sender extends BaseSender
{
    /**
     * Send notifications.
     *
     * @param Item       $item
     * @param array      $codes_data
     * @param bool       $force_new_booking
     * @param bool|array $queue
     */
    public static function send( Item $item, $codes_data = array(), $force_new_booking = false, &$queue = false )
    {
        static::sendForOrder( Order::createFromItem( $item ), $codes_data, $force_new_booking, $queue );
    }

    /**
     * Send notifications for customer_appointment record.
     *
     * @param CustomerAppointment $ca
     * @param Appointment         $appointment
     * @param array               $codes_data
     * @param bool                $force_new_booking
     * @param bool|array          $queue
     */
    public static function sendForCA( CustomerAppointment $ca, Appointment $appointment = null, $codes_data = array(), $force_new_booking = false, &$queue = false )
    {
        $simple = Simple::create( $ca );
        if ( $appointment ) {
            $simple->setAppointment( $appointment );
        }

        static::send( $simple, $codes_data, $force_new_booking, $queue );
    }

    /**
     * Send notifications for order.
     *
     * @param Order      $order
     * @param array      $codes_data
     * @param bool       $force_new_booking
     * @param bool|array $queue
     */
    public static function sendForOrder( Order $order, $codes_data = array(), $force_new_booking = false, &$queue = false )
    {
        if ( Config::proActive() ) {
            $queue = Proxy\Pro::sendCombinedToClient( $queue, $order );
        }

        $codes = new Codes( $order );
        if ( isset ( $codes_data['cancellation_reason'] ) ) {
            $codes->cancellation_reason = $codes_data['cancellation_reason'];
        }

        $notifications = array(
            Notification::TYPE_NEW_BOOKING                                   => null,
            Notification::TYPE_NEW_BOOKING_RECURRING                         => null,
            Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED           => null,
            Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING => null,
        );

        foreach ( $order->getItems() as $item ) {
            $type = $item->isSeries() ?
                ( $item->getCA()->isJustCreated() || $force_new_booking ? Notification::TYPE_NEW_BOOKING_RECURRING : Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING ) :
                ( $item->getCA()->isJustCreated() || $force_new_booking ? Notification::TYPE_NEW_BOOKING : Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED );

            if ( ! isset ( $notifications[ $type ] ) ) {
                $notifications[ $type ] = static::getNotifications( $type );
            }

            // Notify client.
            static::notifyClient( $notifications[ $type ]['client'], $item, $order, $codes, $queue );

            // Notify staff and admins.
            static::notifyStaffAndAdmins( $notifications[ $type ]['staff'], $item, $order, $codes, $queue );
        }
    }
}