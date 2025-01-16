<?php
namespace Bookly\Lib\DataHolders\Booking;

use Bookly\Lib;

class Compound extends Item
{
    /** @var Lib\Entities\Service */
    protected $compound_service;
    /** @var string */
    protected $compound_token;
    /** @var Simple[] */
    protected $items = array();
    /** @var array */
    protected $extras;
    /** @var int */
    protected $service_duration;
    /** @var Lib\Slots\DatePoint */
    protected $total_end;

    /**
     * Constructor.
     *
     * @param Lib\Entities\Service $compound_service
     */
    public function __construct( Lib\Entities\Service $compound_service )
    {
        $this->type = Item::TYPE_COMPOUND;
        $this->compound_service = $compound_service;
    }

    /**
     * @inheritDoc
     */
    public function getAppointment()
    {
        return $this->items[0]->getAppointment();
    }

    /**
     * @inheritDoc
     */
    public function getCA()
    {
        return $this->items[0]->getCA();
    }

    /**
     * @inheritDoc
     */
    public function getDeposit()
    {
        return $this->compound_service->getDeposit();
    }

    /**
     * @inheritDoc
     */
    public function getExtras()
    {
        if ( $this->extras === null ) {
            $this->extras = array();
            foreach ( $this->items as $item ) {
                $this->extras += $item->getExtras();
            }
        }

        return $this->extras;
    }

    /**
     * Add item.
     *
     * @param Simple $item
     * @return $this
     */
    public function addItem( Simple $item )
    {
        $this->items[] = $item;
        $item->setParent( $this );

        return $this;
    }

    /**
     * Get items.
     *
     * @return Simple[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function getService()
    {
        return $this->compound_service;
    }

    /**
     * @inheritDoc
     */
    public function getServiceDuration()
    {
        if ( $this->service_duration === null ) {
            $result = Lib\Entities\SubService::query( 'ss' )
                ->select( 'SUM(COALESCE(s.duration, ss.duration)) AS duration' )
                ->leftJoin( 'Service', 's', 's.id = ss.sub_service_id' )
                ->where( 'ss.service_id', $this->compound_service->getId() )
                ->fetchRow()
            ;
            $this->service_duration = $result['duration'];
        }

        return $this->service_duration;
    }

    /**
     * @inheritDoc
     */
    public function getServicePrice()
    {
        return $this->compound_service->getPrice();
    }

    /**
     * @inheritDoc
     */
    public function getStaff()
    {
        return $this->items[0]->getStaff();
    }

    /**
     * @inheritDoc
     */
    public function getTax()
    {
        if ( ! $this->tax ) {
            $rates = Lib\Proxy\Taxes::getServiceTaxRates();
            if ( $rates ) {
                $this->tax = Lib\Proxy\Taxes::calculateTax( $this->getTotalPrice(), $rates[ $this->getService()->getId() ] );
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
                $price = $this->getServicePrice();
                $nop   = $this->getCA()->getNumberOfPersons();

                $this->tax = Lib\Proxy\Taxes::calculateTax( $price * $nop, $rates[ $this->getService()->getId() ] );
            }
        }

        return $this->tax;
    }

    /**
     * Get compound token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->compound_token;
    }

    /**
     * Set compound token.
     *
     * @param string $token
     * @return $this
     */
    public function setToken( $token )
    {
        $this->compound_token = $token;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotalEnd()
    {
        if ( $this->total_end === null ) {
            foreach ( $this->items as $item ) {
                $item_end = $item->getTotalEnd();
                if ( $this->total_end === null ) {
                    $this->total_end = $item_end;
                } elseif ( $item_end->gt( $this->total_end ) ) {
                    $this->total_end = $item_end;
                }
            }
        }

        return $this->total_end;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPrice()
    {
        $extras_total_price = (float) Lib\Proxy\ServiceExtras::getTotalPrice( (array) json_decode( $this->getCA()->getExtras(), true ), $this->getCA()->getNumberOfPersons() );

        return $this->getServicePrice() * $this->getCA()->getNumberOfPersons() + $extras_total_price;
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
     * @inerhitDoc
     */
    public function getLocationId()
    {
        return $this->getAppointment()->getLocationId();
    }

    /**
     * Create new item.
     *
     * @param Lib\Entities\Service $compound_service
     * @return static
     */
    public static function create( Lib\Entities\Service $compound_service )
    {
        return new static( $compound_service );
    }

    /**
     * Create new item.
     *
     * @param string $token
     * @param array  $statuses
     * @return Compound
     */
    public static function createByToken( $token, $statuses = array() )
    {
        $query = Lib\Entities\CustomerAppointment::query( 'ca' )
            ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
            ->where( 'ca.compound_token', $token );
        if ( $statuses ) {
            $query->whereIn( 'ca.status', $statuses );
        }

        $ca_list = $query->sortBy( 'a.start_date' )->find();

        $self = new static( Lib\Entities\Service::find( $ca_list[0]->getCompoundServiceId() ) );

        foreach ( $ca_list as $ca ) {
            $self->addItem( Simple::create( $ca ) );
        }

        return $self;
    }

    /**
     * Create from simple item.
     *
     * @param Simple $item
     * @return static
     */
    public static function createFromSimple( Simple $item )
    {
        return static::create( Lib\Entities\Service::find( $item->getCA()->getCompoundServiceId() ) )->addItem( $item );
    }
}