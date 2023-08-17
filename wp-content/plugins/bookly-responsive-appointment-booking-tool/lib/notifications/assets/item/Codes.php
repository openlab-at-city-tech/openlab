<?php
namespace Bookly\Lib\Notifications\Assets\Item;

use Bookly\Lib\Config;
use Bookly\Lib\DataHolders\Booking\Item;
use Bookly\Lib\Entities\Category;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Notifications\Assets\Order;
use Bookly\Lib\Notifications\Base\Reminder;
use Bookly\Lib\Notifications\WPML;
use Bookly\Lib\Utils;

/**
 * Class Codes
 *
 * @package Bookly\Lib\Notifications\Assets\Item
 */
class Codes extends Order\Codes
{
    // Core
    public $appointment_id;
    public $appointment_end;
    public $appointment_end_info;
    public $appointment_notes;
    public $appointment_online_meeting_url;
    public $appointment_online_meeting_password;
    public $appointment_online_meeting_start_url;
    public $appointment_online_meeting_join_url;
    public $appointment_start;
    public $appointment_start_info;
    public $appointment_token;
    public $cancellation_time_limit;
    public $booking_number;
    public $cancellation_reason;
    public $category_info;
    public $category_image;
    public $category_name;
    public $number_of_persons;
    public $service_duration;
    public $service_info;
    public $service_name;
    public $service_image;
    public $service_price;
    public $staff_email;
    public $staff_info;
    public $staff_name;
    public $staff_phone;
    public $staff_photo;
    public $staff_timezone;
    public $staff_category_info;
    public $staff_category_image;
    public $staff_category_name;
    public $status;
    public $internal_note;
    // Custom Fields
    public $custom_fields;
    public $custom_fields_data;
    public $custom_fields_2c;
    // Customer Information
    public $info_fields;
    public $info_fields_data;
    // Files
    public $files_count;
    // Locations
    public $location_info;
    public $location_name;
    // Packages
    public $package_life_time;
    public $package_name;
    public $package_price;
    public $package_size;
    // Ratings
    public $staff_rating_url;
    // Recurring Appointments
    public $schedule;
    public $series_token;
    // Service Extras
    public $extras;
    public $extras_total_price;
    // Taxes
    public $service_tax;
    public $service_tax_rate;

    /** @var Item */
    protected $item;
    /** @var string */
    protected $lang;
    /** @var string */
    protected $recipient;

    /**
     * Prepare codes for given order item.
     *
     * @param Item $item
     * @param string $recipient "client" or "staff"
     */
    public function prepareForItem( Item $item, $recipient )
    {
        $lang = WPML::getLang();

        if (
            $this->item === $item &&
            $this->lang == $lang &&
            (
                $this->recipient == $recipient || (
                    $item->getCA()->getTimeZoneOffset() === null && $item->getStaff()->getTimeZone( false ) === null
                )
            )
        ) {
            return;
        }

        $this->item = $item;
        $this->lang = $lang;
        $this->recipient = $recipient;
        $category = $item->getService()->getCategoryId() ? Category::find( $item->getService()->getCategoryId() ) : false;

        $this->appointment_end = $this->tz( $item->getTotalEnd()->format( 'Y-m-d H:i:s' ) );
        $this->appointment_end_info = $item->getService()->getEndTimeInfo();
        $this->appointment_id = $item->getAppointment()->getId();
        $this->appointment_notes = $item->getCA()->getNotes();
        $this->appointment_start = $this->tz( $item->getAppointment()->getStartDate() );
        $this->appointment_start_info = $item->getService()->getStartTimeInfo();
        $this->appointment_token = $item->getCA()->getToken();
        $this->booking_number = Config::groupBookingActive() ? $item->getAppointment()->getId() . '-' . $item->getCA()->getId() : $item->getCA()->getId();
        $this->category_name = $item->getService()->getTranslatedCategoryName();
        $this->category_info = $category ? $category->getTranslatedInfo() : '';
        $this->category_image = $category ? $category->getImageUrl() : '';
        $this->client_timezone = $item->getCA()->getTimeZone()
            ?: ( $item->getCA()->getTimeZoneOffset() !== null
                ? 'UTC' . Utils\DateTime::formatOffset( - $item->getCA()->getTimeZoneOffset() * 60 )
                : '' );
        $this->client_locale = $item->getCA()->getLocale() ?: '';
        $this->number_of_persons = $item->getCA()->getNumberOfPersons();
        $this->service_duration = $item->getServiceDuration();
        $this->service_info = $item->getService()->getTranslatedInfo();
        $this->service_name = $item->getService()->getTranslatedTitle();
        $this->service_price = $item->getServicePrice();
        $this->staff_email = $item->getStaff()->getEmail();
        $this->staff_info = $item->getStaff()->getTranslatedInfo();
        $this->staff_name = $item->getStaff()->getTranslatedName();
        $this->staff_phone = $item->getStaff()->getPhone();
        $this->staff_photo = $item->getStaff()->getImageUrl();
        $this->service_image = $item->getService()->getImageUrl();
        $this->staff_timezone = $item->getStaff()->getTimeZone( false );
        $this->internal_note = $item->getAppointment()->getInternalNote();
        if ( ! $this->order->hasPayment() ) {
            $this->total_price = $item->getTotalPrice();
            $this->total_tax = $item->getTax();
            if ( Config::taxesActive() && get_option( 'bookly_taxes_in_price' ) == 'excluded' ) {
                $this->total_price += $this->total_tax;
            }
        }

        Proxy\Shared::prepareCodes( $this );
    }

