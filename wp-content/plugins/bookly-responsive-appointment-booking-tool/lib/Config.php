<?php
namespace Bookly\Lib;

use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Utils\Codes;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Utils\Price;

/**
 * Class Config
 *
 * @package Bookly\Lib
 * @method static bool advancedGoogleCalendarActive()  Check whether Advanced Google Calendar add-on is active or not.
 * @method static bool authorizeNetActive()            Check whether Authorize.Net add-on is active or not.
 * @method static bool cartActive()                    Check whether Cart add-on is active or not.
 * @method static bool chainAppointmentsActive()       Check whether Chain Appointment add-on is active or not.
 * @method static bool collaborativeServicesActive()   Check whether Collaborative Services add-on is active or not.
 * @method static bool compoundServicesActive()        Check whether Compound Services add-on is active or not.
 * @method static bool couponsActive()                 Check whether Coupons add-on is active or not.
 * @method static bool customDurationActive()          Check whether Custom Duration add-on is active or not.
 * @method static bool customFieldsActive()            Check whether Custom Fields add-on is active or not.
 * @method static bool customJavaScriptActive()        Check whether Custom JavaScript add-on is active or not.
 * @method static bool customStatusesActive()          Check whether Custom Statuses add-on is active or not.
 * @method static bool customerGroupsActive()          Check whether Customer Groups add-on is active or not.
 * @method static bool customerInformationActive()     Check whether Customer Information add-on is active or not.
 * @method static bool depositPaymentsActive()         Check whether Deposit Payments add-on is active or not.
 * @method static bool discountsActive()               Check whether Discounts add-on is active or not.
 * @method static bool filesActive()                   Check whether Files add-on is active or not.
 * @method static bool googleMapsAddressActive()       Check whether Google Maps Address add-on is active or not.
 * @method static bool groupBookingActive()            Check whether Group Booking add-on is active or not.
 * @method static bool invoicesActive()                Check whether Invoices add-on is active or not.
 * @method static bool locationsActive()               Check whether Locations add-on is active or not.
 * @method static bool mailchimpActive()               Check whether Mailchimp add-on is active or not.
 * @method static bool mollieActive()                  Check whether Mollie add-on is active or not.
 * @method static bool multiplyAppointmentsActive()    Check whether Multiply Appointments add-on is active or not.
 * @method static bool packagesActive()                Check whether Packages add-on is active or not.
 * @method static bool paypalCheckoutActive()          Check whether PayPal checkout add-on is active or not.
 * @method static bool paypalPaymentsStandardActive()  Check whether PayPal payments standard add-on is active or not.
 * @method static bool paysonActive()                  Check whether Payson add-on is active or not.
 * @method static bool payuBizActive()                 Check whether PayUbiz add-on is active or not.
 * @method static bool payuLatamActive()               Check whether PayU Latam add-on is active or not.
 * @method static bool proActive()                     Check whether Pro add-on is active or not.
 * @method static bool ratingsActive()                 Check whether Ratings add-on is active or not.
 * @method static bool recurringAppointmentsActive()   Check whether Recurring Appointments add-on is active or not.
 * @method static bool serviceExtrasActive()           Check whether Extras add-on is active or not.
 * @method static bool serviceScheduleActive()         Check whether Service Schedule add-on is active or not.
 * @method static bool specialDaysActive()             Check whether Special Days add-on is active or not.
 * @method static bool specialHoursActive()            Check whether Special Hours add-on is active or not.
 * @method static bool staffCabinetActive()            Check whether Staff Cabinet add-on is active or not.
 * @method static bool stripeActive()                  Check whether Stripe add-on is active or not.
 * @method static bool tasksActive()                   Check whether Tasks add-on is active or not.
 * @method static bool taxesActive()                   Check whether Taxes add-on is active or not.
 * @method static bool waitingListActive()             Check whether Waiting List add-on is active or not.
 */
abstract class Config
{
    /** @var string */
    private static $wp_timezone;

