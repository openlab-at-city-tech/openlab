<?php
namespace Bookly\Backend\Components\Dialogs\Appointment\Edit;

use Bookly\Lib;
use Bookly\Lib\Entities\CustomerAppointment;

class Dialog extends Lib\Base\Component
{
    /**
     * Render create/edit appointment dialog.
     *
     * @param bool $show_wp_users
     */
    public static function render( $show_wp_users = true )
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/appointment.js' => array( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueData( array(
            'extras_list',
            'extras_multiply_nop',
        ), 'bookly-appointment.js' );

        $statuses = array();
        foreach ( CustomerAppointment::getStatuses() as $status ) {
            $statuses[] = array(
                'id' => $status,
                'title' => CustomerAppointment::statusToString( $status ),
                'icon' => CustomerAppointment::statusToIcon( $status ),
            );
        }

        wp_localize_script( 'bookly-appointment.js', 'BooklyL10nAppDialog', Proxy\Shared::prepareL10n( array(
            'statuses' => $statuses,
            'freeStatuses' => Lib\Proxy\CustomStatuses::prepareFreeStatuses( array(
                CustomerAppointment::STATUS_CANCELLED,
                CustomerAppointment::STATUS_REJECTED,
                CustomerAppointment::STATUS_WAITLISTED,
            ) ),
            'send_notifications' => (int) get_user_meta( get_current_user_id(), 'bookly_appointment_form_send_notifications', true ),
            'appropriate_slots' => get_option( 'bookly_appointments_displayed_time_slots', 'all' ) === 'appropriate',
            'service_main' => get_option( 'bookly_appointments_main_value', 'all' ) === 'service',
            'l10n' => array(
                'edit_appointment' => __( 'Edit appointment', 'bookly' ),
                'new_appointment' => __( 'New appointment', 'bookly' ),
                'send_notifications' => __( 'Send notifications', 'bookly' ),
                'provider' => __( 'Provider', 'bookly' ),
                'service' => __( 'Service', 'bookly' ),
                'select_a_service' => __( '-- Select a service --', 'bookly' ),
                'location' => __( 'Location', 'bookly' ),
                'staff_any' => get_option( 'bookly_l10n_option_employee' ),
                'date' => __( 'Date', 'bookly' ),
                'period' => __( 'Period', 'bookly' ),
                'to' => __( 'to', 'bookly' ),
                'customers' => __( 'Customers', 'bookly' ),
                'selected_maximum' => __( 'Selected / maximum', 'bookly' ),
                'minimum_capacity' => __( 'Minimum capacity', 'bookly' ),
                'edit_booking_details' => __( 'Edit booking details', 'bookly' ),
                'status' => __( 'Status', 'bookly' ),
                'payment' => __( 'Payment', 'bookly' ),
                'remove_customer' => __( 'Remove customer', 'bookly' ),
                'search_customers' => __( '-- Search customers --', 'bookly' ),
                'new_customer' => __( 'New customer', 'bookly' ),
                'no_result_found' => __( 'No result found', 'bookly' ),
                'searching' => __( 'Searching', 'bookly' ),
                'save' => __( 'Save', 'bookly' ),
                'close' => __( 'Close', 'bookly' ),
                'internal_note' => __( 'Internal note', 'bookly' ),
                'chose_queue_type_info' => __( 'If you have added a new customer to this appointment or changed the appointment status for an existing customer, and for these records you want the corresponding email or SMS notifications to be sent to their recipients, select the "Send if new or status changed" option before clicking Send. You can also send notifications as if all customers were added as new by selecting "Send as for new".', 'bookly' ),
                'send_if_new_or_status_changed' => __( 'Send if new or status changed', 'bookly' ),
                'send_as_for_new' => __( 'Send as for new', 'bookly' ),
                'send' => __( 'Send', 'bookly' ),
                'view' => __( 'View', 'bookly' ),
                'internal_note_help' => __( 'This text can be inserted into notifications with {internal_note} code', 'bookly' ),
                'notices' => array(
                    'service_required' => __( 'Please select a service', 'bookly' ),
                    'provider_required' => __( 'Please select a provider', 'bookly' ),
                    'date_interval_not_available' => __( 'The selected period is occupied by another appointment', 'bookly' ),
                    'date_interval_warning' => __( 'Selected period doesn\'t match service duration', 'bookly' ),
                    'interval_not_in_staff_schedule' => __( 'Selected period doesn\'t match provider\'s schedule', 'bookly' ),
                    'no_timeslots_available' => __( 'No timeslots available', 'bookly' ),
                ),
            ),
        ) ) );

        self::renderTemplate( 'edit', compact( 'show_wp_users' ) );
    }
}