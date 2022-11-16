<?php
namespace Bookly\Backend\Components\Notices\Lite;

use Bookly\Lib;

/**
 * Class Notice
 * @package Bookly\Backend\Components\Notices\Lite
 */
class Notice extends Lib\Base\Component
{
    /**
     * Render subscribe notice.
     */
    public static function render()
    {
        if ( Lib\Utils\Common::isCurrentUserAdmin() &&
            get_user_meta( get_current_user_id(), 'bookly_show_lite_rebranding_notice', true ) ) {

            self::enqueueStyles( array(
                'alias' => array( 'bookly-backend-globals', ),
            ) );

            self::enqueueScripts( array(
                'module' => array( 'js/lite-rebranding.js' => array( 'bookly-backend-globals' ), ),
            ) );

            self::renderTemplate( 'lite_rebranding' );
        }
    }
}