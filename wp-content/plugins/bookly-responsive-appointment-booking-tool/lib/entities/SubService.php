<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

/**
 * Class SubService
 *
 * @package Bookly\Lib\Entities
 */
class SubService extends Lib\Base\Entity
{
    const TYPE_SERVICE    = 'service';
    const TYPE_SPARE_TIME = 'spare_time';

    /** @var  string */
    protected $type = self::TYPE_SERVICE;
    /** @var  int */
    protected $service_id;
    /** @var  int */
    protected $sub_service_id;
    /** @var  int */
    protected $duration;
    /** @var  int */
    protected $position;

    /** @var Service */
    public $service;

    protected static $table = 'bookly_sub_services';

    protected static $schema = array(
        'id'             => array( 'format' => '%d' ),
        'type'           => array( 'format' => '%s' ),
        'service_id'     => array( 'format' => '%d', 'reference' => array( 'entity' => 'Service' ) ),
        'sub_service_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Service' ) ),
        'duration'       => array( 'format' => '%d' ),
        'position'       => array( 'format' => '%d', 'sequent' => true ),
    );

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

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
     * @param int $service_id
     * @return $this
     */
    public function setServiceId( $service_id )
    {
        $this->service_id = $service_id;

        return $this;
    }

    /**
     * Gets sub_service_id
     *
     * @return int
     */
    public function getSubServiceId()
    {
        return $this->sub_service_id;
    }

    /**
     * Sets sub_service_id
     *
     * @param int $sub_service_id
     * @return $this
     */
    public function setSubServiceId( $sub_service_id )
    {
        $this->sub_service_id = $sub_service_id;

        return $this;
    }

    /**
     * Gets duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Sets duration
     *
     * @param int $duration
     * @return $this
     */
    public function setDuration( $duration )
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Gets position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition( $position )
    {
        $this->position = $position;

        return $this;
    }

}