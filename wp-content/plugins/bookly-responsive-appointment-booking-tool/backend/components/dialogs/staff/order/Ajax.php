<?php
namespace Bookly\Backend\Components\Dialogs\Staff\Order;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * Update staff positions
     */
    public static function updateStaffPositions()
    {
        foreach ( self::parameter( 'staff', array() ) as $position => $staff_id ) {
            Lib\Entities\Staff::query( 's' )
                ->update()
                ->set( 'position', $position )
                ->where( 'id', $staff_id )
                ->whereNot( 'position', $position )
                ->execute();
        }

        wp_send_json_success();
    }
}