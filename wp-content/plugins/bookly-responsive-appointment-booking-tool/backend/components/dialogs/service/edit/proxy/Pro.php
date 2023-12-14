<?php
namespace Bookly\Backend\Components\Dialogs\Service\Edit\Proxy;

use Bookly\Lib;

/**
 * @method static void renderPadding( array $service ) Render padding settings for service.
 * @method static void renderStaffPreference( array $service ) Render staff preference rules "any" is selected.
 * @method static void renderGatewayPreference( array $service ) Render gateway preference rules.
 * @method static void renderVisibility( array $service ) Render visibility option for service.
 * @method static void renderAdvancedTab() Render advanced tab.
 * @method static void renderWCTab() Render Woocommerce tab.
 * @method static string getAdvancedHtml( array $service ) Render Advanced settings.
 * @method static string getWCHtml( array $service ) Render Woocommerce settings.
 */
abstract class Pro extends Lib\Base\Proxy
{

}