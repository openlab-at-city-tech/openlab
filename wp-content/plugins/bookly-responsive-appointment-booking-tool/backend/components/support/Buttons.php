<?php
namespace Bookly\Backend\Components\Support;

use Bookly\Lib;
use Bookly\Backend\Modules;
use Bookly\Backend\Components\Notices;
use Bookly\Backend\Components\Support\Lib\Urls;

/**
 * Class Buttons
 * @package Bookly\Backend\Components\Support
 */
class Buttons extends Lib\Base\Component
{
    /**
     * Render support buttons.
     *
     * @param string $page_slug
     */
    public static function render( $page_slug )
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/support.js' => array( 'bookly-backend-globals', ), ),
        ) );

        wp_localize_script( 'bookly-support.js', 'BooklySupportL10n', array(
            'csrfToken' => Lib\Utils\Common::getCsrfToken(),
            'featuresRequestUrl' => Lib\Utils\Common::prepareUrlReferrers( Urls::FEATURES_REQUEST_PAGE, 'notification_bar' ),
        ) );

        $days_in_use = (int) ( ( time() - Lib\Plugin::getInstallationTime() ) / DAY_IN_SECONDS );

        // Whether to show contact us notice or not.
        $show_contact_us_notice = $days_in_use < 7 &&
            ! get_user_meta( get_current_user_id(), Lib\Plugin::getPrefix() . 'dismiss_contact_us_notice', true ) &&
            ! Notices\Statistic\Notice::needShowCollectStatNotice();

        // Whether to show feedback notice.
        $show_feedback_notice = $days_in_use >= 7 &&
            ! get_user_meta( get_current_user_id(), Lib\Plugin::getPrefix() . 'dismiss_feedback_notice', true ) &&
            ! get_user_meta( get_current_user_id(), Lib\Plugin::getPrefix() . 'contact_us_btn_clicked', true );

        $current_user = wp_get_current_user();

        $demo_links = array();

        if ( ! Lib\Config::proActive() ) {
            // Empty key for page bookly-settings
            $demo_links = array( '' => 'https://www.booking-wp-plugin.com/demo/full/wp-admin/admin.php?page=bookly-settings' );
            foreach ( array( 'calendar', 'appointments', 'staff', 'services', 'customers', 'notifications', 'payments', 'appearance' ) as $slug ) {
                $demo_links[ 'bookly-' . $slug ] = 'https://www.booking-wp-plugin.com/demo/full/wp-admin/admin.php?page=bookly-' . $slug;
            }
        }

        static::renderTemplate( 'buttons', compact(
            'current_user',
            'demo_links',
            'page_slug',
            'show_contact_us_notice',
            'show_feedback_notice'
        ) );
    }
}