<?php
namespace Bookly\Lib\Cloud;

class Zapier extends Product
{
    const ACTIVATE                = '/1.0/users/%token%/products/zapier/activate';                  //POST
    const DEACTIVATE_NEXT_RENEWAL = '/1.0/users/%token%/products/zapier/deactivate/next-renewal';   //POST
    const DEACTIVATE_NOW          = '/1.0/users/%token%/products/zapier/deactivate/now';            //POST
    const GENERATE_API_KEY        = '/1.0/users/%token%/products/zapier/generate/api-key';          //POST
    const REVERT_CANCEL           = '/1.0/users/%token%/products/zapier/revert-cancel';             //POST
    const ENDPOINT                = '/1.0/users/%token%/products/zapier/endpoint';                  //POST

    /**
     * @return string
     */
    public function getEndPoint()
    {
        return add_query_arg( array( 'action' => 'bookly_cloud_zapier' ), admin_url( 'admin-ajax.php' ) );
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
            if ( isset( $response['account'][ Account::PRODUCT_ZAPIER ] ) ) {
                $zapier->setApiKey( $response['account'][ Account::PRODUCT_ZAPIER ]['api_key'] );
            } else {
                $zapier->setApiKey( '' );
            }
        } );
    }
}