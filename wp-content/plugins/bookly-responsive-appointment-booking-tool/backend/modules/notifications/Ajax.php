<?php
namespace Bookly\Backend\Modules\Notifications;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Modules\Notifications
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Test email notifications.
     */
    public static function testEmailNotifications()
    {
        $to_email      = self::parameter( 'to_email' );
        $sender_name   = self::parameter( 'bookly_email_sender_name' );
        $sender_email  = self::parameter( 'bookly_email_sender' );
        $send_as       = self::parameter( 'bookly_email_send_as' );
        $notification_ids   = (array) self::parameter( 'notification_ids' );
        $reply_to_customers = self::parameter( 'bookly_email_reply_to_customers' );

        Lib\Notifications\Test\Sender::send( $to_email, $sender_name, $sender_email, $send_as, $reply_to_customers, $notification_ids, 'email' );

        wp_send_json_success();
    }

    /**
     * Save general settings for notifications.
     */
    public static function saveGeneralSettingsForNotifications()
    {
        update_option( 'bookly_email_send_as', self::parameter( 'bookly_email_send_as' ) );
        update_option( 'bookly_email_reply_to_customers', self::parameter( 'bookly_email_reply_to_customers' ) );
        update_option( 'bookly_email_sender', self::parameter( 'bookly_email_sender' ) );
        update_option( 'bookly_email_sender_name', self::parameter( 'bookly_email_sender_name' ) );
        update_option( 'bookly_ntf_processing_interval', (int) self::parameter( 'bookly_ntf_processing_interval' ) );
        Proxy\Pro::saveSettings( self::parameters() );

        wp_send_json_success();
    }

    /**
     * Load tab data for email notifications page.
     */
    public static function emailNotificationsLoadTab()
    {
        $tab = self::parameter( 'tab', 'notifications' );

        switch ( $tab ) {
            case 'settings' :
                $response = array(
                    'html' => self::renderTemplate( '_settings', array(), false ),
                );
                break;
            case 'logs' :
                $response = array(
                    'html' => Proxy\Pro::renderLogs(),
                );
                break;
            default:
                $datatables = Lib\Utils\Tables::getSettings( Lib\Utils\Tables::EMAIL_NOTIFICATIONS );
                $response = array(
                    'html' => self::renderTemplate( '_notifications', compact( 'datatables' ), false ),
                );
                break;
        }

        wp_send_json_success( $response );
    }
}