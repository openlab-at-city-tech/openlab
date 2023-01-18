<?php
namespace Bookly\Backend\Modules\Appearance\Proxy;

use Bookly\Lib;

/**
 * Class RecurringAppointments
 *
 * @package Bookly\Backend\Modules\Appearance\Proxy
 * @method static void renderInfoMessage() Render editable info message in appearance.
 * @method static void renderRepeatStepSettings() Render settings on Repeat step.
 * @method static void renderShowStep() Render "Show Repeat step".
 * @method static void renderStep( string $progress_tracker ) Render Repeat step.
 */
abstract class RecurringAppointments extends Lib\Base\Proxy
{

}