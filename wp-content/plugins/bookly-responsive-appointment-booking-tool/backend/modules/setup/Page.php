<?php
namespace Bookly\Backend\Modules\Setup;

use Bookly\Lib;
use Bookly\Lib\Utils\DateTime;
use Bookly\Backend\Modules\Calendar\Page as CalendarPage;

/**
 * Class Page
 *
 * @package Bookly\Backend\Modules\Setup
 */
class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        $tel_input_enabled = get_option( 'bookly_cst_phone_default_country' ) != 'disabled';

        self::enqueueStyles( array(
            'frontend' => $tel_input_enabled
                ? array( 'css/intlTelInput.css' )
                : array(),
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
            'module' => array( 'css/setup.css' => array( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueScripts( array(
            'frontend' => $tel_input_enabled
                ? array( 'js/intlTelInput.min.js' => array( 'jquery' ) )
                : array(),
            'module' => array( 'js/setup.js' => array( 'bookly-backend-globals' ) ),
        ) );

        $industries = Lib\Utils\Common::getIndustries();

        $sizes = array(
            '1' => __( '1 - 9 employees', 'bookly' ),
            '10' => __( '10 - 19 employees', 'bookly' ),
            '20' => __( '20 - 49 employees', 'bookly' ),
            '50' => __( '50 - 249 employees', 'bookly' ),
            '250' => __( '250 or more employees', 'bookly' ),
        );
        $durations = array();
        for ( $j = 15; $j <= 60; $j += 15 ) {
            $durations[] = array( 'title' => DateTime::secondsToInterval( $j * 60 ), 'value' => $j * 60 );
        }
        for ( $j = 60 * 2; $j <= 60 * 12; $j += 60 ) {
            $durations[] = array( 'title' => DateTime::secondsToInterval( $j * 60 ), 'value' => $j * 60 );
        }
        for ( $j = 60 * 24; $j <= 60 * 24 * 7; $j += 60 * 24 ) {
            $durations[] = array( 'title' => DateTime::secondsToInterval( $j * 60 ), 'value' => $j * 60 );
        }

        wp_localize_script( 'bookly-setup.js', 'BooklyL10nSetupForm', Proxy\Pro::prepareOptions( array(
            'step' => get_option( 'bookly_setup_step', 1 ),
            'industries' => $industries,
            'sizes' => $sizes,
            'intlTelInput' => array(
                'enabled' => $tel_input_enabled,
                'utils' => plugins_url( 'intlTelInput.utils.js', Lib\Plugin::getDirectory() . '/frontend/resources/js/intlTelInput.utils.js' ),
                'country' => get_option( 'bookly_cst_phone_default_country' ),
            ),
            'finish_url' => add_query_arg( array( 'page' => CalendarPage::pageSlug() ), admin_url( 'admin.php' ) ),
            'durations' => $durations,
            'l10n' => array(
                'company_name' => __( 'Company name', 'bookly' ),
                'industry' => __( 'Industry', 'bookly' ),
                'select_industry' => __( 'Select industry', 'bookly' ),
                'size' => __( 'Company size', 'bookly' ),
                'select_size' => __( 'Select company size', 'bookly' ),
                'company_email' => __( 'Company owner email', 'bookly' ),
                'staff_name' => __( 'Full name', 'bookly' ),
                'staff_email' => __( 'Email', 'bookly' ),
                'staff_phone' => __( 'Phone', 'bookly' ),
                'service_title' => __( 'Title', 'bookly' ),
                'service_duration' => __( 'Duration', 'bookly' ),
                'add_service' => __( 'Add service', 'bookly' ),
                'required' => __( 'Required', 'bookly' ),
                'continue' => __( 'Continue', 'bookly' ),
                'finish' => __( 'Finish', 'bookly' ),
                'skip' => __( 'Skip', 'bookly' ),
                'back' => __( 'Back', 'bookly' ),
                'delete' => __( 'Delete', 'bookly' ),
                'steps' => array(
                    __( 'Company', 'bookly' ),
                    __( 'Staff', 'bookly' ),
                    __( 'Service', 'bookly' ),
                    __( 'Done', 'bookly' ),
                ),
                'done' => array(
                    'string_1' => __( 'Congratulations, the initial setup is complete!', 'bookly' ),
                    'string_2' => sprintf( __( 'Shortcut of your form is %s', 'bookly' ), '<input type="text" class="form-control d-inline" value="[bookly-form]" readonly style="max-width: 124px;">' ),
                    'string_3' => __( 'Open the page where you want to add the booking form in page edit mode and paste this shortcut in a free block.', 'bookly' ),
                    'string_4' => sprintf( __( 'You can find more detailed instructions for publishing Bookly form in our %s', 'bookly' ), sprintf( '<a href="https://support.booking-wp-plugin.com/hc/en-us/articles/212800185">%s</a>', __( 'Help center', 'bookly' ) ) ),
                    'string_5' => sprintf( __( 'Bookly can boost your sales and scale together with your business. Get more features and remove the limits by upgrading to the paid version with the %s, which allows you to use a vast number of additional features and settings for online scheduling, install other add-ons for Bookly, and includes six months of customer support.', 'bookly' ), '<a href="https://codecanyon.net/item/bookly-booking-plugin-responsive-appointment-booking-and-scheduling/7226091">Bookly Pro (Add-on)</a>' ),
                ),
            ),
        ) ) );

        self::renderTemplate( 'index' );
    }
}