<?php
namespace Bookly\Lib\Cloud\Proxy;

use Bookly\Lib;

/**
 * @method static Lib\Cloud\Product getProduct( string $slug, Lib\Cloud\API $api ) get Product
 * @method static void renderCloudMenu( array $product )
 */
abstract class Shared extends Lib\Base\Proxy
{

}