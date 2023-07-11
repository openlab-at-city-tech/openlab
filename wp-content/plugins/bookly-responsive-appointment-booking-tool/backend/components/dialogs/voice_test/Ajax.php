<?php
namespace Bookly\Backend\Components\Dialogs\VoiceTest;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Dialogs\VoiceTest
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Test notification.
     */
    public static function makeTestNotificationCall()
    {
        $phone = self::parameter( 'phone' );
        if( $phone ) {
            $notification_id = self::parameter( 'notification_id' );

            $cloud = Lib\Cloud\API::getInstance();
            Lib\Notifications\Test\Sender::send( '', '', '', '', '', array( $notification_id ), 'voice', $phone )
                ? wp_send_json_success( array( 'message' => sprintf( __( 'Calling %s', 'bookly' ), $phone ) . ' …' ) )
                : wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ?: __( 'Failed', 'bookly' ) ) );
        }
        wp_send_json_error( array( 'message' => __( 'Phone number is empty.', 'bookly' ) ) );
    }
}