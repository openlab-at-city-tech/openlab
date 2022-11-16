<?php
namespace Bookly\Backend\Components\Dialogs\Appointment\Edit\Proxy;

use Bookly\Lib;

/**
 * Class ServiceSchedule
 * @package Bookly\Backend\Components\Dialogs\Appointment\Edit\Proxy
 *
 * @method static array checkAppointmentErrors( array $result, $start_date, $end_date, $service_id, $service_duration ) Check whether appointment settings produce errors
 */
abstract class ServiceSchedule extends Lib\Base\Proxy
{

}