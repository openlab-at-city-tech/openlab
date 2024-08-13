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

        Lib\Entities\Staff::query()
            ->update()
            ->set( 'cloud_msc_token', null )
            ->whereIn( 'cloud_msc_token', $keys )
            ->execute();
        $api = Lib\Cloud\API::getInstance();
        $staff_members = Lib\Entities\Staff::query()
            ->select( 'id, full_name' )
            ->sortBy( 'full_name' )
            ->whereNot( 'visibility', 'archive' )
            ->where( 'cloud_msc_token', null )
            ->fetchArray();
        $api->getProduct( Lib\Cloud\Account::PRODUCT_MOBILE_STAFF_CABINET )->revokeKeys( $keys )
            ? wp_send_json_success( compact( 'staff_members' ) )
            : wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
    }

    public static function cloudMobileStaffCabinetGetAccessTokens()
    {
        $api = Lib\Cloud\API::getInstance();
        $keys_list = $api->getProduct( Lib\Cloud\Account::PRODUCT_MOBILE_STAFF_CABINET )->getKeysList();
        $fields = array( 'id', 'full_name', 'email', 'cloud_msc_token' );
        $data = array();
        if ( $keys_list ) {
            $data = Lib\Entities\Staff::query()
                ->select( implode( ',', $fields ) )->whereIn( 'cloud_msc_token', $keys_list )
                ->fetchArray();
            foreach ( $data as $staff ) {
                if ( ( $idx = array_search( $staff['cloud_msc_token'], $keys_list ) ) !== false ) {
                    unset ( $keys_list[ $idx ] );
                }
            }
            foreach ( $keys_list as $token ) {
                $row = array_fill_keys( $fields, null );
                $row['cloud_msc_token'] = $token;
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