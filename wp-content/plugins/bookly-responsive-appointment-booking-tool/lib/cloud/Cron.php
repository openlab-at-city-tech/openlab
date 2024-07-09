<?php
namespace Bookly\Lib\Cloud;

class Cron extends Product
{
    const ACTIVATE                = '/1.1/users/%token%/products/cron/activate';                  //POST
    const DEACTIVATE_NEXT_RENEWAL = '/1.0/users/%token%/products/cron/deactivate/next-renewal';   //POST
    const DEACTIVATE_NOW          = '/1.0/users/%token%/products/cron/deactivate/now';            //POST
    const REVERT_CANCEL           = '/1.0/users/%token%/products/cron/revert-cancel';             //POST
    const ENDPOINT                = '/1.0/users/%token%/products/cron/endpoint';                  //POST

    /**
     * Activate Cron product
     *
     * @param integer $product_price
     * @param integer $purchase_code
     *
     * @return boolean
     */
    public function activate( $product_price, $purchase_code = null )
    {
        $status = parent::activate( $product_price, null );
        if ( $status ) {
            update_option( 'bookly_cloud_cron_api_key', $this->response['api-key'] );
        }

        return $status;
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
                return __( 'Bookly Cloud couldn\'t connect to your server.', 'bookly' ) . '<br>' . __( 'Please check your firewall settings.', 'bookly' ) . '<br>' . sprintf( __( 'If problem persists, please contact us at <a href="mailto:%1$s">%1$s</a>', 'bookly'), 'support@bookly.info' );
            default:
                return null;
        }
    }
}