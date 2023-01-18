<?php
namespace Bookly\Backend\Components\Dialogs\Service\Create;

use Bookly\Lib;
use Bookly\Backend\Modules\Services\Proxy;

/**
 * Class Dialog
 * @package Bookly\Backend\Components\Dialogs\Service\Create
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
            'module' => array( 'js/service-create-dialog.js' => array( 'bookly-backend-globals' ) ),
        ) );

        $type_icons = Proxy\Shared::prepareServiceIcons( array( Lib\Entities\Service::TYPE_SIMPLE => 'far fa-calendar-check' ) );

        self::renderTemplate( 'dialog', compact( 'type_icons' ) );
    }
}