<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib\Base;
use BooklyEvents\Lib\Entities;

/**
 * @method static void addBooklyMenuItem() Add 'Events' to a Bookly menu.
 * @method static array getIcsFromOrder( \Bookly\Lib\Entities\Order $order ) Get ICS from order.
 * @method static array getList( array $staff_ids, \DateTime $start_date, \DateTime $end_date ) Get a list of events data.
 * @method static sendNotifications( \Bookly\Lib\DataHolders\Booking\Order $order )
 * @method static Entities\EventTicketType getTicketType( $event_ticket_type_id )
 * @method static Entities\Event getEvent( $event_id )
 */
abstract class Events extends Base\Proxy
{

}