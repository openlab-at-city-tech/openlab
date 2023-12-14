<?php
namespace Bookly\Lib\Base;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Components;
use Bookly\Frontend\Modules\Booking;
use Bookly\Frontend\Modules\Payment\Request;
use Bookly\Lib\Config;
use Bookly\Lib\Payment;
use Bookly\Lib\Entities;
use Bookly\Lib\Utils\Codes;

abstract class Gateway
{
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    const EVENT_RETRIEVE = 'retrieve';
    const EVENT_CANCEL = 'cancel';

    /** @var string */
    protected $type;
    /** @var Request */
    protected $request;
    /** @var Entities\Payment */
    protected $payment;
    /** @var BooklyLib\DataHolders\Booking\Order */
    protected $order;
    /** @var bool */
    protected $on_site = false;

    public function __construct( Request $request )
    {
        BooklyLib\Utils\Common::noCache();
        $this->request = $request;
    }

    /**
     * @return string
     */
    abstract public function retrieveStatus();

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getMetaData()
    {
        $meta = $this->getInternalMetaData();
        foreach ( $meta as $value ) {
            if ( is_string( $value ) && preg_match( '/{[^}]*}/', $value ) ) {
                return $this->applyCodes( $meta );
            }
        }

        return $meta;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function createIntent()
    {
        if ( $this->createPayment() ) {
            $this->order = $this->request->getUserData()->save( $this->getPayment() );
            $data = $this->createGatewayIntent();

            $payment = $this->getPayment();
            if ( $payment ) {
                $payment
                    ->setDetailsFromOrder( $this->order, $this->request->getCartInfo() );
                if ( isset( $data['ref_id'] ) ) {
                    $payment->setRefId( $data['ref_id'] );
                    unset( $data['ref_id'] );
                }
                $payment->save();
            }
            $data['on_site'] = $this->isOnSite();
            // Storing order_id
            $this->request->getUserData()->sessionSave();

            return $data;
        }

        throw new \Exception( 'Can\'t save payment' );
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function createCheckout()
    {
        try {
            return array(
                'target_url' => $this->getCheckoutUrl( $this->createIntent() ),
                'bookly_order' => BooklyLib\Entities\Order::find( $this->order->getOrderId() )->getToken(),
            );
        } catch ( \Exception $e ) {
            $this->fail();
            throw $e;
        }
    }

    /**
     * @return Entities\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param Entities\Payment $payment
     * @return $this
     */
    public function setPayment( Entities\Payment $payment )
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Retrieve payment status
     *
     * @return string
     */
    public function retrieve()
    {
        try {
            if ( $this->getType() === null ) {
                $status = $this->retrieveStatus();
            } else {
                switch ( $this->payment->getStatus() ) {
                    case Entities\Payment::STATUS_PENDING:
                        $status = $this->retrieveStatus();
                        break;
                    case Entities\Payment::STATUS_COMPLETED:
                        $status = self::STATUS_COMPLETED;
                        break;
                    default:
                        $status = self::STATUS_FAILED;
                }
            }
        } catch ( \Exception $e ) {
            $path = explode( '\\', get_class( $this ) );
            BooklyLib\Utils\Log::put( BooklyLib\Utils\Log::ACTION_ERROR, array_pop( $path ), null, $e->getFile() . ':' . $e->getLine(), $this->payment->getRefId(), $e->getMessage() );
            $status = self::STATUS_FAILED;
        }

        if ( $status === self::STATUS_COMPLETED ) {
            $this->complete();
        } elseif ( $status === self::STATUS_FAILED ) {
            $this->fail();
        }

        if ( $this->getPayment() && $this->request->isBookingForm() ) {
            $this->request->getUserData()->setPaymentStatus( $status )
                ->sessionSave();
        }
        return $status;
    }

    /**
     * Payment completed
     *
     * @return void
     */
    public function complete()
    {
        $payment = $this->getPayment();

        $required_sync = false;
        if ( $payment ) {
            // Re-fetch a status from the database
            $status = Entities\Payment::query()->where( 'id', $payment->getId() )->fetchVar( 'status' );
            if ( $status !== Entities\Payment::STATUS_COMPLETED ) {
                if ( $payment->getType() !== Entities\Payment::TYPE_LOCAL || $payment->getTotal() == 0 ) {
                    $payment->setStatus( Entities\Payment::STATUS_COMPLETED )->save();
                }
                if ( $payment->getCouponId() ) {
                    Booking\Proxy\Coupons::claim( $payment->getCouponId() );
                }
                $required_sync = true;
            }
        } else {
            $required_sync = true;
        }

        if ( $required_sync ) {
            if ( $payment && $payment->getTarget() === Entities\Payment::TARGET_GIFT_CARDS ) {
                Payment\Proxy\Pro::completeGiftCard( $payment );
            } else {
                $order_id = $payment
                    ? $payment->getOrderId()
                    : Entities\Order::query()->where( 'token', $this->request->get( 'bookly_order' ) )->fetchVar( 'id' );
                $order = BooklyLib\DataHolders\Booking\Order::createFromOrderId( $order_id );

                if ( $order ) {
                    $type = current( $order->getItems() )->getType();
                    if ( $type !== BooklyLib\DataHolders\Booking\Item::TYPE_PACKAGE ) {
                        current( $order->getItems() )->getCA()->setJustCreated( true );
                        BooklyLib\Notifications\Cart\Sender::send( $order );
                    }
                    list( $sync, $gc, $oc ) = Config::syncCalendars();
                    foreach ( $order->getFlatItems() as $item ) {
                        if ( $item->getType() === BooklyLib\DataHolders\Booking\Item::TYPE_PACKAGE ) {
                            Payment\Proxy\Shared::complete( $item );
                        } elseif ( $sync ) {
                            if ( $gc && $item->getAppointment()->getGoogleEventId() !== null ) {
                                BooklyLib\Proxy\Pro::syncGoogleCalendarEvent( $item->getAppointment() );
                            }
                            if ( $oc && $item->getAppointment()->getOutlookEventId() !== null ) {
                                BooklyLib\Proxy\OutlookCalendar::syncEvent( $item->getAppointment() );
                            }
                        }
                    }
                }
            }
        }

        if ( $this->request->isBookingForm() ) {
            $this->request->getUserData()->setPaymentStatus( self::STATUS_COMPLETED )->sessionSave();
        }
    }

    /**
     * Payment failed
     *
     * @return void
     */
    public function fail()
    {
        $this->getPayment() && $this->removeCascade( $this->getPayment() );
        if ( $this->request->isBookingForm() ) {
            foreach ( $this->request->getUserData()->cart->getItems() as $cart_item ) {
                // Appointment was deleted
                $cart_item->setAppointmentId( null );
            }
        }
        $this->request->getUserData()->setPaymentStatus( self::STATUS_FAILED )->sessionSave();
        Entities\Order::query()->delete()->where( 'token', $this->request->get( 'bookly_order' ) )->execute();
    }

    /**
     * @return bool
     */
    public function isOnSite()
    {
        return $this->on_site;
    }

    /**
     * @return array
     * @throws \Exception
     */
    abstract protected function createGatewayIntent();

    /**
     * @param array $intent_data
     * @return string
     */
    abstract protected function getCheckoutUrl( array $intent_data );

    /**
     * @return array
     */
    abstract protected function getInternalMetaData();

    /**
     * Create a shared webhook endpoint for payment system events
     *
     * @return string
     */
    protected function getWebHookUrl()
    {
        $data = array(
            'action' => 'bookly_handle_webhook',
            'bookly_order' => Entities\Order::find( $this->order->getOrderId() )->getToken(),
        );

        return add_query_arg(
            $data,
            admin_url( 'admin-ajax.php' )
        );
    }

    /**
     * @param string $event self::EVENT_*
     * @return string
     */
    protected function getResponseUrl( $event )
    {
        if ( $this->request->isBookingForm() ) {
            $data = array(
                'action' => 'bookly_back_from_payment_system',
                'bookly_fid' => $this->request->getFormId(),
                'bookly_referer' => $this->request->get( 'response_url' ),
            );
        } else {
            $data = array(
                'action' => 'bookly_pro_checkout_response',
            );
        }
        $data['bookly_event'] = $event;
        $data['bookly_order'] = BooklyLib\Entities\Order::find( $this->order->getOrderId() )->getToken();

        return add_query_arg(
            $data,
            admin_url( 'admin-ajax.php' )
        );
    }

    /**
     * @return bool|int
     */
    protected function createPayment()
    {
        if ( $this->request->getGateway()->getType() !== null ) {
            $this->payment = new Entities\Payment();

            return $this->payment
                ->setCartInfo( $this->request->getCartInfo() )
                ->setTarget( $this->request->getCartInfo()->getTarget() )
                ->save();
        }

        return true;
    }

    /**
     * @param float $received
     * @param string $currency_code
     * @return bool
     */
    protected function validatePaymentData( $received, $currency_code )
    {
        return abs( (float) $received - (float) $this->getPayment()->getPaid() ) <= 0.01 && ( BooklyLib\Config::getCurrency() == $currency_code );
    }

    /**
     * @return float
     */
    protected function getGatewayAmount()
    {
        return $this->request->getCartInfo()->getGatewayAmount();
    }

    /**
     * @return float
     */
    protected function getGatewayTax()
    {
        return $this->request->getCartInfo()->getGatewayTax();
    }

    /**
     * Delete cascade related items
     *
     * @param Entities\Payment $payment
     * @return void
     */
    private function removeCascade( Entities\Payment $payment )
    {
        if ( $payment->getId() ) {
            if ( $payment->getTarget() === Entities\Payment::TARGET_APPOINTMENTS ) {
                foreach ( Entities\CustomerAppointment::query()->where( 'payment_id', $payment->getId() )->find() as $ca ) {
                    $ca->deleteCascade();
                }
            } elseif ( $payment->getTarget() === Entities\Payment::TARGET_PACKAGES ) {
                Payment\Proxy\Packages::deleteCascade( $payment );
            }
            $payment->delete();
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function applyCodes( array $data )
    {
        if ( $data ) {
            $codes = $this->getCodes();
            foreach ( $data as $key => $text ) {
                $data[ $key ] = is_array( $text )
                    ? $this->applyCodes( $text )
                    : Codes::replace( $text, $codes, false );
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    private function getCodes()
    {
        static $codes;
        if ( $codes === null ) {
            $codes = Components\Booking\InfoText::getCodes( Booking\Lib\Steps::PAYMENT, $this->request->getUserData() );
        }

        return $codes;
    }

}