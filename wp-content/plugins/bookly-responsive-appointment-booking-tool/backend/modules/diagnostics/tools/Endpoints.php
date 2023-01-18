<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

use Bookly\Lib;
use Bookly\Lib\Cloud\Account;

/**
 * Class Endpoints
 *
 * @package Bookly\Backend\Modules\Diagnostics\Tests
 */
class Endpoints extends Tool
{
    protected $slug = 'products-endpoint';
    protected $hidden = true;
    protected $troubles;

    public function __construct()
    {
        $this->title = __( 'End points', 'bookly' );
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $products = array();
        if ( $this->getTroubles() ) {
            foreach ( Lib\Cloud\API::getInstance()->general->getProducts() as $product ) {
                $products[ $product['id'] ] = $product['texts']['title'];
            }
        }

        return self::renderTemplate( '_endpoints', array( 'products' => $products, 'troubles' => $this->troubles, ), false );
    }

    /**
     * @inheritDoc
     */
    public function hasError()
    {
        $this->getTroubles();

        return ! empty( $this->troubles );
    }

    /**
     * Update product endpoint
     *
     * @param array $post
     * @return void
     */
    public function updateEndPoint( $post )
    {
        $api = Lib\Cloud\API::getInstance();
        $product = null;
        switch ( $post['params']['product'] ) {
            case Account::PRODUCT_ZAPIER;
                $product = $api->zapier;
                break;
            case Account::PRODUCT_STRIPE;
                $product = $api->stripe;
                break;
            case Account::PRODUCT_CRON;
                $product = $api->cron;
        }
        if ( $product && $product->updateEndPoint() ) {
            wp_send_json_success();
        }

        wp_send_json_error( array( 'message' => current( $api->getErrors() ) ?: __( 'Failed', 'bookly' ) ) );
    }

    /**
     * @return array
     */
    private function getTroubles()
    {
        if ( $this->troubles === null ) {
            $this->troubles = array();
            $api = Lib\Cloud\API::getInstance();
            foreach ( $api->account->getEndPoints() as $product => $endpoint ) {
                switch ( $product ) {
                    case Account::PRODUCT_STRIPE:
                        $expected_endpoint = $api->stripe->getEndPoint();
                        break;
                    case Account::PRODUCT_ZAPIER;
                        $expected_endpoint = $api->zapier->getEndPoint();
                        break;
                    case Account::PRODUCT_CRON;
                        $expected_endpoint = $api->cron->getEndPoint();
                        break;
                    default:
                        continue 2;
                }
                if ( strcasecmp( $endpoint, $expected_endpoint ) != 0 ) {
                    $this->troubles[ $product ] = array(
                        'current' => $endpoint,
                        'expected' => $expected_endpoint,
                    );
                }
            }
        }

        return $this->troubles;
    }
}