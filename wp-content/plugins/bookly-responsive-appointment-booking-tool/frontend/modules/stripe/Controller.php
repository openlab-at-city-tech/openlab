<?php
namespace Bookly\Frontend\Modules\Stripe;

use Bookly\Lib;
use Bookly\Lib\Entities\Payment;
use Bookly\Lib\UserBookingData;
use Bookly\Lib\Utils\Common;
use Bookly\Frontend\Components\Booking;
use Bookly\Frontend\Modules\Booking\Lib\Steps;

/**
 * Class Controller
 *
 * @package Bookly\Frontend\Modules\Stripe
 */
class Controller extends Lib\Base\Component
{
    public static $remove_parameters = array( 'bookly_action', 'bookly_fid', 'error_msg', 'payment' );

    /**
     * Create Stripe session
     */
    public static function checkout()
    {
        $form_id = self::parameter( 'bookly_fid' );
        $response_url = add_query_arg( array( 'bookly_fid' => $form_id ), self::parameter( 'response_url' ) );
        $userData = new Lib\UserBookingData( $form_id );
        $userData->load();
        try {
            wp_redirect( self::createSession( $userData, $response_url ) );
        } catch ( \Exception $e ) {
            $userData->setFailedPaymentStatus( Lib\Entities\Payment::TYPE_CLOUD_STRIPE, 'error', $e->getMessage() );
            @wp_redirect( remove_query_arg( self::$remove_parameters, $response_url ) );
        }
        exit;
    }

    /**
     * Handle success request
     */
    public static function success()
    {
        $userData = new UserBookingData( self::parameter( 'bookly_fid' ) );
        if ( $userData->load() ) {
            $userData->setPaymentStatus( Payment::TYPE_CLOUD_STRIPE, 'processing' )
                ->sessionSave();
        }

        @wp_redirect( remove_query_arg( self::$remove_parameters, Common::getCurrentPageURL() ) );
        exit;
    }

    /**
     * Cancel session
     */
    public static function cancel()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'bookly_fid' ) );
        if ( $userData->load() ) {
            $userData->setFailedPaymentStatus( Lib\Entities\Payment::TYPE_CLOUD_STRIPE, 'cancelled' )
                ->sessionSave();
        }

        @wp_redirect( remove_query_arg( self::$remove_parameters, Lib\Utils\Common::getCurrentPageURL() ) );
        exit;
    }

    /**
     * @param UserBookingData $userData
     * @param string $response_url
     * @return string
     * @throws \Exception
     */
    public static function createSession( UserBookingData $userData, $response_url )
    {
        $payment = new Lib\Entities\Payment();
        $cart_info = $userData->cart->getInfo( Lib\Entities\Payment::TYPE_CLOUD_STRIPE );
        $items = $userData->cart->getItems();
        $target = reset( $items )->getService()->getType() === Lib\Entities\Service::TYPE_PACKAGE
            ? Lib\Entities\Payment::TARGET_PACKAGES
            : Lib\Entities\Payment::TARGET_APPOINTMENTS;

        $payment
            ->setType( Lib\Entities\Payment::TYPE_CLOUD_STRIPE )
            ->setCartInfo( $cart_info )
            ->setStatus( Lib\Entities\Payment::STATUS_PENDING )
            ->setTarget( $target )
            ->save();
        $metadata = array();
        // Build custom metadata.
        if ( get_option( 'bookly_cloud_stripe_custom_metadata' ) ) {
            $codes = Booking\InfoText::getCodes( Steps::PAYMENT, $userData );
            foreach ( get_option( 'bookly_cloud_stripe_metadata', array() ) as $meta ) {
                $metadata[ preg_replace( '/[^ \w]+/', '', $meta['name'] ) ] = Lib\Utils\Codes::replace( $meta['value'], $codes, false );
            }
        }
        $metadata['payment_id'] = $payment->getId();
        $info = array(
            'total' => $cart_info->getGatewayAmount(),
            'description' => $userData->cart->getItemsTitle(),
            'customer_email' => $userData->getEmail(),
            'metadata' => $metadata,
        );
        $api = Lib\Cloud\API::getInstance();
        $response = $api->stripe
            ->createSession(
                $info,
                add_query_arg( array( 'bookly_action' => 'cloud_stripe-success', 'payment' => $payment->getToken() ), $response_url ),
                add_query_arg( array( 'bookly_action' => 'cloud_stripe-cancel', 'payment' => $payment->getToken() ), $response_url )
            );
        if ( $response ) {
            $order = $userData->save( $payment );
            $payment
                ->setDetailsFromOrder( $order, $cart_info )
                ->setRefId( $response['payment_intent'] )
                ->save();
            $userData->sessionSave();

            return $response['redirect_url'];
        } else {
            $payment->delete();
        }

        throw new \Exception( current( $api->getErrors() ) );
    }
}