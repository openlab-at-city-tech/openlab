<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class ScheduleItemBreak extends Lib\Base\Entity
{
    /** @var  int */
    protected $staff_schedule_item_id;
    /** @var  int */
    protected $start_time;
    /** @var  int */
    protected $end_time;

    protected static $table = 'bookly_schedule_item_breaks';

    protected static $schema = array(
        'id'                     => array( 'format' => '%d' ),
        'staff_schedule_item_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'StaffScheduleItem' ) ),
        'start_time'             => array( 'format' => '%s' ),
        'end_time'               => array( 'format' => '%s' ),
    );

    /**
     * Remove all breaks for certain staff member
     *
     * @param $staff_id
     */
    public function removeBreaksByStaffId( $staff_id )
    {
        self::$wpdb->get_results( self::$wpdb->prepare(
            'DELETE `break` FROM `' . self::getTableName() . '` AS `break`
            LEFT JOIN `' . StaffScheduleItem::getTableName() . '` AS `item` ON `item`.`id` = `break`.`staff_schedule_item_id`
            WHERE `item`.`staff_id` = %d',
            $staff_id
        ) );
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets staff_schedule_item_id
     *
     * @return int
     */
    public function getStaffScheduleItemId()
    {
        return $this->staff_schedule_item_id;
    }

    /**
     * Sets staff_schedule_item_id
     *
     * @param int $staff_schedule_item_id
     * @return $this
     */
    public function setStaffScheduleItemId( $staff_schedule_item_id )
    {
        $this->staff_schedule_item_id = $staff_schedule_item_id;

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

}