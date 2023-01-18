<?php
namespace Bookly\Backend\Components\Notices\Promotion;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Notices\Promotion
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Dismiss SMS promotion notice
     */
    public static function dismissSmsPromotionNotice()
    {
        $id = self::parameter( 'id' );
        $dismiss = self::parameter( 'dismiss' );
        $dismissed = get_user_meta( get_current_user_id(), 'bookly_dismiss_cloud_promotion_notices', true ) ?: array();
        $dismissed[ $id ] = time() + ( $dismiss == 'remind' ? 7 : 30 ) * DAY_IN_SECONDS;
        update_user_meta( get_current_user_id(), 'bookly_dismiss_cloud_promotion_notices', $dismissed );

        wp_send_json_success();
    }
}