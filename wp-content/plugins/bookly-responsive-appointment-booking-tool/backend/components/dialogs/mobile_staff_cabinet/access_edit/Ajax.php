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
     * Binding users with Cloud Mobile Staff Cabinet
     *
     * @return void
     */
    public static function mobileStaffCabinetGrantToken()
    {
        $auth_data = self::parameter( 'auth' );
        $access_token = array_key_exists( 'token', $auth_data )
            ? $auth_data['token']
            : null;
        $auth = new Lib\Entities\Auth();
        if ( $access_token ) {
            $auth = new Lib\Entities\Auth();
            $auth->loadBy( array( 'token' => $access_token ) );
            $auth
                ->setToken( $access_token );
        }

        $auth_email = null;
        $wp_user = null;
        $staff = null;

        if ( $auth_data['role'] === 'wp_user' ) {
            if ( $auth_data['wp_user_id'] ) {
                /** @var \WP_User $wp_user */
                $wp_user = get_user_by( 'id', $auth_data['wp_user_id'] );
            }

            if ( $wp_user ) {
                $auth_email = $wp_user->user_email;
                $auth
                    ->setWpUserId( $auth_data['wp_user_id'] )
                    ->setStaffId( null );

                $staff = new Lib\Entities\Staff();
                $staff
                    ->setEmail( $auth_email )
                    ->setFullName( $wp_user->display_name );
            } else {
                wp_send_json_error( array( 'message' => __( 'WordPress user required', 'bookly' ) ) );
            }
        } elseif ( $auth_data['role'] === 'staff' ) {
            if ( $auth_data['staff_id'] ) {
                $staff = Lib\Entities\Staff::find( $auth_data['staff_id'] );
            }
            if ( $staff ) {
                $auth_email = $staff->getEmail();
                $auth
                    ->setWpUserId( null )
                    ->setStaffId( $auth_data['staff_id'] );
            } else {
                wp_send_json_error( array( 'message' => __( 'Staff member required', 'bookly' ) ) );
            }
        } else {
            wp_send_json_error( array( 'message' => __( 'Invalid role', 'bookly' ) ) );
        }

        $api = Lib\Cloud\API::getInstance();
        if ( $access_token ) {
            if ( ! $api->getProduct( Lib\Cloud\Account::PRODUCT_MOBILE_STAFF_CABINET )->patchKey( $access_token, $auth_email ) ) {
                wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
            }
            $auth->save();
        } elseif ( $api->getProduct( Lib\Cloud\Account::PRODUCT_MOBILE_STAFF_CABINET )->grantKey( $auth, $auth_email ) ) {
            $access_token = $auth->getToken();
        } else {
            wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
        }

        $staff_members = Lib\Entities\Staff::query( 's' )
            ->select( 's.id, s.full_name' )
            ->leftJoin( 'Auth', 'a', 'a.staff_id = s.id' )
            ->sortBy( 's.full_name' )
            ->whereNot( 's.visibility', 'archive' )
            ->where( 'a.token', null )
            ->fetchArray();

        $data = array(
            'staff_members' => $staff_members,
        );
        $queue = new NotificationList();
        if ( $access_token && self::parameter( 'send_notifications' ) ) {
            $attachments = null;
            $codes = new App\StaffCabinet\Codes();
            $codes->setStaff( $staff )->setAuth( $auth );
            $notifications = Lib\Notifications\Base\Sender::getNotifications( Lib\Entities\Notification::TYPE_MOBILE_SC_GRANT_ACCESS_TOKEN );
            foreach ( $notifications['staff'] as $notification ) {
                Sender::sendToStaff( $staff, $notification, $codes, $attachments, null, $queue );
                Sender::sendToAdmins( $notification, $codes, $attachments, null, $queue );
                Sender::sendToCustom( $notification, $codes, $attachments, null, $queue );
            }
            $list = $queue->getList();
            if ( $list ) {
                $db_queue = new Lib\Entities\NotificationQueue();
                $db_queue
                    ->setData( json_encode( array( 'all' => $list ) ) )
                    ->save();

                $data['queue'] = array( 'token' => $db_queue->getToken(), 'all' => $queue->getInfo() );
            }
        }

        wp_send_json_success( $data );
    }
}