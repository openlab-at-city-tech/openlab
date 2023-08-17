<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;
use Bookly\Lib\DataHolders\Booking as DataHolders;

/**
 * Class Payment
 *
 * @package Bookly\Lib\Entities
 */
class Payment extends Lib\Base\Entity
{
    const TYPE_LOCAL = 'local';
    /** @deprecated for compatibility with bookly-addon-taxes <= ver: 1.8 */
    const TYPE_COUPON       = 'free';
    const TYPE_FREE         = 'free';
    const TYPE_PAYPAL       = 'paypal';
    const TYPE_STRIPE       = 'stripe';
    const TYPE_CLOUD_STRIPE = 'cloud_stripe';
    const TYPE_AUTHORIZENET = 'authorize_net';
    const TYPE_2CHECKOUT    = '2checkout';
    const TYPE_PAYUBIZ      = 'payu_biz';
    const TYPE_PAYULATAM    = 'payu_latam';
    const TYPE_PAYSON       = 'payson';
    const TYPE_MOLLIE       = 'mollie';
    const TYPE_CLOUD_SQUARE = 'cloud_square';
    const TYPE_WOOCOMMERCE  = 'woocommerce';
    const TYPE_CLOUD_GIFT   = 'cloud_gift';

    const STATUS_COMPLETED = 'completed';
    const STATUS_PENDING   = 'pending';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_REFUNDED  = 'refunded';

    const PAY_DEPOSIT = 'deposit';
    const PAY_IN_FULL = 'in_full';

    const TARGET_APPOINTMENTS = 'appointments';
    const TARGET_PACKAGES = 'packages';
    const TARGET_GIFT_CARDS = 'gift_cards';

    /** @var int */
    protected $coupon_id;
    /** @var int */
    protected $gift_card_id;
    /** @var string */
    protected $target = self::TARGET_APPOINTMENTS;
    /** @var string */
    protected $type;
    /** @var float */
    protected $total;
    /** @var float */
    protected $tax = 0;
    /** @var float */
    protected $paid;
    /** @var float */
    protected $gateway_price_correction;
    /** @var string */
    protected $paid_type = self::PAY_IN_FULL;
    /** @var string */
    protected $status = self::STATUS_COMPLETED;
    /** @var string */
    protected $token;
    /** @var string */
    protected $details;
    /** @var string */
    protected $ref_id;
    /** @var string */
    protected $created_at;
    /** @var string */
    protected $updated_at;

    protected static $table = 'bookly_payments';

