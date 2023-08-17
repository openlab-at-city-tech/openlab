<?php
namespace Bookly\Lib;

use Bookly\Frontend\Modules\Booking\Proxy as BookingProxy;
use Bookly\Lib\Entities\Payment;

/**
 * Class CartInfo
 *
 * @package Bookly\Lib\Booking
 */
class CartInfo
{
    /** @var UserBookingData $userData */
    protected $userData;
    /** @var float  cost of services based on deposit value (without services in waiting list) */
    protected $deposit = 0;
    /** @var float  cost of services included in waiting list */
    protected $waiting_list_total = 0;
    /** @var float  cost of services based on deposit value included in waiting list */
    protected $waiting_list_deposit = 0;
    /** @var float  cost of services before discounts */
    protected $subtotal = 0;
    /** @var float  amount of discount based on the discount addon */
    protected $addon_discount = 0;
    /** @var float  amount of discount based on the customer group */
    protected $group_discount = 0;
    /** @var float  amount of discount for applied coupon */
    protected $coupon_discount = 0;
    /** @var float  amount of discount for applied gift */
    protected $gift_discount = 0;
    /** @var float  cost of services including coupon and group discount */
    protected $total = 0;
    /** @var array [['rate' =>float, 'deposit' => float, 'total' => float, 'allow_coupon' => bool]]
     * data for each service provided for consequent calculation of tax amount (without services in waiting list)*/
    protected $amounts_taxable = array();
    /** @var float  amount of discount/extra charge for payment gateway */
    protected $price_correction = 0;
    /** @var float  indicates whether there's a need to send tax info to payment gateway */
    protected $gateway_send_tax = false;
    /** @var float  indicates the method of calculating the value that is sent as tax to payment system */
    protected $gateway_tax_calculation_rule;

    /** @var float  tax amount for partial payment for services */
    private $deposit_tax;
    /** @var float  total tax amount */
    private $total_tax;
    /** @var string payment system name */
    private $gateway;

    /** @var \BooklyCoupons\Lib\Entities\Coupon|null */
    private $coupon;
    /** @var \BooklyPro\Lib\Entities\GiftCard|null */
    private $gift_card;
    /** @var bool */
    private $tax_included = true;
    /** @var bool */
    private $pay_full;

    /**
     * CartInfo constructor.
     *
     * @param bool $apply_discounts apply coupon/gift card
     * @param UserBookingData $userData
     */
    public function __construct( UserBookingData $userData, $apply_discounts )
    {
        $this->userData = $userData;

        if ( $apply_discounts ) {
            $this->coupon = $userData->getCoupon();
            $this->gift_card = $userData->getGiftCard();
        }

        if ( Config::taxesActive() ) {
            $this->tax_included = get_option( 'bookly_taxes_in_price' ) != 'excluded';
        } else {
            $this->total_tax = $this->deposit_tax = 0;
        }
        // Customer's preference to pay part or full cost of services
        $this->pay_full = $userData->getDepositFull();

        $coupon_total = 0;
        foreach ( $userData->cart->getItems() as $key => $item ) {
            if (
                $item->getSeriesUniqueId()
                && get_option( 'bookly_recurring_appointments_payment' ) === 'first'
                && ( ! $item->getFirstInSeries() )
            ) {
                continue;
            }

            // Cart contains a service that was already removed/deleted from Bookly (WooCommerce)
            if ( $item->getService() ) {
                $item_price = $item->getServicePrice( $item->getNumberOfPersons() );
                if ( Config::waitingListActive() && get_option( 'bookly_waiting_list_enabled' ) && $item->toBePutOnWaitingList() ) {
                    $this->waiting_list_total += $item_price;
                    $this->waiting_list_deposit += Proxy\DepositPayments::prepareAmount( $item_price, $item->getDeposit(), $item->getNumberOfPersons() );
                } else {
                    $allow_coupon = false;
                    if ( $this->coupon && $this->coupon->validForCartItem( $item ) ) {
                        $coupon_total += $item_price;
                        $allow_coupon = true;
                    }
                    $this->subtotal += $item_price;
                    $this->deposit += Proxy\DepositPayments::prepareAmount( $item_price, $item->getDeposit(), $item->getNumberOfPersons() );
                    $this->amounts_taxable = Proxy\Taxes::prepareTaxRateAmounts( $this->amounts_taxable, $item, $allow_coupon );
                }
            }
        }

        $this->total = $this->subtotal;

        // Discounts order
        // 1.Coupon
        // 2.Addon Discounts
        // 3.Group Discounts
        // 4.Payment System

        if ( $this->coupon ) {
            $this->coupon_discount = $this->coupon->apply( $coupon_total ) - $coupon_total;
            $this->total += $this->coupon_discount;
        }

        $total_without_discount = $this->total;
        $this->total = BookingProxy\Discounts::prepareCartTotalPrice( $total_without_discount, $userData );
        $this->addon_discount = $this->total - $total_without_discount;

        $total_without_group_discount = $this->total;
        $this->total = BookingProxy\CustomerGroups::prepareCartTotalPrice( $total_without_group_discount, $userData );
        $this->group_discount = $this->total - $total_without_group_discount;
    }

