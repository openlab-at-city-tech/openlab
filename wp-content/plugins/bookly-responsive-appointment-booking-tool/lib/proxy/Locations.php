<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;
use BooklyLocations\Lib\Entities\Location;

/**
 * Class Locations
 *
 * @package Bookly\Lib\Proxy
 * @method static void            addBooklyMenuItem() Add 'Locations' to Bookly menu.
 * @method static Lib\Slots\Staff addServices( Lib\Slots\Staff $staff, int $staff_id, int $service_id )
 * @method static Location|false  findById( int $location_id ) Find location by id
 * @method static Location[]      findByStaffId( int $staff_id ) Find locations by staff id.
 * @method static array           prepareLocationsForCombinedServices( array $locations, array $services ) Prepare Location Ids for combined services.
 * @method static int             prepareStaffLocationId( int $location_id, int $staff_id ) Prepare StaffService Location Id.
 * @method static int             prepareStaffScheduleLocationId( int $location_id, int $staff_id ) Prepare StaffService Location Id.
 * @method static int             prepareStaffSpecialDaysLocationId( int $location_id, int $staff_id ) Prepare StaffSpecialDays Location Id.
 * @method static Lib\Query       prepareStaffServiceQuery( Lib\Query $query ) Prepare StaffService Query.
 * @method static Lib\Query       prepareAppointmentsQuery( Lib\Query $query ) Prepare appointments Query.
 * @method static Lib\Query       prepareWorkingSchedule( Lib\Query $working_schedule, array $staff_ids ) Prepare working schedule for Finder
 * @method static Lib\Query       prepareSpecialDays( Lib\Query $special_days, array $staff_ids, \DateTime $start_date, \DateTime $end_date ) Prepare special days for Finder
 * @method static bool            servicesPerLocationAllowed() Get allow-services-per-location option.
 * @method static array           beforeDelete( array $location_ids ) Before deleting locations
 */
abstract class Locations extends Lib\Base\Proxy
{

}