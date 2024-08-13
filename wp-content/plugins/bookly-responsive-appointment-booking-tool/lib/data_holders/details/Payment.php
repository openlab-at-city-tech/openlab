<?php
namespace Bookly\Lib\DataHolders\Details;

use Bookly\Lib;

class Payment extends Base
{
    /** @var Lib\CartInfo */
    protected $cart_info;
    /** @var Lib\Entities\Customer */
    protected $client;
    /** @var Lib\Entities\Payment */
    protected $payment;

    protected $fields = array(
        'items',
        'coupon',
        'coupon_id',
        'gift_card',
        'gift_card_id',
        'subtotal',
        'customer',
        'customer_id',
        'customer_group',
        'tax_in_price',
        'tax_paid',
        'extras_multiply_nop',
        'gateway_ref_id',
        'tips',
        'adjustments',
        'refundable',
        'group_discount',
        'discounts',
        'from_backend'
    );

    public function getData()
    {
        $data = array();
        foreach ( $this->fields as $field ) {
            $data[ $field ] = $this->getValue( $field );
        }
        $data['adjustments'] = $data['adjustments'] ?: array();

        return $data;
    }

    /**
     * @param Base $details
     * @return $this
     */
    public function addDetails( Base $details )
    {
        switch ( $details->getType() ) {
            case Lib\Entities\Payment::ITEM_APPOINTMENT:
            case Lib\Entities\Payment::ITEM_PACKAGE:
            case Lib\Entities\Payment::ITEM_GIFT_CARD:
                $this->data['items'][] = $details->getData();
                if ( ! isset( $this->data['subtotal']['price'] ) ) {
                    $this->data['subtotal']['deposit'] = 0;
                    $this->data['subtotal']['price'] = 0;
                }
                $this->data['subtotal']['deposit'] += $details->getDeposit();
                $this->data['subtotal']['price'] += $details->getPrice();
                break;
            case Lib\Entities\Payment::ITEM_ADJUSTMENT:
                $this->data['adjustments'][] = $details->getData();
                break;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->data['items'] ?: array();
    }

    /**
     * @param Lib\DataHolders\Booking\Order $order
     * @param Lib\CartInfo $cart_info
     * @return $this
     */
    public function setOrder( Lib\DataHolders\Booking\Order $order, Lib\CartInfo $cart_info )
    {
        $this->cart_info = $cart_info;
        $this->setCustomer( $order->getCustomer() );
        $this->payment = $order->getPayment();

        foreach ( $order->getItems() as $order_item ) {
            $items = $order_item->isSeries() ? $order_item->getItems() : array( $order_item );
            /** @var Lib\DataHolders\Booking\Item $sub_item */
            foreach ( $items as $sub_item ) {
                if ( $sub_item->getCA() && ( $sub_item->getCA()->getPaymentId() != $this->payment->getId() ) ) {
                    // Skip items not related to this payment (e.g. series items with no associated payment).
                    continue;
                }
                $details = Base::create( $sub_item );
                $this->addDetails( $details );
            }
        }
        $this->setData( array(
            'tips' => $cart_info->getUserData()->getTips(),
            'extras_multiply_nop' => (int) get_option( 'bookly_service_extras_multiply_nop', 1 ),
        ) );

        Lib\Proxy\Shared::preparePaymentDetails( $this );

        return $this;
    }

    /**
     * @return Lib\CartInfo
     */
    public function getCartInfo()
    {
        return $this->cart_info;
    }

    /**
     * @return Lib\Entities\Customer
     */
    public function getCustomer()
    {
        return $this->client;
    }

    /**
     * @return Lib\Entities\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param Lib\Entities\Payment $payment
     * @return $this
     */
    public function setPayment( Lib\Entities\Payment $payment )
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        $customer_name = $this->getValue( 'customer' );
        if ( $this->getValue( 'customer_id' ) ) {
            $customer = Lib\Entities\Customer::find( $this->getValue( 'customer_id' ) );
            $customer_name = $customer ? $customer->getFullName() : $customer_name;
        }

        return $customer_name;
    }

    /**
     * @param Lib\Entities\Customer $client
     * @return $this
     */
    public function setCustomer( $client )
    {
        if ( $client ) {
            $this->client = $client;
            $this->setData(
                array(
                    'customer' => $client->getFullName(),
                    'customer_id' => $client->getId(),
                )
            );
        }

        return $this;
    }

    /**
     * @inerhitDoc
     */
    public function getDeposit()
    {
        return $this->data['subtotal']['deposit'];
    }

    /**
     * @inerhitDoc
     */
    public function getPrice()
    {
        return $this->data['subtotal']['price'];
    }
}