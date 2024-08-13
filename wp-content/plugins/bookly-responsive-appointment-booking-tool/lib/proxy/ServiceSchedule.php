<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * @method static array getSchedule( int $service_id ) Get schedule for service.
 * @method static array getWeeklySchedule( array $service_ids ) Get schedule for services.
 */
abstract class ServiceSchedule extends Lib\Base\Proxy
{

}