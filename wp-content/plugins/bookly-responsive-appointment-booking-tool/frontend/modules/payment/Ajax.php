<?php
namespace Bookly\Frontend\Modules\Payment;

use Bookly\Frontend\Modules\Booking\Lib\Errors;
use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'anonymous' );
    }

    public static function createPaymentIntent()
    {
        $request = Request::getInstance();
        $userData = $request->getUserData();
        $failed_cart_key = $userData->cart->getFailedKey();
        if ( $failed_cart_key === null ) {
            try {
                wp_send_json_success( $request->getGateway()->isOnSite()
                    ? $request->getGateway()->createIntent()
                    : $request->getGateway()->createCheckout()
                );
            } catch ( \Error $e ) {
                $request->getGateway()->fail();
                wp_send_json( array( 'success' => false, 'error' => Errors::PAYMENT_ERROR, 'error_message' => $e->getMessage() ) );
            } catch ( \Exception $e ) {
                $request->getGateway()->fail();
                wp_send_json( array( 'success' => false, 'error' => Errors::PAYMENT_ERROR, 'error_message' => $e->getMessage() ) );
            }
        }

        wp_send_json( array( 'success' => false, 'error' => Errors::CART_ITEM_NOT_AVAILABLE, 'failed_cart_key' => $failed_cart_key, ) );
    }

    /**
     * Back from payment systems checkout in booking-form
     *
     * @return void
     */
    public static function backFromPaymentSystem()
    {
        $request = Request::getInstance();
        $order = new Lib\Entities\Order();
        if ( $order->loadBy( array( 'token' => $request->get( 'bookly_order' ) ) ) ) {
            $gateway = $request->getGateway();
            try {
                switch ( $request->get( 'bookly_event' ) ) {
                    case Lib\Base\Gateway::EVENT_CANCEL:
                        $gateway->fail();
                        break;
                    case Lib\Base\Gateway::EVENT_RETRIEVE:
                        $gateway->retrieve();
                        break;
                }
            } catch ( \Exception $e ) {
                $gateway->fail();
            }
        }

        Lib\Utils\Common::redirect( self::parameter( 'bookly_referer' ) );
    }

    /**
     * @return void
     * @throws \Exception
     */
    public static function rollbackOrder()
    {
        Request::getInstance()->getGateway()->fail();
        wp_send_json_success();
    }

    /**
     * @return void
     */
    public static function handleWebhook()
    {
        $response_code = 200;
        try {
            Request::getInstance()->getGateway()->retrieve();
        } catch ( \Exception $e ) {
            Lib\Utils\Log::error( $e->getMessage(), $e->getFile(), $e->getLine() );
            $response_code = 400;
        }
        Lib\Utils\Common::emptyResponse( $response_code );
    }

    /**
     * @inerhitDoc
     */
    protected static function csrfTokenValid( $action = null )
    {
        return true;
    }
}