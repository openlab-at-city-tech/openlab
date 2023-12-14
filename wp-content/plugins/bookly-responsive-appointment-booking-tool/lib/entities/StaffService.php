<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class StaffService extends Lib\Base\Entity
{
    /** @var  int */
    protected $staff_id;
    /** @var  int */
    protected $service_id;
    /** @var  int */
    protected $location_id;
    /** @var  float */
    protected $price = 0;
    /** @var  int */
    protected $capacity_min = 1;
    /** @var  int */
    protected $capacity_max = 1;
    /** @var  string  */
    protected $deposit = '100%';

    protected static $table = 'bookly_staff_services';

    protected static $schema = array(
        'id'            => array( 'format' => '%d' ),
        'staff_id'      => array( 'format' => '%d', 'reference' => array( 'entity' => 'Staff' ) ),
        'service_id'    => array( 'format' => '%d', 'reference' => array( 'entity' => 'Service' ) ),
        'location_id'   => array( 'format' => '%d', 'reference' => array( 'entity' => 'Location', 'namespace' => '\BooklyLocations\Lib\Entities', 'required' => 'bookly-addon-locations' ) ),
        'price'         => array( 'format' => '%f' ),
        'capacity_min'  => array( 'format' => '%d' ),
        'capacity_max'  => array( 'format' => '%d' ),
        'deposit'       => array( 'format' => '%s' ),
    );

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets staff_id
     *
     * @return int
     */
    public function getStaffId()
    {
        return $this->staff_id;
    }

    /**
     * Sets staff_id
     *
     * @param Staff $staff
     * @return $this
     */
    public function setStaff( Staff $staff )
    {
        return $this->setStaffId( $staff->getId() );
    }

    /**
     * Sets staff_id
     *
     * @param int $staff_id
     * @return $this
     */
    public function setStaffId( $staff_id )
    {
        $this->staff_id = $staff_id;

        return $this;
    }

    /**
     * Gets service_id
     *
     * @return int
     */
    public function getServiceId()
    {
        return $this->service_id;
    }

    /**
     * Sets service_id
     *
     * @param Service $service
     * @return $this
     */
    public function setService( Service $service )
    {
        return $this->setServiceId( $service->getId() );
    }

    /**
     * Sets service_id
     *
     * @param int $service_id
     * @return $this
     */
    public function setServiceId( $service_id )
    {
        $this->service_id = $service_id;

        return $this;
    }

    /**
     * Sets location_id
     *
     * @param int $location_id
     * @return $this
     */
    public function setLocationId( $location_id )
    {
        $this->location_id = $location_id;

        return $this;
    }

    /**
     * Gets location_id
     *
     * @return int
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * Gets price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice( $price )
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Gets capacity_min
     *
     * @return int
     */
    public function getCapacityMin()
    {
        return $this->capacity_min;
    }

    /**
     * Sets capacity_min
     *
     * @param int $capacity_min
     * @return $this
     */
    public function setCapacityMin( $capacity_min )
    {
        $this->capacity_min = $capacity_min;

        return $this;
    }

    /**
     * Gets capacity_max
     *
     * @return int
     */
    public function getCapacityMax()
    {
        return $this->capacity_max;
    }

    /**
     * Sets capacity_max
     *
     * @param int $capacity_max
     * @return $this
     */
    public function setCapacityMax( $capacity_max )
    {
        $this->capacity_max = $capacity_max;

        return $this;
    }

    /**
     * Gets deposit
     *
     * @return string
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * Sets deposit
     *
     * @param string $deposit
     * @return $this
     */
    public function setDeposit( $deposit )
    {
        $this->deposit = $deposit;

        return $this;
    }

}