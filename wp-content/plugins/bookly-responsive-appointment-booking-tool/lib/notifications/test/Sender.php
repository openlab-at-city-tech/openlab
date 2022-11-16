<?php
namespace Bookly\Lib\Notifications\Test;

use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Notifications\Base;
use Bookly\Lib\Notifications\Assets\Item\Attachments;
use Bookly\Lib\Notifications\Assets\Test\Codes;

/**
 * Class Sender
 * @package Bookly\Lib\Notifications\Instant
 */
abstract class Sender extends Base\Sender
{
    /**
     * Send test notification emails.
     *
     * @param string $to_email
     * @param string $sender_name
     * @param string $sender_email
     * @param string $send_as
     * @param bool $reply_to_customers
     * @param array $notification_ids
     */
    public static function send( $to_email, $sender_name, $sender_email, $send_as, $reply_to_customers, array $notification_ids )
    {
        $codes = new Codes();
        $attachments  = new Attachments( $codes );
        $notification = new Notification();

        $from = array(
            'name'  => $sender_name,
            'email' => $sender_email,
        );
        $reply_to = $reply_to_customers ? array(
            'name'  => $codes->client_name,
            'email' => $codes->client_email,
        ) : null;

        foreach ( $notification_ids as $id ) {
            $notification->loadBy( array( 'id' => $id, 'gateway' => 'email' ) );

            switch ( $notification->getType() ) {
                case Notification::TYPE_CUSTOMER_BIRTHDAY:
                case Notification::TYPE_VERIFY_EMAIL:
                    if ( $notification->getToCustomer() ) {
                        static::_sendEmailTo(
                            self::RECIPIENT_CLIENT,
                            $to_email,
                            $notification,
                            $codes,
                            null,
                            null,
                            $send_as,
                            $from
                        );
                    }
                    break;
                case Notification::TYPE_STAFF_WAITING_LIST:
                case Notification::TYPE_STAFF_DAY_AGENDA:
                    if ( $notification->getToStaff() ) {
                        static::_sendEmailTo(
                            self::RECIPIENT_STAFF,
                            $to_email,
                            $notification,
                            $codes,
                            null,
                            $reply_to,
                            $send_as,
                            $from
                        );
                    }
                    break;
                case Notification::TYPE_NEW_PACKAGE:
                case Notification::TYPE_PACKAGE_DELETED:
                case Notification::TYPE_APPOINTMENT_REMINDER:
                case Notification::TYPE_LAST_CUSTOMER_APPOINTMENT:
                case Notification::TYPE_NEW_BOOKING:
                case Notification::TYPE_NEW_BOOKING_RECURRING:
                case Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED:
                case Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING:
                    if ( $notification->getToAdmin() ) {
                        static::_sendEmailTo(
                            self::RECIPIENT_ADMINS,
                            $to_email,
                            $notification,
                            $codes,
                            $attachments,
                            $reply_to,
                            $send_as,
                            $from
                        );
                    }
                    if ( $notification->getToCustomer() ) {
                        static::_sendEmailTo(
                            self::RECIPIENT_CLIENT,
                            $to_email,
                            $notification,
                            $codes,
                            $attachments,
                            null,
                            $send_as,
                            $from
                        );
                    }
                    if ( $notification->getToStaff() ) {
                        static::_sendEmailTo(
                            self::RECIPIENT_STAFF,
                            $to_email,
                            $notification,
                            $codes,
                            $attachments,
                            $reply_to,
                            $send_as,
                            $from
                        );
                    }
                    break;
                default:
                    Proxy\Shared::send( $to_email,
                        $notification,
                        $codes,
                        $attachments,
                        $reply_to,
                        $send_as,
                        $from
                    );
            }
        }
    }
}