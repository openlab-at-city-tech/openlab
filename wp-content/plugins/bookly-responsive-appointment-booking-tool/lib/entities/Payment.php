<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;
use Bookly\Lib\DataHolders\Booking as DataHolders;

class Payment extends Lib\Base\Entity
{
    const TYPE_LOCAL = 'local';
    const TYPE_FREE = 'free';
    const TYPE_PAYPAL = 'paypal';
    const TYPE_STRIPE = 'stripe';
    const TYPE_CLOUD_STRIPE = 'cloud_stripe';
    const TYPE_AUTHORIZENET = 'authorize_net';
    const TYPE_2CHECKOUT = '2checkout';
    const TYPE_PAYUBIZ = 'payu_biz';
    const TYPE_PAYULATAM = 'payu_latam';
    const TYPE_PAYSON = 'payson';
    const TYPE_MOLLIE = 'mollie';
    const TYPE_CLOUD_SQUARE = 'cloud_square';
    const TYPE_WOOCOMMERCE = 'woocommerce';
    const TYPE_CLOUD_GIFT = 'cloud_gift';

    const STATUS_COMPLETED = 'completed';
    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REFUNDED = 'refunded';

    const PAY_DEPOSIT = 'deposit';
    const PAY_IN_FULL = 'in_full';

    const ITEM_APPOINTMENT = 'appointment';
    const ITEM_PACKAGE = 'package';
    const ITEM_GIFT_CARD = 'gift_card';
    const ITEM_ADJUSTMENT = 'adjustment';

    /** @var int */
    protected $coupon_id;
    /** @var int */
    protected $gift_card_id;
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
    /** @var int */
    protected $order_id;
    /** @var string */
    protected $ref_id;
    /** @var string */
    protected $invoice_id;
    /** @var string */
    protected $created_at;
    /** @var string */
    protected $updated_at;

    /** @var Lib\DataHolders\Details\Payment */
    protected $details_data;

    protected static $table = 'bookly_payments';

