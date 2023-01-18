<?php

namespace Bookly\Frontend\Modules\ModernBookingForm\Proxy;

use Bookly\Lib;

/**
 * Class Shared
 *
 * @package Bookly\Frontend\Modules\ModernBookingForm\Proxy
 * @method static array prepareFormOptions( array $bookly_options ) Modify form options.
 * @method static array prepareAppearance( array $bookly_options ) Modify form options.
 * @method static void  renderForm( string $form_id ) Render form.
 * @method static void  validate( $request ) Validate request.
 */
abstract class Shared extends Lib\Base\Proxy
{

}