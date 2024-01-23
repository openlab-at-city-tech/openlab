<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class StaffScheduleItem extends Lib\Base\Entity
{
    /** @var  int */
    protected $staff_id;
    /** @var  int */
    protected $location_id;
    /** @var  int */
    protected $day_index;
    /** @var  int */
    protected $start_time;
    /** @var  int */
    protected $end_time;

    protected static $table = 'bookly_staff_schedule_items';

    protected static $schema = array(
        'id'          => array( 'format' => '%d' ),
        'staff_id'    => array( 'format' => '%d', 'reference' => array( 'entity' => 'Staff' ) ),
        'location_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Location', 'namespace' => '\BooklyLocations\Lib\Entities', 'required' => 'bookly-addon-locations' ) ),
        'day_index'   => array( 'format' => '%d' ),
        'start_time'  => array( 'format' => '%s' ),
        'end_time'    => array( 'format' => '%s' ),
    );

    /**
     * Checks if
     *
     * @param $start_time
     * @param $end_time
     * @param $break_id
     * @return bool
     */
    public function isBreakIntervalAvailable( $start_time, $end_time, $break_id = 0 )
    {
        return ScheduleItemBreak::query()
            ->where( 'staff_schedule_item_id', $this->getId() )
            ->whereNot( 'id', $break_id )
            ->whereLt( 'start_time', $end_time )
            ->whereGt( 'end_time', $start_time )
            ->count() == 0;
    }

    /**
     * Get list of breaks
     *
     * @return array
     */
    public function getBreaksList()
    {
        return ScheduleItemBreak::query()
            ->where( 'staff_schedule_item_id', $this->getId() )
            ->sortBy( 'start_time, end_time' )
            ->fetchArray();
    }

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
     * Gets location_id
     *
     * @return int
     */
    public function getLocationId()
    {
        return $this->location_id;
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
     * Gets day_index
     *
     * @return int
     */
    public function getDayIndex()
    {
        return $this->day_index;
    }

    /**
     * Sets day_index
     *
     * @param int $day_index
     * @return $this
     */
    public function setDayIndex( $day_index )
    {
        $this->day_index = $day_index;

        return $this;
    }

    /**
     * Gets start_time
     *
     * @return int
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Sets start_time
     *
     * @param int $start_time
     * @return $this
     */
    public function setStartTime( $start_time )
    {
        $this->start_time = $start_time;

        return $this;
    }

    /**
     * Gets end_time
     *
     * @return int
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Sets end_time
     *
     * @param int $end_time
     * @return $this
     */
    public function setEndTime( $end_time )
    {
        $this->end_time = $end_time;

        return $this;
    }

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    public function save()
    {
        $list = $this->getBreaksList();
        foreach ( $list as $row ) {
            $break = new ScheduleItemBreak();
            $break->setFields( $row );
            if (
                $this->getStartTime()     > $break->getStartTime()
                || $break->getStartTime() >= $this->getEndTime()
                || $this->getStartTime()  >= $break->getEndTime()
                || $break->getEndTime()   > $this->getEndTime()
            ) {
                $break->delete();
            }
        }

        parent::save();
    }

}