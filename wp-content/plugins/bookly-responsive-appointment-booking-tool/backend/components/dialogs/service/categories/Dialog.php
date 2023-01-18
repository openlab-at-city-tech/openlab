<?php
namespace Bookly\Backend\Components\Dialogs\Service\Categories;

use Bookly\Lib;

/**
 * Class Dialog
 * @package Bookly\Backend\Components\Dialogs\Service\Categories
 */
class Dialog extends Lib\Base\Component
{
    /**
     * Render create service dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'backend' => array(
                'js/sortable.min.js' => array( 'bookly-backend-globals' ),
            ),
            'module' => array( 'js/service-categories-dialog.js' => array( 'bookly-sortable.min.js' ) ),
        ) );

        self::renderTemplate( 'dialog' );
    }
}