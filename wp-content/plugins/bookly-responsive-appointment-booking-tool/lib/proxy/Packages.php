<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * Class Packages
 *
 * @package Bookly\Lib\Proxy
 * @method static void addBooklyMenuItem() Add 'Packages' to Bookly menu.
 * @method static \DateTime|null getPackageExpireDate( int $package_id ) Get package expire date.
 * @method static array preparePaymentDetails( array $details, Lib\Entities\Payment $payment ) Add info about payment
 */
abstract class Packages extends Lib\Base\Proxy
{

}