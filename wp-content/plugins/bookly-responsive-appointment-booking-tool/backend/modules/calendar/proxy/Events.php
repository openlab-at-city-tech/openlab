<?php
namespace Bookly\Backend\Modules\Calendar\Proxy;

use Bookly\Lib;

/**
 * @method static array buildEventsForCalendar( array $events, array $staff_members, \DateTime $start_date, \DateTime $end_date, array $location_ids )
 */
abstract class Events extends Lib\Base\Proxy
{

}