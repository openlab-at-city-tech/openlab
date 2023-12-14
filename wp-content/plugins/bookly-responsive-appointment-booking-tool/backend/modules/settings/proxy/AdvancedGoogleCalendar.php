<?php
namespace Bookly\Backend\Modules\Settings\Proxy;

use Bookly\Lib;

/**
 * @method static array preSaveSettings( array $alert, array $params ) Pre-save Google Calendar settings.
 * @method static void  renderSettings() Render Advanced Google Calendar settings.
 */
abstract class AdvancedGoogleCalendar extends Lib\Base\Proxy
{

}