    /**
     * Get categories, services and staff members for drop down selects
     * for the 1st step of booking wizard.
     *
     * @return array
     */
    public static function getCaSeSt()
    {
        $result = array(
            'locations' => array(),
            'categories' => array(),
            'services' => array(),
            'staff' => array(),
        );
        $show_category_info = get_option( 'bookly_app_show_category_info' );

        // Services.
        $query = Entities\Service::query( 's' )
            ->select( 's.id, s.category_id, s.title, s.position, s.duration, s.price, s.type, s.info, s.attachment_id, s.recurrence_enabled' )
            ->addSelect( sprintf( '%s AS min_capacity, %s AS max_capacity',
                Proxy\Shared::prepareStatement( 1, 'MIN(ss.capacity_min)', 'StaffService' ),
                Proxy\Shared::prepareStatement( 1, 'MAX(ss.capacity_max)', 'StaffService' )
            ) )
            ->innerJoin( 'StaffService', 'ss', 'ss.service_id = s.id' )
            ->leftJoin( 'Staff', 'st', 'st.id = ss.staff_id' )
            ->where( 's.type', Entities\Service::TYPE_SIMPLE )
            ->where( 'st.visibility', 'public' )
            ->groupBy( 's.id' );

        $query = Proxy\Shared::prepareCaSeStQuery( $query );

        if ( ! Proxy\Locations::servicesPerLocationAllowed() ) {
            $query->where( 'ss.location_id', null );
        }

        foreach ( $query->fetchArray() as $row ) {

            $min_time_prior_booking = Slots\DatePoint::now()->modify( Proxy\Pro::getMinimumTimePriorBooking( $row['id'] ) )->toClientTz();

            $result['services'][ $row['id'] ] = array(
                'id' => (int) $row['id'],
                'img' => Utils\Common::getAttachmentUrl( $row['attachment_id'] ),
                'category_id' => (int) $row['category_id'] ?: -1,
                'name' => $row['title'] == ''
                    ? __( 'Untitled', 'bookly' )
                    : Utils\Common::getTranslatedString( 'service_' . $row['id'], $row['title'] ),
                'duration' => Utils\DateTime::secondsToInterval( $row['duration'] ),
                'price' => (float) $row['price'],
                'min_capacity' => (int) $row['min_capacity'],
                'max_capacity' => (int) $row['max_capacity'],
                'has_extras' => (int) Proxy\ServiceExtras::findByServiceId( $row['id'] ),
                'info' => self::getServiceInfoCodes( $row ),
                'type' => $row['type'],
                'pos' => (int) $row['position'],
                'recurrence_enabled' => (int) $row['recurrence_enabled'],
                'min_time_prior_booking' => array( (int) $min_time_prior_booking->format( 'Y' ), (int) $min_time_prior_booking->format( 'n' ) - 1, (int) $min_time_prior_booking->format( 'j' ), ),
            );

            $result = Proxy\Shared::prepareCategoryService( $result, $row );
        }

        $result = Proxy\Shared::prepareServices( $result );

        $categories = array();
        foreach ( $result['services'] as $service ) {
            if ( $service['category_id'] && ! in_array( $service['category_id'], $categories, false ) ) {
                $categories[] = $service['category_id'];
            }
            if ( $service['category_id'] === -1 && ! isset ( $result['categories'][ -1 ] ) ) {
                $result['categories'][ -1 ] = array(
                    'id' => -1,
                    'name' => __( 'Uncategorized', 'bookly' ),
                    'pos' => 99999,
                );
            }
        }

        // Categories.
        if ( count( $categories ) ) {
            $rows = Entities\Category::query()->whereIn( 'id', $categories )->fetchArray();
            foreach ( $rows as $row ) {
                $result['categories'][ $row['id'] ] = array(
                    'id' => (int) $row['id'],
                    'name' => Utils\Common::getTranslatedString( 'category_' . $row['id'], $row['name'] ),
                    'pos' => (int) $row['position'],
                    'img' => Utils\Common::getAttachmentUrl( $row['attachment_id'] ),
                    'info_text' => Utils\Common::getTranslatedString( 'category_' . $row['id'] . '_info', $row['info'] ),
                );
                if ( $show_category_info ) {
                    $result['categories'][ $row['id'] ]['info'] = self::getCategoryInfoCodes( $row );
                }
            }
        }

        // Staff.
        $query = Entities\Staff::query( 'st' )
            ->select( sprintf( 'st.id, st.full_name, st.position, st.info, st.attachment_id, ss.service_id, %s AS capacity_min, %s AS capacity_max, ss.price',
                Proxy\Shared::prepareStatement( 1, 'ss.capacity_min', 'StaffService' ),
                Proxy\Shared::prepareStatement( 1, 'ss.capacity_max', 'StaffService' )
            ) )
            ->innerJoin( 'StaffService', 'ss', 'ss.staff_id = st.id' )
            ->leftJoin( 'Service', 's', 's.id = ss.service_id' )
            ->where( 'st.visibility', 'public' )
            ->where( 's.type', Entities\Service::TYPE_SIMPLE );

        $query = Proxy\Shared::prepareCaSeStQuery( $query );

        if ( ! Proxy\Locations::servicesPerLocationAllowed() ) {
            $query
                ->addSelect( 'ss.location_id' )
                ->where( 'ss.location_id', null );
        }

        foreach ( $query->fetchArray() as $row ) {
            $staff_name = Utils\Common::getTranslatedString( 'staff_' . $row['id'], $row['full_name'] );
            $staff_info = $row['info'] == '' ? '' : Utils\Common::getTranslatedString( 'staff_' . $row['id'] . '_info', $row['info'] );
            if ( $row['attachment_id'] != '' && $img = wp_get_attachment_image_src( $row['attachment_id'], 'full' ) ) {
                $staff_image_url = $img[0];
            } else {
                $staff_image_url = '';
            }
            $staff_codes = array(
                'staff_name' => $staff_name,
                'staff_info' => $staff_info,
                'staff_photo' => '<img src="' . $staff_image_url . '"/>',
                'staff_photo_url' => $staff_image_url,
            );
            if ( ! isset ( $result['staff'][ $row['id'] ] ) ) {
                $result['staff'][ $row['id'] ] = array(
                    'id' => (int) $row['id'],
                    'name' => $staff_name,
                    'img' => $staff_image_url,
                    'services' => array(),
                    'pos' => (int) $row['position'],
                    'info' => nl2br( Codes::replace( Utils\Common::getTranslatedOption( 'bookly_l10n_step_service_staff_info' ), $staff_codes, false ) ),
                );
            }

            $location_data = array(
                'min_capacity' => (int) $row['capacity_min'],
                'max_capacity' => (int) $row['capacity_max'],
                'price' => Utils\Price::format( $row['price'] ),
            );
            $location_data = Proxy\Shared::prepareCategoryServiceStaffLocation( $location_data, $row );

            $result['staff'][ $row['id'] ]['services'][ $row['service_id'] ]['locations'][ (int) $row['location_id'] ] = $location_data;
        }

        $result = Proxy\Shared::prepareCaSeSt( $result );

        return Proxy\Ratings::prepareCaSeSt( $result );
    }

