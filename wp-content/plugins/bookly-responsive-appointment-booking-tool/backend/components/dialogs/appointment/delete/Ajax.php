<?php
namespace Bookly\Backend\Components\Dialogs\Appointment\Delete;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => array( 'staff', 'supervisor' ) );
    }

    /**
     * Delete single appointment.
     */
    public static function deleteAppointment()
    {
        wp_send_json( Lib\Utils\Appointment::delete(
            self::parameter( 'appointment_id' ),
            self::parameter( 'notify' ),
            self::parameter( 'reason' ) )
        );
    }
}