    /**
     * @param array $replace_codes
     * @param string $format
     * @return array
     */
    public function prepareReplaceCodes( $replace_codes, $format )
    {
        // Prepare data.
        $staff_photo = '';
        $service_image = '';
        $category_image = '';
        if ( $format === 'html' ) {
            $category_image = Utils\Common::getImageTag( $this->category_image, $this->category_name );
            $service_image = Utils\Common::getImageTag( $this->service_image, $this->service_name );
            $staff_photo = Utils\Common::getImageTag( $this->staff_photo, $this->staff_name );
        }
        $cancel_appointment_confirm_url = get_option( 'bookly_url_cancel_confirm_page_url' );
        $cancel_appointment_confirm_url = $this->appointment_token
            ? add_query_arg( 'bookly-appointment-token', $this->appointment_token, $cancel_appointment_confirm_url )
            : '';

        // Add replace codes.
        $replace_codes += array(
            'appointment_id' => $this->appointment_id,
            'appointment_date' => $this->appointment_start === null ? __( 'N/A', 'bookly' ) : Utils\DateTime::formatDate( $this->appointment_start ),
            'appointment_time' => $this->appointment_start === null ? __( 'N/A', 'bookly' ) : ( $this->service_duration < DAY_IN_SECONDS ? Utils\DateTime::formatTime( $this->appointment_start ) : $this->appointment_start_info ),
            'appointment_end_date' => $this->appointment_start === null ? __( 'N/A', 'bookly' ) : Utils\DateTime::formatDate( $this->appointment_end ),
            'appointment_end_time' => $this->appointment_start === null ? __( 'N/A', 'bookly' ) : ( $this->service_duration < DAY_IN_SECONDS ? Utils\DateTime::formatTime( $this->appointment_end ) : $this->appointment_end_info ),
            'appointment_notes' => $this->appointment_notes ? ( $format == 'html' ? nl2br( $this->appointment_notes ) : $this->appointment_notes ) : '',
            'approve_appointment_url' => $this->appointment_token ? admin_url( 'admin-ajax.php?action=bookly_approve_appointment&token=' . urlencode( Utils\Common::xorEncrypt( $this->appointment_token, 'approve' ) ) ) : '',
            'booking_number' => $this->booking_number,
            'cancellation_reason' => $this->cancellation_reason,
            'cancel_appointment_url' => $this->appointment_token ? admin_url( 'admin-ajax.php?action=bookly_cancel_appointment&token=' . $this->appointment_token ) : '',
            'cancel_appointment_confirm_url' => $cancel_appointment_confirm_url,
            'category_name' => $this->category_name,
            'category_info' => $this->category_info,
            'category_image' => $category_image,
            'google_calendar_url' => sprintf(
                'https://calendar.google.com/calendar/render?action=TEMPLATE&text=%s&dates=%s/%s&details=%s',
                urlencode( $this->service_name ),
                date( 'Ymd\THis', strtotime( $this->appointment_start ) ),
                date( 'Ymd\THis', strtotime( $this->appointment_end ) ),
                urlencode( sprintf( "%s\n%s", $this->service_name, $this->staff_name ) )
            ),
            'number_of_persons' => $this->number_of_persons,
            'reject_appointment_url' => $this->appointment_token
                ? admin_url( 'admin-ajax.php?action=bookly_reject_appointment&token=' . urlencode( Utils\Common::xorEncrypt( $this->appointment_token, 'reject' ) ) )
                : '',
            'service_info' => $format === 'html' && $this->service_info ? nl2br( $this->service_info ) : $this->service_info,
            'service_name' => $this->service_name,
            'service_image' => $service_image,
            'service_price' => Utils\Price::format( $this->service_price ),
            'service_duration' => Utils\DateTime::secondsToInterval( $this->service_duration ),
            'staff_email' => $this->staff_email,
            'staff_info' => $format === 'html' && $this->staff_info ? nl2br( $this->staff_info ) : $this->staff_info,
            'staff_name' => $this->staff_name,
            'staff_phone' => $this->staff_phone,
            'staff_photo' => $staff_photo,
            'staff_timezone' => $this->staff_timezone,
            'internal_note' => $this->internal_note,
        );
        $replace_codes['appointments'] = array(
            array(
                'appointment_date' => $replace_codes['appointment_date'],
                'appointment_time' => $replace_codes['appointment_time'],
                'internal_note' => $replace_codes['internal_note'],
                'category_name' => $replace_codes['category_name'],
                'service_name' => $replace_codes['service_name'],
                'service_price' => $replace_codes['service_price'],
                'staff_name' => $replace_codes['staff_name'],
                'staff_phone' => $replace_codes['staff_phone'],
                'staff_email' => $replace_codes['staff_email'],
            ),
        );
        $replace_codes['participants'][0]['status'] = CustomerAppointment::statusToString( $this->status );
        $replace_codes['cancel_appointment'] = $format === 'html'
            ? sprintf( '<a href="%1$s">%1$s</a>', $replace_codes['cancel_appointment_url'] )
            : $replace_codes['cancel_appointment_url'];

        return Proxy\Shared::prepareReplaceCodes( $replace_codes, $this, $format );
    }

