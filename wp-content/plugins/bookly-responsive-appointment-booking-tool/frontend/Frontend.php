<?php
namespace Bookly\Frontend;

use Bookly\Frontend\Modules\Stripe\Controller as Stripe;
use Bookly\Lib;

/**
 * Class Frontend
 * @package Bookly\Frontend
 */
abstract class Frontend
{
    /**
     * Register hooks.
     */
    public static function registerHooks()
    {
        add_action( 'wp_loaded', array( __CLASS__, 'handleRequest' ) );
    }

    /**
     * Handle request.
     */
    public static function handleRequest()
    {
        // Payments ( PayPal Express Checkout and etc. )
        if ( isset ( $_REQUEST['bookly_action'] ) ) {
            // Disable caching.
            Lib\Utils\Common::noCache();

            Lib\Proxy\Shared::handleRequestAction( $_REQUEST['bookly_action'] );

            if ( Lib\Cloud\API::getInstance()->account->productActive( Lib\Cloud\Account::PRODUCT_STRIPE ) ) {
                switch ( $_REQUEST['bookly_action'] ) {
                    case 'cloud_stripe-checkout':
                        Stripe::checkout();
                        break;
                    case 'cloud_stripe-success':
                        Stripe::success();
                        break;
                    case 'cloud_stripe-cancel':
                        Stripe::cancel();
                        break;
                    /**
                     * Stripe Cloud notify
                     *
                     * @see \Bookly\Frontend\Modules\Stripe\Ajax::cloudStripeNotify
                     */
                }
            }
        }
    }
}