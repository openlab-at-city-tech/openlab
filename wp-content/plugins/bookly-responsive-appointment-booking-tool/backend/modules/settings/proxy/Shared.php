<?php
namespace Bookly\Backend\Modules\Settings\Proxy;

use Bookly\Lib;

/**
 * Class Shared
 * @package Bookly\Backend\Modules\Settings\Proxy
 *
 * @method static void  enqueueAssets() Enqueue assets for Settings page.
 * @method static array prepareCalendarAppointmentCodes( array $codes, string $participants ) Prepare codes for appointment description displayed in calendar.
 * @method static array prepareCodes( array $codes, string $section )
 * @method static array preparePaymentGatewaySettings( array $payment_data ) Prepare payment gateway settings.
 * @method static void  renderMenuItem() Render tab in settings page.
 * @method static void  renderTab() Render add-on settings form.
 * @method static void  renderUrlSettings() Render URL settings on Settings page.
 * @method static array saveSettings( array $alert, string $tab, array $params ) Save add-on settings.
 */
abstract class Shared extends Lib\Base\Proxy
{

}