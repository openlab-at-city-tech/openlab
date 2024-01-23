<?php
namespace Bookly\Backend\Modules\Appearance\Proxy;

use Bookly\Lib;

/**
 * @method static array prepareCodes( array $codes ) Alter array of codes to be displayed in Bookly Appearance.
 * @method static string prepareGatewayTitle( string $title, string $gateway ) Get payment system title.
 * @method static array prepareOptions( array $options_to_save, array $options ) Alter array of options to be saved in Bookly Appearance.
 * @method static array paymentGateways( array $data ) get payment gateways data for rendering.
 * @method static int   renderServiceStepSettings() Render checkbox settings.
 * @method static int   renderTimeStepSettings() Render checkbox settings.
 */
abstract class Shared extends Lib\Base\Proxy
{

}