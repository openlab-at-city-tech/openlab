<?php
namespace Bookly\Backend\Components\Dashboard\Appointments;

use Bookly\Lib;

class Widget extends Lib\Base\Component
{
    public static function init()
    {
        $current_user = wp_get_current_user();

        if ( $current_user && $current_user->has_cap( Lib\Utils\Common::getRequiredCapability() ) ) {
            $class = __CLASS__;
            add_action( 'wp_dashboard_setup', function () use ( $class ) {
                wp_add_dashboard_widget( strtolower( str_replace( '\\', '-', $class ) ), 'Bookly - ' . __( 'Appointments', 'bookly' ), array( $class, 'renderWidget' ) );
            } );
        }
    }

    /**
     * Render widget on WordPress dashboard.
     */
    public static function renderWidget()
    {
        self::enqueueAssets();
        self::renderTemplate( 'widget' );
    }

    /**
     * Render on Bookly/Dashboard page.
     */
    public static function renderChart()
    {
        self::enqueueAssets();
        self::renderTemplate( 'block' );
    }

    /**
     * Enqueue assets
     */
    private static function enqueueAssets()
    {
        self::enqueueStyles( array(
            'backend'  => array( 'css/fontawesome-all.min.css' ),
        ) );

        self::enqueueScripts( array(
            'module' => array(
                'js/chart.min.js',
                'js/appointments-dashboard.js' => array( 'bookly-chart.min.js' ),
            ),
        ) );

        $currencies = Lib\Utils\Price::getCurrencies();

        wp_localize_script( 'bookly-appointments-dashboard.js', 'BooklyAppointmentsWidgetL10n', array(
            'csrfToken' => Lib\Utils\Common::getCsrfToken(),
            'appointments' => __( 'Appointments', 'bookly' ),
            'revenue' => __( 'Revenue', 'bookly' ),
            'currency' => $currencies[ Lib\Config::getCurrency() ]['symbol'],
            'based_on' => get_option( 'bookly_dashboard_based_on_appointment' ),
        ) );
    }
}