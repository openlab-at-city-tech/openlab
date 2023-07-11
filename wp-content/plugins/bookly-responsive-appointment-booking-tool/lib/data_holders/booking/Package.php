<?php
namespace Bookly\Lib\DataHolders\Booking;

use Bookly\Lib;

/**
 * Class Package
 * @package Bookly\Lib\DataHolders\Booking
 */
class Package extends Simple
{
    /** @var \BooklyPackages\Lib\Entities\Package */
    protected $package;

    /**
     * @inheritDoc
     */
    public function __construct( Lib\Entities\CustomerAppointment $ca )
    {
        parent::__construct( $ca );
        $this->type = Item::TYPE_PACKAGE;
    }

    /**
     * @return \BooklyPackages\Lib\Entities\Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param \BooklyPackages\Lib\Entities\Package $package
     * @return Package
     */
    public function setPackage( $package )
    {
        $this->package = $package;

        return $this;
    }
}