    /**
     * Get available days and available time ranges
     * for the 1st step of booking wizard.
     *
     * @return array
     */
    public static function getDaysAndTimes()
    {
        /** @var \WP_Locale $wp_locale */
        global $wp_locale;

        $result = array(
            'days' => array(),
            'times' => array(),
        );

        $res = array_merge(
            Entities\StaffScheduleItem::query()
                ->select( '`r`.`staff_id`, `r`.`day_index`, MIN(`r`.`start_time`) AS `start_time`, MAX(`r`.`end_time`) AS `end_time`, `st`.`time_zone`' )
                ->leftJoin( 'Staff', 'st', '`st`.`id` = `r`.`staff_id`' )
                ->whereNot( 'r.start_time', null )
                ->where( 'st.visibility', 'public' )
                ->groupBy( 'staff_id' )
                ->groupBy( 'day_index' )
                ->fetchArray(),
            Proxy\SpecialDays::getDaysAndTimes() ?: array()
        );

        /** @var Slots\TimePoint $min_start_time */
        /** @var Slots\TimePoint $max_end_time */
        $min_start_time = null;
        $max_end_time = null;
        $days = array();
        $wp_tz_offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;

        foreach ( $res as $row ) {
            $start_time = Slots\TimePoint::fromStr( $row['start_time'] );
            $end_time = Slots\TimePoint::fromStr( $row['end_time'] );

            if ( $row['time_zone'] ) {
                $staff_tz_offset = DateTime::timeZoneOffset( $row['time_zone'] );
                $start_time = $start_time->toTz( $staff_tz_offset, $wp_tz_offset );
                $end_time = $end_time->toTz( $staff_tz_offset, $wp_tz_offset );
            }

            if ( $min_start_time === null || $min_start_time->gt( $start_time ) ) {
                $min_start_time = $start_time;
            }
            if ( $max_end_time === null || $max_end_time->lt( $end_time ) ) {
                $max_end_time = $end_time;
            }

            // Convert to client time zone.
            $start_time = $start_time->toClientTz();
            $end_time = $end_time->toClientTz();

            // Add day(s).
            if ( $start_time->value() < 0 ) {
                $prev_day = $row['day_index'] - 1;
                if ( $prev_day < 1 ) {
                    $prev_day = 7;
                }
                $days[ $prev_day ] = true;
            }
            if ( $start_time->value() < HOUR_IN_SECONDS * 24 && $end_time->value() > 0 ) {
                $days[ $row['day_index'] ] = true;
            }
            if ( $end_time->value() > HOUR_IN_SECONDS * 24 ) {
                $next_day = $row['day_index'] + 1;
                if ( $next_day > 7 ) {
                    $next_day = 1;
                }
                $days[ $next_day ] = true;
            }
        }

        $start_of_week = get_option( 'start_of_week' );
        $week_days = array_values( $wp_locale->weekday_abbrev );

        // Sort days considering start_of_week;
        uksort( $days, function ( $a, $b ) use ( $start_of_week ) {
            $a -= $start_of_week;
            $b -= $start_of_week;
            if ( $a < 1 ) {
                $a += 7;
            }
            if ( $b < 1 ) {
                $b += 7;
            }

            return $a - $b;
        } );

        // Fill days.
        foreach ( array_keys( $days ) as $day_id ) {
            $result['days'][ $day_id ] = $week_days[ $day_id - 1 ];
        }

        if ( $min_start_time && $max_end_time ) {
            $start = $min_start_time;
            $end = $max_end_time;
            $client_start = $start->toClientTz();
            $client_end = $end->toClientTz();

            while ( $start->lte( $end ) ) {
                $result['times'][ Utils\DateTime::buildTimeString( $start->value(), false ) ] = $client_start->formatI18nTime();
                // The next value will be rounded to integer number of hours, i.e. e.g. 8:00, 9:00, 10:00 and so on.
                $start = $start->modify( HOUR_IN_SECONDS - ( $start->value() % HOUR_IN_SECONDS ) );
                $client_start = $client_start->modify( HOUR_IN_SECONDS - ( $client_start->value() % HOUR_IN_SECONDS ) );
            }
            // The last value should always be the end time.
            $result['times'][ Utils\DateTime::buildTimeString( $end->value(), false ) ] = $client_end->formatI18nTime();
        }

        return $result;
    }

