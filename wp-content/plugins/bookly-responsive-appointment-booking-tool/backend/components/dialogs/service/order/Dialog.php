<?php
namespace Bookly\Backend\Components\Dialogs\Service\Order;

use Bookly\Lib;

/**
 * Class Dialog
 * @package Bookly\Backend\Components\Dialogs\Service\Order
 */
class Dialog extends Lib\Base\Component
{
    /**
     * Render create service dialog.
     */
    public static function render( array $services = array() )
    {
        self::enqueueStyles( array(
            'backend'  => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/service-order-dialog.js' => array( 'bookly-backend-globals', ) ),
        ) );

        wp_localize_script( 'bookly-service-order-dialog.js', 'BooklyServiceOrderDialogL10n', array(
            'services'  => $services,
        ) );

        self::renderTemplate( 'dialog' );
    }
}