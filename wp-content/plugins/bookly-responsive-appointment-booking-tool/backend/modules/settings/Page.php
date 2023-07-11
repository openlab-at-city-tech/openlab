<?php
namespace Bookly\Backend\Modules\Settings;

use Bookly\Lib;
use Bookly\Backend\Components\Schedule\Component as ScheduleComponent;
use Bookly\Lib\Entities\CustomerAppointment;

/**
 * Class Page
 *
 * @package Bookly\Backend\Modules\Settings
 */
class Page extends Lib\Base\Ajax
{
    /**
     * Render page.
     */
    public static function render()
    {
        /** @var \WP_Locale $wp_locale */
        global $wp_locale;

        wp_enqueue_media();
        self::enqueueStyles( array(
            'wp' => array( 'wp-color-picker' ),
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'wp' => array( 'wp-color-picker' ),
            'backend' => array(
                'js/sortable.min.js' => array( 'jquery' ),
                'js/jCal.js' => array( 'jquery' ),
                'js/range-tools.js' => array( 'bookly-backend-globals' ),
            ),
            'module' => array( 'js/settings.js' => array( 'bookly-intlTelInput.min.js', 'bookly-sortable.min.js', 'bookly-range-tools.js' ) ),
            'frontend' => array( 'js/intlTelInput.min.js' => array( 'jquery' ), ),
        ) );

        $current_tab = self::hasParameter( 'tab' ) ? self::parameter( 'tab' ) : 'general';
        $alert = array( 'success' => array(), 'error' => array() );

        // Save the settings.
        if ( ! empty ( $_POST ) && self::csrfTokenValid() ) {
            switch ( self::parameter( 'tab' ) ) {
                case 'calendar':  // Calendar form.
                    update_option( 'bookly_cal_show_only_business_days', self::parameter( 'bookly_cal_show_only_business_days' ) );
                    update_option( 'bookly_cal_show_only_business_hours', self::parameter( 'bookly_cal_show_only_business_hours' ) );
                    update_option( 'bookly_cal_show_only_staff_with_appointments', self::parameter( 'bookly_cal_show_only_staff_with_appointments' ) );
                    update_option( 'bookly_cal_one_participant', self::parameter( 'bookly_cal_one_participant' ) );
                    update_option( 'bookly_cal_many_participants', self::parameter( 'bookly_cal_many_participants' ) );
                    update_option( 'bookly_cal_month_view_style', self::parameter( 'bookly_cal_month_view_style' ) );
                    update_option( 'bookly_cal_coloring_mode', self::parameter( 'bookly_cal_coloring_mode' ) );
                    update_option( 'bookly_cal_show_new_appointments_badge', self::parameter( 'bookly_cal_show_new_appointments_badge' ) );
                    update_option( 'bookly_cal_last_seen_appointment', self::parameter( 'bookly_cal_last_seen_appointment' ) );
                    foreach ( self::parameter( 'status' ) as $status => $color ) {
                        if ( in_array( $status, array( CustomerAppointment::STATUS_PENDING, CustomerAppointment::STATUS_APPROVED, CustomerAppointment::STATUS_CANCELLED, CustomerAppointment::STATUS_REJECTED, 'mixed' ) ) ) {
                            update_option( sprintf( 'bookly_appointment_status_%s_color', $status ), $color );
                        }
                    }
                    $alert['success'][] = __( 'Settings saved.', 'bookly' );
                    break;
                case 'payments':  // Payments form.
                    update_option( 'bookly_pmt_order', self::parameter( 'bookly_pmt_order' ) );
                    update_option( 'bookly_pmt_currency', self::parameter( 'bookly_pmt_currency' ) );
                    update_option( 'bookly_pmt_price_format', self::parameter( 'bookly_pmt_price_format' ) );
                    update_option( 'bookly_pmt_local', self::parameter( 'bookly_pmt_local' ) );
                    if ( Lib\Cloud\API::getInstance()->account->productActive( Lib\Cloud\Account::PRODUCT_STRIPE ) ) {
                        update_option( 'bookly_cloud_stripe_enabled', self::parameter( 'bookly_cloud_stripe_enabled' ) );
                        update_option( 'bookly_cloud_stripe_timeout', self::parameter( 'bookly_cloud_stripe_timeout' ) );
                        update_option( 'bookly_cloud_stripe_increase', self::parameter( 'bookly_cloud_stripe_increase' ) );
                        update_option( 'bookly_cloud_stripe_addition', self::parameter( 'bookly_cloud_stripe_addition' ) );
                        update_option( 'bookly_cloud_stripe_custom_metadata', self::parameter( 'bookly_cloud_stripe_custom_metadata' ) );
                        if ( self::parameter( 'bookly_cloud_stripe_custom_metadata' ) ) {
                            $metadata = array();
                            $names = self::parameter( 'bookly_cloud_stripe_meta_name', array() );
                            $values = self::parameter( 'bookly_cloud_stripe_meta_value', array() );
                            foreach ( $names as $index => $name ) {
                                if ( $name != '' ) {
                                    $metadata[] = array(
                                        'name' => $name,
                                        'value' => $values[ $index ],
                                    );
                                }
                            }
                            update_option( 'bookly_cloud_stripe_metadata', $metadata );
                        }
                    }
                    $alert['success'][] = __( 'Settings saved.', 'bookly' );
                    break;
                case 'business_hours':  // Business hours form.
                    foreach ( array( 'bookly_bh_monday_start', 'bookly_bh_monday_end', 'bookly_bh_tuesday_start', 'bookly_bh_tuesday_end', 'bookly_bh_wednesday_start', 'bookly_bh_wednesday_end', 'bookly_bh_thursday_start', 'bookly_bh_thursday_end', 'bookly_bh_friday_start', 'bookly_bh_friday_end', 'bookly_bh_saturday_start', 'bookly_bh_saturday_end', 'bookly_bh_sunday_start', 'bookly_bh_sunday_end', ) as $option_name ) {
                        update_option( $option_name, self::parameter( $option_name ) );
                    }
                    $alert['success'][] = __( 'Settings saved.', 'bookly' );
                    break;
                case 'general':  // General form.
                    $bookly_gen_time_slot_length = self::parameter( 'bookly_gen_time_slot_length' );
                    if ( in_array( $bookly_gen_time_slot_length, array( 2, 4, 5, 10, 12, 15, 20, 30, 45, 60, 90, 120, 180, 240, 360 ) ) ) {
                        update_option( 'bookly_gen_time_slot_length', $bookly_gen_time_slot_length );
                    }
                    update_option( 'bookly_gen_delete_data_on_uninstall', self::parameter( 'bookly_gen_delete_data_on_uninstall' ) );
                    update_option( 'bookly_gen_service_duration_as_slot_length', (int) self::parameter( 'bookly_gen_service_duration_as_slot_length' ) );
                    update_option( 'bookly_gen_allow_staff_edit_profile', (int) self::parameter( 'bookly_gen_allow_staff_edit_profile' ) );
                    update_option( 'bookly_gen_link_assets_method', self::parameter( 'bookly_gen_link_assets_method' ) );
                    update_option( 'bookly_gen_max_days_for_booking', (int) self::parameter( 'bookly_gen_max_days_for_booking' ) );
                    update_option( 'bookly_gen_use_client_time_zone', (int) self::parameter( 'bookly_gen_use_client_time_zone' ) );
                    update_option( 'bookly_gen_collect_stats', self::parameter( 'bookly_gen_collect_stats' ) );
                    update_option( 'bookly_gen_show_powered_by', self::parameter( 'bookly_gen_show_powered_by' ) );
                    update_option( 'bookly_gen_prevent_caching', (int) self::parameter( 'bookly_gen_prevent_caching' ) );
                    update_option( 'bookly_gen_prevent_session_locking', (int) self::parameter( 'bookly_gen_prevent_session_locking' ) );
                    update_option( 'bookly_gen_badge_consider_news', (int) self::parameter( 'bookly_gen_badge_consider_news' ) );
                    update_option( 'bookly_gen_session_type', self::parameter( 'bookly_gen_session_type' ) );
                    update_option( 'bookly_email_gateway', self::parameter( 'bookly_email_gateway' ) );
                    update_option( 'bookly_smtp_host', self::parameter( 'bookly_smtp_host' ) );
                    update_option( 'bookly_smtp_port', self::parameter( 'bookly_smtp_port' ) );
                    update_option( 'bookly_smtp_user', self::parameter( 'bookly_smtp_user' ) );
                    update_option( 'bookly_smtp_password', self::parameter( 'bookly_smtp_password' ) );
                    update_option( 'bookly_smtp_secure', self::parameter( 'bookly_smtp_secure' ) );
                    $alert['success'][] = __( 'Settings saved.', 'bookly' );
                    break;
                case 'url': // URL settings form.
                    update_option( 'bookly_url_approve_page_url', self::parameter( 'bookly_url_approve_page_url' ) );
                    update_option( 'bookly_url_approve_denied_page_url', self::parameter( 'bookly_url_approve_denied_page_url' ) );
                    update_option( 'bookly_url_cancel_page_url', self::parameter( 'bookly_url_cancel_page_url' ) );
                    update_option( 'bookly_url_cancel_denied_page_url', self::parameter( 'bookly_url_cancel_denied_page_url' ) );
                    update_option( 'bookly_url_reject_denied_page_url', self::parameter( 'bookly_url_reject_denied_page_url' ) );
                    update_option( 'bookly_url_reject_page_url', self::parameter( 'bookly_url_reject_page_url' ) );
                    $alert['success'][] = __( 'Settings saved.', 'bookly' );
                    break;
                case 'customers':  // Customers form.
                    update_option( 'bookly_cst_allow_duplicates', self::parameter( 'bookly_cst_allow_duplicates' ) );
                    update_option( 'bookly_cst_default_country_code', self::parameter( 'bookly_cst_default_country_code' ) );
                    update_option( 'bookly_cst_phone_default_country', self::parameter( 'bookly_cst_phone_default_country' ) );
                    update_option( 'bookly_cst_remember_in_cookie', self::parameter( 'bookly_cst_remember_in_cookie' ) );
                    update_option( 'bookly_cst_show_update_details_dialog', self::parameter( 'bookly_cst_show_update_details_dialog' ) );
                    update_option( 'bookly_cst_verify_customer_details', self::parameter( 'bookly_cst_verify_customer_details' ) );
                    // Update email required option if creating WordPress account for customers
                    $bookly_cst_required_details = get_option( 'bookly_cst_required_details', array() );
                    if ( self::parameter( 'bookly_cst_create_account' ) && ! in_array( 'email', $bookly_cst_required_details ) ) {
                        $bookly_cst_required_details[] = 'email';
                        update_option( 'bookly_cst_required_details', $bookly_cst_required_details );
                    }
                    $alert['success'][] = __( 'Settings saved.', 'bookly' );
                    break;
                case 'appointments':
                    update_option( 'bookly_l10n_ics_customer_template', self::parameter( 'bookly_l10n_ics_customer_template' ) );
                    do_action( 'wpml_register_single_string', 'bookly', 'bookly_l10n_ics_customer_template', self::parameter( 'bookly_l10n_ics_customer_template' ) );
                    update_option( 'bookly_ics_staff_template', self::parameter( 'bookly_ics_staff_template' ) );
                    update_option( 'bookly_appointment_default_status', self::parameter( 'bookly_appointment_default_status' ) );
                    $alert['success'][] = __( 'Settings saved.', 'bookly' );
                    break;
                case 'company':  // Company form.
                    update_option( 'bookly_co_address', self::parameter( 'bookly_co_address' ) );
                    update_option( 'bookly_co_logo_attachment_id', self::parameter( 'bookly_co_logo_attachment_id' ) );
                    update_option( 'bookly_co_name', self::parameter( 'bookly_co_name' ) );
                    update_option( 'bookly_co_phone', self::parameter( 'bookly_co_phone' ) );
                    update_option( 'bookly_co_website', self::parameter( 'bookly_co_website' ) );
                    update_option( 'bookly_co_industry', self::parameter( 'bookly_co_industry' ) );
                    update_option( 'bookly_co_size', self::parameter( 'bookly_co_size' ) );
                    update_option( 'bookly_co_email', self::parameter( 'bookly_co_email' ) );
                    $alert['success'][] = __( 'Settings saved.', 'bookly' );
                    break;
                case 'logs':  // Logs form.
                    update_option( 'bookly_logs_enabled', self::parameter( 'bookly_logs_enabled' ) );
                    $alert['success'][] = __( 'Settings saved.', 'bookly' );
                    break;
            }

            // Let Add-ons save their settings.
            $alert = Proxy\Shared::saveSettings( $alert, self::parameter( 'tab' ), self::parameters() );
        }

        Proxy\Shared::enqueueAssets();

        wp_localize_script( 'bookly-settings.js', 'BooklyL10n', array(
            'alert' => $alert,
            'current_tab' => $current_tab,
            'default_country' => get_option( 'bookly_cst_phone_default_country' ),
            'holidays' => self::_getHolidays(),
            'loading_img' => plugins_url( 'bookly-responsive-appointment-booking-tool/backend/resources/images/loading.gif' ),
            'firstDay' => get_option( 'start_of_week' ),
            'days' => array_values( $wp_locale->weekday_abbrev ),
            'months' => array_values( $wp_locale->month ),
            'close' => __( 'Close', 'bookly' ),
            'repeat' => __( 'Repeat every year', 'bookly' ),
            'we_are_not_working' => __( 'We are not working on this day', 'bookly' ),
            'sample_price' => number_format_i18n( 10, 3 ),
            'are_you_sure' => __( 'Are you sure?', 'bookly' ),
            'datePicker' => Lib\Utils\DateTime::datePickerOptions(),
            'dateRange' => Lib\Utils\DateTime::dateRangeOptions( array( 'lastMonth' => __( 'Last month', 'bookly' ), ) ),
            'stripeCloudMetadata' => get_option( 'bookly_cloud_stripe_metadata', array() ),
            'zeroRecords' => __( 'No records for selected period.', 'bookly' ),
            'processing' => __( 'Processing...', 'bookly' ),
        ) );
        $values = array();
        foreach ( array( 2, 4, 5, 10, 12, 15, 20, 30, 45, 60, 90, 120, 180, 240, 360 ) as $duration ) {
            $values['bookly_gen_time_slot_length'][] = array( $duration, Lib\Utils\DateTime::secondsToInterval( $duration * MINUTE_IN_SECONDS ) );
        }
        foreach (
            Lib\Proxy\CustomStatuses::prepareAllStatuses( array(
                Lib\Entities\CustomerAppointment::STATUS_APPROVED,
                Lib\Entities\CustomerAppointment::STATUS_PENDING,
            ) ) as $status
        ) {
            $values['statuses'][] = array( $status, Lib\Entities\CustomerAppointment::statusToString( $status ) );
        }
        $values['colors_status'] = Lib\Proxy\Shared::prepareColorsStatuses( array(
            CustomerAppointment::STATUS_PENDING => get_option( 'bookly_appointment_status_pending_color' ),
            CustomerAppointment::STATUS_APPROVED => get_option( 'bookly_appointment_status_approved_color' ),
            CustomerAppointment::STATUS_CANCELLED => get_option( 'bookly_appointment_status_cancelled_color' ),
            CustomerAppointment::STATUS_REJECTED => get_option( 'bookly_appointment_status_rejected_color' ),
        ) );
        if ( Lib\Config::proActive() ) {
            $values['colors_status']['mixed'] = get_option( 'bookly_appointment_status_mixed_color' );
        }

        $payments = self::_getPayments();
        $business_hours = self::_getBusinessHours();

        self::renderTemplate( 'index', compact( 'values', 'payments', 'business_hours' ) );
    }

