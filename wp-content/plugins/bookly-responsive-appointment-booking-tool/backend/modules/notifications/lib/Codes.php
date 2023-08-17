<?php
namespace Bookly\Backend\Modules\Notifications\Lib;

use Bookly\Lib;
use Bookly\Lib\Entities\Notification;
use Bookly\Backend\Modules\Notifications\Proxy;

/**
 * Class Codes
 *
 * @package Bookly\Backend\Modules\Notifications\Lib
 */
class Codes
{
    /** @var string */
    protected $type;

    /** @var array */
    protected $codes;

    /**
     * Constructor.
     *
     * @param string $type
     */
    public function __construct( $type = 'email' )
    {
        $this->type = $type;
        $this->codes = array(
            'appointment' => array(
                'appointment_id' => array( 'description' => __( 'Appointment ID', 'bookly' ) ),
                'appointment_date' => array( 'description' => __( 'Date of appointment', 'bookly' ), 'if' => true ),
                'appointment_end_date' => array( 'description' => __( 'End date of appointment', 'bookly' ), 'if' => true ),
                'appointment_end_time' => array( 'description' => __( 'End time of appointment', 'bookly' ), 'if' => true ),
                'appointment_notes' => array( 'description' => __( 'Customer notes for appointment', 'bookly' ), 'if' => true ),
                'appointment_time' => array( 'description' => __( 'Time of appointment', 'bookly' ), 'if' => true ),
                'booking_number' => array( 'description' => __( 'Booking number', 'bookly' ) ),
                'internal_note' => array( 'description' => __( 'Internal note', 'bookly' ) ),
            ),
            'cart' => array(
                'cart_info' => array( 'description' => __( 'Cart information', 'bookly' ) ),
                'cart_info_c' => array( 'description' => __( 'Cart information with cancel', 'bookly' ) ),
                'cancel_all_combined_appointments' => array( 'description' => __( 'Cancel all appointments in chain link', 'bookly' ) ),
                'cancel_all_combined_appointments_url' => array( 'description' => __( 'URL of cancel all appointments link (to use inside <a> tag)', 'bookly' ) ),
                'appointment_notes' => array( 'description' => __( 'Customer notes for appointment', 'bookly' ), 'if' => true ),
            ),
            'category' => array(
                'category_name' => array( 'description' => __( 'Name of category', 'bookly' ), 'if' => true ),
                'category_info' => array( 'description' => __( 'Info of category', 'bookly' ), 'if' => true ),
            ),
            'company' => array(
                'company_address' => array( 'description' => __( 'Address of company', 'bookly' ), 'if' => true ),
                'company_name' => array( 'description' => __( 'Name of company', 'bookly' ), 'if' => true ),
                'company_phone' => array( 'description' => __( 'Company phone', 'bookly' ), 'if' => true ),
                'company_website' => array( 'description' => __( 'Company web-site address', 'bookly' ), 'if' => true ),
            ),
            'customer' => array(
                'client_address' => array( 'description' => __( 'Address of client', 'bookly' ), 'if' => true ),
                'client_email' => array( 'description' => __( 'Email of client', 'bookly' ), 'if' => true ),
                'client_first_name' => array( 'description' => __( 'First name of client', 'bookly' ), 'if' => true ),
                'client_last_name' => array( 'description' => __( 'Last name of client', 'bookly' ), 'if' => true ),
                'client_name' => array( 'description' => __( 'Full name of client', 'bookly' ), 'if' => true ),
                'client_note' => array( 'description' => __( 'Note of client', 'bookly' ), 'if' => true ),
                'client_phone' => array( 'description' => __( 'Phone of client', 'bookly' ), 'if' => true ),
            ),
            'customer_timezone' => array(
                'client_timezone' => array( 'description' => __( 'Time zone of client', 'bookly' ), 'if' => true ),
            ),
            'customer_locale' => array(
                'client_locale' => array( 'description' => __( 'Locale of client', 'bookly' ), 'if' => true ),
            ),
            'customer_appointment' => array(
                'approve_appointment_url' => array( 'description' => __( 'URL of approve appointment link (to use inside <a> tag)', 'bookly' ) ),
                'cancel_appointment_confirm_url' => array( 'description' => __( 'URL of cancel appointment link with confirmation (to use inside <a> tag)', 'bookly' ) ),
                'cancel_appointment_url' => array( 'description' => __( 'URL of cancel appointment link (to use inside <a> tag)', 'bookly' ) ),
                'cancellation_reason' => array( 'description' => __( 'Reason mentioned while cancelling appointment', 'bookly' ), 'if' => true ),
                'google_calendar_url' => array( 'description' => __( 'URL for adding event to Google Calendar (to use inside <a> tag)', 'bookly' ) ),
                'reject_appointment_url' => array( 'description' => __( 'URL of reject appointment link (to use inside <a> tag)', 'bookly' ) ),
                'cancellation_time_limit' => array( 'description' => __( 'Time limit to which appointments can be cancelled ', 'bookly' ) ),
            ),
            'payment' => array(
                'payment_type' => array( 'description' => __( 'Payment type', 'bookly' ) ),
                'payment_status' => array( 'description' => __( 'Payment status', 'bookly' ) ),
                'total_price' => array( 'description' => __( 'Total price of booking (sum of all cart items after applying coupon)' ) ),
            ),
            'service' => array(
                'service_duration' => array( 'description' => __( 'Duration of service', 'bookly' ) ),
                'service_info' => array( 'description' => __( 'Info of service', 'bookly' ), 'if' => true ),
                'service_name' => array( 'description' => __( 'Name of service', 'bookly' ) ),
                'service_price' => array( 'description' => __( 'Price of service', 'bookly' ) ),
            ),
            'staff' => array(
                'staff_email' => array( 'description' => __( 'Email of staff', 'bookly' ), 'if' => true ),
                'staff_info' => array( 'description' => __( 'Info of staff', 'bookly' ), 'if' => true ),
                'staff_name' => array( 'description' => __( 'Name of staff', 'bookly' ) ),
                'staff_phone' => array( 'description' => __( 'Phone of staff', 'bookly' ), 'if' => true ),
                'staff_category_name' => array( 'description' => __( 'Name of staff category', 'bookly' ), 'if' => true ),
                'staff_category_info' => array( 'description' => __( 'Info of staff category', 'bookly' ), 'if' => true ),
            ),
            'staff_agenda' => array(
                'agenda_date' => array( 'description' => __( 'Agenda date', 'bookly' ) ),
                'next_day_agenda' => array( 'description' => __( 'Staff agenda for next day', 'bookly' ) ),
                'tomorrow_date' => array( 'description' => __( 'Date of next day', 'bookly' ) ),
            ),
            'user_credentials' => array(
                'new_password' => array( 'description' => __( 'Customer new password', 'bookly' ) ),
                'new_username' => array( 'description' => __( 'Customer new username', 'bookly' ) ),
                'site_address' => array( 'description' => __( 'Site address', 'bookly' ) ),
            ),
            'verification_code' => array(
                'verification_code' => array( 'description' => __( 'Verification code', 'bookly' ) ),
            ),
        );
        $this->codes['appointments_list'] = array(
            'appointments' => array(
                'description' => array(
                    __( 'Loop over appointments list', 'bookly' ),
                    __( 'Loop over appointments list with delimiter', 'bookly' ),
                ),
                'loop' => array(
                    'item' => 'appointment',
                    'codes' => array_merge(
                        $this->codes['appointment'],
                        $this->codes['service'],
                        $this->codes['staff'],
                        $this->codes['category'],
                        $this->codes['customer_appointment']
                    ),
                ),
            ),
        );

        if ( $type == 'email' ) {
            // Only email.
            $this->codes['category']['category_image'] = array( 'description' => __( 'Image of category', 'bookly' ), 'if' => true );
            $this->codes['company']['company_logo'] = array( 'description' => __( 'Company logo', 'bookly' ), 'if' => true );
            $this->codes['customer_appointment']['cancel_appointment'] = array( 'description' => __( 'Cancel appointment link', 'bookly' ) );
            $this->codes['service']['service_image'] = array( 'description' => __( 'Image of service', 'bookly' ), 'if' => true );
            $this->codes['staff']['staff_photo'] = array( 'description' => __( 'Photo of staff', 'bookly' ), 'if' => true );
        }

        // Add codes from add-ons.
        $this->codes = Proxy\Shared::prepareNotificationCodes( $this->codes, $type );
    }

