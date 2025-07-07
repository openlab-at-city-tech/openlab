<?php
namespace Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;

use Bookly\Lib;

/**
 * @method static void   enqueueAssets() Enqueue assets for staff edit dialog.
 * @method static void   renderArchivingComponents()
 * @method static void   renderCreateWPUser() Render option for creating WordPress user
 * @method static string getAdvancedHtml( Lib\Entities\Staff $staff, array $tpl_data, bool $for_backend ) Render Advanced settings.
 * @method static string renderGoogleCalendarsList( array $calendars, $selected_calendar_id ) Render calendars list of Google Calendar
 */
abstract class Pro extends Lib\Base\Proxy
{

}