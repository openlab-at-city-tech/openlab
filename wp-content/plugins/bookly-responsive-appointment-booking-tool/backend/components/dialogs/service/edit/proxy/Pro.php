<?php
namespace Bookly\Backend\Components\Dialogs\Service\Edit\Proxy;

use Bookly\Lib;

/**
 * Class Pro
 *
 * @package Bookly\Backend\Components\Service\Proxy
 *
 * @method static void renderPadding( array $service ) Render padding settings for service.
 * @method static void renderStaffPreference( array $service ) Render staff preference rules "any" is selected.
 * @method static void renderVisibility( array $service ) Render visibility option for service.
 * @method static void renderAdvancedTab() Render advanced tab.
 * @method static void renderWCTab() Render Woocommerce tab.
 * @method static string getAdvancedHtml( array $service, array $service_types, array $service_collection, array $staff_dropdown_data, array $categories_collection ) Render Advanced settings.
 * @method static string getWCHtml( array $service ) Render Woocommerce settings.
 */
abstract class Pro extends Lib\Base\Proxy
{

}