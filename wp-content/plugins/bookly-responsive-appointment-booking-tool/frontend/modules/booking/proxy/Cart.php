<?php
namespace Bookly\Frontend\Modules\Booking\Proxy;

use Bookly\Lib;

/**
 * @method static string getStepHtml( Lib\UserBookingData $userData, string $progress_tracker, string $info_text, bool $show_back_btn ) Get Cart step HTML.
 * @method static void renderButton() Render Cart button.
 */
abstract class Cart extends Lib\Base\Proxy
{

}