<?php
namespace Bookly\Backend\Components\Notices\Nps;

use Bookly\Lib;
use Bookly\Backend\Modules;
use Bookly\Backend\Components\Notices\Rate;

/**
 * Class Notice
 * @package Bookly\Backend\Components\Notices\Nps
 */
class Notice extends Lib\Base\Component
{
    /**
     * Render Net Promoter Score notice.
     */
    public static function render()
    {
        if ( Lib\Utils\Common::isCurrentUserAdmin() ) {
            $dismiss_value = (int) get_user_meta( get_current_user_id(), 'bookly_dismiss_nps_notice', true );
            // Show notice 1 month after it was closed the last time.
            if ( ! $dismiss_value || $dismiss_value > 1 && time() - $dismiss_value >= 30 * DAY_IN_SECONDS ) {
                // Show notice 1 month after installation time.
                if ( time() - Lib\Plugin::getInstallationTime() >= 30 * DAY_IN_SECONDS ) {
                    self::enqueueStyles( array(
                        'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
                    ) );

                    self::enqueueScripts( array(
                        'module' => array( 'js/nps.js' => array( 'bookly-backend-globals', ), ),
                    ) );

                    self::renderTemplate( 'nps', array( 'current_user' => wp_get_current_user() ) );

                    Rate\Notice::create( 'bookly-js-rate-bookly' )->render();
                }
            } elseif ( $dismiss_value === 1 && (int) get_user_meta( get_current_user_id(), 'bookly_nps_rate', true ) > 0 ) {
                $hidden_until = (int) get_user_meta( get_current_user_id(), 'bookly_notice_rate_on_wp_hide_until', true );
                if ( ( $hidden_until != -1 ) && ( time() > $hidden_until ) ) {
                    Rate\Notice::create( 'bookly-js-rate-bookly' )->render();
                }
            }
        }
    }
}