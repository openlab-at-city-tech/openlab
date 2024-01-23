<?php
namespace Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;

use Bookly\Lib;

/**
 * @method static string getStaffSpecialDaysHtml( string $default, int $staff_id, int|null $location_id )
 * @method static array  getStaffSpecialDays( int $staff_id , int $location_id )
 */
abstract class SpecialDays extends Lib\Base\Proxy
{

}