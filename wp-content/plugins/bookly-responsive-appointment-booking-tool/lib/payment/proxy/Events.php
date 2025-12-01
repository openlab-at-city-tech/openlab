<?php
namespace Bookly\Lib\Payment\Proxy;

use Bookly\Lib as BooklyLib;

/**
 * @method static BooklyLib\Entities\Payment completeEventAttendee( BooklyLib\Entities\Payment $payment ) Create event attendee
 * @method static void redeemReservedAttendees( BooklyLib\Entities\Payment $payment ) Redeem reserved attendees.
 */
abstract class Events extends BooklyLib\Base\Proxy
{

}