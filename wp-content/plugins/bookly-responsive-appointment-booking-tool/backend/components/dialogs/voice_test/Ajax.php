<?php
namespace Bookly\Backend\Components\Dialogs\VoiceTest;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * Test notification.
     */
    public static function makeTestNotificationCall()
    {
        $phone = self::parameter( 'phone' );
        if ( $phone ) {
            Lib\Notifications\Test\Sender::call( $phone, self::parameter( 'notification_id' ) )
                ? wp_send_json_success( array( 'message' => sprintf( __( 'Calling %s', 'bookly' ), $phone ) . ' …' ) )
                : wp_send_json_error( array( 'message' => current( Lib\Cloud\API::getInstance()->getErrors() ) ?: __( 'Failed', 'bookly' ) ) );
        }
        wp_send_json_error( array( 'message' => __( 'Phone number is empty.', 'bookly' ) ) );
    }
}