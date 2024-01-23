<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * @method static bool hideChildAppointments( bool $default, Lib\CartItem $cart_item ) If only first appointment in series needs to be paid hide next appointments.
 */
abstract class RecurringAppointments extends Lib\Base\Proxy
{

}