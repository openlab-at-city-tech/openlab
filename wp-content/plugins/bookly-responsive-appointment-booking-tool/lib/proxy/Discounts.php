<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * Class Discounts
 * @package Bookly\Lib\Proxy
 *
 * @method static void addBooklyMenuItem() Add 'Discounts' to Bookly menu.
 * @method static float prepareServicePrice( float $price, int $service_id, int $nop )  Prepare total price of a service with given original service price, number of persons.
 */
abstract class Discounts extends Lib\Base\Proxy
{

}