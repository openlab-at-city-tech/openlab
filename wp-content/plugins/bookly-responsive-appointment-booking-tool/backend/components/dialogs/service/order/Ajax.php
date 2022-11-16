<?php
namespace Bookly\Backend\Components\Dialogs\Service\Order;

use Bookly\Lib;

/**
 * Class Dialog
 * @package Bookly\Backend\Components\Dialogs\Service\Order
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Update service positions
     */
    public static function updateServicePositions()
    {
        foreach ( (array) self::parameter( 'services' ) as $position => $service_id ) {
            Lib\Entities\Service::query( 's' )
                ->update()
                ->set( 'position', $position )
                ->where( 'id', $service_id )
                ->execute();
        }

        wp_send_json_success( Lib\Entities\Service::query()->sortBy( 'position' )->fetchArray() );
    }
}