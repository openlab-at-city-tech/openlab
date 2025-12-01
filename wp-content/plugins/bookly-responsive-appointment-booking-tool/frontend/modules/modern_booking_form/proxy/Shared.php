<?php
namespace Bookly\Frontend\Modules\ModernBookingForm\Proxy;

use Bookly\Lib;
use BooklyPro\Frontend\Modules\ModernBookingForm\Lib\Request;

/**
 * @method static array prepareFormOptions( array $bookly_options ) Modify form options.
 * @method static array prepareAppearance( array $bookly_options ) Modify form options.
 * @method static array prepareAppearanceData( array $bookly_options ) Modify appearance data.
 * @method static array prepareBookingResults( array $data, Lib\Entities\Order $order ) Modify booking results data.
 * @method static void  renderForm( string $form_id ) Render form.
 * @method static void  pendingPaymentCreated( \Bookly\Frontend\Modules\Payment\Request $request ) Pending payment created.
 * @method static void  processNullGateway( \Bookly\Frontend\Modules\Payment\Request $request ) Process null gateway.
 * @method static void  validate( Request $request ) Validate request.
 */
abstract class Shared extends Lib\Base\Proxy
{

}