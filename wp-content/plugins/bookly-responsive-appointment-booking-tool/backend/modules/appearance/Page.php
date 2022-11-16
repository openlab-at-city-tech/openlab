<?php
namespace Bookly\Backend\Modules\Appearance;

use Bookly\Lib;


/**
 * Class Page
 *
 * @package Bookly\Backend\Modules\Appearance
 */
class Page extends Lib\Base\Component
{
    public static function render()
    {
        if ( self::hasParameter( 'bookly-form' ) || ! Lib\Config::proActive() ) {
            BooklyForm::render();
        } else {
            Proxy\Pro::renderModernAppearance();
        }
    }
}