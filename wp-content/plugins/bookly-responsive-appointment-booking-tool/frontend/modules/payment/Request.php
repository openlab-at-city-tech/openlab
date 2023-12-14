<?php
namespace Bookly\Frontend\Modules\Payment;

use Bookly\Frontend\Modules\Booking\Proxy as BookingProxy;
use Bookly\Lib;
use Bookly\Lib\Entities;
use Bookly\Lib\CartItem;
use Bookly\Lib\Payment;
use BooklyPro\Backend\Modules\Appearance;

class Request extends Lib\Base\Component
{
    const BOOKING_STATUS_COMPLETED = 'completed';
    const BOOKING_STATUS_GROUP_SKIP_PAYMENT = 'group_skip_payment';
    const BOOKING_STATUS_PAYMENT_IMPOSSIBLE = 'payment_impossible';
    const BOOKING_STATUS_APPOINTMENTS_LIMIT_REACHED = 'appointments_limit_reached';

    /** @var array */
    protected $customer = array();
    /** @var Lib\UserBookingData */
    protected $userData;
    /** @var Lib\CartInfo */
    protected $cart_info;
    /** @var string */
    protected $type; // appointment, package, gift_card
    /** @var string */
    protected $gateway_name;
    /** @var Lib\Base\Gateway */
    protected $gateway;

    protected function __construct()
    {
    }

    /**
     * @return Request
     */
    public static function getInstance()
    {
        if ( ! self::hasInCache( __FUNCTION__ ) ) {
            self::putInCache( __FUNCTION__, new static() );
        }

        return self::getFromCache( __FUNCTION__ );
    }

    /**
     * Get staff id
     *
     * @return int
     */
    public function getStaffId()
    {
        return self::parameter( 'staff_id' );
    }

    /**
     * Get location id
     *
     * @return int
     */
    public function getLocationId()
    {
        return self::parameter( 'location_id' );
    }

    /**
     * Get service id
     *
     * @return int
     */
    public function getServiceId()
    {
        return self::parameter( 'service_id' );
    }

    /**
     * Get customer data
     *
     * @return array
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Get form ID
     *
     * @return array
     */
    public function getFormId()
    {
        return self::parameter( 'form_id' );
    }

    /**
     * @return bool
     */
    public function isBookingForm()
    {
        return ( $this->get( 'form_slug' ) === 'booking-form' ) || $this->get( 'bookly_fid' );
    }

    /**
     * @return Lib\UserBookingData
     */
    public function getUserData()
    {
        if ( $this->userData === null ) {
            if ( $this->isBookingForm() ) {
                $this->userData = new Lib\UserBookingData( $this->getFormId() ?: $this->get( 'bookly_fid' ) );
                $this->userData->load();
            } else {
                $this->userData = new Lib\UserBookingData( null );
                $customer = $this->get( 'customer' );
                if ( $customer ) {
                    $service_id = $this->getServiceId();
                    $staff_id = $this->getStaffId();
                    $location_id = $this->getLocationId() ?: null;
                    $nop = self::parameter( 'nop' );
                    $units = self::parameter( 'units', 1 );
                    $extras = self::parameter( 'extras', array() );
                    $coupon = self::parameter( 'coupon' );
                    $gift_card = self::parameter( 'gift_card' );
                    foreach ( array_keys( $extras, 0, false ) as $key ) {
                        unset( $extras[ $key ] );
                    }

                    $this->userData
                        ->setCouponCode( $coupon )
                        ->setGiftCode( $gift_card )
                        ->setFullAddress( isset( $customer['full_address'] ) && $customer['full_address'] !== '' ? $customer['full_address'] : null )
                        ->setModernFormCustomer( $customer );

                    $client_fields = array();
                    if ( in_array( 'address', $this->getSettings()->get( 'details_fields_show' ) ) ) {
                        $address = $this->getSettings()->get( 'address' );
                        if ( isset( $address['show'] ) ) {
                            $client_fields = array_merge( $client_fields, $address['show'] );
                        }
                    }
                    if ( in_array( 'notes', $this->getSettings()->get( 'details_fields_show' ) ) ) {
                        $client_fields[] = 'notes';
                    }
                    foreach ( $client_fields as $field ) {
                        if ( array_key_exists( $field, $customer ) ) {
                            $this->userData->fillData( array( $field => $customer[ $field ] ) );
                        }
                    }

                    $cart_item = new CartItem();
                    $cart_item->setType( $this->get( 'type' ) );

                    switch ( $cart_item->getType() ) {
                        case CartItem::TYPE_APPOINTMENT:
                        case CartItem::TYPE_PACKAGE:
                            $slot = $this->get( 'type' ) === CartItem::TYPE_APPOINTMENT
                                ? self::parameter( 'slot' )
                                : array( 'value' => sprintf( '[[%d,%d,null,%s]]', $service_id, $staff_id, $location_id ?: 'null' ) );
                            $slots = json_decode( $slot['value'], true );

                            // Validate ?
                            /** @todo */
                            $custom_fields = array_map( function ( $id, $value ) {
                                return compact( 'id', 'value' );
                            }, array_keys( self::parameter( 'custom_fields', array() ) ), self::parameter( 'custom_fields', array() ) );

                            $cart_item
                                ->setStaffIds( array( $staff_id ) )
                                ->setServiceId( $service_id )
                                ->setNumberOfPersons( $nop )
                                ->setLocationId( $location_id )
                                ->setUnits( $units )
                                ->setExtras( $extras )
                                ->setCustomFields( $custom_fields )
                                ->setSlots( $slots );
                            break;
                        default:
                            $cart_item = Payment\Proxy\Shared::prepareCartItem( $cart_item, $this );
                            break;
                    }

                    $this->userData->setSlots( $cart_item->getSlots() );
                    $this->userData->cart->add( $cart_item );
                }
            }
        }

        return $this->userData;
    }

