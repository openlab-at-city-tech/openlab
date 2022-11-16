<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib as BooklyLib;

/**
 * Class Invoices
 * @package Bookly\Lib\Proxy
 *
 * @method static string|null getInvoice( BooklyLib\Entities\Payment $payment ) Return path to pdf file.
 * @method static void downloadInvoice( BooklyLib\Entities\Payment $payment ) Download pdf file
 */
abstract class Invoices extends BooklyLib\Base\Proxy
{
}