<?php
namespace Bookly\Frontend\Modules\Booking;

use Bookly\Lib;
use Bookly\Frontend\Modules\Booking\Lib\Errors;

/**
 * Class ShortCode
 *
 * @package Bookly\Frontend\Modules\Booking
 */
class ShortCode extends Lib\Base\ShortCode
{
    public static $code = 'bookly-form';

    /**
     * Link styles.
     */
    public static function linkStyles()
    {
        $styles = array(
            'bookly' => array(
                'frontend/resources/css/picker.classic.css' => array( 'bookly-frontend-globals' ),
                'frontend/resources/css/picker.classic.date.css',
            ),
        );
        if ( get_option( 'bookly_cst_phone_default_country' ) === 'disabled' ) {
            $styles['bookly']['frontend/resources/css/bookly-main.css'] = array( 'bookly-picker.classic.date.css' );
        } else {
            $styles['bookly']['frontend/resources/css/intlTelInput.css'] = array();
            $styles['bookly']['frontend/resources/css/bookly-main.css'] = array( 'bookly-intlTelInput.css', 'bookly-picker.classic.date.css' );
        }
        if ( is_rtl() ) {
            $styles['bookly']['frontend/resources/css/bookly-rtl.css'] = array();
        }

        self::enqueueStyles( $styles );
    }

    /**
     * Link scripts.
     */
    public static function linkScripts()
    {
        /** @global \WP_Locale $wp_locale */
        global $wp_locale, $sitepress;

        if ( ! $wp_locale ) {
            $wp_locale = new \WP_Locale();
        }

        // Disable emoji in IE11
        if ( array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'Trident/7.0' ) !== false ) {
            Lib\Utils\Common::disableEmoji();
        }

        self::enqueueScripts( array(
            'bookly' => array(
                'frontend/resources/js/hammer.min.js' => array( 'bookly-frontend-globals' ),
                'frontend/resources/js/jquery.hammer.min.js' => array( 'jquery' ),
                'frontend/resources/js/picker.js' => array( 'jquery' ),
                'frontend/resources/js/picker.date.js' => array( 'bookly-picker.js' ),
                'frontend/resources/js/bookly.min.js' => Proxy\Shared::enqueueBookingScripts( array( 'bookly-hammer.min.js', 'bookly-picker.date.js' ) ),
            ),
        ) );
        if ( get_option( 'bookly_cst_phone_default_country' ) !== 'disabled' ) {
            self::enqueueScripts( array(
                'bookly' => array( 'frontend/resources/js/intlTelInput.min.js' => array( 'jquery' ) ),
            ) );
        }

        // Prepare URL for AJAX requests.
        $ajaxurl = admin_url( 'admin-ajax.php' );

        // Support WPML.
        if ( $sitepress instanceof \SitePress ) {
            $ajaxurl = add_query_arg( array( 'lang' => $sitepress->get_current_language() ), $ajaxurl );
        }

