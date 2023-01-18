<?php
namespace Bookly\Backend\Modules\Notifications\Proxy;

use Bookly\Lib;

/**
 * @method static void saveSettings( array $parameters ) Save notifications settings
 * @method static string renderLogs() Render 'Email Logs' content
 * @method static void renderLogsSettings() Render 'Email Logs' settings
 * @method static void renderLogsTab( string $tab ) Render 'Email Logs' tab
 */
abstract class Pro extends Lib\Base\Proxy
{
}