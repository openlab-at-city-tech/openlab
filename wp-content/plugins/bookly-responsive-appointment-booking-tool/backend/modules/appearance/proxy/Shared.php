<?php
namespace Bookly\Backend\Modules\Appearance\Proxy;

use Bookly\Lib;

/**
 * Class Shared
 * @package Bookly\Backend\Modules\Appearance\Proxy
 *
 * @method static array prepareCodes( array $codes ) Alter array of codes to be displayed in Bookly Appearance.
 * @method static array prepareOptions( array $options_to_save, array $options ) Alter array of options to be saved in Bookly Appearance.
 * @method static array paymentGateways( array $data ) get payment gateways data for rendering.
 * @method static void  renderGatewayOptions( string $slug ) Render gateway custom options.
 * @method static int   renderServiceStepSettings() Render checkbox settings.
 * @method static int   renderTimeStepSettings() Render checkbox settings.
 */
abstract class Shared extends Lib\Base\Proxy
{

}