<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;
use BooklyAdvancedGoogleCalendar\Lib\Google\Calendar;
use BooklyPro\Lib\Google\Client;

/**
 * Class AdvancedGoogleCalendar
 * @package Bookly\Lib\Proxy
 *
 * @method static Calendar createApiCalendar( Client $client ) Create new instance of Calendar.
 * @method static void reSync() Re-sync with GC if 2-way sync is enabled.
 */
abstract class AdvancedGoogleCalendar extends Lib\Base\Proxy
{

}