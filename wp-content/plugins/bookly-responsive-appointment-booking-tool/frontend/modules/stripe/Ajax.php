<?php
namespace Bookly\Frontend\Modules\Stripe;

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

    public static function cloudStripeNotify()
    {
        $response_code = 200;
        if ( Lib\Cloud\API::getInstance()->account->productActive( 'stripe' ) ) {
            try {
                self::notify();
            } catch ( \Exception $e ) {
                Lib\Utils\Log::error( $e->getMessage(), $e->getFile(), $e->getLine() );
                $response_code = 400;
            }
        }
        Lib\Utils\Common::emptyResponse( $response_code );
    }

    /**
     * Retrieve event by notifying from Bookly Cloud
     *
     * @throws \Exception
     */
    private static function notify()
    {
        $event = Lib\Cloud\API::getInstance()->getProduct( Lib\Cloud\Account::PRODUCT_STRIPE )->retrieveEvent( $_POST['event_id'] );
        switch ( $event['type'] ) {
            case 'checkout.session.completed':
                self::processCheckoutSessionCompleted( $event );
                break;
            case 'charge.refunded':
                self::processChargeRefunded( $event );
                break;
        }
    }

    /**
     * Process Stripe event checkout.session.completed
     *
     * @param array $event
     */
    private static function processCheckoutSessionCompleted( $event )
    {
        $gateway = new Lib\Payment\StripeCloudGateway( \Bookly\Frontend\Modules\Payment\Request::getInstance() );
        $payment = new Lib\Entities\Payment();
        if ( $payment->loadBy( array( 'id' => $event['metadata']['payment_id'], 'type' => Lib\Entities\Payment::TYPE_CLOUD_STRIPE ) ) ) {
            if ( array_key_exists( 'payment_intent', $event ) ) {
                $payment->setRefId( $event['payment_intent'] )->save();
            }
            $gateway->setPayment( $payment )->retrieve();
        }
    }

    /**
     * Process Stripe charge.refunded
     *
     * @param array $data
     */
    private static function processChargeRefunded( $data )
    {
        /** @var Lib\Entities\Payment $payment */
        $payment = Lib\Entities\Payment::query()
            ->where( 'id', $data['metadata']['payment_id'] )
            ->where( 'type', Lib\Entities\Payment::TYPE_CLOUD_STRIPE )
            ->whereNot( 'status', Lib\Entities\Payment::STATUS_REFUNDED )
            ->findOne();
        if ( $payment ) {
            $payment
                ->setStatus( Lib\Entities\Payment::STATUS_REFUNDED )
                ->save();
        }
    }

    /**
     * Override parent method to exclude actions from CSRF token verification.
     *
     * @param string $action
     * @return bool
     */
    protected static function csrfTokenValid( $action = null )
    {
        return $action === 'cloudStripeNotify' || parent::csrfTokenValid( $action );
    }
}