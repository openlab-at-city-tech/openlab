<?php
namespace Bookly\Lib\Notifications\Assets\Order\Proxy;

use Bookly\Lib;
use Bookly\Lib\Notifications\Assets\Order;

/**
 * Class Shared
 * @package Bookly\Lib\Notifications\Assets\Order\Proxy
 *
 * @method static void  prepareCodes( Order\Codes $codes ) Prepare codes data for order.
 * @method static array prepareReplaceCodes( array $replace_codes, Order\Codes $codes, $format ) Prepare replacement codes for order.
 */
abstract class Shared extends Lib\Base\Proxy
{

}