<?php
namespace Bookly\Backend\Components\Dialogs\Appointment\Delete;

use Bookly\Lib;

class Dialog extends Lib\Base\Component
{
    /**
     * Render delete appointment dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals' ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/delete_dialog.js' => array( 'bookly-backend-globals' ), ),
        ) );

        static::renderTemplate( 'delete' );
    }
}