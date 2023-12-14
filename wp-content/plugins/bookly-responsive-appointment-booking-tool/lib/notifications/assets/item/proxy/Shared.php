<?php
namespace Bookly\Lib\Notifications\Assets\Item\Proxy;

use Bookly\Lib;
use Bookly\Lib\Notifications\Assets\Item;

/**
 * @method static void  prepareCodes( Item\Codes $codes ) Prepare codes data for new order item (translatable data should be set here).
 * @method static array prepareReplaceCodes( array $replace_codes, Item\Codes $codes, $format ) Prepare replacement codes for order item.
 */
abstract class Shared extends Lib\Base\Proxy
{

}