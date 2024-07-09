<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib\Base;

/**
 * @method static array getSchedule( array $staff_ids, \DateTime $start_date, \DateTime $end_date ) Get special days with breaks for given staff ids.
 * @method static array getServiceSchedule( int|array $service_id, \DateTime $start_date, \DateTime $end_date ) Get service schedule ( working time & breaks ).
 * @method static array getDaysAndTimes() Get days and times for the first step of booking.
 */
abstract class SpecialDays extends Base\Proxy
{

}