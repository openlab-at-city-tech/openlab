<?php

namespace Bookly\Lib\Cloud;

/**
 * Class Cron
 *
 * @package Bookly\Lib\Cloud
 */
class Cron extends Product
{
    const ACTIVATE                = '/1.0/users/%token%/products/cron/activate';                  //POST
    const DEACTIVATE_NEXT_RENEWAL = '/1.0/users/%token%/products/cron/deactivate/next-renewal';   //POST
    const DEACTIVATE_NOW          = '/1.0/users/%token%/products/cron/deactivate/now';            //POST
    const REVERT_CANCEL           = '/1.0/users/%token%/products/cron/revert-cancel';             //POST
    const ENDPOINT                = '/1.0/users/%token%/products/cron/endpoint';                  //POST

    /**
     * Activate Cron product
     *
     * @param integer $product_price
     *
     * @return boolean
     */
    public function activate( $product_price )
    {
        $data = $this->getActivatingData( $product_price );
        $data['test_endpoint'] = add_query_arg( array( 'action' => 'bookly_cloud_cron_test' ), admin_url( 'admin-ajax.php' ) );

        $response = $this->api
            ->setRequestTimeout( 90 )
            ->sendPostRequest( self::ACTIVATE, $data );
        if ( $response ) {
            update_option( 'bookly_cloud_account_products', $response['products'] );
            update_option( 'bookly_cloud_cron_api_key', $response['api-key'] );

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getEndPoint()
    {
        return add_query_arg( array( 'action' => 'bookly_cloud_cron' ), admin_url( 'admin-ajax.php' ) );
    }

    /**
     * Revert cancel Cron product
     *
     * @return bool
     */
    public function revertCancel()
    {
        $data = array(
            'endpoint' => $this->getEndPoint(),
        );
        $response = $this->api->sendPostRequest( self::REVERT_CANCEL, $data );
        if ( $response ) {
            update_option( 'bookly_cloud_account_products', $response['products'] );
            update_option( 'bookly_cloud_cron_api_key', $response['api-key'] );

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function translateError( $error_code )
    {
        switch ( $error_code ) {
            case 'ENDPOINT_ACCESS_ERROR':
                return __( 'Bookly Cloud couldn\'t connect to your server.', 'bookly' );
            default:
                return null;
        }
    }
}