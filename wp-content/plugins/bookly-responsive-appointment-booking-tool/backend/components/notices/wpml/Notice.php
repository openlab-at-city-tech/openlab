<?php
namespace Bookly\Backend\Components\Notices\Wpml;

use Bookly\Lib;

/**
 * Class Notice
 * @package Bookly\Backend\Components\Notices\Wpml
 */
class Notice extends Lib\Base\Component
{
    /**
     * Render notice.
     */
    public static function render()
    {
        if ( Lib\Utils\Common::isCurrentUserAdmin() && get_option( 'bookly_show_wpml_resave_required_notice' ) ) {

            self::enqueueStyles( array(
                'alias' => array( 'bookly-backend-globals', ),
            ) );

            self::enqueueScripts( array(
                'module' => array( 'js/wpml-notice.js' => array( 'bookly-backend-globals' ), ),
            ) );

            self::renderTemplate( 'resave' );
        }
    }
}