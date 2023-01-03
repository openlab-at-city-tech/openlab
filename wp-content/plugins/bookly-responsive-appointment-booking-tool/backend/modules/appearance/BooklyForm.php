<?php
namespace Bookly\Backend\Modules\Appearance;

use Bookly\Lib;


/**
 * Class BooklyForm
 *
 * @package Bookly\Backend\Modules\Appearance
 */
class BooklyForm extends Lib\Base\Component
{
    /**
     *  Render [bookly-form] appearance page.
     */
    public static function render()
    {
        /** @var \WP_Locale $wp_locale */
        global $wp_locale;

        self::enqueueStyles( array(
            'frontend' => array_merge(
                ( get_option( 'bookly_cst_phone_default_country' ) == 'disabled'
                    ? array()
                    : array( 'css/intlTelInput.css' ) ),
                array(
                    'css/picker.classic.css',
                    'css/picker.classic.date.css',
                ),
                is_rtl()
                    ? array( 'css/bookly-rtl.css', 'css/bookly-main.css', )
                    : array( 'css/bookly-main.css', )
            ),
            'wp' => array( 'wp-color-picker', ),
            'module' => array( 'css/appearance.css' => array( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueScripts( array(
            'frontend' => array_merge(
                array(
                    'js/picker.js' => array( 'bookly-backend-globals' ),
                    'js/picker.date.js' => array( 'bookly-picker.js' ),
                ),
                get_option( 'bookly_cst_phone_default_country' ) == 'disabled'
                    ? array()
                    : array( 'js/intlTelInput.min.js' => array( 'jquery' ) )
            ),
            'wp' => array( 'wp-color-picker' ),
            'module' => array(
                'js/appearance.js' => array( 'bookly-picker.date.js' ),
            ),
        ) );

        wp_localize_script( 'bookly-appearance.js', 'BooklyL10n', array(
            'csrf_token' => Lib\Utils\Common::getCsrfToken(),
            'nop_format' => get_option( 'bookly_group_booking_nop_format' ),
            'today' => __( 'Today', 'bookly' ),
            'months' => array_values( $wp_locale->month ),
            'daysFull' => array_values( $wp_locale->weekday ),
            'days' => array_values( $wp_locale->weekday_abbrev ),
            'nextMonth' => __( 'Next month', 'bookly' ),
            'prevMonth' => __( 'Previous month', 'bookly' ),
            'date_format' => Lib\Utils\DateTime::convertFormat( 'date', Lib\Utils\DateTime::FORMAT_PICKADATE ),
            'firstDay' => (int) get_option( 'start_of_week' ),
            'saved' => __( 'Settings saved.', 'bookly' ),
            'intlTelInput' => array(
                'enabled' => get_option( 'bookly_cst_phone_default_country' ) != 'disabled',
                'utils' => plugins_url( 'intlTelInput.utils.js', Lib\Plugin::getDirectory() . '/frontend/resources/js/intlTelInput.utils.js' ),
                'country' => get_option( 'bookly_cst_phone_default_country' ),
            ),
        ) );

        // Initialize steps (tabs).
        $steps = array(
            1 => array( 'step' => 1, 'show' => true, 'title' => get_option( 'bookly_l10n_step_service' ) ),
            3 => array( 'step' => 3, 'show' => true, 'title' => get_option( 'bookly_l10n_step_time' ) ),
            6 => array( 'step' => 6, 'show' => true, 'title' => get_option( 'bookly_l10n_step_details' ) ),
            7 => array( 'step' => 7, 'show' => true, 'title' => get_option( 'bookly_l10n_step_payment' ) ),
            8 => array( 'step' => 8, 'show' => true, 'title' => get_option( 'bookly_l10n_step_done' ) ),
        );
        if ( Lib\Config::serviceExtrasActive() ) {
            if ( get_option( 'bookly_service_extras_after_step_time' ) ) {
                $steps[2] = $steps[3];
                $steps[3] = array( 'step' => 2, 'show' => get_option( 'bookly_service_extras_enabled' ), 'title' => get_option( 'bookly_l10n_step_extras' ) );
            } else {
                $steps[2] = array( 'step' => 2, 'show' => get_option( 'bookly_service_extras_enabled' ), 'title' => get_option( 'bookly_l10n_step_extras' ) );
            }
        }
        if ( Lib\Config::recurringAppointmentsActive() ) {
            $steps[4] = array( 'step' => 4, 'show' => get_option( 'bookly_recurring_appointments_enabled' ), 'title' => get_option( 'bookly_l10n_step_repeat' ) );
        }
        if ( Lib\Config::cartActive() ) {
            $steps[5] = array( 'step' => 5, 'show' => get_option( 'bookly_cart_enabled' ), 'title' => get_option( 'bookly_l10n_step_cart' ) );
        }
        ksort( $steps );

        $custom_css = get_option( 'bookly_app_custom_styles' );

        // Payment options.
        $gateways = array(
            'local' => array(
                'label_option_name' => 'bookly_l10n_label_pay_locally',
                'title' => __( 'Local', 'bookly' ),
                'with_card' => false,
                'logo_url' => null,
            ),
        );
        if ( Lib\Cloud\API::getInstance()->account->productActive( 'stripe' ) ) {
            $gateways[ Lib\Entities\Payment::TYPE_CLOUD_STRIPE ] = array(
                'label_option_name' => 'bookly_l10n_label_pay_cloud_stripe',
                'title' => 'Stripe Cloud',
                'with_card' => true,
                'logo_url' => 'default',
            );
        }

        $gateways = array_map( function( $gateway ) {
            if ( $gateway['logo_url'] === 'default' ) {
                $gateway['logo_url'] = plugins_url( 'frontend/resources/images/cards.png', Lib\Plugin::getMainFile() );
            }

            return $gateway;
        }, Proxy\Shared::paymentGateways( $gateways ) );

        $order = Lib\Config::getGatewaysPreference();
        $payment_options = array();

        if ( $order ) {
            foreach ( $order as $payment_system ) {
                if ( array_key_exists( $payment_system, $gateways ) ) {
                    $payment_options[] = $gateways[ $payment_system ];
                    unset( $gateways[ $payment_system ] );
                }
            }
        }
        $payment_options = array_merge( $payment_options, $gateways );

        // Render general layout.
        self::renderTemplate( 'index', compact( 'steps', 'custom_css', 'payment_options' ) );
    }

}