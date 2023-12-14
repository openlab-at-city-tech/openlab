<?php
namespace Bookly\Backend\Components\Notices\Rate;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => array( 'staff', 'supervisor' ) );
    }

    /**
     * Dismiss rate notice
     */
    public static function hideUntilRateNotice()
    {
        switch ( self::parameter( 'hide_until' ) ) {
            case 'forever':
                $hide_until = -1;
                break;
            case 'short-time':
                $hide_until = strtotime( '+7 day' );
                update_user_meta( get_current_user_id(), 'bookly_notice_rate_on_wp_remember_me', '1' );
                break;
            case 'long-time':
            default:
                $hide_until = strtotime( '+30 day' );
                break;
        }

        update_user_meta( get_current_user_id(), 'bookly_notice_rate_on_wp_hide_until', $hide_until );

        wp_send_json_success();
    }
}