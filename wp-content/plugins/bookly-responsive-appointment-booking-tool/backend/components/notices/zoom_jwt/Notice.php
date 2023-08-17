<?php
namespace Bookly\Backend\Components\Notices\ZoomJwt;

use Bookly\Lib;

/**
 * Class Notice
 *
 * @package Bookly\Backend\Components\Notices\ZoomJwt
 */
class Notice extends Lib\Base\Component
{
    /**
     * Render collect stats notice.
     */
    public static function render()
    {
        if ( self::needShowNotice() ) {
            self::enqueueStyles( array(
                'alias' => array( 'bookly-backend-globals', ),
            ) );
            self::enqueueScripts( array(
                'module' => array( 'js/zoom-jwt.js' => array( 'bookly-backend-globals' ), ),
            ) );

            self::renderTemplate( 'notice' );
        }
    }

    /**
     * @return bool
     */
    public static function needShowNotice()
    {
        if ( Lib\Utils\Common::isCurrentUserAdmin() && Lib\Config::proActive() && get_option( 'bookly_zoom_authentication' ) === 'jwt' && get_option( 'bookly_zoom_jwt_api_key' ) ) {
            $user_id = get_current_user_id();
            if ( ! get_user_meta( $user_id, 'bookly_dismiss_zoom_jwt_notice', true ) ) {
                return true;
            }
        }

        return false;
    }
}