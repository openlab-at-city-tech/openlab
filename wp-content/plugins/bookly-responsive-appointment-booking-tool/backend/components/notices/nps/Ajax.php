<?php
namespace Bookly\Backend\Components\Notices\Nps;

use Bookly\Lib;
use Bookly\Backend\Modules;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Notices
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Send Net Promoter Score.
     */
    public static function npsSend()
    {
        $rate  = self::parameter( 'rate' );
        $msg   = self::parameter( 'msg', '' );
        $email = self::parameter( 'email', '' );

        Lib\API::sendNps( $rate, $msg, $email );

        update_user_meta( get_current_user_id(), 'bookly_dismiss_nps_notice', 1 );
        update_user_meta( get_current_user_id(), 'bookly_nps_rate', $rate );

        wp_send_json_success( array( 'message' => __( 'Sent successfully.', 'bookly' ) ) );
    }

    /**
     * Dismiss NPS notice.
     */
    public static function dismissNpsNotice()
    {
        if ( get_user_meta( get_current_user_id(), 'bookly_dismiss_nps_notice', true ) != 1 ) {
            update_user_meta( get_current_user_id(), 'bookly_dismiss_nps_notice', time() );
        }

        wp_send_json_success();
    }
}