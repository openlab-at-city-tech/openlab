<?php
namespace Bookly\Backend\Components\Notices\ZoomJwt;

use Bookly\Lib;

/**
 * Class Ajax
 *
 * @package Bookly\Backend\Components\Notices\Statistic
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Dismiss 'Zoom JWT' notice.
     */
    public static function dismissZoomJwtNotice()
    {
        update_user_meta( get_current_user_id(), 'bookly_dismiss_zoom_jwt_notice', 1 );

        wp_send_json_success();
    }
}