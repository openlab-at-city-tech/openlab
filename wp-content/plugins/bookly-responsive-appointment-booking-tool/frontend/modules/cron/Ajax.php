<?php
namespace Bookly\Frontend\Modules\Cron;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'anonymous' );
    }

    /**
     * Get resources
     */
    public static function cloudCron()
    {
        Lib\Routines::doRoutine();

        wp_send_json_success();
    }

    public static function cloudCronTest()
    {
        wp_send_json_success();
    }

    /**
     * @inheritDoc
     */
    protected static function hasAccess( $action )
    {
        if ( $action === 'cloudCron' ) {
            $api_key = get_option( 'bookly_cloud_cron_api_key' );
            if ( ! $api_key || self::parameter( 'api_key' ) !== $api_key ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Override parent method to exclude actions from CSRF token verification.
     *
     * @param string $action
     * @return bool
     */
    protected static function csrfTokenValid( $action = null )
    {
        return true;
    }
}