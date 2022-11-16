<?php
namespace Bookly\Backend\Components\Dialogs\Staff\Order;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Dialogs\Staff\Order
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Update staff positions
     */
    public static function updateStaffPositions()
    {
        foreach ( (array) self::parameter( 'staff' ) as $position => $staff_id ) {
            Lib\Entities\Staff::query( 's' )
                ->update()
                ->set( 'position', $position )
                ->where( 'id', $staff_id )
                ->execute();
        }

        wp_send_json_success();
    }
}