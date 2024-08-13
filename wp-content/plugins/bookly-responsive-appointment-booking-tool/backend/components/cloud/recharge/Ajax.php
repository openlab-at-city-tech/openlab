<?php
namespace Bookly\Backend\Components\Cloud\Recharge;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * Initial for enabling Auto-Recharge balance with PayPal
     */
    public static function initAutoRechargePaypal()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $url = $cloud->account->getBillingAgreementUrl( self::parameter( 'recharge' ), self::parameter( 'url' ) );
        if ( $url !== false ) {
            wp_send_json_success( array( 'paypal_preapproval' => $url ) );
        } else {
            $errors = $cloud->getErrors();
            $message = __( 'Auto-Recharge has failed, please replenish your balance directly.', 'bookly' );
            if ( array_key_exists( 'ERROR_PROMOTION_NOT_AVAILABLE', $errors ) ) {
                $message = $errors['ERROR_PROMOTION_NOT_AVAILABLE'];
            }
            wp_send_json_error( compact( 'message' ) );
        }
    }

    /**
     * Create Stripe Checkout session
     */
    public static function createStripeCheckoutSession()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $result = $cloud->account->createStripeCheckoutSession(
            self::parameter( 'recharge' ),
            self::parameter( 'mode' ),
            self::parameter( 'url' )
        );

        if ( $result === false ) {
            $errors = $cloud->getErrors();
            if ( array_key_exists( 'ERROR_RECHARGE_NOT_AVAILABLE', $errors ) ) {
                wp_send_json_error( array( 'message' => $errors['ERROR_RECHARGE_NOT_AVAILABLE'] ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Card payment has failed, please use another payment option', 'bookly' ) ) );
            }
        } else {
            wp_send_json( $result );
        }
    }

    /**
     * Create PayPal order
     */
    public static function createPaypalOrder()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $order_url = $cloud->account->createPayPalOrder(
            self::parameter( 'recharge' ),
            self::parameter( 'url' )
        );

        if ( $order_url === false ) {
            $errors = $cloud->getErrors();
            if ( array_key_exists( 'ERROR_RECHARGE_NOT_AVAILABLE', $errors ) ) {
                wp_send_json_error( array( 'message' => $errors['ERROR_RECHARGE_NOT_AVAILABLE'] ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Payment has failed, please use another payment option', 'bookly' ) ) );
            }
        } else {
            wp_send_json_success( compact( 'order_url' ) );
        }
    }

    /**
     * Disable Auto-Recharge balance
     */
    public static function disableAutoRecharge()
    {
        $disabled = Lib\Cloud\API::getInstance()->account->disableAutoRecharge();
        if ( $disabled !== false ) {
            update_option( 'bookly_cloud_auto_recharge_gateway', '' );
            wp_send_json_success( array( 'message' => __( 'Auto-Recharge disabled', 'bookly' ) ) );
        } else {
            wp_send_json_error( array( 'message' => sprintf( __( 'Can\'t disable Auto-Recharge, please contact us at %s', 'bookly' ), '<a href="mailto:support@bookly.info">support@bookly.info</a>' ) ) );
        }
    }
}