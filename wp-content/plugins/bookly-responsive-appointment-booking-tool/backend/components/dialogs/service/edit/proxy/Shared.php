<?php
namespace Bookly\Backend\Components\Dialogs\Service\Edit\Proxy;

use Bookly\Lib;

/**
 * @method static void  enqueueAssetsForServices() Enqueue assets for page Services.
 * @method static array prepareUpdateService( array $data ) Prepare update service settings in add-ons.
 * @method static array prepareUpdateServiceResponse( array $response, Lib\Entities\Service $service ) Prepare response for updated service.
 * @method static string prepareAfterServiceList( string $html, array $simple_services ) Render content after services forms.
 * @method static array updateService( array $alert, Lib\Entities\Service $service, array $parameters ) Update service settings in add-ons.
 */
abstract class Shared extends Lib\Base\Proxy
{

}