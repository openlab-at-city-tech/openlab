<?php
namespace Bookly\Frontend\Modules\Booking\Proxy;

use Bookly\Lib;

/**
 * @method static string getStepHtml( Lib\UserBookingData $userData, bool $show_cart_btn, string $info_text, string $progress_tracker ) Render Repeat step.
 * @method static array buildSchedule( Lib\UserBookingData $userData, string $start_time, string $end_time, string $repeat, array $params, int[] $slots ) Build schedule with passed slots.
 * @method static bool canBeRepeated( bool $default, Lib\UserBookingData $userData ) Check if appointment can be repeated (Appointment from repeat appointment can't be repeated).
 * @method static void renderInfoMessage( Lib\UserBookingData $userData ) Render info message in booking steps.
 */
abstract class RecurringAppointments extends Lib\Base\Proxy
{

}