    protected $loggable = true;

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'coupon_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Coupon', 'namespace' => '\BooklyCoupons\Lib\Entities', 'required' => 'bookly-addon-coupons' ) ),
        'gift_card_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'GiftCard', 'namespace' => '\BooklyPro\Lib\Entities', 'required' => 'bookly-addon-pro' ) ),
        'target' => array( 'format' => '%s' ),
        'type' => array( 'format' => '%s' ),
        'total' => array( 'format' => '%f' ),
        'tax' => array( 'format' => '%f' ),
        'paid' => array( 'format' => '%f' ),
        'paid_type' => array( 'format' => '%s' ),
        'gateway_price_correction' => array( 'format' => '%f' ),
        'status' => array( 'format' => '%s' ),
        'token' => array( 'format' => '%s' ),
        'details' => array( 'format' => '%s' ),
        'ref_id' => array( 'format' => '%s' ),
        'created_at' => array( 'format' => '%s' ),
        'updated_at' => array( 'format' => '%s' ),
    );

    /**
     * Get display name for given payment type.
     *
     * @param string $type
     * @return string
     */
    public static function typeToString( $type )
    {
        switch ( $type ) {
            case self::TYPE_PAYPAL:
                return 'PayPal';
            case self::TYPE_LOCAL:
                return __( 'Local', 'bookly' );
            case self::TYPE_STRIPE:
                return 'Stripe';
            case self::TYPE_CLOUD_STRIPE:
                return 'Stripe Cloud';
            case self::TYPE_CLOUD_SQUARE:
                return 'Square Cloud';
            case self::TYPE_AUTHORIZENET:
                return 'Authorize.Net';
            case self::TYPE_2CHECKOUT:
                return '2Checkout';
            case self::TYPE_PAYUBIZ:
                return 'PayUbiz';
            case self::TYPE_PAYULATAM:
                return 'PayU Latam';
            case self::TYPE_PAYSON:
                return 'Payson';
            case self::TYPE_MOLLIE:
                return 'Mollie';
            case self::TYPE_FREE:
                return __( 'Free', 'bookly' );
            case self::TYPE_CLOUD_GIFT:
                return __( 'Gift card', 'bookly' );
            case self::TYPE_WOOCOMMERCE:
                return 'WooCommerce';
            default:
                return '';
        }
    }

    /**
     * Get image for given payment type.
     *
     * @param string $type
     * @return array
     */
    public static function typeToImage( $type )
    {
        $height = '50px';
        switch ( $type ) {
            case self::TYPE_LOCAL:
                $src = plugins_url( 'frontend/resources/images/wallet2.svg', Lib\Plugin::getMainFile() );
                break;
            case self::TYPE_CLOUD_GIFT:
                $src = plugins_url( 'frontend/resources/images/ticket.svg', Lib\Plugin::getMainFile() );
                break;
            case self::TYPE_CLOUD_STRIPE:
            case self::TYPE_CLOUD_SQUARE:
            default:
                $src = Lib\Proxy\Shared::preparePaymentImage( plugins_url( 'frontend/resources/images/card.svg', Lib\Plugin::getMainFile() ), $type );
        }

        return compact( 'src', 'height' );
    }

    /**
     * @param string $type
     * @return string
     */
    public static function typeToProduct( $type )
    {
        switch ( $type ) {
            case self::TYPE_CLOUD_STRIPE:
                return Lib\Cloud\Account::PRODUCT_STRIPE;
            case self::TYPE_CLOUD_SQUARE:
                return Lib\Cloud\Account::PRODUCT_SQUARE;
            case self::TYPE_CLOUD_GIFT:
                return Lib\Cloud\Account::PRODUCT_GIFT;
            default:
                return '';
        }
    }

    /**
     * Get all types
     *
     * @return string[]
     */
    public static function getTypes()
    {
        return array(
            self::TYPE_LOCAL,
            self::TYPE_2CHECKOUT,
            self::TYPE_PAYPAL,
            self::TYPE_AUTHORIZENET,
            self::TYPE_STRIPE,
            self::TYPE_CLOUD_STRIPE,
            self::TYPE_PAYUBIZ,
            self::TYPE_PAYULATAM,
            self::TYPE_PAYSON,
            self::TYPE_MOLLIE,
            self::TYPE_CLOUD_SQUARE,
            self::TYPE_FREE,
            self::TYPE_WOOCOMMERCE,
            self::TYPE_CLOUD_GIFT,
        );
    }

    /**
     * Get status of payment.
     *
     * @param string $status
     * @return string
     */
    public static function statusToString( $status )
    {
        $caption = '';
        switch ( $status ) {
            case self::STATUS_COMPLETED:
                $caption = __( 'Completed', 'bookly' );
                break;
            case self::STATUS_PENDING:
                $caption = __( 'Pending', 'bookly' );
                break;
            case self::STATUS_REJECTED:
                $caption = __( 'Rejected', 'bookly' );
                break;
            case self::STATUS_REFUNDED:
                $caption = __( 'Refunded', 'bookly' );
                break;
        }

        return $caption;
    }

    /**
     * @param DataHolders\Order $order
     * @param Lib\CartInfo $cart_info
     * @param array $extra
     * @return $this
     */
    public function setDetailsFromOrder( DataHolders\Order $order, Lib\CartInfo $cart_info, $extra = array() )
    {
        $extras_multiply_nop = (int) get_option( 'bookly_service_extras_multiply_nop', 1 );

        $details = array(
            'items' => array(),
            'coupon' => null,
            'gift_card' => null,
            'subtotal' => array( 'price' => 0, 'deposit' => 0 ),
            'customer' => $order->getCustomer()->getFullName(),
            'tax_in_price' => 'excluded',
            'tax_paid' => null,
            'extras_multiply_nop' => $extras_multiply_nop,
            'gateway_ref_id' => isset( $extra['reference_id'] ) ? $extra['reference_id'] : null,
            'tips' => $cart_info->getUserData()->getTips(),
        );

        $rates = Lib\Proxy\Taxes::getServiceTaxRates() ?: array();
        foreach ( $order->getItems() as $order_item ) {
            $items = $order_item->isSeries() ? $order_item->getItems() : array( $order_item );
            /** @var DataHolders\Item $sub_item */
            foreach ( $items as $sub_item ) {
                if ( $sub_item->getCA()->getPaymentId() != $this->getId() ) {
                    // Skip items not related to this payment (e.g. series items with no associated payment).
                    continue;
                }
                $extras = array();
                $extras_price = 0;
                $sub_items = array();
                if ( $sub_item->isCollaborative() || $sub_item->isCompound() ) {
                    foreach ( $sub_item->getItems() as $si ) {
                        $sub_items[] = $si;
                    }
                } else {
                    $sub_items[] = $sub_item;
                }
                foreach ( $sub_items as $item ) {
                    if ( $item->getCA()->getExtras() != '[]' ) {
                        $_extras = json_decode( $item->getCA()->getExtras(), true );
                        $service_id = $item->getService()->getId();
                        $rate = array_key_exists( $service_id, $rates ) ? $rates[ $service_id ] : 0;
                        /** @var \BooklyServiceExtras\Lib\Entities\ServiceExtra $service_extra */
                        foreach ( Lib\Proxy\ServiceExtras::findByIds( array_keys( $_extras ) ) ?: array() as $service_extra ) {
                            $quantity = (int) $_extras[ $service_extra->getId() ];
                            $extras_amount = $service_extra->getPrice() * $quantity;
                            if ( $extras_multiply_nop ) {
                                $extras_amount *= $item->getCA()->getNumberOfPersons();
                            }
                            $extras[] = array(
                                'title' => $service_extra->getTitle(),
                                'price' => $service_extra->getPrice(),
                                'quantity' => $quantity,
                                'tax' => Lib\Config::taxesActive()
                                    ? Lib\Proxy\Taxes::calculateTax( $extras_amount, $rate )
                                    : null,
                            );
                            $extras_price += $service_extra->getPrice() * $quantity;
                        }
                    }
                }

                $wait_listed = $sub_item->getCA()->getStatus() == CustomerAppointment::STATUS_WAITLISTED;

                $deposit_format = null;
                if ( ! $wait_listed ) {
                    $price = $sub_item->getServicePrice() * $sub_item->getCA()->getNumberOfPersons();
                    $price += Lib\Proxy\Discounts::prepareServicePrice( $extras_multiply_nop ? $extras_price * $sub_item->getCA()->getNumberOfPersons() : $extras_price, $sub_item->getService()->getId(), $sub_item->getCA()->getNumberOfPersons() );

                    $details['subtotal']['price'] += $price;
                    if ( Lib\Config::depositPaymentsActive() ) {
                        $deposit_price = Lib\Proxy\DepositPayments::prepareAmount( $price, $sub_item->getDeposit(), $sub_item->getCA()->getNumberOfPersons() );
                        $deposit_format = Lib\Proxy\DepositPayments::formatDeposit( $deposit_price, $sub_item->getDeposit() );
                        $details['subtotal']['deposit'] += $deposit_price;
                    }
                }

                $details['items'][] = Lib\Proxy\Shared::preparePaymentDetailsItem(
                    array(
                        'ca_id' => $sub_item->getCA()->getId(),
                        'appointment_date' => $sub_item->getAppointment()->getStartDate(),
                        'app_start_info' => $sub_item->getService()->getDuration() >= DAY_IN_SECONDS ? $sub_item->getService()->getStartTimeInfo() : null,
                        'service_name' => $sub_item->getService()->getTitle(),
                        'service_price' => $sub_item->getServicePrice(),
                        'service_tax' => $wait_listed ? null : $sub_item->getServiceTax(),
                        'wait_listed' => $wait_listed,
                        'deposit_format' => $deposit_format,
                        'number_of_persons' => $sub_item->getCA()->getNumberOfPersons(),
                        'units' => $sub_item->getCA()->getUnits(),
                        'duration' => $sub_item->getService()->getDuration(),
                        'staff_name' => $sub_item->getStaff()->getFullName(),
                        'extras' => $extras,
                    ),
                    $sub_item
                );
            }
        }

        $details = Lib\Proxy\Shared::preparePaymentDetails( $details, $order, $cart_info );

        if ( $cart_info->getCoupon() ) {
            $this->coupon_id = $cart_info->getCoupon()->getId();
        }
        if ( $cart_info->getGiftCard() ) {
            $this->gift_card_id = $cart_info->getGiftCard()->getId();
        }

        $this->details = json_encode( $details );

        return $this;
    }

    /**
     * Payment data for rendering payment details and invoice.
     *
     * @return array
     */
    public function getPaymentData()
    {
        $details = json_decode( $this->getDetails(), true );

        $customer = $details['customer'];
        if ( $this->target === self::TARGET_APPOINTMENTS ) {
            $customer = Lib\Entities\Customer::query( 'c' )
                ->select( 'c.full_name' )
                ->leftJoin( 'CustomerAppointment', 'ca', 'ca.customer_id = c.id' )
                ->where( 'ca.payment_id', $this->getId() )
                ->fetchRow();
            $customer = empty( $customer ) ? $details['customer'] : $customer['full_name'];
        } elseif ( isset( $details['customer_id'] ) ) {
            $customer = Lib\Entities\Customer::find( $details['customer_id'] );
            $customer = $customer ? $customer->getFullName() : $details['customer'];
        }

        if ( $this->target === self::TARGET_APPOINTMENTS ) {
            foreach ( $details['items'] as &$item ) {
                if ( isset( $item['ca_id'] ) ) {
                    $data = CustomerAppointment::query( 'ca' )
                        ->select( 'COALESCE(ca.compound_service_id, ca.collaborative_service_id, a.service_id) AS service_id, a.staff_id, s.title, st.full_name' )
                        ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
                        ->leftJoin( 'Service', 's', 's.id = COALESCE(ca.compound_service_id, ca.collaborative_service_id, a.service_id)' )
                        ->leftJoin( 'Staff', 'st', 'st.id = a.staff_id' )
                        ->where( 'ca.id', $item['ca_id'] )
                        ->fetchRow();
                    if ( $data ) {
                        if ( $data['service_id'] ) {
                            $service = new Service( array( 'id' => $data['service_id'], 'title' => $data['title'] ) );
                            $item['service_name'] = $service->getTranslatedTitle();
                        }
                        if ( $data['staff_id'] ) {
                            $staff = new Staff( array( 'id' => $data['staff_id'], 'full_name' => $data['full_name'] ) );
                            $item['staff_name'] = $staff->getTranslatedName();
                        }
                    }
                }
            }
        } else {
            $details = Proxy\Shared::preparePaymentDetails( $details, $this );
        }

        return array(
            'payment' => array(
                'id' => (int) $this->id,
                'target' => $this->target,
                'status' => $this->status,
                'type' => $this->type,
                'created_at' => $this->created_at,
                'token' => $this->token,
                'customer' => $customer,
                'items' => $details['items'],
                'subtotal' => $details['subtotal'],
                'group_discount' => isset ( $details['customer_group']['discount_format'] ) ? $details['customer_group']['discount_format'] : false,
                'discounts' => isset ( $details['discounts'] ) ? $details['discounts'] : array(),
                'coupon' => $details['coupon'],
                'gift_card' => isset( $details['gift_card'] ) ? $details['gift_card'] : null,
                'price_correction' => $this->gateway_price_correction,
                'gateway' => $this->getType(),
                'gateway_ref_id' => isset ( $details['gateway_ref_id'] ) ? $details['gateway_ref_id'] : null,
                'paid' => $this->paid,
                'tax_paid' => $details['tax_paid'],
                'total' => $this->total,
                'tax_total' => $this->tax,
                'tax_in_price' => $details['tax_in_price'],
                'from_backend' => (bool) ( isset( $details['from_backend'] ) ? $details['from_backend'] : false ),
                'extras_multiply_nop' => (bool) ( isset ( $details['extras_multiply_nop'] ) ? $details['extras_multiply_nop'] : true ),
                'tips' => (float) ( isset ( $details['tips'] ) ? $details['tips'] : 0 ),
            ),
            'adjustments' => isset( $details['adjustments'] ) ? $details['adjustments'] : array(),
        );
    }

    /**
     * Get HTML for payment info displayed in a popover in the edit appointment form
     *
     * @param float $paid
     * @param float $total
     * @param string $type
     * @param string $status
     * @return string
     */
    public static function paymentInfo( $paid, $total, $type, $status )
    {
        $result = Lib\Utils\Price::format( $paid );
        if ( $paid != $total ) {
            $result = sprintf( __( '%s of %s', 'bookly' ), $result, Lib\Utils\Price::format( $total ) );
        }
        $result .= sprintf(
            ' %s <span%s>%s</span>',
            self::typeToString( $type ),
            $status == self::STATUS_PENDING ? ' class="text-danger"' : '',
            self::statusToString( $status )
        );

        return $result;
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets coupon_id
     *
     * @return int
     */
    public function getCouponId()
    {
        return $this->coupon_id;
    }

    /**
     * Sets coupon_id
     *
     * @param int $coupon_id
     * @return $this
     */
    public function setCouponId( $coupon_id )
    {
        $this->coupon_id = $coupon_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getGiftCardId()
    {
        return $this->gift_card_id;
    }

    /**
     * @param int $gift_card_id
     * @return Payment
     */
    public function setGiftCardId( $gift_card_id )
    {
        $this->gift_card_id = $gift_card_id;

        return $this;
    }

    /**
     * Gets target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Sets target
     *
     * @param string $target
     * @return $this
     */
    public function setTarget( $target )
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Gets type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets type
     *
     * @param string $type
     * @return $this
     */
    public function setType( $type )
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Sets total
     *
     * @param float $total
     * @return $this
     */
    public function setTotal( $total )
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Gets tax
     *
     * @return float
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Sets tax
     *
     * @param float $tax
     * @return $this
     */
    public function setTax( $tax )
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Gets paid
     *
     * @return float
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Sets paid
     *
     * @param float $paid
     * @return $this
     */
    public function setPaid( $paid )
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Gets fee
     *
     * @return float
     */
    public function getGatewayPriceCorrection()
    {
        return $this->gateway_price_correction;
    }

    /**
     * Sets fee
     *
     * @param float $gateway_price_correction
     * @return $this
     */
    public function setGatewayPriceCorrection( $gateway_price_correction )
    {
        $this->gateway_price_correction = $gateway_price_correction;

        return $this;
    }

    /**
     * Gets paid_type
     *
     * @return string
     */
    public function getPaidType()
    {
        return $this->paid_type;
    }

    /**
     * Sets paid_type
     *
     * @param string $paid_type
     * @return $this
     */
    public function setPaidType( $paid_type )
    {
        $this->paid_type = $paid_type;

        return $this;
    }

    /**
     * Gets status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus( $status )
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Gets token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets token
     *
     * @param string $token
     * @return $this
     */
    public function setToken( $token )
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Gets details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Sets details
     *
     * @param string $details
     * @return $this
     */
    public function setDetails( $details )
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Gets ref_id
     *
     * @return string
     */
    public function getRefId()
    {
        return $this->ref_id;
    }

    /**
     * Sets ref_id
     *
     * @param string $ref_id
     * @return $this
     */
    public function setRefId( $ref_id )
    {
        $this->ref_id = $ref_id;

        return $this;
    }

    /**
     * Gets created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Sets created_at
     *
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt( $created_at )
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @param Lib\CartInfo $cart_info
     * @return $this
     */
    public function setCartInfo( Lib\CartInfo $cart_info )
    {
        $this
            ->setTotal( $cart_info->getTotal() )
            ->setPaid( $cart_info->getPayNow() )
            ->setGatewayPriceCorrection( $cart_info->getPriceCorrection() )
            ->setPaidType( $cart_info->getTotal() == $cart_info->getPayNow() ? self::PAY_IN_FULL : self::PAY_DEPOSIT )
            ->setTax( $cart_info->getTotalTax() );

        return $this;
    }

    /**
     * Gets updated_at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Sets updated_at
     *
     * @param string $updated_at
     * @return $this
     */
    public function setUpdatedAt( $updated_at )
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    public function save()
    {
        if ( $this->getId() === null ) {
            $this
                ->setCreatedAt( current_time( 'mysql' ) )
                ->setUpdatedAt( current_time( 'mysql' ) );
        } elseif ( $this->getModified() ) {
            $this->setUpdatedAt( current_time( 'mysql' ) );
        }
        // Generate new token if it is not set.
        if ( ! $this->getToken() ) {
            $this->setToken( Lib\Utils\Common::generateToken( get_class( $this ), 'token' ) );
        }

        return parent::save();
    }
}