<?php
namespace Bookly\Frontend\Modules\Payment;

use Bookly\Frontend\Modules\Booking\Proxy as BookingProxy;
use Bookly\Lib;
use Bookly\Lib\Entities;
use Bookly\Lib\CartItem;
use BooklyPro\Backend\Modules\Appearance;

class Request extends Lib\Base\Component
{
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
        return ! $this->get( 'modern_booking_form' ) || $this->get( 'bookly_fid' );
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
                $customer = $this->get( 'customer', array() );
                if ( $customer ) {
                    $coupon = self::parameter( 'coupon' );
                    $gift_card = self::parameter( 'gift_card' );

                    $this->userData
                        ->setCouponCode( $coupon )
                        ->setGiftCode( $gift_card )
                        ->setModernFormCustomer( $customer, $this->getSettings() );

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
                    if ( in_array( 'birthday', $this->getSettings()->get( 'details_fields_show' ) ) ) {
                        $client_fields[] = 'birthday';
                    }
                    foreach ( $client_fields as $field ) {
                        if ( array_key_exists( $field, $customer ) ) {
                            $this->userData->fillData( array( $field => $customer[ $field ] ) );
                        }
                    }

                    // Deposit
                    if ( Lib\Config::depositPaymentsActive() && get_option( 'bookly_deposit_allow_full_payment', '0' ) !== '0' ) {
                        $this->userData->setDepositFull( ! self::parameter( 'deposit' ) );
                    }

                    $bookly_recurring_appointments_payment = get_option( 'bookly_recurring_appointments_payment' ) === 'first';
                    $processed_series = array( 0 );
                    $slots = array();
                    foreach ( self::parameter( 'cart' ) as $item ) {
                        $service_id = $item['service_id'];
                        $staff_id = $item['staff_id'];
                        $location_id = $item['location_id'];
                        $nop = isset( $item['nop'] ) ? $item['nop'] : 1;
                        $units = isset( $item['units'] ) ? $item['units'] : 1;
                        $extras = isset( $item['extras'] ) ? $item['extras'] : array();
                        foreach ( array_keys( $extras, 0, false ) as $key ) {
                            unset( $extras[ $key ] );
                        }

                        $cart_item = new CartItem();
                        $cart_item->setType( $item['type'] );

                        switch ( $cart_item->getType() ) {
                            case CartItem::TYPE_APPOINTMENT:
                            case CartItem::TYPE_PACKAGE:
                                $slot = $item['type'] === CartItem::TYPE_APPOINTMENT
                                    ? $item['slot']['slot']
                                    : array( array( $service_id, $staff_id, null, $location_id ?: null ) );
                                $slots[] = $slot;
                                $series_id = isset( $item['seriesId'] ) ? $item['seriesId'] : 0;
                                $first_in_series = false;
                                if ( $bookly_recurring_appointments_payment && isset( $item['seriesId'] ) && ! in_array( $series_id, $processed_series ) ) {
                                    $processed_series[] = $series_id;
                                    $first_in_series = true;
                                }

                                $custom_fields = isset( $item['custom_fields'] ) ? $item['custom_fields'] : array();
                                $custom_fields = array_map( function( $id, $value ) {
                                    return compact( 'id', 'value' );
                                }, array_keys( $custom_fields ), $custom_fields );

                                $cart_item
                                    ->setStaffIds( is_array( $staff_id ) ? $staff_id : array( $staff_id ) )
                                    ->setServiceId( $service_id )
                                    ->setNumberOfPersons( $nop )
                                    ->setLocationId( $location_id )
                                    ->setUnits( $units )
                                    ->setExtras( $extras )
                                    ->setCustomFields( $custom_fields )
                                    ->setSeriesUniqueId( $series_id )
                                    ->setSlots( $slot )
                                    ->setFirstInSeries( $first_in_series );
                                break;
                            default:
                                $cart_item->setCartTypeId( $item['gift_card_type'] );
                                break;
                        }

                        $this->userData->cart->add( $cart_item );
                    }
                    $this->userData->setSlots( $slots );
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
                $this->gateway = new Lib\Payment\NullGateway( $this );
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
                    } elseif ( Entities\Order::query()->where( 'token', $this->get( 'bookly_order' ) )->fetchVar( 'id' ) !== null ) {
                        $this->gateway = new Lib\Payment\ZeroGateway( $this );
                        if ( $this->getCartInfo()->getPayNow() > 0 ) {
                            throw new \Exception( __( 'Incorrect payment data', 'bookly' ) );
                        }
                    } elseif ( Lib\Config::wooCommerceEnabled() ) {
                        $this->gateway = new Lib\Payment\ZeroGateway( $this );
                    } else {
                        throw new \Exception( 'There is no order, the payment may have been canceled by webhook' );
                    }
                } else {
                    $this->gateway = $this->getGatewayByName( $gateway );
                    $ci = $this->getCartInfo();
                    if ( $ci->getPayNow() > 0 ) {
                        if ( $this->gateway->getType() === Entities\Payment::TYPE_FREE ) {
                            throw new \Exception( __( 'Incorrect payment data', 'bookly' ) );
                        }
                    } elseif ( ( $ci->getSubtotal() + $ci->getDiscount() ) > 0 ) {
                        $this->gateway = new Lib\Payment\LocalGateway( $this );
                    } else {
                        // Coupon, Gift Card or Discounts make free service
                        $this->gateway = new Lib\Payment\ZeroGateway( $this );
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
                return new Lib\Payment\StripeCloudGateway( $this );
            case Entities\Payment::TYPE_LOCAL:
                return new Lib\Payment\LocalGateway( $this );
            case Entities\Payment::TYPE_FREE:
                return new Lib\Payment\ZeroGateway( $this );
            default:
                return Lib\Payment\Proxy\Shared::getGatewayByName( $gateway, $this );
        }
    }
}