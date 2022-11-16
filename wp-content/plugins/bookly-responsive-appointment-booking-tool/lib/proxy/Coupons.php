<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * Class Coupons
 * @package Bookly\Lib\Proxy
 *
 * @method static void addBooklyMenuItem() Add 'Coupons' to Bookly menu.
 * @method static \BooklyCoupons\Lib\Entities\Coupon findOneByCode( string $code ) Return coupon entity.
 */
abstract class Coupons extends Lib\Base\Proxy
{

}