<?php
namespace Bookly\Lib\Cloud;

use Bookly\Backend\Modules\CloudProducts;
use Bookly\Lib\Config;

class Stripe extends Base
{
    const CONNECT        = '/1.0/users/%token%/products/stripe/connect';            //POST|DELETE
    const CREATE_SESSION = '/1.1/users/%token%/products/stripe/checkout/sessions';  //POST
    const RETRIEVE_EVENT = '/1.0/users/%token%/products/stripe/events/%event_id%';  //GET
    const RETRIEVE_PAYMENT_INTENT = '/1.0/users/%token%/products/stripe/payment-intents/%payment_intent_id%';  //GET
    const REFUND         = '/1.0/users/%token%/products/stripe/refund';             //POST
    const ENDPOINT       = '/1.0/users/%token%/products/stripe/endpoint';           //POST

    /**
     * @param array  $info
     * @param array  $customer
     * @param string $success_url
     * @param string $cancel_url
     * @return bool|array
     */
    public function createSession( $info, $customer, $success_url, $cancel_url )
    {
        $info['currency'] = Config::getCurrency();
        $info = array(
            'order_data' => $info,
            'customer' => $customer,
            'success_url' => $success_url,
            'cancel_url' => $cancel_url,
        );

        return $this->api->sendPostRequest( self::CREATE_SESSION, $info );
    }

    /**
     * Stripe connect
     *
     * @return bool|string
     */
    public function connect()
    {
        $data = $this->addTestCanIUse( array(
            'notify_url' => $this->getEndPoint(),
            'success_url' => add_query_arg( array( 'page' => CloudProducts\Page::pageSlug() ), admin_url( 'admin.php' ) ) . '#cloud-product=stripe&status=activated',
            'cancel_url' => add_query_arg( array( 'page' => CloudProducts\Page::pageSlug() ), admin_url( 'admin.php' ) ) . '#cloud-product=stripe&status=cancelled',
        ) );
        $response = $this->api->sendPostRequest( self::CONNECT, $data );
        if ( $response ) {
            return $response['redirect_url'];
        }

        return false;
    }

    /**
     * @return string
     */
    public function getEndPoint()
    {
        return add_query_arg( array( 'action' => 'bookly_cloud_stripe_notify' ), admin_url( 'admin-ajax.php' ) );
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
     * Disconnect Stripe account
     *
     * @return bool
     */
    public function disconnect()
    {
        return $this->api->sendDeleteRequest( self::CONNECT, array() );
    }

    /**
     * Refund Stripe payment intent
     *
     * @return array|false
     */
    public function refund( $payment_intent )
    {
        return $this->api->sendPostRequest( self::REFUND, compact( 'payment_intent' ) );
    }

    /**
     * Retrieve event
     *
     * @param string $event_id
     * @return array
     * @throws \Exception
     */
    public function retrieveEvent( $event_id )
    {
        $data = array( '%event_id%' => $event_id );
        $response = $this->api->sendGetRequest( self::RETRIEVE_EVENT, $data );

        if ( $response ) {
            return $response['data'];
        } else {
            throw new \Exception( current( $this->api->getErrors() ) );
        }
    }

    /**
     * Retrieve payment_intent
     *
     * @param string $payment_intent_id
     * @return array
     * @throws \Exception
     */
    public function retrievePaymentIntent( $payment_intent_id )
    {
        $data = array( '%payment_intent_id%' => $payment_intent_id );
        $response = $this->api->sendGetRequest( self::RETRIEVE_PAYMENT_INTENT, $data );

        if ( $response ) {
            return $response['data'];
        } else {
            throw new \LogicException( current( $this->api->getErrors() ) );
        }
    }

    /**
     * @inheritDoc
     */
    public function translateError( $error_code )
    {
        $translated = null;
        switch ( $error_code ) {
            case 'ERROR_STRIPE_NOT_CONNECTED':
                $translated = __( 'Stripe not connected', 'bookly' );
                break;
            case 'ERROR_STRIPE_ACCOUNT_NOT_FOUND':
                $translated = __( 'Stripe account not found', 'bookly' );
                break;
            case 'ERROR_STRIPE_REFUND_FAILED':
                $translated = __( 'Refund cannot be processed. Try again later or process it manually in your payment system', 'bookly' );
                break;
        }

        return $translated;
    }
}