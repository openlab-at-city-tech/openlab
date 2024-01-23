<?php
namespace Bookly\Backend\Modules\Appearance\Proxy;

use Bookly\Lib;

/**
 * @method static void renderStepCompleteOption() render option for customers, which skip step payment
 * @method static void renderStepCompleteInfo() render info text for customers, which skip step payment
 */
abstract class CustomerGroups extends Lib\Base\Proxy
{
}