<?php
namespace Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;

use Bookly\Lib;

/**
 * @method static array editStaffAdvanced( array $data, Lib\Entities\Staff $staff ) Prepare edit staff form.
 * @method static array preUpdateStaffAdvanced( array $data, Lib\Entities\Staff $staff, array $parameters ) Do stuff before staff update.
 * @method static void  renderStaffDetails( Lib\Entities\Staff $staff ) Render Details tab of staff edit form.
 * @method static array preUpdateStaffDetails( array $data, Lib\Entities\Staff $staff, array $parameters ) Update staff settings in add-ons.
 * @method static void  updateStaffDetails( Lib\Entities\Staff $staff, array $parameters ) Update staff settings in add-ons.
 * @method static array updateStaffAdvanced( array $data, Lib\Entities\Staff $staff, array $params ) Update staff settings in add-ons.
 * @method static void  updateStaffSchedule( array $_post ) Update staff schedule settings in add-ons.
 * @method static void  updateStaffSpecialDays( array $_post ) Update staff special days settings in add-ons.
 * @method static void  updateStaffServices( array $_post ) Update staff services settings in add-ons.
 * @method static void  renderStaffServiceLabels() Render column header for controls on Services tab.
 * @method static void  renderStaffService( int $staff_id, Lib\Entities\Service $service, array $services_data, array $attributes = array() ) Render controls for staff on Services tab.
 * @method static void  renderStaffServiceTail( int $staff_id, Lib\Entities\Service $service, int $location_id, $attributes = array() ) Render controls for Staff on tab services.
 * @method static void  renderStaffTab() Render staff tab.
 */
abstract class Shared extends Lib\Base\Proxy
{

}