<?php
namespace Bookly\Backend\Modules\Appointments;

use Bookly\Lib;

/**
 * Class Ajax
 *
 * @package Bookly\Backend\Modules\Appointments
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'supervisor' );
    }

    /**
     * Get list of appointments.
     */
    public static function getAppointments()
    {
        $columns = self::parameter( 'columns' );
        $order = self::parameter( 'order', array() );
        $filter = self::parameter( 'filter' );
        $limits = array(
            'length' => self::parameter( 'length' ),
            'start' => self::parameter( 'start' ),
        );

        $data = self::getAppointmentsTableData( $filter, $limits, $columns, $order );

        unset( $filter['date'] );

        Lib\Utils\Tables::updateSettings( 'appointments', $columns, $order, $filter );

        wp_send_json( array(
            'draw' => ( int ) self::parameter( 'draw' ),
            'recordsTotal' => $data['total'],
            'recordsFiltered' => $data['filtered'],
            'data' => $data['data'],
        ) );
    }

    /**
     * Delete customer appointments.
     */
    public static function deleteCustomerAppointments()
    {
        // Customer appointments to delete
        $ca_list = array();
        // Appointments without customers to delete
        $appointments_list = array();
        foreach ( self::parameter( 'data', array() ) as $ca_data ) {
            if ( $ca_data['ca_id'] === 'null' ) {
                $appointments_list[] = $ca_data['id'];
            } else {
                $ca_list[] = $ca_data['ca_id'];
            }
        }
        $queue = array();
        /** @var Lib\Entities\CustomerAppointment $ca */
        foreach ( Lib\Entities\CustomerAppointment::query()->whereIn( 'id', $ca_list )->find() as $ca ) {
            if ( self::parameter( 'notify' ) ) {
                switch ( $ca->getStatus() ) {
                    case Lib\Entities\CustomerAppointment::STATUS_PENDING:
                    case Lib\Entities\CustomerAppointment::STATUS_WAITLISTED:
                        $ca->setStatus( Lib\Entities\CustomerAppointment::STATUS_REJECTED );
                        break;
                    case Lib\Entities\CustomerAppointment::STATUS_APPROVED:
                        $ca->setStatus( Lib\Entities\CustomerAppointment::STATUS_CANCELLED );
                        break;
                    default:
                        $busy_statuses = (array) Lib\Proxy\CustomStatuses::prepareBusyStatuses( array() );
                        if ( in_array( $ca->getStatus(), $busy_statuses ) ) {
                            $ca->setStatus( Lib\Entities\CustomerAppointment::STATUS_CANCELLED );
                        }
                }
                Lib\Notifications\Booking\Sender::sendForCA(
                    $ca,
                    null,
                    array( 'cancellation_reason' => self::parameter( 'reason' ) ),
                    false,
                    $queue
                );
            }
            $ca->deleteCascade();
        }

        /** @var Lib\Entities\Appointment $appointment */
        foreach ( Lib\Entities\Appointment::query()->whereIn( 'id', $appointments_list )->find() as $appointment ) {
            $ca = $appointment->getCustomerAppointments();
            if ( empty( $ca ) ) {
                $appointment->delete();
            }
        }
        $response = array();
        if ( $queue ) {
            $db_queue = new Lib\Entities\NotificationQueue();
            $db_queue
                ->setData( json_encode( array( 'all' => $queue ) ) )
                ->save();

            $response['queue'] = array( 'token' => $db_queue->getToken(), 'all' => $queue );
        }
        wp_send_json_success( $response );
    }

    /**
     * @param array $filter
     * @param array $limits
     * @param array $columns
     * @param array $order
     * @return array
     */
    public static function getAppointmentsTableData( $filter = array(), $limits = array(), $columns = array(), $order = array() )
    {
        $postfix_any = sprintf( ' (%s)', get_option( 'bookly_l10n_option_employee' ) );
        $postfix_archived = sprintf( ' (%s)', __( 'Archived', 'bookly' ) );

        $query = Lib\Entities\Appointment::query( 'a' )
            ->select( 'a.id,
                ca.payment_id,
                ca.status,
                ca.id        AS ca_id,
                ca.notes,
                ca.number_of_persons,
                ca.extras,
                ca.extras_multiply_nop,
                ca.rating,
                ca.rating_comment,
                COALESCE(ca.created_at, a.created_at) AS created_date,
                a.start_date,
                a.staff_any,
                a.online_meeting_provider,
                a.online_meeting_id,
                a.internal_note,
                c.full_name  AS customer_full_name,
                c.phone      AS customer_phone,
                c.email      AS customer_email,
                c.birthday   AS customer_birthday,
                c.country    AS customer_country,
                c.state      AS customer_state,
                c.postcode   AS customer_postcode,
                c.city       AS customer_city,
                c.street     AS customer_street,
                c.street_number AS customer_street_number,
                c.additional_address AS customer_additional_address,
                st.full_name AS staff_name,
                st.visibility AS staff_visibility,
                p.paid       AS payment,
                p.total      AS payment_total,
                p.type       AS payment_type,
                p.status     AS payment_status,
                COALESCE(s.title, a.custom_service_name) AS service_title,
                (TIME_TO_SEC(TIMEDIFF(a.end_date, a.start_date)) + a.extras_duration) AS service_duration' )
            ->leftJoin( 'CustomerAppointment', 'ca', 'a.id = ca.appointment_id' )
            ->leftJoin( 'Service', 's', 's.id = a.service_id' )
            ->leftJoin( 'Customer', 'c', 'c.id = ca.customer_id' )
            ->leftJoin( 'Payment', 'p', 'p.id = ca.payment_id' )
            ->leftJoin( 'Staff', 'st', 'st.id = a.staff_id' )
            ->leftJoin( 'StaffService', 'ss', 'ss.staff_id = st.id AND ss.service_id = s.id AND ss.location_id = a.location_id' );

        $total = $query->count();

        $sub_query = Lib\Proxy\Files::getSubQueryAttachmentExists();
        if ( ! $sub_query ) {
            $sub_query = '0';
        }
        $query->addSelect( '(' . $sub_query . ') AS attachment' );

        Lib\Proxy\Locations::prepareAppointmentsQuery( $query );

        if ( $filter['id'] != '' ) {
            if ( Lib\Config::groupBookingActive() ) {
                if ( is_numeric( $filter['id'] ) ) {
                    $query->whereRaw( 'ca.id = %s OR a.id = %s', array( $filter['id'], $filter['id'] ) );
                } else {
                    $query->whereRaw( 'CONCAT(a.id, \'-\', ca.id) = %s', array( $filter['id'] ) );
                }
            } else {
                $query->where( 'a.id', $filter['id'] );
            }
        }

        if ( $filter['date'] == 'any' ) {
            $query->whereNot( 'a.start_date', null );
        } elseif ( $filter['date'] == 'null' ) {
            $query->where( 'a.start_date', null );
        } else {
            list ( $start, $end ) = explode( ' - ', $filter['date'], 2 );
            $end = date( 'Y-m-d 23:59:59', strtotime( $end ) );
            $query->whereBetween( 'a.start_date', $start, $end );
        }

        if ( $filter['created_date'] != 'any' ) {
            list ( $start, $end ) = explode( ' - ', $filter['created_date'], 2 );
            $end = date( 'Y-m-d', strtotime( $end ) + DAY_IN_SECONDS );
            $query->whereBetween( 'COALESCE(ca.created_at, a.created_at)', $start, $end );
        }

        if ( $filter['staff'] != '' ) {
            $query->where( 'a.staff_id', $filter['staff'] );
        }

        if ( $filter['customer'] != '' ) {
            $query->where( 'ca.customer_id', $filter['customer'] );
        }

        if ( $filter['service'] != '' ) {
            $query->where( 'a.service_id', $filter['service'] ?: null );
        }

        if ( isset( $filter['location'] ) && $filter['location'] != '' ) {
            if ( $filter['location'] == 'w/o' ) {
                $query->where( 'a.location_id', null );
            } else {
                $query->where( 'a.location_id', $filter['location'] );
            }
        }

        if ( isset( $filter['status'] ) && count( $filter['status'] ) !== count( Lib\Entities\CustomerAppointment::getStatuses() ) && count( $filter['status'] ) !== 0 ) {
            $query->whereIn( 'ca.status', $filter['status'] );
        }

        foreach ( $order as $sort_by ) {
            $query->sortBy( str_replace( '.', '_', $columns[ $sort_by['column'] ]['data'] ) )
                ->order( $sort_by['dir'] == 'desc' ? Lib\Query::ORDER_DESCENDING : Lib\Query::ORDER_ASCENDING );
        }

        $custom_fields = array();
        $fields_data = (array) Lib\Proxy\CustomFields::getWhichHaveData();
        foreach ( $fields_data as $field_data ) {
            $custom_fields[ $field_data->id ] = '';
        }

        $filtered = $query->count();

        if ( ! empty( $limits ) ) {
            $query->limit( $limits['length'] )->offset( $limits['start'] );
        }

        $locations_active = Lib\Config::locationsActive();

        $data = array();
        foreach ( $query->fetchArray() as $row ) {
            // Service duration.
            $service_duration = Lib\Utils\DateTime::secondsToInterval( $row['service_duration'] );
            // Payment title.
            $payment_title = '';
            $payment_raw_title = '';
            if ( $row['payment'] !== null && $row['status'] !== Lib\Entities\CustomerAppointment::STATUS_WAITLISTED ) {
                $payment_title = Lib\Utils\Price::format( $row['payment'] );
                if ( $row['payment'] != $row['payment_total'] ) {
                    $payment_title = sprintf( __( '%s of %s', 'bookly' ), $payment_title, Lib\Utils\Price::format( $row['payment_total'] ) );
                }

                $payment_raw_title = trim( sprintf(
                    '%s %s %s',
                    $payment_title,
                    Lib\Entities\Payment::typeToString( $row['payment_type'] ),
                    Lib\Entities\Payment::statusToString( $row['payment_status'] )
                ) );

                $payment_title .= sprintf(
                    ' %s <span%s>%s</span>',
                    Lib\Entities\Payment::typeToString( $row['payment_type'] ),
                    $row['payment_status'] == Lib\Entities\Payment::STATUS_PENDING ? ' class="text-danger"' : '',
                    Lib\Entities\Payment::statusToString( $row['payment_status'] )
                );
            }
            // Appointment status.
            $row['status'] = Lib\Entities\CustomerAppointment::statusToString( $row['status'] );
            // Custom fields
            $customer_appointment = new Lib\Entities\CustomerAppointment();
            $customer_appointment->load( $row['ca_id'] );
            foreach ( (array) Lib\Proxy\CustomFields::getForCustomerAppointment( $customer_appointment, false, null, false ) as $custom_field ) {
                $custom_fields[ $custom_field['id'] ] = $custom_field['value'];
            }
            if ( $row['ca_id'] !== null ) {
                $extras = (array) Lib\Proxy\ServiceExtras::getInfo( json_decode( $row['extras'], true ) ?: array(), false );
                if ( $row['extras_multiply_nop'] && $row['number_of_persons'] > 1 ) {
                    foreach ( $extras as $index => $extra ) {
                        $extras[ $index ]['title'] = '<i class="far fa-user"></i>&nbsp;' . $row['number_of_persons'] . '&nbsp;&times;&nbsp;' . $extra['title'];
                    }
                }
            } else {
                $extras = array();
            }

            $data[] = array(
                'id' => $row['id'],
                'no' => Lib\Config::groupBookingActive() && $row['ca_id'] ? $row['id'] . '-' . $row['ca_id'] : $row['ca_id'],
                'start_date' => $row['start_date'] === null ? __( 'N/A', 'bookly' ) : Lib\Utils\DateTime::formatDateTime( $row['start_date'] ),
                'staff' => array(
                    'name' => $row['staff_name'] . ( $row['staff_any'] ? $postfix_any : '' ) . ( $row['staff_visibility'] == 'archive' ? $postfix_archived : '' ),
                ),
                'customer' => array(
                    'full_name' => $row['ca_id'] === null ? __( 'N/A', 'bookly' ) : $row['customer_full_name'],
                    'phone' => $row['ca_id'] === null ? __( 'N/A', 'bookly' ) : $row['customer_phone'],
                    'email' => $row['ca_id'] === null ? __( 'N/A', 'bookly' ) : $row['customer_email'],
                    'birthday' => $row['customer_birthday'] ? Lib\Utils\DateTime::formatDate( $row['customer_birthday'] ) : '',
                    'address' => Lib\Proxy\Pro::getFullAddressByCustomerData( array(
                        'country' => $row['customer_country'],
                        'state' => $row['customer_state'],
                        'postcode' => $row['customer_postcode'],
                        'city' => $row['customer_city'],
                        'street' => $row['customer_street'],
                        'street_number' => $row['customer_street_number'],
                        'additional_address' => $row['customer_additional_address'],
                    ) ),
                ),
                'service' => array(
                    'title' => $row['service_title'],
                    'duration' => $service_duration,
                    'extras' => $extras,
                ),
                'status' => $row['status'],
                'location' => $locations_active ? $row['location'] : '',
                'payment' => $payment_title,
                'payment_raw_title' => $payment_raw_title,
                'notes' => $row['notes'],
                'number_of_persons' => (int) $row['number_of_persons'],
                'rating' => $row['rating'],
                'rating_comment' => $row['rating_comment'],
                'custom_fields' => $custom_fields,
                'ca_id' => $row['ca_id'],
                'attachment' => $row['attachment'],
                'payment_id' => $row['payment_id'],
                'internal_note' => $row['internal_note'],
                'online_meeting_provider' => $row['online_meeting_provider'],
                'online_meeting_id' => $row['online_meeting_id'],
                'created_date' => Lib\Utils\DateTime::formatDateTime( $row['created_date'] ),
            );

            $custom_fields = array_map( function() { return ''; }, $custom_fields );
        }

        return compact( 'data', 'total', 'filtered' );
    }
}