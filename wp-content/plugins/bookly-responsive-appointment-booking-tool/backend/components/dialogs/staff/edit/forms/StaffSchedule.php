<?php
namespace Bookly\Backend\Components\Dialogs\Staff\Edit\Forms;

use Bookly\Lib;

/**
 * Class StaffSchedule
 * @package Bookly\Backend\Components\Dialogs\Staff\Edit\Forms
 */
class StaffSchedule extends Lib\Base\Form
{
    protected static $entity_class = 'StaffScheduleItem';

    public function configure()
    {
        $this->setFields( array( 'ssi', 'staff_id', 'start_time', 'end_time', 'location_id' ) );
    }

    public function save()
    {
        if ( isset( $this->data['ssi'] ) ) {
            foreach ( $this->data['ssi'] as $id => $day_index ) {
                $res_schedule = new Lib\Entities\StaffScheduleItem();
                $res_schedule->load( $id );
                $res_schedule->setDayIndex( $day_index );
                if ( ! $res_schedule->getLocationId() ) {
                    $res_schedule->setLocationId( null );
                }
                if ( $this->data['start_time'][ $id ] ) {
                    $res_schedule
                        ->setStartTime( $this->data['start_time'][ $id ] )
                        ->setEndTime( $this->data['end_time'][ $id ] );
                } else {
                    $res_schedule
                        ->setStartTime( null )
                        ->setEndTime( null );
                }
                $res_schedule->save();
            }
        }
    }

}
