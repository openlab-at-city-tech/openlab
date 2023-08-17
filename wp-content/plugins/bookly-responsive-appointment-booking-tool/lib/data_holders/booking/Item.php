<?php
namespace Bookly\Lib\DataHolders\Booking;

use Bookly\Lib;
use Bookly\Lib\DataHolders\Booking;

/**
 * Class Item
 * @package Bookly\Lib\DataHolders\Booking
 */
abstract class Item
{
    const TYPE_SIMPLE        = 1;
    const TYPE_COLLABORATIVE = 2;
    const TYPE_COMPOUND      = 3;
    const TYPE_SERIES        = 4;
    const TYPE_PACKAGE       = 5;

    /** @var int */
    protected $type;
    /** @var Item|null */
    protected $parent;
    /** @var float */
    protected $tax = 0;

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Check if item is simple.
     *
     * @return bool
     */
    public function isSimple()
    {
        return $this->type == self::TYPE_SIMPLE;
    }

    /**
     * Check if item is collaborative.
     *
     * @return bool
     */
    public function isCollaborative()
    {
        return $this->type == self::TYPE_COLLABORATIVE;
    }

    /**
     * Check if item is compound.
     *
     * @return bool
     */
    public function isCompound()
    {
        return $this->type == self::TYPE_COMPOUND;
    }

    /**
     * Check if item is series.
     *
     * @return bool
     */
    public function isSeries()
    {
        return $this->type == self::TYPE_SERIES;
    }

    /**
     * Check if item is package.
     *
     * @return bool
     */
    public function isPackage()
    {
        return $this->type == self::TYPE_PACKAGE;
    }

    /**
     * Set parent item
     *
     * @param Item $item
     */
    protected function setParent( Item $item )
    {
        $this->parent = $item;
    }

    /**
     * Get parent item
     *
     * @return Item|null
     */
    protected function getParent()
    {
        return $this->parent;
    }

    /**
     * Get root item
     *
     * @return Item
     */
    public function getRoot()
    {
        $root = $this;
        while ( $root->getParent() ) {
            $root = $root->getParent();
        }

        return $root;
    }

    /**
     * Collects in item related customer appointments
     *
     * @param Lib\Entities\CustomerAppointment $ca
     * @param array                            $exclude_statuses
     * @return null|Booking\Collaborative|Booking\Compound|Booking\Simple
     */
    public static function collect( Lib\Entities\CustomerAppointment $ca, array $exclude_statuses = array() )
    {
        $item = null;
        if ( $ca->getCollaborativeToken() || $ca->getCompoundToken() ) {
            $query = Lib\Entities\CustomerAppointment::query();
            if ( $exclude_statuses ) {
                $query->whereNotIn( 'status', $exclude_statuses );
            }
            if ( $ca->getCollaborativeToken() ) {
                $co = Collaborative::create( Lib\Entities\Service::find( $ca->getCollaborativeServiceId() ) )
                    ->setToken( $ca->getCollaborativeToken() );
                $query->where( 'collaborative_token', $ca->getCollaborativeToken() )
                    ->where( 'collaborative_service_id', $ca->getCollaborativeServiceId() );
            } elseif ( $ca->getCompoundToken() ) {
                $co = Compound::create( Lib\Entities\Service::find( $ca->getCompoundServiceId() ) )
                    ->setToken( $ca->getCompoundToken() );
                $query->where( 'compound_token', $ca->getCompoundToken() )
                      ->where( 'compound_service_id', $ca->getCompoundServiceId() );
            }
            /** @var Lib\Entities\CustomerAppointment[] $ca_list */
            $ca_list = $query->find();
            if ( $ca_list ) {
                foreach ( $ca_list as $customer_appointment ) {
                    $co->addItem( Lib\DataHolders\Booking\Simple::create( $customer_appointment ) );
                }
                $item = $co;
            }
        } else {
            if ( ! in_array( $ca->getStatus(), $exclude_statuses ) ) {
                $item = Lib\DataHolders\Booking\Simple::create( $ca );
            }
        }

        return $item;
    }

    /**
     * Get appointment.
     *
     * @return Lib\Entities\Appointment
     */
    abstract public function getAppointment();

    /**
     * Get customer appointment.
     *
     * @return Lib\Entities\CustomerAppointment
     */
    abstract public function getCA();

    /**
     * Get deposit.
     *
     * @return string
     */
    abstract public function getDeposit();

    /**
     * Get extras.
     *
     * @return array
     */
    abstract public function getExtras();

    /**
     * Get service.
     *
     * @return Lib\Entities\Service;
     */
    abstract public function getService();

    /**
     * Get service duration.
     *
     * For compound or collaborative services the duration
     * is calculated based on duration of sub services.
     *
     * @return int
     */
    abstract public function getServiceDuration();

    /**
     * Get service price.
     *
     * @return float
     */
    abstract public function getServicePrice();

    /**
     * Get staff.
     *
     * @return Lib\Entities\Staff
     */
    abstract public function getStaff();

    /**
     * Get tax.
     *
     * @return string
     */
    abstract public function getTax();

    /**
     * Get tax.
     *
     * @return string
     */
    abstract public function getServiceTax();

    /**
     * Get appointment end time taking into account extras duration.
     *
     * For compound or collaborative services the total end
     * is calculated based on ending time of sub services.
     *
     * @return Lib\Slots\DatePoint
     */
    abstract public function getTotalEnd();

    /**
     * Get total price.
     *
     * @return float
     */
    abstract public function getTotalPrice();

    /**
     * Get items.
     *
     * For compound or collaborative services return sub services.
     *
     * @return Simple[]
     */
    abstract public function getItems();

    /**
     * Set status for all sub services.
     *
     * @param string $status
     */
    abstract public function setStatus( $status );
}