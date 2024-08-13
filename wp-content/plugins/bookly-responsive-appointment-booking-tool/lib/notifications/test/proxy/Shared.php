<?php
namespace Bookly\Lib\Notifications\Test\Proxy;

use Bookly\Lib;

/**
 * @method static void send( string $to_email, Lib\Entities\Notification $notification, $codes, $attachments, $reply_to, string $send_as, $from )
 */
abstract class Shared extends Lib\Base\Proxy
{

}