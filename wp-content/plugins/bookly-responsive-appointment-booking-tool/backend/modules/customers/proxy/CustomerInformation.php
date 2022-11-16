<?php
namespace Bookly\Backend\Modules\Customers\Proxy;

use Bookly\Lib;

/**
 * Class CustomerInformation
 * @package Bookly\Backend\Modules\Customers\Proxy
 *
 * @method static array prepareCustomerListData( array $customer_data, array $row ) Prepare customer info fields for customers list.
 */
abstract class CustomerInformation extends Lib\Base\Proxy
{

}