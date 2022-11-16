<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;
use BooklyCustomStatuses\Lib\Entities\CustomStatus;

/**
 * Class CustomStatuses
 * @package Bookly\Lib\Proxy
 *
 * @method static CustomStatus[] getAll() Get all custom statuses.
 * @method static array prepareAllStatuses( array $statuses ) Prepare all available statuses.
 * @method static array prepareBusyStatuses( array $statuses ) Prepare statuses which are considered busy.
 * @method static array prepareFreeStatuses( array $statuses ) Prepare statuses which are considered free.
 * @method static string statusToString( string $status ) Convert status to human readable string.
 * @method static string statusToIcon( string $status ) Convert status to CSS class for icon
 */
abstract class CustomStatuses extends Lib\Base\Proxy
{

}