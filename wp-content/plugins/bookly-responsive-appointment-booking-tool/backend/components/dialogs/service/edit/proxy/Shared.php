<?php
namespace Bookly\Backend\Components\Dialogs\Service\Edit\Proxy;

use Bookly\Lib;

/**
 * Class Shared
 * @package Bookly\Backend\Components\Dialogs\Service\Edit\Proxy
 *
 * @method static void  enqueueAssetsForServices() Enqueue assets for page Services.
 * @method static array prepareUpdateService( array $data ) Prepare update service settings in add-ons.
 * @method static array prepareUpdateServiceResponse( array $response, Lib\Entities\Service $service, array $_post ) Prepare response for updated service.
 * @method static string prepareAfterServiceList( string $html, array $service_collection ) Render content after services forms.
 * @method static array updateService( array $alert, Lib\Entities\Service $service, array $_post ) Update service settings in add-ons.
 */
abstract class Shared extends Lib\Base\Proxy
{

}