<?php
namespace Bookly\Backend\Components\Dialogs\Appointment\Edit\Proxy;

use Bookly\Lib;

/**
 * @method static void createBackendPayment( Lib\Entities\Series $series, array $customer ) Create payment for series.
 * @method static void attachBackendPayment( Lib\Entities\Series $series, array $customer ) Attach payment for series.
 */
abstract class RecurringAppointments extends Lib\Base\Proxy
{

}