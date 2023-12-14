<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;
use Bookly\Lib\CartItem;

/**
 * @method static void  addBooklyMenuItem() Add 'Taxes' to Bookly menu.
 * @method static float getItemTaxAmount( CartItem $cart_item ) Get amount of tax.
 * @method static float getServiceTaxAmount( CartItem $cart_item  ) Get amount of tax for services without extras
 * @method static float calculateTax( float $amount, float $rate )
 * @method static array prepareTaxRateAmounts( array $amounts, CartItem $cart_item, bool $allow_coupon ) Filling up array (%tax, deposit value, service price, etc.) for each service provided for consequent calculation of tax amount.
 * @method static array getServiceTaxRates() Get rate for all services.
 * @method static bool  showTaxColumn() Show tax column in code {cart_info[_c]} when the tax amount is excluded from price.
 */
abstract class Taxes extends Lib\Base\Proxy
{

}