<?php
namespace Bookly\Backend\Components\Dialogs\Service\Order;

use Bookly\Lib;
use Bookly\Backend\Modules\Services\Proxy;

/**
 * Class Dialog
 * @package Bookly\Backend\Components\Dialogs\Service\Order
 */
class Dialog extends Lib\Base\Component
{
    /**
     * Render create service dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend'  => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/service-order-dialog.js' => array( 'bookly-backend-globals', ) ),
        ) );

        $services = Lib\Entities\Service::query( 's' )
            ->select( 'id, title' )
            ->whereIn( 's.type', array_keys( Proxy\Shared::prepareServiceTypes( array( Lib\Entities\Service::TYPE_SIMPLE => Lib\Entities\Service::TYPE_SIMPLE ) ) ) )
            ->sortBy( 'position' )
            ->fetchArray();

        wp_localize_script( 'bookly-service-order-dialog.js', 'BooklyServiceOrderDialogL10n', array(
            'services' => $services,
        ) );

        self::renderTemplate( 'dialog' );
    }
}