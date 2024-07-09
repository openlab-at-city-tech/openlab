<?php
namespace Bookly\Backend\Modules\Calendar\Proxy;

use Bookly\Lib;

/**
 * @method static array prepareAppointmentCodesData( array $codes, array $appointment_data, string $participants ) Prepare codes data for appointment description displayed in calendar.
 * @method static void  prepareAppointmentsQueryForCalendar( Lib\Query $query, \DateTime $start_date, \DateTime $end_date, array $location_ids ) Prepare appointments query for full calendar
 * @method static Lib\Query prepareCalendarQuery( Lib\Query $query )
 * @method static void  renderAddOnsComponents() Render components on calendar page.
 */
abstract class Shared extends Lib\Base\Proxy
{

}