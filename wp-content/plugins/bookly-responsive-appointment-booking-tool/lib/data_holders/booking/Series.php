<?php
namespace Bookly\Lib\DataHolders\Booking;

use Bookly\Lib;

/**
 * Class Series
 * @package Bookly\Lib\DataHolders\Booking
 */
class Series extends Item
{
    /** @var Lib\Entities\Series */
    protected $series;
    /** @var Item[] */
    protected $items = array();

    /**
     * Constructor.
     *
     * @param Lib\Entities\Series $series
     */
    public function __construct( Lib\Entities\Series $series )
    {
        $this->type   = Item::TYPE_SERIES;
        $this->series = $series;
    }

    /**
     * @inheritDoc
     */
    public function getAppointment()
    {
        return $this->getFirstItem()->getAppointment();
    }

    /**
     * @inheritDoc
     */
    public function getCA()
    {
        return $this->getFirstItem()->getCA();
    }

    /**
     * @inheritDoc
     */
    public function getDeposit()
    {
        return $this->getFirstItem()->getDeposit();
    }

    /**
     * @inheritDoc
     */
    public function getExtras()
    {
        return $this->getFirstItem()->getExtras();
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
        $item->setParent( $this );

        return $this;
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
     * Get array of sub-items (i.e. simple items)
     *
     * @return array
     */
    public function getSubItems()
    {
        $sub_items = array();
        foreach ( $this->getItems() as $item ) {
            foreach ( $item->getItems() as $simple ) {
                $sub_items[] = $simple;
            }
        }

        return $sub_items;
    }

    /**
     * @inheritDoc
     */
    public function getService()
    {
        return $this->getFirstItem()->getService();
    }

    /**
     * @inheritDoc
     */
    public function getServiceDuration()
    {
        return $this->getFirstItem()->getServiceDuration();
    }

    /**
     * @inheritDoc
     */
    public function getServicePrice()
    {
        return $this->getFirstItem()->getServicePrice();
    }

    /**
     * Get series.
     *
     * @return Lib\Entities\Series
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * @inheritDoc
     */
    public function getStaff()
    {
        return $this->getFirstItem()->getStaff();
    }

    /**
     * @inheritDoc
     */
    public function getTax()
    {
        if ( ! $this->tax ) {
            $rates = Lib\Proxy\Taxes::getServiceTaxRates();
            if ( $rates ) {
                foreach ( $this->getItems() as $item ) {
                    $this->tax += Lib\Proxy\Taxes::calculateTax( $item->getTotalPrice(), $rates[ $item->getService()->getId() ] );
                }
            }
        }

        return $this->tax;
    }

    /**
     * @inheritDoc
     */
    public function getServiceTax()
    {
        if ( ! $this->tax ) {
            $rates = Lib\Proxy\Taxes::getServiceTaxRates();
            if ( $rates ) {
                foreach ( $this->getItems() as $item ) {
                    $price = $this->getServicePrice();
                    $nop   = $this->getCA()->getNumberOfPersons();

                    $this->tax += Lib\Proxy\Taxes::calculateTax( $price * $nop, $rates[ $item->getService()->getId() ] );
                }
            }
        }

        return $this->tax;
    }

    /**
     * @inheritDoc
     */
    public function getTotalEnd()
    {
        return $this->getFirstItem()->getTotalEnd();
    }

    /**
     * @inheritDoc
     */
    public function getTotalPrice()
    {
        $price = 0.0;
        $break_on_first = get_option( 'bookly_recurring_appointments_payment' ) == 'first';
        foreach ( $this->items as $item ) {
            $price += $item->getTotalPrice();
            if ( $break_on_first ) {
                break;
            }
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function setStatus( $status )
    {
        foreach ( $this->items as $item ) {
            $item->setStatus( $status );
        }
    }

    /**
     * Create new item.
     *
     * @param Lib\Entities\Series $series
     * @return static
     */
    public static function create( Lib\Entities\Series $series )
    {
        return new static( $series );
    }

    /**
     * Get fist item from array items.
     *
     * @return Item
     */
    public function getFirstItem()
    {
        // Keep internal pointer of items array
        $clone = $this->items;

        return reset( $clone );
    }
}