<?php
namespace Bookly\Lib\Cloud;

/**
 * Class Zapier
 * @package Bookly\Lib\Cloud
 */
class Zapier extends Base
{
    const ACTIVATE                = '/1.0/users/%token%/products/zapier/activate';                  //POST
    const DEACTIVATE_NEXT_RENEWAL = '/1.0/users/%token%/products/zapier/deactivate/next-renewal';   //POST
    const DEACTIVATE_NOW          = '/1.0/users/%token%/products/zapier/deactivate/now';            //POST
    const GENERATE_API_KEY        = '/1.0/users/%token%/products/zapier/generate/api-key';          //POST
    const REVERT_CANCEL           = '/1.0/users/%token%/products/zapier/revert-cancel';             //POST
    const ENDPOINT                = '/1.0/users/%token%/products/zapier/endpoint';                  //POST

    /**
     * Activate Zapier product
     *
     * @param integer $product_price
     *
     * @return boolean
     */
    public function activate( $product_price )
    {
        $data = array(
            'endpoint'      => $this->getEndPoint(),
            'product_price' => $product_price,
        );

        $response = $this->api
            ->setRequestTimeout( 90 )
            ->sendPostRequest( self::ACTIVATE, $data );
        if ( $response ) {
            update_option( 'bookly_cloud_account_products', $response['products'] );

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getEndPoint()
    {
        return add_query_arg( array( 'action' => 'bookly_cloud_zapier' ), admin_url( 'admin-ajax.php' ) );
    }

    /**
     * @return bool
     */
    public function updateEndPoint()
    {
        $endpoint = $this->getEndPoint();

        return $this->api->sendPostRequest( self::ENDPOINT, compact( 'endpoint' ) );
    }

    /**
     * Deactivate Zapier product
     * @param string $status
     *
     * @return bool
     */
    public function deactivate( $status = 'now' )
    {
        if ( $status == 'now' ) {
            $response = $this->api->sendPostRequest( self::DEACTIVATE_NOW );
        } else {
            $response = $this->api->sendPostRequest( self::DEACTIVATE_NEXT_RENEWAL );
        }

        if ( $response ) {
            update_option( 'bookly_cloud_account_products', $response['products'] );

            return true;
        }

        return false;
    }

    /**
     * Revert cancel Zapier product
     *
     * @return bool
     */
    public function revertCancel()
    {
        $response = $this->api->sendPostRequest( self::REVERT_CANCEL );
        if ( $response ) {
            update_option( 'bookly_cloud_account_products', $response['products'] );

            return true;
        }

        return false;
    }

    /**
     * Generate new Api Key
     *
     * @return array|false
     */
    public function generateNewApiKey()
    {
        return $this->api->sendPostRequest( self::GENERATE_API_KEY, array() );
    }

    /**
     * Check API Key
     *
     * @param string $api_key
     * @return bool
     */
    public static function checkApiKey( $api_key )
    {
        return $api_key && ( $api_key == get_option( 'bookly_cloud_zapier_api_key' ) );
    }

    /**
     * Set API Key
     *
     * @param string $api_key
     * @return $this
     */
    public function setApiKey( $api_key )
    {
        update_option( 'bookly_cloud_zapier_api_key', $api_key );

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function setupListeners()
    {
        $zapier = $this;

        $this->api->listen( Events::ACCOUNT_PROFILE_LOADED, function ( $response ) use ( $zapier ) {
            if ( isset( $response['account']['zapier'] ) ) {
                $zapier->setApiKey( $response['account']['zapier']['api_key'] );
            } else {
                $zapier->setApiKey( '' );
            }
        } );
    }
}