    protected $loggable = true;

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'coupon_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Coupon', 'namespace' => '\BooklyCoupons\Lib\Entities', 'required' => 'bookly-addon-coupons' ) ),
        'gift_card_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'GiftCard', 'namespace' => '\BooklyPro\Lib\Entities', 'required' => 'bookly-addon-pro' ) ),
        'type' => array( 'format' => '%s' ),
        'total' => array( 'format' => '%f' ),
        'tax' => array( 'format' => '%f' ),
        'paid' => array( 'format' => '%f' ),
        'paid_type' => array( 'format' => '%s' ),
        'gateway_price_correction' => array( 'format' => '%f' ),
        'status' => array( 'format' => '%s' ),
        'token' => array( 'format' => '%s' ),
        'details' => array( 'format' => '%s' ),
        'order_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Order' ) ),
        'ref_id' => array( 'format' => '%s' ),
        'invoice_id' => array( 'format' => '%s' ),
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
        if ( $type === self::TYPE_LOCAL ) {
            $src = plugins_url( 'frontend/resources/images/wallet2.svg', Lib\Plugin::getMainFile() );
        } else {
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
     * @return Lib\DataHolders\Details\Payment
     */
    public function getDetailsData()
    {
        if ( ! $this->details_data instanceof Lib\DataHolders\Details\Payment ) {
            $details = json_decode( $this->details ?: '[]', true ) ?: array();
            $this->details_data = new Lib\DataHolders\Details\Payment( $details );
            $this->details_data->setPayment( $this );
        }

        return $this->details_data;
    }

    /**
     * Get invoice number, if not set - payment ID
     *
     * @return int
     */
    public function getInvoiceNumber()
    {
        return $this->invoice_id === null ? $this->id : $this->invoice_id;
    }

    /**
     * @param DataHolders\Order $order
     * @param Lib\CartInfo $cart_info
     * @param array $extra
     * @return $this
     */
    public function setDetailsFromOrder( DataHolders\Order $order, Lib\CartInfo $cart_info, $extra = array() )
    {
        $this->details_data = new Lib\DataHolders\Details\Payment( array(
            'gateway_ref_id' => isset( $extra['reference_id'] ) ? $extra['reference_id'] : null,
        ) );
        $this->setOrderId( $order->getOrderId() );

        $this->details_data->setOrder( $order, $cart_info );

        return $this;
    }

    /**
     * Payment data for rendering payment details and invoice.
     *
     * @return array
     */
    public function getPaymentData()
    {
        $details = $this->getDetailsData();
        $data = $details->getData();

        foreach ( $data['items'] as &$item ) {
            if ( $item['type'] === self::ITEM_APPOINTMENT && isset( $item['ca_id'] ) ) {
                $record = CustomerAppointment::query( 'ca' )
                    ->select( 'COALESCE(ca.compound_service_id, ca.collaborative_service_id, a.service_id) AS service_id, a.staff_id, s.title, st.full_name' )
                    ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
                    ->leftJoin( 'Service', 's', 's.id = COALESCE(ca.compound_service_id, ca.collaborative_service_id, a.service_id)' )
                    ->leftJoin( 'Staff', 'st', 'st.id = a.staff_id' )
                    ->where( 'ca.id', $item['ca_id'] )
                    ->fetchRow();
                if ( $record ) {
                    if ( $record['service_id'] ) {
                        $service = new Service( array( 'id' => $record['service_id'], 'title' => $record['title'] ) );
                        $item['service_name'] = $service->getTranslatedTitle();
                    }
                    if ( $record['staff_id'] ) {
                        $staff = new Staff( array( 'id' => $record['staff_id'], 'full_name' => $record['full_name'] ) );
                        $item['staff_name'] = $staff->getTranslatedName();
                    }
                }
            }
        }

        // Item discounts
        foreach ( $data['items'] as &$item ) {
            $discounts = array();
            if ( isset( $item['discounts'] ) ) {
                foreach ( $item['discounts'] as $discount ) {
                    if ( $discount['discount'] > 0 || $discount['deduction'] > 0 ) {
                        $discounts[] = $discount;
                    }
                }
            }
            $item['discounts'] = $discounts;
        }

        // Order discounts
        $discounts = array();
        if ( isset( $data['discounts'] ) ) {
            foreach ( $data['discounts'] as $discount ) {
                if ( $discount['discount'] > 0 || $discount['deduction'] > 0 ) {
                    $discounts[] = $discount;
                }
            }
        }

        return array(
            'payment' => array(
                'id' => (int) $this->id,
                'status' => $this->status,
                'type' => $this->type,
                'created_at' => $this->created_at,
                'token' => $this->token,
                'customer' => $details->getCustomerName(),
                'items' => $data['items'],
                'subtotal' => $data['subtotal'],
                'group_discount' => isset ( $data['customer_group']['discount_format'] ) ? $data['customer_group']['discount_format'] : false,
                'discounts' => $discounts,
                'coupon' => $data['coupon'],
                'gift_card' => isset( $data['gift_card'] ) ? $data['gift_card'] : null,
                'price_correction' => $this->gateway_price_correction,
                'gateway' => $this->getType(),
                'gateway_ref_id' => isset ( $data['gateway_ref_id'] ) ? $data['gateway_ref_id'] : null,
                'paid' => $this->paid,
                'tax_paid' => $data['tax_paid'],
                'total' => $this->total,
                'tax_total' => $this->tax,
                'tax_in_price' => $data['tax_in_price'],
                'from_backend' => (bool) ( isset( $data['from_backend'] ) ? $data['from_backend'] : false ),
                'extras_multiply_nop' => (bool) ( isset ( $data['extras_multiply_nop'] ) ? $data['extras_multiply_nop'] : true ),
                'tips' => (float) ( isset ( $data['tips'] ) ? $data['tips'] : 0 ),
                'invoice_number' => $this->invoice_id === null ? $this->id : $this->invoice_id,
            ),
            'adjustments' => $details->getValue( 'adjustments', array() )
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
     * @return int
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * @param int $order_id
     * @return Payment
     */
    public function setOrderId( $order_id )
    {
        $this->order_id = $order_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceId()
    {
        return $this->invoice_id;
    }

    /**
     * @param string $invoice_id
     * @return Payment
     */
    public function setInvoiceId( $invoice_id )
    {
        $this->invoice_id = $invoice_id;

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
        $pay_now = $cart_info->getGateway() === self::TYPE_LOCAL
            ? 0
            : $cart_info->getPayNow();

        if ( $pay_now > 0 ) {
            $type = $cart_info->getGateway();
        } else {
            $type = $cart_info->getSubtotal() + $cart_info->getDiscount() > 0
                ? self::TYPE_LOCAL
                : self::TYPE_FREE;
        }

        $this
            ->setType( $type )
            ->setStatus( self::STATUS_PENDING )
            ->setTotal( $cart_info->getTotal() )
            ->setPaid( $pay_now )
            ->setGatewayPriceCorrection( $cart_info->getPriceCorrection() )
            ->setPaidType( ( $cart_info->getPayFull() || $cart_info->getTotal() == $pay_now )
                ? self::PAY_IN_FULL
                : self::PAY_DEPOSIT
            )
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
            if ( Lib\Config::invoicesActive() && get_option( 'bookly_invoices_id_fill_gaps', 0 ) === '1' ) {
                $invoice_ids = self::query()->whereNot( 'invoice_id', null )->fetchCol( 'invoice_id' );
                $invoice_ids = array_fill_keys( $invoice_ids, null );
                $invoice_id = (int) get_option( 'bookly_invoices_id_start_number', 1 );
                while ( array_key_exists( $invoice_id, $invoice_ids ) ) {
                    $invoice_id++;
                }
                $this->setInvoiceId( $invoice_id );
            }
        } elseif ( $this->getModified() ) {
            $this->setUpdatedAt( current_time( 'mysql' ) );
        }
        // Generate new token if it is not set.
        if ( ! $this->getToken() ) {
            $this->setToken( Lib\Utils\Common::generateToken( get_class( $this ), 'token' ) );
        }
        if ( $this->details_data !== null ) {
            $this->details = json_encode( $this->details_data->getData() );
            // optimization
            $this->details_data = null;
        }

        return parent::save();
    }
}