<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class Holiday extends Lib\Base\Entity
{
    /** @var  int */
    protected $staff_id;
    /** @var  int */
    protected $parent_id;
    /** @var  string */
    protected $date;
    /** @var  int */
    protected $repeat_event;

    protected static $table = 'bookly_holidays';

    protected static $schema = array(
        'id'           => array( 'format' => '%d' ),
        'staff_id'     => array( 'format' => '%d', 'reference' => array( 'entity' => 'Staff' ) ),
        'parent_id'    => array( 'format' => '%d' ),
        'date'         => array( 'format' => '%s' ),
        'repeat_event' => array( 'format' => '%s' ),
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
     * @param int $staff_id
     * @return $this
     */
    public function setStaffId( $staff_id )
    {
        $this->staff_id = $staff_id;

        return $this;
    }

    /**
     * Gets parent_id
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Sets parent
     *
     * @param Holiday $parent
     * @return $this
     */
    public function setParent( Holiday $parent )
    {
        return $this->setParentId( $parent->getId() );
    }

    /**
     * Sets parent_id
     *
     * @param int $parent_id
     * @return $this
     */
    public function setParentId( $parent_id )
    {
        $this->parent_id = $parent_id;

        return $this;
    }

    /**
     * Gets date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets date
     *
     * @param string $date
     * @return $this
     */
    public function setDate( $date )
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Gets repeat_event
     *
     * @return int
     */
    public function getRepeatEvent()
    {
        return $this->repeat_event;
    }

    /**
     * Sets repeat_event
     *
     * @param int $repeat_event
     * @return $this
     */
    public function setRepeatEvent( $repeat_event )
    {
        $this->repeat_event = $repeat_event;

        return $this;
    }

}
