<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib\Base;

/**
 * Class DepositPayments
 * Invoke local methods from Deposit Payments add-on.
 *
 * @package Bookly\Lib\Proxy
 *
 * @method static string formatDeposit( double $deposit_amount, string $deposit ) Return formatted deposit amount
 * @method static double|string prepareAmount( float $amount, string $deposit, int $number_of_persons ) Return deposit amount for all persons
 */
abstract class DepositPayments extends Base\Proxy
{

}