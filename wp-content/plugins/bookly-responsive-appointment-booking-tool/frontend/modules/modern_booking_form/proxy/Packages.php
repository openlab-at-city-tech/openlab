<?php

namespace Bookly\Frontend\Modules\ModernBookingForm\Proxy;

use Bookly\Lib as BooklyLib;
use BooklyPro\Frontend\Modules\ModernBookingForm\Lib\Request;

/**
 * Class Packages
 *
 * @package Bookly\Frontend\Modules\ModernBookingForm\Proxy
 * @method static \BooklyPackages\Lib\Entities\Package createPackage( Request $request, BooklyLib\Entities\Payment|null $payment ) Create package
 * @method static void sendNotifications( \BooklyPackages\Lib\Entities\Package $package ) Send notifications
 * @method static void deleteCascade( BooklyLib\Entities\Payment $payment ) Delete package associated with payment
 */
abstract class Packages extends BooklyLib\Base\Proxy
{

}