    /**
     * Gets coupon.
     *
     * @return \BooklyCoupons\Lib\Entities\Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * Gets gift card.
     *
     * @return \BooklyPro\Lib\Entities\GiftCard
     */
    public function getGiftCard()
    {
        return $this->gift_card;
    }

    /**
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @return float
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * @return float
     */
    public function getDue()
    {
        if ( Config::depositPaymentsActive() && ! $this->pay_full ) {
            return $this->getTotal() - $this->getDepositPay();
        }

        return 0;
    }

    /**
     * Get cost of services included in waiting list.
     *
     * @return float
     */
    public function getWaitingListTotal()
    {
        return $this->waiting_list_total;
    }

    /**
     * Gets cost of services based on deposit value included in waiting list.
     *
     * @return float
     */
    public function getWaitingListDeposit()
    {
        return $this->waiting_list_deposit;
    }

    /**
     * Set payment gateway for discount/extra charge.
     *
     * @param string $gateway
     * @return $this
     */
    public function setGateway( $gateway )
    {
        $this->price_correction = 0;
        $amount = $this->getPayNow();
        if ( $amount > 0 ) {
            $this->gateway = $gateway;
            $increase = (float) get_option( 'bookly_' . $gateway . '_increase' );
            $addition = (float) get_option( 'bookly_' . $gateway . '_addition' );
            $this->price_correction = Utils\Price::correction( $amount, -$increase, -$addition ) - $amount;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * Get amount of discount/extra charge for payment gateway.
     *
     * @return float
     */
    public function getPriceCorrection()
    {
        return $this->price_correction;
    }

    /**
     * Set method of calculating the value that is sent as tax to payment system.
     *
     * @param string $calculate_rule ['tax_increases_the_cost','tax_in_the_price']
     */
    public function setGatewayTaxCalculationRule( $calculate_rule )
    {
        $this->gateway_tax_calculation_rule = $calculate_rule;
        if ( $this->gateway ) {
            $this->gateway_send_tax = get_option( 'bookly_' . $this->gateway . '_send_tax' );
        }
    }

    /**
     * Get amount for payment system.
     *
     * @return float
     */
    public function getGatewayAmount()
    {
        $cost = $this->pay_full
            ? $this->total
            : $this->deposit;
        $cost = $this->gift_card ? max( 0, $cost - $this->gift_card->getBalance() ) : $cost;
        switch ( $this->gateway_tax_calculation_rule ) {
            case 'tax_increases_the_cost':
                if ( $this->gateway_send_tax ) {
                    if ( $this->tax_included ) {
                        if ( $cost < $this->total ) {
                            $amount = $cost - $this->getDepositTax();
                        } else {
                            $amount = $this->total - $this->getTotalTax();
                        }
                    } else {
                        $amount = min( $cost, $this->total );
                    }

                    return $amount + $this->price_correction + $this->userData->getTips();
                }

                return $this->getPayNow();
            case 'tax_in_the_price':
                return $this->getPayNow();
        }

        return $this->getPayNow();
    }

    /**
     * Get amount of tax for payment system.
     *
     * @return float|int
     */
    public function getGatewayTax()
    {
        switch ( $this->gateway_tax_calculation_rule ) {
            case 'tax_in_the_price':
            case 'tax_increases_the_cost':
                return $this->gateway_send_tax
                    ? $this->getPayTax()
                    : 0;
        }

        return 0;
    }

    /**
     * Get amount of discount based on the customer group.
     *
     * @return float
     */
    public function getGroupDiscount()
    {
        return $this->group_discount;
    }

    /**
     * Get amount of discount based on the discount addon.
     *
     * @return float
     */
    public function getAddonDiscount()
    {
        return $this->addon_discount;
    }

    /**
     * @return bool
     */
    public function withDiscount()
    {
        return ( $this->coupon_discount + $this->group_discount + $this->price_correction + $this->addon_discount + ( $this->gift_card ? 1 : 0 ) ) < 0;
    }

    /**
     * @return UserBookingData
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**************************************************************************
     * Private                                                                *
     **************************************************************************/

    /**
     * @return float
     */
    private function getDiscount()
    {
        return $this->coupon_discount + $this->group_discount + $this->price_correction + $this->addon_discount;
    }

    /**************************************************************************
     * Amounts dependent on taxes                                             *
     **************************************************************************/

    /**
     * Get paying amount without gift card.
     *
     * @return float
     */
    public function getPayNowWithoutGiftCard()
    {
        return $this->pay_full || $this->gateway === Payment::TYPE_LOCAL
            ? $this->getTotal( false )
            : min( $this->getDepositPay( false ), $this->getTotal( false ) );
    }

    /**
     * Get gift card amount.
     *
     * @return float
     */
    public function getGiftCardAmount()
    {
        return $this->gift_card ? ( $this->userData->getGiftCardAmount() ?: min( $this->getPayNowWithoutGiftCard(), $this->gift_card->getBalance() ) ) : 0;
    }

    /**
     * Get paying amount.
     *
     * @return float
     */
    public function getPayNow()
    {
        return $this->getPayNowWithoutGiftCard() - $this->getGiftCardAmount();
    }

    /**
     * Get paying tax amount.
     *
     * @return mixed
     */
    public function getPayTax()
    {
        return $this->pay_full
            ? $this->getTotalTax()
            : min( $this->getDepositTax(), $this->getTotalTax() );
    }

    /**
     * Get total price.
     *
     * @return float
     */
    public function getTotal( $with_gift_card = true )
    {
        if ( $this->tax_included ) {
            $total = $this->subtotal + $this->getDiscount() + $this->userData->getTips();
        } else {
            $total = $this->subtotal + $this->getDiscount() + $this->userData->getTips() + $this->getTotalTax();
        }

        return $with_gift_card && $this->gift_card ? max( 0, $total - $this->getGiftCardAmount() ) : $total;
    }

    /**
     * Get total without tax.
     *
     * @return float|int
     */
    public function getTotalNoTax()
    {
        $total_no_tax = $this->subtotal + $this->getDiscount();
        if ( $this->tax_included ) {
            $total_no_tax -= $this->getTotalTax();
        }

        return $total_no_tax;
    }

    /**
     * Get total tax amount.
     *
     * @return float|int
     */
    public function getTotalTax()
    {
        if ( $this->total_tax == null ) {
            $taxes = array(
                'allow_coupon' => 0,
                'without_coupon' => 0,
            );
            $coupon_total = 0;
            array_walk( $this->amounts_taxable, function ( $amount ) use ( &$taxes, &$coupon_total ) {
                if ( $amount['allow_coupon'] ) {
                    $taxes['allow_coupon'] += Proxy\Taxes::calculateTax( $amount['total'], $amount['rate'] );
                    $coupon_total += $amount['total'];
                } else {
                    $taxes['without_coupon'] += Proxy\Taxes::calculateTax( $amount['total'], $amount['rate'] );
                }
            } );

            if ( $coupon_total > 0 ) {
                $tax_products_with_coupon = 1 - ( $this->coupon->getDiscount() / 100 + $this->coupon->getDeduction() / $coupon_total );
                $tax_products_with_coupon *= $taxes['allow_coupon'];
            } else {
                $tax_products_with_coupon = 0;
            }

            $this->total_tax = $tax_products_with_coupon + $taxes['without_coupon'];
            if ( $this->group_discount != 0 ) {
                $group_discount_percent = $this->group_discount / ( $this->total - $this->group_discount ) * 100;
                $this->total_tax = Utils\Price::correction( $this->total_tax, -$group_discount_percent, 0 );
            }

            $this->total_tax = round( $this->total_tax, 2 );
        }

        return $this->total_tax;
    }

    /**
     * Get amount for deposit payment.
     *
     * @return float
     */
    public function getDepositPay( $with_gift_card = true )
    {
        if ( $this->tax_included ) {
            $deposit = min( $this->deposit, $this->total ) + $this->price_correction + $this->userData->getTips();
        } else {
            $deposit = min( $this->deposit + $this->getDepositTax(), $this->total + $this->getTotalTax() ) + $this->price_correction + $this->userData->getTips();
        }

        return $with_gift_card && $this->gift_card ? max( 0, $deposit - $this->getGiftCardAmount() ) : $deposit;
    }

    /**
     * Get tax for deposit payment.
     *
     * @return float
     */
    private function getDepositTax()
    {
        if ( $this->deposit_tax === null ) {
            $taxes_without_coupon = 0;
            foreach ( $this->amounts_taxable as $amount ) {
                $taxes_without_coupon += Proxy\Taxes::calculateTax( $amount['deposit'], $amount['rate'] );
            }
            $this->deposit_tax = $taxes_without_coupon;
        }

        return $this->deposit_tax;
    }

}