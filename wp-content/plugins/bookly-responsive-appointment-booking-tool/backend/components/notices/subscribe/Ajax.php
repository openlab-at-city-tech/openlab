<?php
namespace Bookly\Backend\Components\Notices\Subscribe;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Notices\Subscribe
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Subscribe to monthly emails.
     */
    public static function subscribe()
    {
        $email = self::parameter( 'email' );
        $state = 'invalid';
        if ( is_email( $email ) ) {
            $state = Lib\API::registerSubscriber( $email );
        }

        switch ( $state ) {
            case 'success':
                wp_send_json_success( array( 'message' => __( 'Please, check your email to confirm the subscription. Thank you!', 'bookly' ) ) );
                break;
            case 'exists':
                wp_send_json_success( array( 'message' => __( 'Given email address is already subscribed, thank you!', 'bookly' ) ) );
                break;
            case 'invalid':
            default:
                wp_send_json_error( array( 'message' => __( 'This email address is not valid.', 'bookly' ) ) );
                break;
        }
    }

    /**
     * Dismiss subscribe notice.
     */
    public static function dismissSubscribeNotice()
    {
        update_user_meta( get_current_user_id(), Lib\Plugin::getPrefix() . 'dismiss_subscribe_notice', 1 );

        wp_send_json_success();
    }
}