    /**
     * Render codes for given notification type.
     *
     * @param string $notification_type
     */
    public function render( $notification_type )
    {
        $codes = $this->_build( $notification_type );
        ksort( $codes );

        $tbody = '';
        foreach ( $codes as $key => $code ) {
            if ( ! isset( $code['loop'] ) ) {
                $tbody .= sprintf(
                    '<tr><td class="p-0"><input value="{%s}" class="border-0 bookly-outline-0" readonly="readonly" onclick="this.select()" /> &ndash; %s</td></tr>',
                    $key,
                    esc_html( $code['description'] )
                );
            }
        }

        printf(
            '<table class="bookly-js-codes bookly-js-codes-%s"><tbody>%s</tbody></table>',
            $notification_type,
            $tbody
        );
    }

    /**
     * Get a list of codes.
     *
     * @param string $notification_type
     * @return array
     */
    public function getCodes( $notification_type )
    {
        $codes = $this->_build( $notification_type );
        ksort( $codes );

        return $codes;
    }

    /**
     * Build array of codes for given notification type.
     *
     * @param $notification_type
     * @return array
     */
    private function _build( $notification_type )
    {
        $codes = array();

        switch ( $notification_type ) {
            case Notification::TYPE_APPOINTMENT_REMINDER:
            case Notification::TYPE_NEW_BOOKING:
            case Notification::TYPE_NEW_BOOKING_RECURRING:
            case Notification::TYPE_LAST_CUSTOMER_APPOINTMENT:
            case Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED:
            case Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING:
                $codes = array_merge(
                    $this->codes['appointment'],
                    $this->codes['category'],
                    $this->codes['company'],
                    $this->codes['customer'],
                    $this->codes['customer_appointment'],
                    $this->codes['customer_timezone'],
                    $this->codes['customer_locale'],
                    $this->codes['payment'],
                    $this->codes['service'],
                    $this->codes['staff']
                );
                if ( Lib\Config::invoicesActive() &&
                    in_array( $notification_type, array(
                        Notification::TYPE_NEW_BOOKING,
                        Notification::TYPE_NEW_BOOKING_RECURRING,
                        Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED,
                        Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING,
                    ) )
                ) {
                    $codes = array_merge( $codes, $this->codes['invoice'] );
                }
                if ( in_array( $notification_type, array( Notification::TYPE_NEW_BOOKING_RECURRING, Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING ) ) ) {
                    $codes = array_merge( $codes, $this->codes['series'] );
                }
                if ( Lib\Config::ratingsActive() && ( $notification_type == Notification::TYPE_APPOINTMENT_REMINDER ) ) {
                    $codes = array_merge( $codes, $this->codes['rating'] );
                }
                break;
            case Notification::TYPE_STAFF_DAY_AGENDA:
                $codes = array_merge(
                    $this->codes['company'],
                    $this->codes['staff'],
                    $this->codes['staff_agenda']
                );
                break;
            case Notification::TYPE_CUSTOMER_BIRTHDAY:
                $codes = array_merge(
                    $this->codes['company'],
                    $this->codes['customer']
                );
                break;
            case Notification::TYPE_NEW_BOOKING_COMBINED:
                $codes = array_merge(
                    $this->codes['cart'],
                    $this->codes['company'],
                    $this->codes['customer'],
                    $this->codes['customer_timezone'],
                    $this->codes['customer_locale'],
                    $this->codes['payment'],
                    $this->codes['appointments_list']
                );
                break;
            case Notification::TYPE_VERIFY_EMAIL:
            case Notification::TYPE_VERIFY_PHONE:
                $codes = array_merge(
                    $this->codes['company'],
                    $this->codes['customer'],
                    $this->codes['verification_code']
                );
                break;
            default:
                $codes = Proxy\Shared::buildNotificationCodesList( $codes, $notification_type, $this->codes );
        }

        return $codes;
    }

    /**
     * @param array $groups
     * @return array
     */
    public function getGroups( array $groups )
    {
        $codes = array();
        foreach ( $groups as $group ) {
            if ( array_key_exists( $group, $this->codes ) ) {
                $codes = array_merge( $codes, $this->codes[ $group ] );
            }
        }

        ksort( $codes );

        return $codes;
    }
}