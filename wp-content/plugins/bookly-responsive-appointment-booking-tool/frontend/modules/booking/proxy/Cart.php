<?php
namespace Bookly\Frontend\Modules\Booking\Proxy;

use Bookly\Lib;

/**
 * Class Cart
 * @package Bookly\Frontend\Modules\Booking\Proxy
 *
 * @method static string getStepHtml( Lib\UserBookingData $userData, string $progress_tracker, string $info_text, bool $show_back_btn = true ) Get Cart step HTML.
 * @method static void renderButton() Render Cart button.
 */
abstract class Cart extends Lib\Base\Proxy
{

}