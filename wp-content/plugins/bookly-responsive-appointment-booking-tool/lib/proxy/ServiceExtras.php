<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * @method static bool  considerDuration( $default = null ) Consider extras length in appointment duration.
 * @method static \BooklyServiceExtras\Lib\Entities\ServiceExtra[] findByIds( array $extras_ids ) Return extras entities.
 * @method static \BooklyServiceExtras\Lib\Entities\ServiceExtra[] findByServiceId( int $service_id ) Return extras entities.
 * @method static \BooklyServiceExtras\Lib\Entities\ServiceExtra[] findAll() Return all extras entities.
 * @method static array getCAInfo( $ca_id, bool $translate, string $locale = null ) Get extras data for given customer appointment.
 * @method static array getInfo( array $extras, bool $translate, string $locale = null ) Get extras data for given json data of appointment.
 * @method static float getTax( float $price, int $nop, float $rate ) Get tax for extras.
 * @method static int   getTotalDuration( array $extras ) Get total duration of given extras.
 * @method static float getTotalPrice( array $extras, int $nop ) Get total price if given extras.
 * @method static float prepareServicePrice( $default, $service_price, $nop, array $extras ) Prepare total price of a service with given original service price, number of persons and set of extras.
 */
abstract class ServiceExtras extends Lib\Base\Proxy
{

}