<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * @method static \Bookly\Backend\Components\Dialogs\Queue\NotificationList|bool handleParticipantsChange( \Bookly\Backend\Components\Dialogs\Queue\NotificationList|bool $queue, Lib\Entities\Appointment $appointment ) Handle the change of participants of given appointment.
 * @method static \Bookly\Backend\Components\Dialogs\Queue\NotificationList|bool handleAppointmentFreePlace( \Bookly\Backend\Components\Dialogs\Queue\NotificationList|bool $queue, Lib\Entities\Appointment $appointment ) Handle free places in appointment.
 * @method static \Bookly\Backend\Components\Dialogs\Queue\NotificationList|bool handleFreePlace( \Bookly\Backend\Components\Dialogs\Queue\NotificationList|bool $queue, Lib\Entities\CustomerAppointment $ca ) Handle free places in customer appointment.
 * @method static array canUseFreePlace( Lib\Entities\CustomerAppointment $ca ) Handle free places in appointment.
 */
abstract class WaitingList extends Lib\Base\Proxy
{

}