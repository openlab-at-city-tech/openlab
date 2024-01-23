<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;
use BooklyPackages\Lib\Entities\Package;

/**
 * @method static void addBooklyMenuItem() Add 'Packages' to Bookly menu.
 * @method static void attachPackages( Lib\Entities\CustomerAppointment $ca, array $data, int $staff_id, int $location_id ) Attach appointment to package
 * @method static \DateTime|null getPackageExpireDate( int $package_id ) Get package expire date.
 * @method static Package[] getOrderPackages( int $order_id ) Get packages.
 * @method static void sendNotifications( Package $package ) Send notifications
 */
abstract class Packages extends Lib\Base\Proxy
{

}