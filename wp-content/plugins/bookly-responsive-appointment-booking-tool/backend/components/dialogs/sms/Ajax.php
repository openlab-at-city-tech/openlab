<?php
namespace Bookly\Backend\Components\Dialogs\Sms;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Sms
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Save notification.
     */
    public static function saveNotification()
    {
        $data = self::parameter( 'notification' );
        $notification = new Lib\Entities\Notification();
        $is_new = ! $notification->load( $data['id'] );
        if ( ! $is_new ) {
            unset( $data['id'] );
        }
        $notification->setFields( $data )->save();

        wp_send_json_success();
    }

    /**
     * Get notification data.
     */
    public static function getNotificationData()
    {
        $notification = new Lib\Entities\Notification();
        $notification->load( self::parameter( 'id' ) );
        $data = $notification->getFields();
        $data['settings'] = array_merge( Lib\DataHolders\Notification\Settings::getDefault(), json_decode( $data['settings'], true ) );
        if ( get_user_meta( get_current_user_id(), 'rich_editing', true ) !== 'false' && $notification->getGateway() == 'email' ) {
            $data['message'] = wpautop( $data['message'] );
        }

        wp_send_json_success( $data );
    }
}