    /**
     * Get holidays.
     *
     * @return array
     */
    protected static function _getHolidays()
    {
        $collection = Lib\Entities\Holiday::query()->where( 'staff_id', null )->fetchArray();
        $holidays = array();
        if ( count( $collection ) ) {
            foreach ( $collection as $holiday ) {
                $holidays[ $holiday['id'] ] = array(
                    'm' => (int) date( 'm', strtotime( $holiday['date'] ) ),
                    'd' => (int) date( 'd', strtotime( $holiday['date'] ) ),
                );
                // If not repeated holiday, add the year
                if ( ! $holiday['repeat_event'] ) {
                    $holidays[ $holiday['id'] ]['y'] = (int) date( 'Y', strtotime( $holiday['date'] ) );
                }
            }
        }

        return $holidays;
    }

    /**
     * @return ScheduleComponent
     */
    protected static function _getBusinessHours()
    {
        $business_hours = new ScheduleComponent( 'bookly_bh_{index}_start', 'bookly_bh_{index}_end', false );
        $week_days = array(
            1 => 'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
        );
        $start_of_week = (int) get_option( 'start_of_week' );
        for ( $i = 1; $i <= 7; $i++ ) {
            $index = ( $start_of_week + $i ) < 8 ? $start_of_week + $i : $start_of_week + $i - 7;
            $day = $week_days[ $index ];
            $business_hours->addHours( $day, $index, get_option( 'bookly_bh_' . $day . '_start' ), get_option( 'bookly_bh_' . $day . '_end' ) );
        }

        return $business_hours;
    }

    /**
     * @return array
     */
    protected static function _getPayments()
    {
        $payments = array();
        $payment_data = array(
            'local' => self::renderTemplate( '_payment_local', array(), false ),
        );
        if ( Lib\Cloud\API::getInstance()->account->productActive( Lib\Cloud\Account::PRODUCT_STRIPE ) ) {
            $payment_data[ Lib\Entities\Payment::TYPE_CLOUD_STRIPE ] = self::renderTemplate( '_cloud_stripe_settings', array(), false );
        }

        $payment_data = Proxy\Shared::preparePaymentGatewaySettings( $payment_data );

        $order = Lib\Config::getGatewaysPreference();
        foreach ( $order as $payment_system ) {
            if ( array_key_exists( $payment_system, $payment_data ) ) {
                $payments[] = $payment_data[ $payment_system ];
            }
        }
        foreach ( $payment_data as $slug => $data ) {
            if ( ! $order || ! in_array( $slug, $order ) ) {
                $payments[] = $data;
            }
        }

        return $payments;
    }
}