    /**
     * Get array with bounding days for Pickadate.
     *
     * @param Chain $chain
     * @return array
     */
    public static function getBoundingDaysForPickadate( $chain = null )
    {
        $result = array();

        $min_time = Proxy\Pro::getMinimumTimePriorBooking( null );
        if ( $chain !== null ) {
            foreach ( $chain->getItems() as $item ) {
                $min_time = min( $min_time, Proxy\Pro::getMinimumTimePriorBooking( $item->getService()->getId() ) );
            }
        }

        $dp = Slots\DatePoint::now()->modify( $min_time )->toClientTz();
        $result['date_min'] = array(
            (int) $dp->format( 'Y' ),
            (int) $dp->format( 'n' ) - 1,
            (int) $dp->format( 'j' ),
        );
        $dp = $dp->modify( ( self::getMaximumAvailableDaysForBooking() - 1 ) . ' days' );
        $result['date_max'] = array(
            (int) $dp->format( 'Y' ),
            (int) $dp->format( 'n' ) - 1,
            (int) $dp->format( 'j' ),
        );

        return $result;
    }

    /**
     * Check whether payment step is disabled.
     *
     * @return bool
     */
    public static function paymentStepDisabled()
    {
        return ! ( self::payLocallyEnabled()
            || ( self::twoCheckoutActive() && get_option( 'bookly_2checkout_enabled' ) )
            || ( self::authorizeNetActive() && get_option( 'bookly_authorize_net_enabled' ) )
            || ( self::mollieActive() && get_option( 'bookly_mollie_enabled' ) )
            || ( self::paysonActive() && get_option( 'bookly_payson_enabled' ) )
            || ( self::payuBizActive() && get_option( 'bookly_payu_biz_enabled' ) )
            || ( self::payuLatamActive() && get_option( 'bookly_payu_latam_enabled' ) )
            || ( self::stripeActive() && get_option( 'bookly_stripe_enabled' ) )
            || self::stripeCloudEnabled()
            || self::squareEnabled()
            || self::giftEnabled()
            || self::paypalEnabled()
        );
    }

