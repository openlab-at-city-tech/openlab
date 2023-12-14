<?php
namespace Bookly\Backend\Components\Dialogs\Common;

use Bookly\Lib;

class CascadeDelete extends Lib\Base\Component
{
    /**
     * Render cascade delete dialog (used in services and staff lists).
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::renderTemplate( 'delete_cascade' );
    }
}