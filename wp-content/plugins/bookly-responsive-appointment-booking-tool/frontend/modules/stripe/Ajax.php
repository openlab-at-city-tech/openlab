<?php
namespace Bookly\Frontend\Modules\Stripe;

use Bookly\Frontend\Modules\ModernBookingForm\Proxy;
use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Frontend\Modules\Stripe
 */
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
        if ( Lib\Cloud\API::getInstance()->account->productActive( 'stripe' ) ) {
            try {
                self::notify();
            } catch ( \Exception $e ) {
                status_header( 400 );
            }
        }
        exit;
    }

    /**
     * Retrieve event by notifying from Bookly Cloud
     *
     * @throws \Exception
     */
    private static function notify()
    {
        $event = Lib\Cloud\API::getInstance()->stripe->retrieveEvent( $_POST['event_id'] );
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
     * @param array $data
     */
    private static function processCheckoutSessionCompleted( $data )
    {
        $stripe_amount = $data['amount'];
        $payment = new Lib\Entities\Payment();
        $payment->loadBy( array( 'id' => $data['metadata']['payment_id'], 'type' => Lib\Entities\Payment::TYPE_CLOUD_STRIPE ) );
        if ( $payment->getStatus() === Lib\Entities\Payment::STATUS_PENDING ) {
            if ( strtoupper( $data['currency'] ) === Lib\Config::getCurrency() ) {
                $amount = $payment->getPaid();
                if ( ! Lib\Config::isZeroDecimalsCurrency() ) {
                    // Amount in cents
                    $amount = (int) ( $amount * 100 );
                }
                if ( $stripe_amount === $amount ) {
                    if ( $payment->getTarget() === Lib\Entities\Payment::TARGET_GIFT_CARDS ) {
                        Proxy\Pro::setPaymentCompleted( $payment );
                    } else {
                        $payment->setStatus( Lib\Entities\Payment::STATUS_COMPLETED )->save();
                        if ( $order = Lib\DataHolders\Booking\Order::createFromPayment( $payment ) ) {
                            current( $order->getItems() )->getCA()->setJustCreated( true );
                            Lib\Notifications\Cart\Sender::send( $order );

                            foreach ( $order->getFlatItems() as $item ) {
                                if ( $item->getAppointment()->getGoogleEventId() !== null ) {
                                    Lib\Proxy\Pro::syncGoogleCalendarEvent( $item->getAppointment() );
                                }
                                if ( $item->getAppointment()->getOutlookEventId() !== null ) {
                                    Lib\Proxy\OutlookCalendar::syncEvent( $item->getAppointment() );
                                }
                            }
                        }
                    }
                }
            }
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