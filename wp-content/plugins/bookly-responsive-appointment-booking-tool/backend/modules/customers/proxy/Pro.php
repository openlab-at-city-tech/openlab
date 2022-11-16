<?php
namespace Bookly\Backend\Modules\Customers\Proxy;

use Bookly\Lib;

/**
 * Class Pro
 * @package Bookly\Backend\Modules\Customers\Proxy
 *
 * @method static void importCustomers() Import customers from CSV.
 * @method static void renderImportButton() Render import button.
 * @method static void renderExportButton() Render export button.
 * @method static void renderImportDialog() Render import dialog.
 * @method static void renderExportDialog( array $settings, array $columns ) Render export dialog.
 */
abstract class Pro extends Lib\Base\Proxy
{

}