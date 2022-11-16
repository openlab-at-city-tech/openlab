<?php
namespace Bookly\Backend\Modules\Appearance\Proxy;

use Bookly\Lib;

/**
 * Class Cart
 * @package Bookly\Backend\Modules\Appearance\Proxy
 *
 * @method static void renderButton() Render Cart button.
 * @method static void renderCartStepSettings() Render settings on Cart step.
 * @method static void renderShowStep() Render "Show Cart step".
 * @method static void renderStep( string $progress_tracker ) Render Cart step.
 */
abstract class Cart extends Lib\Base\Proxy
{

}