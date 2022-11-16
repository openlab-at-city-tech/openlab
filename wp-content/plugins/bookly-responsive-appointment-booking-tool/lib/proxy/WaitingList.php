<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * Class WaitingList
 * @package Bookly\Lib\Proxy
 *
 * @method static array handleParticipantsChange( array|bool $queue, Lib\Entities\Appointment $appointment ) Handle the change of participants of given appointment.
 * @method static array handleFreePlace( array|bool $queue, Lib\Entities\CustomerAppointment $ca ) Handle free places in appointment.
 * @method static array canUseFreePlace( Lib\Entities\CustomerAppointment $ca ) Handle free places in appointment.
 */
abstract class WaitingList extends Lib\Base\Proxy
{

}