<?php
namespace Bookly\Backend\Modules\Setup;

use Bookly\Lib;
use Bookly\Lib\Utils\DateTime;
use Bookly\Backend\Modules\Calendar\Page as CalendarPage;

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
        ) );

        self::enqueueScripts( array(
            'frontend' => $tel_input_enabled
                ? array( 'js/intlTelInput.min.js' => array( 'jquery' ) )
                : array(),
            'bookly' => array( 'backend/components/cloud/account/resources/js/select-country.js' => array( 'bookly-backend-globals' ) ),
            'module' => array( 'js/setup.js' => array( 'bookly-backend-globals' ) ),
        ) );

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

        $timeslot_options = array();
        foreach ( Lib\Config::getTimeSlotLengthOptions() as $duration ) {
            $timeslot_options[] = array( $duration, Lib\Utils\DateTime::secondsToInterval( $duration * MINUTE_IN_SECONDS ) );
        }

        wp_localize_script( 'bookly-setup.js', 'BooklyL10nSetupForm', Proxy\Pro::prepareOptions( array(
            'step' => get_option( 'bookly_setup_step', 1 ),
            'intlTelInput' => array(
                'enabled' => $tel_input_enabled,
                'country' => get_option( 'bookly_cst_phone_default_country' ),
            ),
            'finish_url' => add_query_arg( array( 'page' => CalendarPage::pageSlug() ), admin_url( 'admin.php' ) ),
            'durations' => $durations,
            'timeslot_options' => $timeslot_options,
            'timeslot_length' => (int) get_option( 'bookly_gen_time_slot_length', 15 ),
            'currencies' => Lib\Utils\Price::getCurrencies(),
            'currency' => Lib\Config::getCurrency(),
            'moment_format_date' => DateTime::convertFormat( 'date', DateTime::FORMAT_MOMENT_JS ),
            'moment_format_time' => DateTime::convertFormat( 'time', DateTime::FORMAT_MOMENT_JS ),
            'color' => get_option( 'bookly_app_color', '#f4662f' ),
            'cloud_logged_in' => Lib\Cloud\API::getInstance()->account->loadProfile() ? esc_html( Lib\Cloud\API::getInstance()->account->getUserName() ) : false,
            'l10n' => array(
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
                'cancel' => __( 'Cancel', 'bookly' ),
                'delete' => __( 'Delete', 'bookly' ),
                'welcome_title' => __( 'Welcome to Bookly!', 'bookly' ),
                'welcome_text' => sprintf( '%s<br/><br/>%s<br/><br/>%s',
                    __( 'As the ultimate appointment booking plugin for online scheduling, Bookly is designed to help you effortlessly manage your booking calendar, services, and client base.', 'bookly' ),
                    __( 'This introduction will guide you through the essential configuration steps to get you started quickly. ', 'bookly' ),
                    sprintf( __( 'You can optionally skip this wizard and refer to %s or watch our %s to learn the basics and get the most out of Bookly.', 'bookly' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://api.booking-wp-plugin.com/go/bookly-help-center', __( 'Bookly Help Center', 'bookly' ) ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://api.booking-wp-plugin.com/go/bookly-youtube', __( 'Video Tutorials', 'bookly' ) ) )
                ),
                'business_hours' => __( 'Set your company business hours. This schedule will serve as a template for all new staff members.', 'bookly' ),
                'to' => __( 'to', 'bookly' ),
                'time_interval' => __( 'Select a time interval to be used as a step when creating all time slots in the system.', 'bookly' ),
                'currency' => __( 'Select the currency for the prices of your services.', 'bookly' ),
                'staff_text' => __( 'In Bookly, \'staff\' refers to any employee or resource that provides services to your clients, such as a consultant, therapist, or any other service provider. Adding staff members allows you to manage their schedules, assign services to them, and track their appointments.', 'bookly' ),
                'services_text' => __( 'In Bookly, a \'service\' refers to the various offerings or activities your business provides to clients, such as consultations, treatments, classes, or other services. By adding services, you can manage their duration, cost, and assign them to specific employees.', 'bookly' ),
                'cloud_text' => sprintf( '%s<br/><br/>%s<br/><br/>%s<br/><br/>%s',
                    __( 'Bookly Cloud is an integral part of the Bookly booking system, offering a range of additional products and features for efficient appointment management and automation.', 'bookly' ),
                    __( 'Features like SMS Notifications, Zapier integration, Stripe and Square Payments, Gift Cards, Voice and WhatsApp Notifications are designed to enhance your operations and boost your online business.', 'bookly' ),
                    sprintf( __( 'Discover the full range of <a href="%s" target="_blank">powerful tools here</a>.', 'bookly' ), 'https://api.booking-wp-plugin.com/go/bookly-cloud-overview' ),
                    __( 'Sign up today and enjoy a welcome bonus! ', 'bookly' )
                ),
                'create_account' => __( 'Create an account', 'bookly' ),
                'login' => __( 'Already registered', 'bookly' ),
                'email' => __( 'Email', 'bookly' ),
                'password' => __( 'Password', 'bookly' ),
                'confirm_password' => __( 'Confirm password', 'bookly' ),
                'country' => __( 'Country', 'bookly' ),
                'cloud_tos' => sprintf( __( 'I accept <a href="%1$s" target="_blank">Service Terms</a> and <a href="%2$s" target="_blank">Privacy Policy</a>', 'bookly' ), 'https://www.booking-wp-plugin.com/terms/', 'https://www.booking-wp-plugin.com/privacy/' ),
                'forgot_password' => __( 'Forgot password', 'bookly' ),
                'forgot_actions' => array(
                    __( 'Send recovery code', 'bookly' ),
                    __( 'Verify recovery code', 'bookly' ),
                    __( 'Set new password', 'bookly' ),
                ),
                'recovery_code' => __( 'Recovery code', 'bookly' ),
                'new_password' => __( 'New password', 'bookly' ),
                'logged_in' => __( 'You are currently logged into Bookly Cloud with the account', 'bookly' ),
                'steps' => array(
                    __( 'Greetings', 'bookly' ),
                    __( 'General', 'bookly' ),
                    __( 'Staff', 'bookly' ),
                    __( 'Service', 'bookly' ),
                    __( 'Cloud', 'bookly' ),
                    __( 'Done', 'bookly' ),
                ),
                'off' => __( 'OFF', 'bookly' ),
                'done' => array(
                    'string_1' => __( 'The initial setup is complete, and you can now continue using Bookly in the admin panel.', 'bookly' ),
                    'string_2' => __( 'You can create staff members and services, add appointments to the calendar, and manage them directly from the backend.', 'bookly' ),
                    'string_3' => __( 'To start receiving appointments via the front-end booking form, follow these steps:', 'bookly' ),
                    'string_4' => __( 'Create a new page in WordPress.', 'bookly' ),
                    'string_5' => sprintf( __( 'Insert the following shortcode: %s', 'bookly' ), '<input type="text" class="form-control d-inline" value="[bookly-form]" readonly style="max-width: 124px;">' ),
                    'string_6' => __( 'Save the page and visit it to see your booking form in action.', 'bookly' ),
                    'string_7' => sprintf( __( 'Have questions? Visit our %s or reach out to our %s.', 'bookly' ), sprintf( '<a href="https://support.booking-wp-plugin.com/hc/en-us/articles/212800185">%s</a>', __( 'Help center', 'bookly' ) ), sprintf( '<a href="https://api.booking-wp-plugin.com/go/bookly-request-support">%s</a>', __( 'Support Team', 'bookly' ) ) ),
                ),
            ),
        ) ) );

        self::renderTemplate( 'index' );
    }
}