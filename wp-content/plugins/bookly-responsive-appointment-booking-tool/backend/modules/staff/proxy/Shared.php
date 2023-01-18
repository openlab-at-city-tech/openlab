<?php
namespace Bookly\Backend\Modules\Staff\Proxy;

use Bookly\Lib;

/**
 * Class Shared
 * @package Bookly\Backend\Modules\Staff\Proxy
 *
 * @method static void   enqueueStaffProfileScripts() Enqueue scripts for page Staff.
 * @method static void   enqueueStaffProfileStyles() Enqueue styles for page Staff.
 * @method static string getAffectedAppointmentsFilter( string $filter_url, int[] $staff_ids ) Get link with filter for appointments page.
 * @method static Lib\Query prepareGetStaffQuery( Lib\Query $query ) Prepare get staff list query.
 * @method static array  renderStaffPage( array $params ) Do stuff on staff page render.
 * @method static array  searchStaff( array $fields, array $columns, Lib\Query $query ) Search staff, prepare query and fields.
 */
abstract class Shared extends Lib\Base\Proxy
{

}