    /**
     * Check whether multiple services booking is enabled.
     *
     * @return bool
     */
    public static function multipleServicesBookingEnabled()
    {
        return (
            self::cartActive() ||
            self::chainAppointmentsActive() ||
            self::multiplyAppointmentsActive() ||
            self::recurringAppointmentsActive()
        );
    }

    /**
     * @return bool
     */
    public static function payLocallyEnabled()
    {
        return get_option( 'bookly_pmt_local' ) == 1;
    }

    /**
     * @return bool
     */
    public static function paypalEnabled()
    {
        return self::proActive() && get_option( 'bookly_paypal_enabled' ) != '0';
    }

    /**
     * @return bool
     */
    public static function squareEnabled()
    {
        return self::proActive() && get_option( 'bookly_cloud_square_enabled' ) && Cloud\API::getInstance()->account->productActive( Cloud\Account::PRODUCT_SQUARE );
    }

    /**
     * @return bool
     */
    public static function giftEnabled()
    {
        return self::proActive() && get_option( 'bookly_cloud_gift_enabled' ) && Cloud\API::getInstance()->account->productActive( Cloud\Account::PRODUCT_GIFT );
    }

    /**
     * @return bool
     */
    public static function stripeCloudEnabled()
    {
        return get_option( 'bookly_cloud_stripe_enabled' ) && Cloud\API::getInstance()->account->productActive( Cloud\Account::PRODUCT_STRIPE );
    }

    /**
     * @return bool
     */
    public static function twoCheckoutActive()
    {
        return self::__callStatic( '2checkoutActive', array() );
    }

    /**
     * Get time slot length in seconds.
     *
     * @return integer
     */
    public static function getTimeSlotLength()
    {
        return (int) get_option( 'bookly_gen_time_slot_length', 15 ) * MINUTE_IN_SECONDS;
    }

    /**
     * Check whether service duration should be used instead of slot length on the frontend.
     *
     * @return bool
     */
    public static function useServiceDurationAsSlotLength()
    {
        return (bool) get_option( 'bookly_gen_service_duration_as_slot_length', false );
    }

    /**
     * Check whether use client time zone.
     *
     * @return bool
     */
    public static function useClientTimeZone()
    {
        return (bool) get_option( 'bookly_gen_use_client_time_zone' );
    }

    /**
     * @return int
     */
    public static function getMaximumAvailableDaysForBooking()
    {
        return (int) get_option( 'bookly_gen_max_days_for_booking', 365 );
    }

    /**
     * Whether to show calendar in the second step of booking form.
     *
     * @return bool
     */
    public static function showCalendar()
    {
        return (bool) get_option( 'bookly_app_show_calendar', false );
    }

    /**
     * Whether to use first and last customer name instead full name.
     *
     * @return bool
     */
    public static function showFirstLastName()
    {
        return (bool) get_option( 'bookly_cst_first_last_name', false );
    }

    /**
     * Whether to use email confirmation.
     *
     * @return bool
     */
    public static function showEmailConfirm()
    {
        return (bool) get_option( 'bookly_app_show_email_confirm', false );
    }

    /**
     * Whether to show notes field.
     *
     * @return bool
     */
    public static function showNotes()
    {
        return (bool) get_option( 'bookly_app_show_notes', false );
    }

    /**
     * Whether to show fully booked time slots in the second step of booking form.
     *
     * @return bool
     */
    public static function showBlockedTimeSlots()
    {
        return (bool) get_option( 'bookly_app_show_blocked_timeslots', false );
    }

    /**
     * Whether to show wide time slots in the time step of booking form.
     *
     * @return bool
     */
    public static function showWideTimeSlots()
    {
        return self::groupBookingActive() && get_option( 'bookly_group_booking_app_show_nop' );
    }

    /**
     * Whether to show single time slot in the time step of booking form.
     *
     * @return bool
     */
    public static function showSingleTimeSlot()
    {
        return get_option( 'bookly_app_show_slots' ) === 'single';
    }

    /**
     * Whether to show single time slot per day in the time step of booking form.
     *
     * @return bool
     */
    public static function showSingleTimeSlotPerDay()
    {
        return get_option( 'bookly_app_show_slots' ) === 'single_per_day';
    }

