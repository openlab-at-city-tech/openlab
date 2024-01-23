<?php
namespace Bookly\Lib\Notifications\Assets\Item\Proxy;

use Bookly\Lib;
use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Item\ICS;

/**
 * @method static ICS createICS( Codes $codes, string $recipient ) Create ICS object.
 */
abstract class RecurringAppointments extends Lib\Base\Proxy
{

}