<?php
namespace Bookly\Lib\DataHolders\Booking;

use Bookly\Lib;

/**
 * Class Order
 * @package Bookly\Lib\DataHolders\Booking
 */
class Order
{
    /** @var Lib\Entities\Customer */
    protected $customer;
    /** @var Lib\Entities\Payment */
    protected $payment;
    /** @var Item[] */
    protected $items = array();

    /**
     * Constructor.
     *
     * @param Lib\Entities\Customer $customer
     */
    public function __construct( Lib\Entities\Customer $customer )
    {
        $this->customer = $customer;
    }

    /**
     * Get customer.
     *
     * @return Lib\Entities\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set payment.
     *
     * @param Lib\Entities\Payment $payment
     * @return $this
     */
    public function setPayment( Lib\Entities\Payment $payment )
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Check if payment exists.
     *
     * @return bool
     */
    public function hasPayment()
    {
        return (bool) $this->payment;
    }

    /**
     * Get payment.
     *
     * @return Lib\Entities\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Add item.
     *
     * @param string $id
     * @param Item $item
     * @return $this
     */
    public function addItem( $id, Item $item )
    {
        $this->items[ $id ] = $item;

        return $this;
    }

    /**
     * Check if item exists.
     *
     * @param string $id
     * @return bool
     */
    public function hasItem( $id )
    {
        return isset ( $this->items[ $id ] );
    }

    /**
     * Get item.
     *
     * @param string $id
     * @return Item
     */
    public function getItem( $id )
    {
        return $this->items[ $id ];
    }

    /**
     * Get items.
     *
     * @return Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get flat array of items.
     *
     * @return Item[]
     */
    public function getFlatItems()
    {
        $result = array();
        foreach ( $this->items as $item ) {
            if ( $item->isSeries() ) {
                /** @var Series $item */
                $result = array_merge( $result, $item->getItems() );
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Create new order.
     *
     * @param Lib\Entities\Customer $customer
     * @return static
     */
    public static function create( Lib\Entities\Customer $customer )
    {
        return new static( $customer );
    }

    /**
     * Create new order from item.
     *
     * @param Item $item
     * @return static
     */
    public static function createFromItem( Item $item )
    {
        $order = static::create( Lib\Entities\Customer::find( $item->getCA()->getCustomerId() ) )->addItem( 0, $item );

        if ( $item->getCA()->getPaymentId() ) {
            $order->setPayment( Lib\Entities\Payment::find( $item->getCA()->getPaymentId() ) );
        }

        return $order;
    }

    /**
     * Create Order from order_id.
     *
     * @param int $order_id
     * @return Order|null
     */
    public static function createFromOrderId( $order_id )
    {
        return self::createOrderByCaList( Lib\Entities\CustomerAppointment::query()->where( 'order_id', $order_id )->find() );
    }

    /**
     * Create Order from payment.
     *
     * @param Lib\Entities\Payment $payment
     * @return Order|null
     */
    public static function createFromPayment( Lib\Entities\Payment $payment )
    {
        return self::createOrderByCaList( Lib\Entities\CustomerAppointment::query()->where( 'payment_id', $payment->getId() )->find() );
    }

    /**
     * @param Lib\Entities\CustomerAppointment[] $ca_list
     * @return static|null
     */
    private static function createOrderByCaList( $ca_list )
    {
        if ( $ca_list ) {
            $series_id = $ca_list[0]->getSeriesId();
            $payment_id = $ca_list[0]->getPaymentId();
            if ( $series_id ) {
                // Make a list of customer appointments from series.
                // Possibly customer paid only for first appointment in series of recurring appointments.
                $ca_list = Lib\Entities\CustomerAppointment::query()->where( 'series_id', $series_id )->find();
            }

            $item_key = 0;
            $customer = Lib\Entities\Customer::find( $ca_list[0]->getCustomerId() );
            $order = static::create( $customer );
            if ( $payment_id ) {
                $order->setPayment( Lib\Entities\Payment::find( $payment_id ) );
            }
            /**
             * @var Lib\DataHolders\Booking\Compound[] $compounds
             * @var Lib\DataHolders\Booking\Collaborative[] $collaboratives
             */
            $compounds = $collaboratives = array();
            $series = null;
            foreach ( $ca_list as $ca ) {
                $type   = Lib\Entities\Service::TYPE_SIMPLE;

                if ( $ca->getCompoundServiceId() !== null ) {
                    $type = Lib\Entities\Service::TYPE_COMPOUND;
                    if ( ! array_key_exists( $ca->getCompoundToken(), $compounds ) ) {
                        $compounds[ $ca->getCompoundToken() ] = Lib\DataHolders\Booking\Compound::create( Lib\Entities\Service::find( $ca->getCompoundServiceId() ) )
                            ->setToken( $ca->getCompoundToken() );
                    }
                } elseif ( $ca->getCollaborativeServiceId() !== null ) {
                    $type = Lib\Entities\Service::TYPE_COLLABORATIVE;
                    if ( ! array_key_exists( $ca->getCollaborativeToken(), $collaboratives ) ) {
                        $collaboratives[ $ca->getCollaborativeToken() ] = Lib\DataHolders\Booking\Collaborative::create( Lib\Entities\Service::find( $ca->getCollaborativeServiceId() ) )
                            ->setToken( $ca->getCollaborativeToken() );
                    }
                }

                // Series.
                if ( $ca->getSeriesId() ) {
                    if ( ( $series === null )
                        || ( $series->getCA()->getSeriesId() != $ca->getSeriesId() ) )
                    {
                        // Unique series item key
                        $series_item_key = 'series_id_' . $ca->getSeriesId();
                        if ( $order->hasItem( $series_item_key ) ) {
                            $series = $order->getItem( $series_item_key );
                        } else {
                            $series = Lib\DataHolders\Booking\Series::create( Lib\Entities\Series::find( $ca->getSeriesId() ) );
                            $order->addItem( $series_item_key, $series );
                        }
                    }
                } else {
                    $series = null;
                }

                $item = Lib\DataHolders\Booking\Simple::create( $ca );

                if ( $type === Lib\Entities\Service::TYPE_COMPOUND ) {
                    $item = $compounds[ $ca->getCompoundToken() ]->addItem( $item );
                } elseif ( $type === Lib\Entities\Service::TYPE_COLLABORATIVE ) {
                    $item = $collaboratives[ $ca->getCollaborativeToken() ]->addItem( $item );
                }

                if ( count( $item->getItems() ) === 1 ) {
                    if ( $series ) {
                        $series->addItem( $item_key ++, $item );
                    } else {
                        $order->addItem( $item_key ++, $item );
                    }
                }
            }

            return $order;
        }

        return null;
    }

}