    /**
     * Whether to show days in the second step of booking form in separate columns or not.
     *
     * @return bool
     */
    public static function showDayPerColumn()
    {
        return (bool) get_option( 'bookly_app_show_day_one_column', false );
    }

    /**
     * Whether to show login button at the time step of booking form.
     *
     * @return bool
     */
    public static function showLoginButton()
    {
        return (bool) get_option( 'bookly_app_show_login_button', false );
    }

    /**
     * Whether phone field is required at the Details step or not.
     *
     * @return bool
     */
    public static function phoneRequired()
    {
        return in_array( 'phone', get_option( 'bookly_cst_required_details', array() ) );
    }

    /**
     * Whether email field is required at the Details step or not.
     *
     * @return bool
     */
    public static function emailRequired()
    {
        return in_array( 'email', get_option( 'bookly_cst_required_details', array() ) );
    }

    /**
     * @return bool
     */
    public static function addressRequired()
    {
        return get_option( 'bookly_cst_required_address' ) == 1;
    }

    /**
     * Whether customer duplicates are allowed or not
     *
     * @return bool
     */
    public static function allowDuplicates()
    {
        return get_option( 'bookly_cst_allow_duplicates' ) == 1;
    }

    /**
     * Whether custom fields attached to services or not.
     *
     * @return bool
     */
    public static function customFieldsPerService()
    {
        return get_option( 'bookly_custom_fields_per_service' ) == 1;
    }

    /**
     * Whether to show single instance of custom fields for repeating services.
     *
     * @return bool
     */
    public static function customFieldsMergeRepeating()
    {
        return get_option( 'bookly_custom_fields_merge_repeating' ) == 1;
    }

    /**
     * Whether step Cart is enabled or not.
     *
     * @return bool
     */
    public static function showStepCart()
    {
        return self::cartActive() && get_option( 'bookly_cart_enabled' );
    }

    /**
     * Check if emails are sent as HTML or plain text.
     *
     * @return bool
     */
    public static function sendEmailAsHtml()
    {
        return get_option( 'bookly_email_send_as' ) == 'html';
    }

    /**
     * Whether to show only business days in calendar
     *
     * @return bool
     */
    public static function showOnlyBusinessDaysInCalendar()
    {
        return get_option( 'bookly_cal_show_only_business_days' ) == 1;
    }

    /**
     * Whether to show only business hours in calendar
     *
     * @return bool
     */
    public static function showOnlyBusinessHoursInCalendar()
    {
        return get_option( 'bookly_cal_show_only_business_hours' ) == 1;
    }

    /**
     * Whether to show only staff members with appointments in calendar Day view or not
     *
     * @return bool
     */
    public static function showOnlyStaffWithAppointmentsInCalendarDayView()
    {
        return get_option( 'bookly_cal_show_only_staff_with_appointments' ) == 1;
    }

