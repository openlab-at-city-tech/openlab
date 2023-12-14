<?php
namespace Bookly\Lib\Entities\Proxy;

use Bookly\Lib;

/**
 * @method static array postSaveCustomer( Lib\Entities\Customer $customer ) After saving the client
 * @method static array postDeleteCustomer( Lib\Entities\Customer $customer ) After deleting the client
 */
abstract class Shared extends Lib\Base\Proxy
{

}