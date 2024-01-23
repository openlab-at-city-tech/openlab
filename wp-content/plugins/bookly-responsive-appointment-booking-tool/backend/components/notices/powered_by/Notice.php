<?php
namespace Bookly\Backend\Components\Notices\PoweredBy;

use Bookly\Lib;

class Notice extends Lib\Base\Component
{
    /**
     * Render show Powered by Bookly notice.
     */
    public static function render()
    {
        if ( Lib\Utils\Common::isCurrentUserAdmin()
            && ! get_option( 'bookly_gen_show_powered_by' )
            && ! get_user_meta( get_current_user_id(), 'bookly_dismiss_powered_by_notice', true )
        ) {
            self::enqueueStyles( array(
                'alias' => array( 'bookly-backend-globals', ),
            ) );
            self::enqueueScripts( array(
                'module' => array( 'js/powered-by.js' => array( 'bookly-backend-globals', ), ),
            ) );

            self::renderTemplate( 'powered_by' );
        }
    }
}