    /**
     * @return Lib\Base\Gateway
     * @throws \Exception
     */
    public function getGateway()
    {
        if ( $this->gateway === null ) {
            if ( Lib\Config::paymentStepDisabled() || BookingProxy\CustomerGroups::getSkipPayment( $this->getUserData()->getCustomer() ) ) {
                $this->gateway = new Payment\NullGateway( $this );
            } else {
                $gateway = $this->getGatewayName();
                if ( $gateway === null ) {
                    $payment = null;
                    if ( $this->get( 'bookly_order' ) ) {
                        /** @var Entities\Payment $payment */
                        $payment = Entities\Payment::query( 'p' )
                            ->leftJoin( 'Order', 'o', 'o.id = p.order_id' )
                            ->where( 'o.token', $this->get( 'bookly_order' ) )
                            ->findOne();
                    }
                    if ( $payment ) {
                        $this->gateway = $this->getGatewayByName( $payment->getType() );
                        $this->gateway->setPayment( $payment );
                    } else {
                        $this->gateway = new Payment\ZeroGateway( $this );
                        if ( $this->getCartInfo()->getPayNow() > 0 ) {
                            throw new \Exception( __( 'Incorrect payment data', 'bookly' ) );
                        }
                    }
                } else {
                    $this->gateway = $this->getGatewayByName( $gateway );
                    if ( ( $this->gateway->getType() === Entities\Payment::TYPE_FREE ) && $this->getCartInfo()->getPayNow() > 0 ) {
                        throw new \Exception( __( 'Incorrect payment data', 'bookly' ) );
                    }
                }
            }
        }

        return $this->gateway;
    }

    /**
     * @return Lib\CartInfo
     */
    public function getCartInfo()
    {
        if ( $this->cart_info === null ) {
            $this->cart_info = $this->getUserData()->cart->getInfo( $this->getGateway()->getType() );
        }

        return $this->cart_info;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get( $key, $default = null )
    {
        static $request;
        if ( $request === null ) {
            $request = self::getRequest();
        }

        return $request->get( $key, $default );
    }

    /**
     * @param string $gateway
     * @return void
     */
    public function setGatewayName( $gateway )
    {
        $this->gateway_name = $gateway;
    }

    /**
     * Get payment system
     *
     * @return string
     */
    protected function getGatewayName()
    {
        return $this->gateway_name ?: self::parameter( 'gateway' );
    }

    /**
     * @return Lib\Utils\Collection
     */
    protected function getSettings()
    {
        static $settings;
        if ( $settings === null ) {
            $settings = new Lib\Utils\Collection( Appearance\ProxyProviders\Local::getAppearance( self::parameter( 'form_type' ), $this->get( 'form_slug' ) ) );
        }

        return $settings;
    }

    /**
     * @param string $gateway
     * @return Lib\Base\Gateway
     */
    protected function getGatewayByName( $gateway )
    {
        switch ( $gateway ) {
            case Entities\Payment::TYPE_CLOUD_STRIPE:
                return new Payment\StripeCloudGateway( $this );
            case Entities\Payment::TYPE_LOCAL:
                return new Payment\LocalGateway( $this );
            case Entities\Payment::TYPE_FREE:
                return new Payment\ZeroGateway( $this );
            default:
                return Payment\Proxy\Shared::getGatewayByName( $gateway, $this );
        }
    }
}