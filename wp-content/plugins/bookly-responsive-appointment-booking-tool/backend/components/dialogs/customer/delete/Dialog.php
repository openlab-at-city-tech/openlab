<?php
namespace Bookly\Backend\Components\Dialogs\Customer\Delete;

use Bookly\Lib;

class Dialog extends Lib\Base\Component
{
    /**
     * Render customer dialog.
     */
    public static function render()
    {
        self::enqueueScripts( array(
            'module' => array( 'js/delete-customers.js' => array( 'bookly-backend-globals' ), )
        ) );

        static::renderTemplate( 'dialog' );
    }
}