<?php
namespace Bookly\Backend\Components\Notices\Subscribe;

use Bookly\Lib;

class Notice extends Lib\Base\Component
{
    /**
     * Render subscribe notice.
     */
    public static function render()
    {
        if ( Lib\Utils\Common::isCurrentUserAdmin() &&
            ! get_user_meta( get_current_user_id(), Lib\Plugin::getPrefix() . 'dismiss_subscribe_notice', true ) ) {

            // Show notice 1 day after installation time.
            if ( time() - Lib\Plugin::getInstallationTime() >= DAY_IN_SECONDS ) {
                self::enqueueStyles( array(
                    'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
                ) );
                self::enqueueScripts( array(
                    'module' => array( 'js/subscribe.js' => array( 'bookly-backend-globals' ), ),
                ) );

                self::renderTemplate( 'subscribe' );
            }
        }
    }
}