<?php
namespace Bookly\Backend\Components\Dialogs\Mailing\CreateList;

use Bookly\Lib;
use Bookly\Backend\Components\Controls\Buttons;

/**
 * Class Dialog
 * @package Bookly\Backend\Components\Dialogs\Mailing\CreateList
 */
class Dialog extends Lib\Base\Component
{
    /**
     * Render create mailing list dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/create-mailing-list-dialog.js' => array( 'bookly-backend-globals' ), ),
        ) );

        self::renderTemplate( 'dialog' );
    }

    /**
     * render button
     */
    public static function renderNewListButton()
    {
        print '<div class="col-auto">';
        Buttons::renderAdd( 'bookly-js-new-mailing-list', 'btn-success', __( 'New list', 'bookly' ) );
        print '</div>';
    }
}