<?php
namespace Bookly\Lib\Cloud;

use Bookly\Lib;

class MobileStaffCabinet extends Product
{
    const ACTIVATE                = '/1.0/users/%token%/products/mobile-staff-cabinet/activate';                  //POST
    const DEACTIVATE_NEXT_RENEWAL = '/1.0/users/%token%/products/mobile-staff-cabinet/deactivate/next-renewal';   //POST
    const DEACTIVATE_NOW          = '/1.0/users/%token%/products/mobile-staff-cabinet/deactivate/now';            //POST
    const REVERT_CANCEL           = '/1.0/users/%token%/products/mobile-staff-cabinet/revert-cancel';             //POST
    const GET_ACCESS_KEYS_LIST    = '/1.0/users/%token%/products/mobile-staff-cabinet/access/keys';               //GET
    const GRANT_ACCESS_KEY        = '/1.0/users/%token%/products/mobile-staff-cabinet/access/keys';               //POST
    const REVOKE_ACCESS_KEYS      = '/1.0/users/%token%/products/mobile-staff-cabinet/access/keys';               //DELETE
    const PATCH_ACCESS_KEY        = '/1.0/users/%token%/products/mobile-staff-cabinet/access/key';                //PATCH

    /**
     * Grant auth key for staff
     *
     * @param Lib\Entities\Staff $staff
     *
     * @return boolean
     */
    public function grantKey( Lib\Entities\Staff $staff )
    {
        $data = array(
            'endpoint' => $this->getEndPoint(),
            'email' => $staff->getEmail(),
        );

        $response = $this->api
            ->sendPostRequest( self::GRANT_ACCESS_KEY, $data );
        if ( $response ) {
            $staff->setMobileStaffCabinetToken( $response['key'] )->save();

            return true;
        }

        return false;
    }

    /**
     * Assign to current access_key endpoint and email
     *
     * @param string $access_key
     * @param Lib\Entities\Staff $staff
     *
     * @return boolean
     */
    public function patchKey( $access_key, Lib\Entities\Staff $staff )
    {
        $data = array(
            'access_key' => $access_key,
            'endpoint' => $this->getEndPoint(),
            'email' => $staff->getEmail(),
        );

        return $this->api
            ->sendPatchRequest( self::PATCH_ACCESS_KEY, $data );
    }

    /**
     * @return array
     */
    public function getKeysList()
    {
        $response = $this->api->sendGetRequest( self::GET_ACCESS_KEYS_LIST );

        return $response
            ? $response['list']
            : array();
    }

    /**
     * @param array $access_keys
     * @return bool
     */
    public function revokeKeys( array $access_keys )
    {
        if ( $access_keys ) {
            return $this->api->sendDeleteRequest( self::REVOKE_ACCESS_KEYS, compact( 'access_keys' ) ) !== false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getEndPoint()
    {
        return add_query_arg( array( 'action' => 'bookly_mobile_staff_cabinet' ), admin_url( 'admin-ajax.php' ) );
    }

    public function updateEndPoint()
    {
        $this->api->addError( 'Not implement' );

        return false;
    }
}