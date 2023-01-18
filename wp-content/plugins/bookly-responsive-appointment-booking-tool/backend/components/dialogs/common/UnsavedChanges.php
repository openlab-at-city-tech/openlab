<?php
namespace Bookly\Backend\Components\Dialogs\Common;

use Bookly\Lib;

/**
 * Class UnsavedChanges
 * @package Bookly\Backend\Components\Dialogs\Common
 */
class UnsavedChanges extends Lib\Base\Component
{
    /**
     * Render unsaved data confirm dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::renderTemplate( 'unsaved_changes' );
    }
}