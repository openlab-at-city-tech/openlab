<?php
namespace Bookly\Lib\Cloud;

class Product extends Base
{
    const ACTIVATE                = ''; //POST
    const DEACTIVATE_NEXT_RENEWAL = ''; //POST
    const DEACTIVATE_NOW          = ''; //POST
    const REVERT_CANCEL           = ''; //POST
    const ENDPOINT                = ''; //POST

    /** @var string */
    protected $product_id;

    protected $response;

    /**
     * Constructor.
     *
     * @param API $api
     * @param string $product_id
     */
    public function __construct( API $api, $product_id = null )
    {
        parent::__construct( $api );
        $this->product_id = $product_id;
    }

    /**
     * Activate Cloud product
     *
     * @param integer $product_price
     * @param string $purchase_code
     *
     * @return boolean
     */
    public function activate( $product_price, $purchase_code = null )
    {
        $this->response = $this->api
            ->setRequestTimeout( 90 )
            ->sendPostRequest( static::ACTIVATE, $this->getActivatingData( $product_price, $purchase_code ) );
        if ( $this->response ) {
            update_option( 'bookly_cloud_account_products', $this->response['products'] );

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
        if ( $status === 'now' ) {
            $response = $this->api->sendPostRequest( static::DEACTIVATE_NOW, $this->withProductId( array() ) );
        } else {
            $response = $this->api->sendPostRequest( static::DEACTIVATE_NEXT_RENEWAL, $this->withProductId( array() ) );
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
        $response = $this->api->sendPostRequest( static::REVERT_CANCEL, $this->withProductId( array() ) );
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
     * @param string $purchase_code
     * @return array
     */
    protected function getActivatingData( $product_price, $purchase_code )
    {
        $data = $purchase_code
            ? compact( 'purchase_code' )
            : compact( 'product_price' );
        if ( method_exists( $this, 'getEndPoint' ) ) {
            $data['endpoint'] = $this->getEndPoint();
            $data = $this->addTestCanIUse( $data );
        }

        return $this->withProductId( $data );
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

    /**
     * @param array $data
     * @return array
     */
    private function withProductId( array $data )
    {
        if ( $this->product_id ) {
            $data['%product_id%'] = $this->product_id;
        }

        return $data;
    }
}