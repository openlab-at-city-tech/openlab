<?php
namespace Bookly\Backend\Modules\Notifications\Proxy;

use Bookly\Lib;

/**
 * @method static array buildNotificationCodesList( array $codes, string $notification_type, array $codes_data ) Build array of codes to be displayed in notification template.
 * @method static array prepareNotificationCodes( array $codes, string $type ) Alter codes for displaying in notification templates.
 * @method static array enqueueAssets() Enqueue assets.
 */
abstract class Shared extends Lib\Base\Proxy
{

}