<?php
namespace Bookly\Backend\Modules\CloudMobileStaffCabinet;

use Bookly\Lib;

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
     * Revoke auth staff token
     *
     * @return void
     */
    public static function cloudMobileStaffCabinetRevokeAccessTokens()
    {
        $keys = self::parameter( 'keys' );

        Lib\Entities\Auth::query()
            ->delete()
            ->whereIn( 'token', $keys )
            ->execute();
        $api = Lib\Cloud\API::getInstance();

        if ( $api->getProduct( Lib\Cloud\Account::PRODUCT_MOBILE_STAFF_CABINET )->revokeKeys( $keys ) ) {
            $staff_members = Lib\Entities\Staff::query( 's' )
                ->select( 's.id, s.full_name' )
                ->leftJoin( 'Auth', 'a', 'a.staff_id = s.id' )
                ->sortBy( 's.full_name' )
                ->whereNot( 's.visibility', 'archive' )
                ->where( 'a.token', null )
                ->fetchArray();

            wp_send_json_success( compact( 'staff_members' ) );
        }

        wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
    }

    /**
     * Get access tokens for mobile staff cabinet.
     *
     * @return void
     */
    public static function cloudMobileStaffCabinetGetAccessTokens()
    {
        $api = Lib\Cloud\API::getInstance();
        $keys_list = $api->getProduct( Lib\Cloud\Account::PRODUCT_MOBILE_STAFF_CABINET )->getKeysList();
        $fields = array( 'id', 'full_name', 'wp_user_id', 'staff_id', 'token' );
        $data = array();
        if ( $keys_list ) {
            global $wpdb;

            $data = Lib\Entities\Auth::query( 'a' )
                ->select( 'a.id, a.staff_id, a.wp_user_id, a.token, COALESCE(s.full_name, wpu.display_name) AS full_name' )
                ->leftJoin( 'Staff', 's', 's.id = a.staff_id AND s.visibility != \'archive\'' )
                ->tableJoin( $wpdb->users, 'wpu', 'wpu.ID = a.wp_user_id' )
                ->whereIn( 'a.token', $keys_list )
                ->fetchArray();

            foreach ( $data as $staff ) {
                if ( ( $idx = array_search( $staff['token'], $keys_list ) ) !== false ) {
                    unset ( $keys_list[ $idx ] );
                }
            }
            foreach ( $keys_list as $token ) {
                $row = array_fill_keys( $fields, null );
                $row['token'] = $token;
                $data[] = $row;
            }
        }

        wp_send_json( array(
            'draw' => ( int ) self::parameter( 'draw' ),
            'recordsTotal' => count( $data ),
            'recordsFiltered' => count( $data ),
            'data' => $data,
        ) );
    }
}