<?php
namespace Bookly\Backend\Components\TinyMce\Proxy;

use Bookly\Lib;

/**
 * @method static void renderMediaButtons( string $version ) Add buttons to WordPress editor.
 * @method static void renderBooklyFormFields() Render controls in popup for bookly-form (build shortcode).
 * @method static void renderBooklyFormHead() Render controls in header of popup for bookly-form (build shortcode).
 * @method static void renderPopup() Render popup windows for WordPress editor.
 */
abstract class Shared extends Lib\Base\Proxy
{

}