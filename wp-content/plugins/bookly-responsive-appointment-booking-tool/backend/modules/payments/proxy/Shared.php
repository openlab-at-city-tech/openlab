<?php
namespace Bookly\Backend\Modules\Payments\Proxy;

use Bookly\Lib;

/**
 * Class Shared
 * @package Bookly\Backend\Modules\Payments\Proxy
 *
 * @method static bool paymentSpecificPriceExists( string $gateway ) Check whether specific price exists for given gateway.
 */
abstract class Shared extends Lib\Base\Proxy
{

}