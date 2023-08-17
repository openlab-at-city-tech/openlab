<?php
namespace Bookly\Lib\Notifications\Base;

use Bookly\Lib\Cloud;
use Bookly\Lib\Entities\Customer;
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Entities\Staff;
use Bookly\Lib\Notifications\Assets\Base\Attachments;
use Bookly\Lib\Notifications\Assets\Base\Codes;
use Bookly\Lib\Proxy;
use Bookly\Lib\Utils;

/**
 * Class Reminder
 *
 * @package Bookly\Lib\Notifications\Base
 */
abstract class Reminder
{
    const RECIPIENT_ADMINS = 'admins';
    const RECIPIENT_CLIENT = 'client';
    const RECIPIENT_STAFF  = 'staff';

    const SEND_AS_HTML = 'html';
    const SEND_AS_TEXT = 'text';

    /**
     * Send notification to administrators.
     *
     * @param Notification $notification
     * @param Codes $codes
     * @param Attachments $attachments
     * @param array $reply_to
     * @param array|bool $queue
     * @return bool
     */
    public static function sendToAdmins( Notification $notification, Codes $codes, $attachments = null, $reply_to = null, &$queue = false )
    {
        if ( ! $notification->getToAdmin() ) {
            // No recipient.
            return false;
        }
        $gateway = $notification->getGateway();
        if ( $gateway === 'sms' ) {
            return static::_sendSmsTo(
                self::RECIPIENT_ADMINS,
                get_option( 'bookly_sms_administrator_phone', '' ),
                $notification,
                $codes,
                array( 'name' => __( 'Admins', 'bookly' ) ),
                $queue
            );
        } elseif ( $gateway === 'email' ) {
            $result = false;
            foreach ( Utils\Common::getAdminEmails() as $email ) {
                if ( static::_sendEmailTo(
                    self::RECIPIENT_ADMINS,
                    $email,
                    $notification,
                    $codes,
                    $attachments,
                    $reply_to,
                    null,
                    null,
                    array( 'name' => __( 'Admins', 'bookly' ) ),
                    $queue
                ) ) {
                    $result = true;
                }
            }

            return $result;
        } elseif ( $gateway === 'voice' ) {
            return static::_callTo(
                self::RECIPIENT_ADMINS,
                get_option( 'bookly_sms_administrator_phone', '' ),
                $notification,
                $codes,
                array( 'name' => __( 'Admins', 'bookly' ) ),
                $queue
            );
        } elseif ( $gateway === 'whatsapp' ) {
            return static::_sendWhatsAppMessageTo(
                self::RECIPIENT_ADMINS,
                get_option( 'bookly_sms_administrator_phone', '' ),
                $notification,
                $codes,
                array( 'name' => __( 'Admins', 'bookly' ) ),
                $queue
            );
        }
    }