        wp_localize_script( 'bookly-bookly.min.js', 'BooklyL10n', array(
            'ajaxurl' => $ajaxurl,
            'csrf_token' => Lib\Utils\Common::getCsrfToken(),
            'today' => __( 'Today', 'bookly' ),
            'months' => array_values( $wp_locale->month ),
            'days' => array_values( $wp_locale->weekday ),
            'daysShort' => array_values( $wp_locale->weekday_abbrev ),
            'monthsShort' => array_values( $wp_locale->month_abbrev ),
            'nextMonth' => __( 'Next month', 'bookly' ),
            'prevMonth' => __( 'Previous month', 'bookly' ),
            'show_more' => __( 'Show more', 'bookly' ),
            'sessionHasExpired' => __( 'Your session has expired. Please press "Ok" to refresh the page', 'bookly' ),
        ) );
    }

    /**
     * Render Bookly shortcode.
     *
     * @param $attributes
     * @return string
     * @throws
     */
    public static function render( $attributes )
    {
        // Disable caching.
        Lib\Utils\Common::noCache();

        // Generate unique form id.
        $form_id = uniqid();

        // Find bookings with any of payment statuses ( PayPal, 2Checkout, PayU Latam ).
        $status = array( 'booking' => 'new' );
        foreach ( Lib\Session::getAllFormsData() as $saved_form_id => $data ) {
            if ( isset ( $data['payment'] ) ) {
                if ( ! isset ( $data['payment']['processed'] ) ) {
                    switch ( $data['payment']['status'] ) {
                        case 'success':
                        case 'processing':
                            $form_id = $saved_form_id;
                            $status = array( 'booking' => 'finished' );
                            break;
                        case 'cancelled':
                        case 'error':
                            $form_id = $saved_form_id;
                            end( $data['cart'] );
                            $status = array( 'booking' => 'cancelled', 'cart_key' => key( $data['cart'] ) );
                            break;
                    }
                    // Mark this form as processed for cases when there are more than 1 booking form on the page.
                    $data['payment']['processed'] = true;
                    Lib\Session::setFormVar( $saved_form_id, 'payment', $data['payment'] );
                }
            } elseif ( isset( $data['last_touched'] ) && $data['last_touched'] + 30 * MINUTE_IN_SECONDS < time() ) {
                // Destroy forms older than 30 min.
                Lib\Session::destroyFormData( $saved_form_id );
            }
        }

        // Check if predefined short code is rendering
        if ( isset( $attributes['id'] ) ) {
            $attributes = apply_filters( 'bookly_form_attributed', $attributes );
        }

        // Handle short code attributes.
        $fields_to_hide = isset ( $attributes['hide'] ) ? explode( ',', $attributes['hide'] ) : array();
        $location_id = (int) ( isset( $_GET['loc_id'] ) ? $_GET['loc_id'] : ( isset( $attributes['location_id'] ) ? $attributes['location_id'] : 0 ) );
        $category_id = (int) ( isset( $_GET['cat_id'] ) ? $_GET['cat_id'] : ( isset( $attributes['category_id'] ) ? $attributes['category_id'] : 0 ) );
        $service_id = (int) ( isset( $_GET['service_id'] ) ? $_GET['service_id'] : ( isset( $attributes['service_id'] ) ? $attributes['service_id'] : 0 ) );
        $staff_id = (int) ( isset( $_GET['staff_id'] ) ? $_GET['staff_id'] : ( isset( $attributes['staff_member_id'] ) ? $attributes['staff_member_id'] : 0 ) );
        $units = (int) ( isset( $_GET['units'] ) ? $_GET['units'] : ( isset( $attributes['units'] ) ? $attributes['units'] : 0 ) );
        $date_from = isset( $_GET['date_from'] ) ? $_GET['date_from'] : ( isset( $attributes['date_from'] ) ? $attributes['date_from'] : 0 );
        $time_from = isset( $_GET['time_from'] ) ? $_GET['time_from'] : ( isset( $attributes['time_from'] ) ? $attributes['time_from'] : 0 );
        $time_to = isset( $_GET['time_to'] ) ? $_GET['time_to'] : ( isset( $attributes['time_to'] ) ? $attributes['time_to'] : 0 );
        $hide_service_part2 = Lib\Config::showSingleTimeSlot();

        $form_attributes = array(
            'hide_categories' => in_array( 'categories', $fields_to_hide ),
            'hide_services' => in_array( 'services', $fields_to_hide ),
            'hide_staff_members' => in_array( 'staff_members', $fields_to_hide ) && ( get_option( 'bookly_app_required_employee' ) ? $staff_id : true ),
            'show_number_of_persons' => (bool) ( isset( $attributes['show_number_of_persons'] ) ? $attributes['show_number_of_persons'] : false ),
            'hide_service_duration' => true,
            'hide_locations' => true,
            'hide_quantity' => true,
            'hide_date' => $hide_service_part2 ?: in_array( 'date', $fields_to_hide ),
            'hide_week_days' => $hide_service_part2 ?: in_array( 'week_days', $fields_to_hide ),
            'hide_time_range' => $hide_service_part2 ?: in_array( 'time_range', $fields_to_hide ),
        );
        if ( $form_attributes['hide_categories'] && $category_id ) {
            // Keeping 'admin' preselected category,
            // for case when customer clicks back to Service step.
            $form_attributes['const_category_id'] = $category_id;
        }

        // Set service step fields for Add-ons.
        if ( Lib\Config::customDurationActive() ) {
            $form_attributes['hide_service_duration'] = in_array( 'service_duration', $fields_to_hide );
        }
        if ( Lib\Config::locationsActive() ) {
            $form_attributes['hide_locations'] = in_array( 'locations', $fields_to_hide );
        }
        if ( Lib\Config::multiplyAppointmentsActive() ) {
            $form_attributes['hide_quantity'] = in_array( 'quantity', $fields_to_hide );
        }

        $hide_service_part1 = (
            ! $form_attributes['show_number_of_persons'] &&
            $form_attributes['hide_categories'] &&
            $form_attributes['hide_services'] &&
            $service_id &&
            $form_attributes['hide_staff_members'] &&
            $form_attributes['hide_locations'] &&
            $form_attributes['hide_service_duration'] &&
            $form_attributes['hide_quantity']
        );

        $hide_service_part2 = $hide_service_part2 ?: ! array_diff( array( 'date', 'week_days', 'time_range' ), $fields_to_hide );

        // Check if defaults parameters exists
        if ( $form_attributes['hide_services'] && $service_id && ! Lib\Entities\Service::find( $service_id ) ) {
            return esc_html( 'The preselected service for shortcode is not available anymore. Please check your shortcode settings.' );
        }

        if ( $hide_service_part1 && $hide_service_part2 ) {
            Lib\Session::setFormVar( $form_id, 'skip_service_step', true );
        }

        // Store parameters in session for later use.
        Lib\Session::setFormVar( $form_id, 'defaults', compact( 'service_id', 'staff_id', 'location_id', 'category_id', 'units', 'date_from', 'time_from', 'time_to' ) );
        Lib\Session::setFormVar( $form_id, 'last_touched', time() );

        // Errors.
        $errors = array(
            Errors::SESSION_ERROR => __( 'Session error.', 'bookly' ),
            Errors::FORM_ID_ERROR => __( 'Form ID error.', 'bookly' ),
            Errors::CART_ITEM_NOT_AVAILABLE => Lib\Utils\Common::getTranslatedOption( Lib\Config::showStepCart() ? 'bookly_l10n_step_cart_slot_not_available' : 'bookly_l10n_step_time_slot_not_available' ),
            Errors::PAY_LOCALLY_NOT_AVAILABLE => __( 'Pay locally is not available.', 'bookly' ),
            Errors::INVALID_GATEWAY => __( 'Invalid gateway.', 'bookly' ),
            Errors::PAYMENT_ERROR => __( 'Error.', 'bookly' ),
            Errors::INCORRECT_USERNAME_PASSWORD => __( 'Incorrect username or password.' ),
        );

        // Set parameters for bookly form.
        $bookly_options = array(
            'form_id' => $form_id,
            'status' => $status,
            'skip_steps' => array(
                /**
                 * [extras,time,repeat]
                 * can be modified @see Proxy\Shared::booklyFormOptions
                 */
                'service_part1' => (int) $hide_service_part1,
                'service_part2' => (int) $hide_service_part2,
                'extras' => 1,
                'time' => 0,
                'repeat' => (int) ( ! Lib\Config::recurringAppointmentsActive() || ! get_option( 'bookly_recurring_appointments_enabled' ) || Lib\Config::showSingleTimeSlot() ),
                'cart' => (int) ! Lib\Config::showStepCart(),
            ),
            'errors' => $errors,
            'form_attributes' => $form_attributes,
            'use_client_time_zone' => (int) Lib\Config::useClientTimeZone(),
            'firstDay' => (int) get_option( 'start_of_week' ),
            'date_format' => Lib\Utils\DateTime::convertFormat( 'date', Lib\Utils\DateTime::FORMAT_PICKADATE ),
            'defaults' => compact( 'service_id', 'staff_id', 'location_id', 'category_id' ),
        );

        $bookly_options = Proxy\Shared::booklyFormOptions( $bookly_options );

        return self::renderTemplate(
            'short_code',
            compact( 'form_id', 'bookly_options' ),
            false
        );
    }
}