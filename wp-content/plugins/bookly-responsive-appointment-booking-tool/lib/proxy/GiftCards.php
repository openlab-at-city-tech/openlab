<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * Class GiftCards
 * @package Bookly\Lib\Proxy
 *
 * @method static void addBooklyMenuItem() Add 'Gift Cards' to Bookly menu.
 * @method static \BooklyGiftCards\Lib\Entities\GiftCard findOneByCode( string $code ) Return gift entity.
 */
abstract class GiftCards extends Lib\Base\Proxy
{

}