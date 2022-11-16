<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * Class CustomerGroups
 * @package Bookly\Lib\Proxy
 *
 * @method static void   addBooklyMenuItem() Add 'Customer Groups' to Bookly menu.
 * @method static bool   getSkipPayment( Lib\Entities\Customer $customer ) Check if customer should skip payment step.
 * @method static float  prepareCartTotalPrice( float $total, Lib\UserBookingData $userData ) Prepare total price depending on group discount.
 * @method static array  prepareDefaultAppointmentStatuses( array $statuses ) Get Default Appointment Status depending for all groups.
 * @method static string takeDefaultAppointmentStatus( string $status, int $group_id ) Get Default Appointment Status depending on group_id.
 */
abstract class CustomerGroups extends Lib\Base\Proxy
{

}