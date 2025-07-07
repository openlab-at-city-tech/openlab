<?php
namespace Bookly\Backend\Components\Dialogs\MobileStaffCabinet\AccessEdit;

use Bookly\Lib;
use Bookly\Lib\Notifications\Base\Sender;
use Bookly\Lib\Notifications\Assets\App;
use Bookly\Backend\Components\Dialogs\Queue\NotificationList;

class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'supervisor' );
    }

    /**
     * Binding staff with Cloud Mobile Staff Cabinet
     *
     * @return void
     */
    public static function mobileStaffCabinetGrantToken()
    {
        $staff = Lib\Entities\Staff::find( self::parameter( 'staff_id' ) );
        $access_token = self::parameter( 'token' );
        $api = Lib\Cloud\API::getInstance();
        if ( $access_token ) {
            Lib\Entities\Staff::query()
                ->update()
                ->set( 'cloud_msc_token', null )
                ->where( 'cloud_msc_token', $access_token )
                ->execute();
            $staff->setMobileStaffCabinetToken( $access_token )->save();
            if ( ! $api->getProduct( Lib\Cloud\Account::PRODUCT_MOBILE_STAFF_CABINET )->patchKey( $access_token, $staff ) ) {
                wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
            }
        } elseif ( ! $api->getProduct( Lib\Cloud\Account::PRODUCT_MOBILE_STAFF_CABINET )->grantKey( $staff ) ) {
            wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
        }

        $staff_members = Lib\Entities\Staff::query()
            ->select( 'id, full_name' )
            ->sortBy( 'full_name' )
            ->whereNot( 'visibility', 'archive' )
            ->where( 'cloud_msc_token', null )
            ->fetchArray();

        $queue = new NotificationList();
        if ( $access_token && self::parameter( 'send_notification' ) ) {
            $attachments = null;
            $codes = new App\StaffCabinet\Codes();
            $codes->setStaff( $staff );
            $notifications = Lib\Notifications\Base\Sender::getNotifications( Lib\Entities\Notification::TYPE_MOBILE_SC_GRANT_ACCESS_TOKEN );
            foreach ( $notifications['staff'] as $notification ) {
                Sender::sendToStaff( $staff, $notification, $codes, $attachments, null, $queue );
                Sender::sendToAdmins( $notification, $codes, $attachments, null, $queue );
                Sender::sendToCustom( $notification, $codes, $attachments, null, $queue );
            }
        }

        $response = compact( 'staff_members' );
        $list = $queue->getList();
        if ( $list ) {
            $db_queue = new Lib\Entities\NotificationQueue();
            $db_queue
                ->setData( json_encode( array( 'all' => $list ) ) )
                ->save();

            $response['queue'] = array( 'token' => $db_queue->getToken(), 'all' => $queue->getInfo() );
        }

        wp_send_json_success( $response );
    }
}