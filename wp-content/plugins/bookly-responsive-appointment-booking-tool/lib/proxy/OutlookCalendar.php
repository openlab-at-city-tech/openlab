<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * Class OutlookCalendar
 * @package Bookly\Lib\Proxy
 *
 * @method static void  deleteEvent( Lib\Entities\Appointment $appointment ) Delete Outlook Calendar event for given appointment.
 * @method static array getBookings( array $staff_ids, Lib\Slots\DatePoint $dp ) Get bookings from Outlook Calendar for Finder.
 * @method static void  reSync() Re-sync with Outlook Calendar if 2-way sync is enabled.
 * @method static void  syncEvent( Lib\Entities\Appointment $appointment ) Synchronize Outlook Calendar with appointment.
 */
abstract class OutlookCalendar extends Lib\Base\Proxy
{

}