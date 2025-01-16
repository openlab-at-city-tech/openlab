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
        return $this->sendPostRequest( static::ACTIVATE, $this->getActivatingData( $product_price, $purchase_code ) );
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
        return $this->sendPostRequest( $status === 'now' ? static::DEACTIVATE_NOW : static::DEACTIVATE_NEXT_RENEWAL );
    }

    /**
     * Revert cancel Cloud product
     *
     * @return bool
     */
    public function revertCancel()
    {
        return $this->sendPostRequest( static::REVERT_CANCEL );
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

        return $data;
    }

    /**
     * @return bool
     */
    public function updateEndPoint()
    {
        return ! method_exists( $this, 'getEndPoint' ) || $this->sendPostRequest( static::ENDPOINT, array( 'endpoint' => $this->getEndPoint() ) );
    }

    /**
     * Send a post request
     *
     * @param string $path
     * @param array $data
     * @return bool
     */
    private function sendPostRequest( $path, $data = array() )
    {
        if ( $this->product_id ) {
            $data['%product_id%'] = $this->product_id;
        }

        $this->response = $this->api
            ->sendPostRequest( $path, $data );
        if ( $this->response ) {
            update_option( 'bookly_cloud_account_products', $this->response['products'] );

            return true;
        }

        return false;
    }
}