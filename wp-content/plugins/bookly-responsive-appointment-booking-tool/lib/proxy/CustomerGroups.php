<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * @method static void   addBooklyMenuItem() Add 'Customer Groups' to Bookly menu.
 * @method static Lib\Query applyFrontendServiceVisibility( Lib\Query $query )
 * @method static array  prepareDefaultAppointmentStatuses( array $statuses ) Get Default Appointment Status depending for all groups.
 * @method static string takeDefaultAppointmentStatus( string $status, int $group_id ) Get Default Appointment Status depending on group_id.
 */
abstract class CustomerGroups extends Lib\Base\Proxy
{

}