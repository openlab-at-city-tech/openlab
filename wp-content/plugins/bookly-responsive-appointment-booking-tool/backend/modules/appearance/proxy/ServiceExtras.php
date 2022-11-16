<?php
namespace Bookly\Backend\Modules\Appearance\Proxy;

use Bookly\Lib;

/**
 * Class ServiceExtras
 * @package Bookly\Backend\Modules\Appearance\Proxy
 *
 * @method static void renderCartExtras() Render extras on Cart step.
 * @method static void renderShowCartExtras() Render "Show extras" on Cart step.
 * @method static void renderShowStep() Render "Show Extras step".
 * @method static void renderStep( string $progress_tracker ) Render Extras step.
 * @method static void renderStepSettings() render a checkboxes "Show title, duration, price and etc."
 */
abstract class ServiceExtras extends Lib\Base\Proxy
{

}