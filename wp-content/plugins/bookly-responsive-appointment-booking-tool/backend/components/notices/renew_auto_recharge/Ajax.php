<?php
namespace Bookly\Backend\Components\Notices\RenewAutoRecharge;

use Bookly\Lib;
use Bookly\Backend\Modules\CloudProducts\Page;

class Ajax extends Lib\Base\Ajax
{
    /**
     * Dismiss 'Auto-Recharge' notice.
     */
    public static function hideRenewNotice()
    {
        switch ( self::parameter( 'hide_until' ) ) {
            case 'short-time':
                $hide_until = strtotime( '+3 day' );
                break;
            case 'forever':
            default:
                $hide_until = - 1;
                break;
        }

        update_user_meta( get_current_user_id(), 'bookly_notice_renew_auto_recharge_hide_until', $hide_until );

        wp_send_json_success();
    }

    public static function renewAutoRecharge()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $return_url = Lib\Utils\Common::escAdminUrl( Page::pageSlug() );
        switch ( self::parameter( 'gateway' ) ) {
            case 'stripe':
                $redirect_url = $cloud->account->getStripeRenewAutoRechargeUrl( $return_url );
                break;
            case 'paypal':
            default:
                $redirect_url = $cloud->account->getPayPalRenewAutoRechargeUrl( $return_url );
        }

        if ( $redirect_url !== false ) {
            wp_send_json_success( compact( 'redirect_url' ) );
        } else {
            $message = __( 'Auto-Recharge has failed, please replenish your balance directly.', 'bookly' );
            wp_send_json_error( compact( 'message' ) );
        }
    }

}