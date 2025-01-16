<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;
use Bookly\Backend\Components\Dialogs\Queue\NotificationList;

/**
 * @method static bool hideChildAppointments( bool $default, Lib\CartItem $cart_item ) If only first appointment in series needs to be paid hide next appointments.
 * @method static bool notifyStaffAndAdmins( bool $sent, Lib\Entities\Staff $staff, Lib\Entities\Notification $notification, Lib\Notifications\Assets\Base\Codes $codes, Lib\Notifications\Assets\Base\Attachments $attachments, $reply_to, NotificationList|null $queue = null )
 */
abstract class RecurringAppointments extends Lib\Base\Proxy
{

}