    /**
     * @inheritDoc
     */
    protected function getReplaceCodes( $format )
    {
        $replace_codes = parent::getReplaceCodes( $format );

        $replace_codes += $this->prepareReplaceCodes( $replace_codes, $format );

        return $replace_codes;
    }

    /**
     * Apply client time zone to given datetime string in WP time zone if recipient is client
     * and staff time zone if recipient is staff
     *
     * @param string $datetime
     * @return false|string
     */
    public function tz( $datetime )
    {
        if ( $this->forClient() ) {
            return $this->applyItemTz( $datetime, $this->item );
        }

        if ( $this->forStaff() ) {
            $staff_tz = $this->item->getStaff()->getTimeZone();
            if ( $staff_tz ) {
                return Utils\DateTime::convertTimeZone( $datetime, Config::getWPTimeZone(), $staff_tz );
            }
        }

        return $datetime;
    }

    /**
     * Get item.
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Check whether recipient is customer
     *
     * @return bool
     */
    public function forClient()
    {
        return $this->recipient == Reminder::RECIPIENT_CLIENT;
    }

    /**
     * Check whether recipient is staff
     *
     * @return bool
     */
    public function forStaff()
    {
        return $this->recipient == Reminder::RECIPIENT_STAFF;
    }

    /**
     * Check whether recipient is admins
     *
     * @return bool
     */
    public function forAdmins()
    {
        return $this->recipient == Reminder::RECIPIENT_ADMINS;
    }
}