    /**
     * Get business hours settings
     *
     * @return array
     */
    public static function getBusinessHours()
    {
        $result = array();
        foreach ( array( 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' ) as $week_day ) {
            $result[] = array(
                'start' => get_option( 'bookly_bh_' . $week_day . '_start' ) ?: null,
                'end' => get_option( 'bookly_bh_' . $week_day . '_end' ) ?: null,
            );
        }

        return $result;
    }

    /**
     * Get WordPress time zone setting.
     *
     * @return string
     */
    public static function getWPTimeZone()
    {
        if ( self::$wp_timezone === null ) {
            if ( $timezone = get_option( 'timezone_string' ) ) {
                // If site timezone string exists, return it.
                self::$wp_timezone = $timezone;
            } else {
                // Otherwise return offset.
                $gmt_offset = get_option( 'gmt_offset' );
                self::$wp_timezone = Utils\DateTime::formatOffset( $gmt_offset * HOUR_IN_SECONDS );
            }
        }

        return self::$wp_timezone;
    }

    /**
     * Get default appointment status
     *
     * @return string
     */
    public static function getDefaultAppointmentStatus()
    {
        $status = get_option( 'bookly_appointment_default_status' );
        if ( ! in_array( $status, CustomerAppointment::getStatuses() ) ) {
            $status = CustomerAppointment::STATUS_APPROVED;
        }

        return $status;
    }

    /**
     * Is bookly setup in progress
     *
     * @return bool
     */
    public static function setupMode()
    {
        return (bool) get_option( 'bookly_setup_step', false );
    }

    /**
     * @param array $row
     * @return string
     */
    public static function getServiceInfoCodes( $row )
    {
        $service_name = $row['title'] === '' ? __( 'Untitled', 'bookly' ) : Utils\Common::getTranslatedString( 'service_' . $row['id'], $row['title'] );
        $service_info = $row['info'] === '' ? '' : Utils\Common::getTranslatedString( 'service_' . $row['id'] . '_info', $row['info'] );
        $service_image_url = Utils\Common::getAttachmentUrl( $row['attachment_id'] );
        $service_codes = array(
            'service_name' => $service_name,
            'service_info' => $service_info,
            'service_image' => Common::getImageTag( $service_image_url, $service_name ),
            'service_image_url' => $service_image_url,
            'service_price' => Price::format( $row['price'] ),
            'service_duration' => DateTime::secondsToInterval( $row['duration'] ),
        );

        return nl2br( Codes::replace( Utils\Common::getTranslatedOption( 'bookly_l10n_step_service_service_info' ), $service_codes, false ) );
    }

    /**
     * @param array $row
     * @return string
     */
    public static function getCategoryInfoCodes( $row )
    {
        $category_name = $row['name'] === '' ? '' : Utils\Common::getTranslatedString( 'category_' . $row['id'], $row['name'] );
        $category_info = $row['info'] === '' ? '' : Utils\Common::getTranslatedString( 'category_' . $row['id'] . '_info', $row['info'] );
        $category_image_url = Utils\Common::getAttachmentUrl( $row['attachment_id'] );
        $category_codes = array(
            'category_name' => $category_name,
            'category_info' => $category_info,
            'category_image' => Common::getImageTag( $category_image_url, $category_name ),
            'category_image_url' => $category_image_url,
        );

        return nl2br( Codes::replace( Utils\Common::getTranslatedOption( 'bookly_l10n_step_service_category_info' ), $category_codes, false ) );
    }

    /******************************************************************************************************************
     * Add-ons                                                                                                        *
     ******************************************************************************************************************/

    /**
     * WooCommerce Plugin enabled or not.
     *
     * @return bool
     */
    public static function wooCommerceEnabled()
    {
        return ( self::proActive() && get_option( 'bookly_wc_enabled' ) && get_option( 'bookly_wc_product' ) && class_exists( 'WooCommerce', false ) && ( wc_get_cart_url() !== false ) );
    }

    /**
     * Call magic functions.
     *
     * @param $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic( $name, array $arguments )
    {
        // <add-on>Active
        // <add-on>Enabled
        if ( preg_match( '/^(\w+)Active/', $name, $match ) ) {
            // Check if Pro Active
            /** @var \BooklyPro\Lib\Plugin $pro_class */
            $pro_class = '\BooklyPro\Lib\Plugin';
            if ( class_exists( $pro_class, false ) ) {
                /** @var Base\Plugin $plugin_class */
                $plugin_class = sprintf( '\Bookly%s\Lib\Plugin', ucfirst( $match[1] ) );

                return class_exists( $plugin_class, false );
            }

            return false;
        }

        return null;
    }

    /**
     * @return string
     */
    public static function getLocale()
    {
        $locale = get_locale();
        if ( function_exists( 'get_user_locale' ) ) {
            $locale = get_user_locale();
        }

        return $locale;
    }

    /**
     * @return string
     */
    public static function getShortLocale()
    {
        $locale = self::getLocale();
        // Cut tail for WP locales like Netherlands (Formeel) nl_NL_formal, Deutsch (Schweiz, Du) de_CH_informal and etc
        if ( $second = strpos( $locale, '_', min( 3, strlen( $locale ) ) ) ) {
            $locale = substr( $locale, 0, $second );
        }

        return $locale;
    }

    /**
     * Get current currency code
     *
     * @return string
     */
    public static function getCurrency()
    {
        static $currency;
        if ( $currency === null ) {
            $currency = get_option( 'bookly_pmt_currency' );
        }

        return $currency;
    }

    /**
     * @return bool
     */
    public static function isZeroDecimalsCurrency()
    {
        return in_array( self::getCurrency(), array( 'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'VND', 'VUV', 'XAF', 'XOF', 'XPF', ) );
    }

    /**
     * Get payment systems preference
     *
     * @return array
     */
    public static function getGatewaysPreference()
    {
        $order = get_option( 'bookly_pmt_order', '' );

        return is_array( $order )
            ? $order
            : explode( ',', $order );
    }
}