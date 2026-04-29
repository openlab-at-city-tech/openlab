<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * @method static void  deleteEvent( Lib\Entities\Appointment $appointment ) Delete Apple Calendar event for given appointment.
 * @method static array getBookings( array $staff_ids, Lib\Slots\DatePoint $dp ) Get bookings from Apple Calendar for Finder.
 * @method static void  reSync() Re-sync with Apple Calendar if 2-way sync is enabled.
 * @method static void  syncEvent( Lib\Entities\Appointment $appointment ) Synchronize Apple Calendar with appointment.
 */
abstract class AppleCalendar extends Lib\Base\Proxy
{

}
