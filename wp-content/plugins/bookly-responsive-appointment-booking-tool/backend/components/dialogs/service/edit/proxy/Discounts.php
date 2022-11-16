<?php
namespace Bookly\Backend\Components\Dialogs\Service\Edit\Proxy;

use Bookly\Lib;

/**
 * Class Discounts
 * @package Bookly\Backend\Components\Dialogs\Service\Edit\Proxy
 *
 * @method static array getDiscounts( int $service_id ) Get discounts list.
 * @method static void renderSubForm( array $service ) Render discounts sub-form.
 */
abstract class Discounts extends Lib\Base\Proxy
{

}