    /**
     * Send notification to custom recipients
     *
     * @param Notification $notification
     * @param Codes $codes
     * @param Attachments $attachments
     * @param array $reply_to
     * @param array|bool $queue
     * @return bool
     */
    public static function sendToCustom( Notification $notification, Codes $codes, $attachments = null, $reply_to = null, &$queue = false )
    {
        $result = false;
        if ( ! $notification->getToCustom() ) {
            // No recipient.
            return $result;
        }
        $gateway = $notification->getGateway();
        if ( $gateway === 'sms' ) {
            foreach ( array_map( 'trim', array_filter( explode( "\n", $notification->getCustomRecipients() ), 'trim' ) ) as $phone ) {
                if ( static::_sendSmsTo(
                    self::RECIPIENT_ADMINS,
                    $phone,
                    $notification,
                    $codes,
                    array( 'name' => __( 'Custom', 'bookly' ) ),
                    $queue
                ) ) {
                    $result = true;
                }
            }
        } elseif ( $gateway === 'email' ) {
            foreach ( array_map( 'trim', array_filter( explode( "\n", $notification->getCustomRecipients() ), 'trim' ) ) as $email ) {
                if ( static::_sendEmailTo(
                    self::RECIPIENT_ADMINS,
                    $email,
                    $notification,
                    $codes,
                    $attachments,
                    $reply_to,
                    null,
                    null,
                    array( 'name' => __( 'Custom', 'bookly' ) ),
                    $queue
                ) ) {
                    $result = true;
                }
            }
        } elseif ( $gateway === 'voice' ) {
            foreach ( array_map( 'trim', array_filter( explode( "\n", $notification->getCustomRecipients() ), 'trim' ) ) as $phone ) {
                if ( static::_callTo(
                    self::RECIPIENT_ADMINS,
                    $phone,
                    $notification,
                    $codes,
                    array( 'name' => __( 'Custom', 'bookly' ) ),
                    $queue
                ) ) {
                    $result = true;
                }
            }
        } elseif ( $gateway === 'whatsapp' ) {
            foreach ( array_map( 'trim', array_filter( explode( "\n", $notification->getCustomRecipients() ), 'trim' ) ) as $phone ) {
                if ( static::_sendWhatsAppMessageTo(
                    self::RECIPIENT_ADMINS,
                    $phone,
                    $notification,
                    $codes,
                    array( 'name' => __( 'Custom', 'bookly' ) ),
                    $queue
                ) ) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Send notification to client.
     *
     * @param Customer $customer
     * @param Notification $notification
     * @param Codes $codes
     * @param Attachments $attachments
     * @param bool|array $queue
     * @return bool
     */
    public static function sendToClient( Customer $customer, Notification $notification, Codes $codes, $attachments = null, &$queue = false )
    {
        if ( ! $notification->getToCustomer() ) {
            // No recipient.
            return false;
        }
        $gateway = $notification->getGateway();
        if ( $gateway === 'sms' ) {
            return static::_sendSmsTo(
                self::RECIPIENT_CLIENT,
                $customer->getPhone(),
                $notification,
                $codes,
                array( 'name' => $customer->getFullName() ),
                $queue
            );
        } elseif ( $gateway === 'email' ) {
            return static::_sendEmailTo(
                self::RECIPIENT_CLIENT,
                $customer->getEmail(),
                $notification,
                $codes,
                $attachments,
                null,
                null,
                null,
                array( 'name' => $customer->getFullName() ),
                $queue
            );
        } elseif ( $gateway === 'voice' ) {
            return static::_callTo(
                self::RECIPIENT_CLIENT,
                $customer->getPhone(),
                $notification,
                $codes,
                array( 'name' => $customer->getFullName() ),
                $queue
            );
        } elseif ( $gateway === 'whatsapp' ) {
            return static::_sendWhatsAppMessageTo(
                self::RECIPIENT_CLIENT,
                $customer->getPhone(),
                $notification,
                $codes,
                array( 'name' => $customer->getFullName() ),
                $queue
            );
        }
    }

    /**
     * Send notification to staff.
     *
     * @param Staff $staff
     * @param Notification $notification
     * @param Codes $codes
     * @param Attachments $attachments
     * @param array $reply_to
     * @param array|bool $queue
     * @return bool
     */
    public static function sendToStaff( Staff $staff, Notification $notification, Codes $codes, $attachments = null, $reply_to = null, &$queue = false )
    {
        if ( ! $notification->getToStaff() || $staff->isArchived() ) {
            // No recipient.
            return false;
        }
        $gateway = $notification->getGateway();
        if ( $gateway === 'sms' ) {
            return static::_sendSmsTo(
                self::RECIPIENT_STAFF,
                $staff->getPhone(),
                $notification,
                $codes,
                array( 'name' => $staff->getFullName() ),
                $queue
            );
        } elseif ( $gateway === 'email' ) {
            return static::_sendEmailTo(
                self::RECIPIENT_STAFF,
                $staff->getEmail(),
                $notification,
                $codes,
                $attachments,
                $reply_to,
                null,
                null,
                array( 'name' => $staff->getFullName() ),
                $queue
            );
        } elseif ( $gateway === 'voice' ) {
            return static::_callTo(
                self::RECIPIENT_STAFF,
                $staff->getPhone(),
                $notification,
                $codes,
                array( 'name' => $staff->getFullName() ),
                $queue
            );
        } elseif ( $gateway === 'whatsapp' ) {
            return static::_sendWhatsAppMessageTo(
                self::RECIPIENT_STAFF,
                $staff->getPhone(),
                $notification,
                $codes,
                array( 'name' => $staff->getFullName() ),
                $queue
            );
        }
    }

    /**
     * Send email.
     *
     * @param string $recipient
     * @param string|array $to_email
     * @param Notification $notification
     * @param Codes $codes ,
     * @param Attachments $attachments
     * @param array $reply_to
     * @param string $force_send_as
     * @param array $force_from
     * @param array $queue_data
     * @param bool|array $queue
     * @return bool
     */
    protected static function _sendEmailTo(
        $recipient,
        $to_email,
        Notification $notification,
        Codes $codes,
        $attachments = null,
        $reply_to = null,
        $force_send_as = null,
        $force_from = null,
        $queue_data = array(),
        &$queue = false
    ) {
        if ( empty ( $to_email ) ) {
            return false;
        }

        $send_as = $force_send_as ?: get_option( 'bookly_email_send_as', self::SEND_AS_HTML );
        $from = $force_from ?: array(
            'name' => get_option( 'bookly_email_sender_name' ),
            'email' => get_option( 'bookly_email_sender' ),
        );

        // Subject & message.
        if ( $recipient == self::RECIPIENT_CLIENT ) {
            $subject = $notification->getTranslatedSubject();
            $message = $notification->getTranslatedMessage();
        } else {
            $subject = $notification->getSubject();
            $message = Proxy\Pro::prepareNotificationMessage( $notification->getMessage(), $recipient, 'email' );
        }
        $subject = $codes->replace( $subject );
        if ( $send_as == self::SEND_AS_HTML ) {
            $message = wpautop( $codes->replace( $message, 'html' ) );
        } else {
            $message = $codes->replace( $message );
        }

        // Headers.
        $headers = array();
        $headers['is_html'] = $send_as == self::SEND_AS_HTML;
        $headers['from'] = $from;
        if ( isset ( $reply_to ) ) {
            $headers['reply_to'] = $reply_to;
        }

        // Do send.
        if ( $queue !== false ) {
            $queue[] = array(
                'data' => $queue_data,
                'gateway' => $notification->getGateway(),
                'name' => $notification->getName(),
                'address' => $to_email,
                'subject' => $subject,
                'message' => $message,
                'headers' => $headers,
                'type_id' => $notification->getTypeId(),
                'attachments' => $attachments ? $attachments->createFor( $notification, $recipient ) : array(),
            );

            return true;
        }

        return Utils\Mail::send( $to_email, $subject, $message, $headers, $attachments ? $attachments->createFor( $notification, $recipient ) : array(), $notification->getTypeId() );
    }

    /**
     * Send SMS.
     *
     * @param string $recipient
     * @param string $phone
     * @param Notification $notification
     * @param Codes $codes
     * @param array $queue_data ,
     * @param array|bool $queue
     * @return bool
     */
    protected static function _sendSmsTo( $recipient, $phone, $notification, Codes $codes, $queue_data = array(), &$queue = false )
    {
        if ( get_option( 'bookly_cloud_token' ) == '' || $phone == '' || ! Cloud\API::getInstance()->account->productActive( Cloud\Account::PRODUCT_SMS_NOTIFICATIONS ) ) {
            return false;
        }

        // Message.
        if ( $recipient == self::RECIPIENT_CLIENT ) {
            $message = $notification->getTranslatedMessage();
        } else {
            $message = Proxy\Pro::prepareNotificationMessage( $notification->getMessage(), $recipient, 'sms' );
        }
        $message = $codes->replaceForSms( $message );

        // Do send.
        if ( $queue !== false ) {
            $queue[] = array(
                'data' => $queue_data,
                'gateway' => $notification->getGateway(),
                'name' => $notification->getName(),
                'address' => $phone,
                'message' => $message['personal'],
                'impersonal' => $message['impersonal'],
                'type_id' => $notification->getTypeId(),
            );

            return true;
        } else {
            return Cloud\API::getInstance()->sms->sendSms( $phone, $message['personal'], $message['impersonal'], $notification->getTypeId() );
        }
    }

    /**
     * Make a call.
     *
     * @param string $recipient
     * @param string $phone
     * @param Notification $notification
     * @param Codes $codes
     * @param array $queue_data ,
     * @param array|bool $queue
     * @return bool
     */
    protected static function _callTo( $recipient, $phone, $notification, Codes $codes, $queue_data = array(), &$queue = false )
    {
        if ( get_option( 'bookly_cloud_token' ) == '' || $phone == '' || ! Cloud\API::getInstance()->account->productActive( Cloud\Account::PRODUCT_VOICE ) ) {
            return false;
        }

        // Message.
        if ( $recipient == self::RECIPIENT_CLIENT ) {
            $message = $notification->getTranslatedMessage();
        } else {
            $message = Proxy\Pro::prepareNotificationMessage( $notification->getMessage(), $recipient, 'sms' );
        }
        $message = $codes->replaceForSms( $message );

        // Do send.
        if ( $queue !== false ) {
            $queue[] = array(
                'data' => $queue_data,
                'gateway' => $notification->getGateway(),
                'name' => $notification->getName(),
                'address' => $phone,
                'message' => $message['personal'],
                'impersonal' => $message['impersonal'],
                'type_id' => $notification->getTypeId(),
            );

            return true;
        } else {
            return Cloud\API::getInstance()->voice->call( $phone, $message['personal'], $message['impersonal'] );
        }
    }

    /**
     * Send WhatsApp message.
     *
     * @param string $recipient
     * @param string $phone
     * @param Notification $notification
     * @param Codes $codes
     * @param array $queue_data ,
     * @param array|bool $queue
     * @return bool
     */
    protected static function _sendWhatsAppMessageTo( $recipient, $phone, $notification, Codes $codes, $queue_data = array(), &$queue = false )
    {
        if ( get_option( 'bookly_cloud_token' ) == '' || $phone == '' || ! Cloud\API::getInstance()->account->productActive( Cloud\Account::PRODUCT_WHATSAPP ) ) {
            return false;
        }
        $message = $codes->replaceForWhatsApp( $notification );
        if ( $queue !== false ) {
            $queue[] = array(
                'data' => $queue_data,
                'gateway' => $notification->getGateway(),
                'name' => $notification->getName(),
                'address' => $phone,
                'message' => $message,
                'impersonal' => null,
                'type_id' => $notification->getTypeId(),
            );

            return true;
        } else {
            return Cloud\API::getInstance()->whatsapp->send( $phone, $message );
        }
    }
}