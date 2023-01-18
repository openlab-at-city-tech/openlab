<?php
namespace Bookly\Lib\Cloud;

/**
 * Class Product
 * @package Bookly\Lib\Cloud
 */
class Product extends Base
{
    const ACTIVATE                = ''; //POST
    const DEACTIVATE_NEXT_RENEWAL = ''; //POST
    const DEACTIVATE_NOW          = ''; //POST
    const REVERT_CANCEL           = ''; //POST
    const ENDPOINT                = ''; //POST

    /**
     * Activate Cloud product
     *
     * @param integer $product_price
     *
     * @return boolean
     */
    public function activate( $product_price )
    {
        $response = $this->api
            ->setRequestTimeout( 90 )
            ->sendPostRequest( static::ACTIVATE, $this->getActivatingData( $product_price ) );
        if ( $response ) {
            update_option( 'bookly_cloud_account_products', $response['products'] );

            return true;
        }

        return false;
    }

    /**
     * Deactivate Cloud product
     *
     * @param string $status
     *
     * @return bool
     */
    public function deactivate( $status = 'now' )
    {
        if ( $status == 'now' ) {
            $response = $this->api->sendPostRequest( static::DEACTIVATE_NOW );
        } else {
            $response = $this->api->sendPostRequest( static::DEACTIVATE_NEXT_RENEWAL );
        }

        if ( $response ) {
            update_option( 'bookly_cloud_account_products', $response['products'] );

            return true;
        }

        return false;
    }

    /**
     * Revert cancel Cloud product
     *
     * @return bool
     */
    public function revertCancel()
    {
        $response = $this->api->sendPostRequest( static::REVERT_CANCEL );
        if ( $response ) {
            update_option( 'bookly_cloud_account_products', $response['products'] );

            return true;
        }

        return false;
    }

    /**
     * Data for activating Cloud product
     *
     * @param integer $product_price
     * @return array
     */
    protected function getActivatingData( $product_price )
    {
        $data = compact( 'product_price' );
        if ( method_exists( $this, 'getEndPoint' ) ) {
            $data['endpoint'] = $this->getEndPoint();
        }

        return $data;
    }

    /**
     * @return bool
     */
    public function updateEndPoint()
    {
        return method_exists( $this, 'getEndPoint' )
            ? $this->api->sendPostRequest( static::ENDPOINT, array( 'endpoint' => $this->getEndPoint() ) )
            : true;
    }
}