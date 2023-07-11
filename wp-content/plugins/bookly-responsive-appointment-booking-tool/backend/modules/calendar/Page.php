<?php
namespace Bookly\Backend\Modules\Calendar;

use Bookly\Lib;
use Bookly\Lib\Config;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Entities\Staff;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Utils\Price;

/**
 * Class Page
 *
 * @package Bookly\Backend\Modules\Calendar
 */
class Page extends Lib\Base\Ajax
{
    /**
     * Render page.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'module' => array( 'css/event-calendar.min.css' => array( 'bookly-backend-globals' ) ),
        ) );

        $id = Lib\Entities\Appointment::query( 'a' )
            ->select( 'MAX(id) as max_id' )
            ->fetchRow();
        update_option( 'bookly_cal_last_seen_appointment', $id['max_id'] );

        if ( Config::proActive() ) {
            if ( Common::isCurrentUserSupervisor() ) {
                $staff_members = Staff::query()
                    ->whereNot( 'visibility', 'archive' )
                    ->sortBy( 'position' )
                    ->find();
                $staff_dropdown_data = Lib\Proxy\Pro::getStaffDataForDropDown();
            } else {
                $staff_members = Staff::query()
                    ->where( 'wp_user_id', get_current_user_id() )
                    ->whereNot( 'visibility', 'archive' )
                    ->find();
                $staff_dropdown_data = array(
                    0 => array(
                        'name' => '',
                        'items' => empty ( $staff_members ) ? array() : array( $staff_members[0]->getFields() ),
                    ),
                );
            }
        } else {
            $staff = Staff::query()->findOne();
            $staff_members = $staff ? array( $staff ) : array();
            $staff_dropdown_data = array(
                0 => array(
                    'name' => '',
                    'items' => empty ( $staff_members ) ? array() : array( $staff_members[0]->getFields() ),
                ),
            );
        }

        self::enqueueScripts(
            $staff_members ?
                array(
                    'module' => array(
                        'js/event-calendar.min.js' => array( 'bookly-backend-globals' ),
                        'js/calendar-common.js' => array( 'bookly-event-calendar.min.js' ),
                        'js/calendar.js' => array( 'bookly-calendar-common.js', 'bookly-dropdown.js' ),
                    ),
                    'backend' => array(
                        'js/nav-scrollable.js' => array( 'bookly-backend-globals' ),
                    ),
                ) :
                array(
                    'alias' => array( 'bookly-backend-globals', ),
                ) );

        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        wp_localize_script( 'bookly-calendar.js', 'BooklyL10n', array_merge(
            Lib\Utils\Common::getCalendarSettings(),
            array(
                'delete' => __( 'Delete', 'bookly' ),
                'are_you_sure' => __( 'Are you sure?', 'bookly' ),
                'filterResourcesWithEvents' => Config::showOnlyStaffWithAppointmentsInCalendarDayView(),
                'recurring_appointments' => array(
                    'active' => (int) Config::recurringAppointmentsActive(),
                    'title' => __( 'Recurring appointments', 'bookly' ),
                ),
                'waiting_list' => array(
                    'active' => (int) Config::waitingListActive(),
                    'title' => __( 'On waiting list', 'bookly' ),
                ),
                'packages' => array(
                    'active' => (int) Config::packagesActive(),
                    'title' => __( 'Package', 'bookly' ),
                ),
            ) ) );

        $refresh_rate = get_user_meta( get_current_user_id(), 'bookly_calendar_refresh_rate', true );
        $services_dropdown_data = Common::getServiceDataForDropDown( 's.type = "simple"' );

        self::renderTemplate( 'calendar', compact( 'staff_members', 'staff_dropdown_data', 'services_dropdown_data', 'refresh_rate' ) );
    }

    /**
     * Build appointments for Event Calendar.
     *
     * @param Lib\Query $query
     * @param string $display_tz
     * @return array
     */
    public static function buildAppointmentsForCalendar( Lib\Query $query, $display_tz )
    {
        $one_participant = Lib\Utils\Codes::tokenize( '<div>' . str_replace( "\n", '</div><div>', get_option( 'bookly_cal_one_participant' ) ) . '</div>' );
        $many_participants = Lib\Utils\Codes::tokenize( '<div>' . str_replace( "\n", '</div><div>', get_option( 'bookly_cal_many_participants' ) ) . '</div>' );
        $tooltip = Lib\Utils\Codes::tokenize( '<i class="fas fa-fw fa-circle mr-1" style="color:{appointment_color}"></i><span>{service_name}</span>{#each participants as participant}<div class="d-flex"><div class="text-muted flex-fill" style="overflow-wrap: anywhere;">{participant.client_name}</div><div class="text-nowrap">{participant.nop}<span class="badge badge-{participant.status_color}">{participant.status}</span></div></div>{/each}<span class="d-block text-muted">{appointment_time} - {appointment_end_time}</span>' );
        $tooltip_all_day = Lib\Utils\Codes::tokenize( '<i class="fas fa-fw fa-circle mr-1" style="color:{appointment_color}"></i><span>{service_name}</span>{#each participants as participant}<div class="d-flex"><div class="text-muted flex-fill" style="overflow-wrap: anywhere;">{participant.client_name}</div><div class="text-nowrap">{participant.nop}<span class="badge badge-{participant.status_color}">{participant.status}</span></div></div>{/each}<span class="d-block text-muted">{description}</span>' );
        $postfix_any = sprintf( ' (%s)', get_option( 'bookly_l10n_option_employee' ) );
        $coloring_mode = get_option( 'bookly_cal_coloring_mode' );
        $default_codes = array(
            'amount_due' => '',
            'amount_paid' => '',
            'appointment_date' => '',
            'appointment_notes' => '',
            'appointment_time' => '',
            'booking_number' => '',
            'category_name' => '',
            'client_address' => '',
            'client_email' => '',
            'client_name' => '',
            'client_first_name' => '',
            'client_last_name' => '',
            'client_phone' => '',
            'client_birthday' => '',
            'client_note' => '',
            'company_address' => get_option( 'bookly_co_address' ),
            'company_name' => get_option( 'bookly_co_name' ),
            'company_phone' => get_option( 'bookly_co_phone' ),
            'company_website' => get_option( 'bookly_co_website' ),
            'custom_fields' => '',
            'extras' => '',
            'extras_total_price' => 0,
            'internal_note' => '',
            'location_name' => '',
            'location_info' => '',
            'number_of_persons' => '',
            'on_waiting_list' => '',
            'payment_status' => '',
            'payment_type' => '',
            'service_capacity' => '',
            'service_duration' => '',
            'service_info' => '',
            'service_name' => '',
            'service_price' => '',
            'signed_up' => '',
            'staff_email' => '',
            'staff_info' => '',
            'staff_name' => '',
            'staff_phone' => '',
            'status' => '',
            'total_price' => '',
        );
        $query
            ->select(
                'a.id, ca.id as ca_id, ca.series_id, a.staff_any, a.location_id, a.internal_note, a.start_date, DATE_ADD(a.end_date, INTERVAL a.extras_duration SECOND) AS end_date,
                COALESCE(s.title,a.custom_service_name) AS service_name, COALESCE(s.color,"silver") AS service_color, s.info AS service_info,
                COALESCE(ss.price,s.price,a.custom_service_price) AS service_price,
                st.id AS staff_id,
                st.full_name AS staff_name, st.email AS staff_email, st.info AS staff_info, st.phone AS staff_phone, st.color AS staff_color,
                (SELECT SUM(ca.number_of_persons) FROM ' . CustomerAppointment::getTableName() . ' ca WHERE ca.appointment_id = a.id) AS total_number_of_persons,
                s.duration,
                s.start_time_info,
                s.end_time_info,
                ca.number_of_persons,
                ca.units,
                ca.custom_fields,
                ca.status AS status,
                ca.extras,
                ca.extras_multiply_nop,
                ca.package_id,
                ca.notes AS appointment_notes,
                ct.name AS category_name,
                c.full_name AS client_name, c.first_name AS client_first_name, c.last_name AS client_last_name, c.phone AS client_phone, c.email AS client_email, c.id AS customer_id, c.birthday AS client_birthday, c.notes AS client_note,
                p.total, p.type AS payment_gateway, p.status AS payment_status, p.paid,
                (SELECT SUM(ca.number_of_persons) FROM ' . CustomerAppointment::getTableName() . ' ca WHERE ca.appointment_id = a.id AND ca.status = "waitlisted") AS on_waiting_list'
            )
            ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
            ->leftJoin( 'Customer', 'c', 'c.id = ca.customer_id' )
            ->leftJoin( 'Payment', 'p', 'p.id = ca.payment_id' )
            ->leftJoin( 'Service', 's', 's.id = a.service_id' )
            ->leftJoin( 'Category', 'ct', 'ct.id = s.category_id' )
            ->leftJoin( 'Staff', 'st', 'st.id = a.staff_id' )
            ->whereNot( 'a.start_date', null )
            // Custom service without customers have not ca.id
            ->groupBy( 'COALESCE(ca.id,CONCAT(\'appointment-\',a.id))' );
        if ( Lib\Proxy\Locations::servicesPerLocationAllowed() ) {
            $query = Proxy\Locations::prepareCalendarQuery( $query );
        } else {
            $query->leftJoin( 'StaffService', 'ss', 'ss.staff_id = a.staff_id AND ss.service_id = a.service_id AND ss.location_id IS NULL' );
        }

        if ( Config::groupBookingActive() ) {
            $query->addSelect( 'COALESCE(ss.capacity_max,s.capacity_max,9999) AS service_capacity' );
        } else {
            $query->addSelect( '1 AS service_capacity' );
        }

        if ( Config::proActive() ) {
            $query->addSelect( 'c.country, c.state, c.postcode, c.city, c.street, c.street_number, c.additional_address, c.info_fields' );
        }

        // Fetch appointments,
        // and shift the dates to appropriate time zone if needed
        $appointments = array();
        $wp_tz = Config::getWPTimeZone();
        $convert_tz = $display_tz !== $wp_tz;

        foreach ( $query->fetchArray() as $appointment ) {
            if ( ! isset ( $appointments[ $appointment['id'] ] ) ) {
                if ( $convert_tz ) {
                    $appointment['start_date'] = DateTime::convertTimeZone( $appointment['start_date'], $wp_tz, $display_tz );
                    $appointment['end_date'] = DateTime::convertTimeZone( $appointment['end_date'], $wp_tz, $display_tz );
                }
                $appointments[ $appointment['id'] ] = $appointment;
            }
            $appointments[ $appointment['id'] ]['customers'][] = array(
                'appointment_id' => $appointment['id'],
                'appointment_notes' => $appointment['appointment_notes'],
                'booking_number' => Config::groupBookingActive() ? $appointment['id'] . '-' . $appointment['ca_id'] : $appointment['ca_id'],
                'client_birthday' => $appointment['client_birthday'],
                'client_email' => $appointment['client_email'],
                'client_first_name' => $appointment['client_first_name'],
                'client_last_name' => $appointment['client_last_name'],
                'client_name' => $appointment['client_name'],
                'client_note' => $appointment['client_note'],
                'client_phone' => $appointment['client_phone'],
                'number_of_persons' => $appointment['number_of_persons'],
                'payment_status' => Lib\Entities\Payment::statusToString( $appointment['payment_status'] ),
                'payment_type' => Lib\Entities\Payment::typeToString( $appointment['payment_gateway'] ),
                'status' => $appointment['status'],
                '_info_fields' => isset( $appointment['info_fields'] ) ? json_decode( $appointment['info_fields'], true ) : array(),
                '_custom_fields' => isset( $appointment['custom_fields'] ) ? json_decode( $appointment['custom_fields'], true ) : array(),
            );
        }

        $status_codes = array(
            CustomerAppointment::STATUS_APPROVED => 'success',
            CustomerAppointment::STATUS_CANCELLED => 'danger',
            CustomerAppointment::STATUS_REJECTED => 'danger',
        );
        $cancelled_statuses = array(
            CustomerAppointment::STATUS_CANCELLED,
            CustomerAppointment::STATUS_REJECTED,
        );
        $pending_statuses = array(
            CustomerAppointment::STATUS_CANCELLED,
            CustomerAppointment::STATUS_REJECTED,
            CustomerAppointment::STATUS_PENDING,
        );
        $colors = array();
        if ( $coloring_mode === 'status' ) {
            $colors = Lib\Proxy\Shared::prepareColorsStatuses( array(
                CustomerAppointment::STATUS_PENDING => get_option( 'bookly_appointment_status_pending_color' ),
                CustomerAppointment::STATUS_APPROVED => get_option( 'bookly_appointment_status_approved_color' ),
                CustomerAppointment::STATUS_CANCELLED => get_option( 'bookly_appointment_status_cancelled_color' ),
                CustomerAppointment::STATUS_REJECTED => get_option( 'bookly_appointment_status_rejected_color' ),
            ) );
            $colors['mixed'] = get_option( 'bookly_appointment_status_mixed_color' );
        }
        foreach ( $appointments as $key => $appointment ) {
            $codes = $default_codes;
            $codes['appointment_id'] = $appointment['id'];
            $codes['appointment_date'] = DateTime::formatDate( $appointment['start_date'] );
            $codes['appointment_time'] = $appointment['duration'] >= DAY_IN_SECONDS && $appointment['start_time_info'] ? $appointment['start_time_info'] : Lib\Utils\DateTime::formatTime( $appointment['start_date'] );
            $codes['booking_number'] = $appointment['id'];
            $codes['internal_note'] = esc_html( $appointment['internal_note'] );
            $codes['on_waiting_list'] = $appointment['on_waiting_list'];
            $codes['service_name'] = $appointment['service_name'] ? esc_html( $appointment['service_name'] ) : __( 'Untitled', 'bookly' );
            $codes['service_price'] = Price::format( $appointment['service_price'] * $appointment['units'] );
            $codes['service_duration'] = DateTime::secondsToInterval( $appointment['duration'] * $appointment['units'] );
            $codes['signed_up'] = $appointment['total_number_of_persons'];
            foreach ( array( 'staff_name', 'staff_phone', 'staff_info', 'staff_email', 'service_info', 'service_capacity', 'category_name', 'client_note' ) as $field ) {
                $codes[ $field ] = esc_html( $appointment[ $field ] );
            }
            if ( $appointment['staff_any'] ) {
                $codes['staff_name'] .= $postfix_any;
            }

            // Customers for popover.
            $overall_status = isset( $appointment['customers'][0] ) ? $appointment['customers'][0]['status'] : '';

            $codes['participants'] = array();
            $event_status = null;
            foreach ( $appointment['customers'] as $customer ) {
                $status_color = 'secondary';
                if ( isset( $status_codes[ $customer['status'] ] ) ) {
                    $status_color = $status_codes[ $customer['status'] ];
                }
                if ( $coloring_mode === 'status' ) {
                    if ( $event_status === null ) {
                        $event_status = $customer['status'];
                    } elseif ( $event_status !== $customer['status'] ) {
                        $event_status = 'mixed';
                    }
                }
                if ( $customer['status'] !== $overall_status && ( ! in_array( $customer['status'], $cancelled_statuses, true ) || ! in_array( $overall_status, $cancelled_statuses, true ) ) ) {
                    if ( in_array( $customer['status'], $pending_statuses, true ) && in_array( $overall_status, $pending_statuses, true ) ) {
                        $overall_status = CustomerAppointment::STATUS_PENDING;
                    } else {
                        $overall_status = '';
                    }
                }
                if ( $customer['number_of_persons'] > 1 ) {
                    $number_of_persons = '<span class="badge badge-info mr-1"><i class="far fa-fw fa-user"></i>×' . $customer['number_of_persons'] . '</span>';
                } else {
                    $number_of_persons = '';
                }
                $customer['status_color'] = $status_color;
                $customer['nop'] = $number_of_persons;
                $customer['status'] = CustomerAppointment::statusToString( $customer['status'] );
                $codes['participants'][] = $customer;
            }

            // Display customer information only if there is 1 customer. Don't confuse with number_of_persons.
            if ( $appointment['number_of_persons'] === $appointment['total_number_of_persons'] ) {
                $participants = 'one';
                $template = $one_participant;
                foreach ( array( 'client_name', 'client_first_name', 'client_last_name', 'client_phone', 'client_email', 'client_birthday' ) as $data_entry ) {
                    $codes[ $data_entry ] = esc_html( $appointment['customers'][0][ $data_entry ] );
                }
                $codes['number_of_persons'] = $appointment['number_of_persons'];
                $codes['appointment_notes'] = $appointment['appointment_notes'];
                // Payment.
                if ( $appointment['total'] ) {
                    $codes['total_price'] = Price::format( $appointment['total'] );
                    $codes['amount_paid'] = Price::format( $appointment['paid'] );
                    $codes['amount_due'] = Price::format( $appointment['total'] - $appointment['paid'] );
                    $codes['payment_type'] = Lib\Entities\Payment::typeToString( $appointment['payment_gateway'] );
                    $codes['payment_status'] = Lib\Entities\Payment::statusToString( $appointment['payment_status'] );
                }
                // Status.
                $codes['status'] = CustomerAppointment::statusToString( $appointment['status'] );
            } else {
                $participants = 'many';
                $template = $many_participants;
            }
            $codes['appointment_color'] = $appointment['service_color'];
            $codes['appointment_end_time'] = ( $appointment['duration'] * $appointment['units'] >= DAY_IN_SECONDS && $appointment['start_time_info'] ? $appointment['end_time_info'] : DateTime::formatTime( $appointment['end_date'] ) );

            $codes = Proxy\Shared::prepareAppointmentCodesData( $codes, $appointment, $participants );

            switch ( $coloring_mode ) {
                case 'status';
                    $color = $colors[ $event_status ?: 'mixed' ];
                    break;
                case 'staff':
                    $color = $appointment['staff_color'];
                    break;
                case 'service':
                default:
                    $color = $appointment['service_color'];
            }
            $codes['description'] = Lib\Utils\Codes::stringify( $template, $codes, false );
            $appointments[ $key ] = array(
                'id' => $appointment['id'],
                'start' => $appointment['start_date'],
                'end' => $appointment['end_date'],
                'title' => ' ',
                'color' => $color,
                'resourceId' => $appointment['staff_id'],
                'allDay' => $appointment['duration'] >= DAY_IN_SECONDS,
                'extendedProps' => array(
                    'tooltip' => Lib\Utils\Codes::stringify( $appointment['duration'] >= DAY_IN_SECONDS ? $tooltip_all_day : $tooltip, $codes, false, array(), true ),
                    'desc' => $codes['description'],
                    'staffId' => $appointment['staff_id'],
                    'series_id' => (int) $appointment['series_id'],
                    'package_id' => (int) $appointment['package_id'],
                    'waitlisted' => (int) $appointment['on_waiting_list'],
                    'staff_any' => (int) $appointment['staff_any'],
                    'overall_status' => $overall_status,
                ),
            );
            if ( $appointment['duration'] * $appointment['units'] >= DAY_IN_SECONDS && $appointment['start_time_info'] ) {
                $appointments[ $key ]['extendedProps']['header_text'] = sprintf( '%s - %s', $appointment['start_time_info'], $appointment['end_time_info'] );
            }
        }

        return array_values( $appointments );
    }

    /**
     * @return int
     */
    public static function getAppointmentsCount()
    {
        if ( isset ( $_REQUEST['page'] ) && $_REQUEST['page'] === self::pageSlug() ) {
            return 0;
        }

        return Lib\Entities\Appointment::query()
            ->whereGt( 'id', get_option( 'bookly_cal_last_seen_appointment', 0 ) )
            ->count();
    }

    /**
     * Show 'News' submenu with counter inside Bookly main menu
     */
    public static function addBooklyMenuItem( $calendar_badge )
    {
        $calendar = __( 'Calendar', 'bookly' );
        if ( $calendar_badge ) {
            add_submenu_page( 'bookly-menu', $calendar, sprintf( '%s <span class="update-plugins count-%d"><span class="update-count">%d</span></span>', $calendar, $calendar_badge, $calendar_badge ), 'read',
                self::pageSlug(), function () { Page::render(); } );
        } else {
            add_submenu_page( 'bookly-menu', $calendar, $calendar, 'read',
                self::pageSlug(), function () { Page::render(); } );
        }
    }
}
