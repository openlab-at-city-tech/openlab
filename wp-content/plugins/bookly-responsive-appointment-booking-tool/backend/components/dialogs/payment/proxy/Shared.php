<?php
namespace Bookly\Backend\Components\Dialogs\Payment\Proxy;

use Bookly\Lib;

/**
 * @method static array preparePaymentDetails( array $data, Lib\Entities\Payment $payment )
 * @method static array preparePaymentInfo( array $payment_info, $total )
 */
abstract class Shared extends Lib\Base\Proxy
{

}