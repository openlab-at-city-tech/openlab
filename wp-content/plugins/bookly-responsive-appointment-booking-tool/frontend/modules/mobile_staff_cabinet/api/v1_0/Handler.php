<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api\V1_0;

use Bookly\Lib;
use Bookly\Lib\Config;
use Bookly\Lib\Entities;
use Bookly\Lib\Entities\Appointment;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Entities\Service;
use Bookly\Lib\Entities\Staff;
use Bookly\Lib\Slots\DatePoint;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Utils\Appointment as UtilAppointment;
use Bookly\Frontend\Modules\MobileStaffCabinet\Api;
use Bookly\Frontend\Modules\MobileStaffCabinet\Api\Exceptions;

class Handler extends Api\ApiHandler
{
    /**
     * @param string $role
     * @param Lib\Base\Request $request
     * @param Api\IResponse $response
     */
    public function __construct( $role, Lib\Base\Request $request, Api\IResponse $response )
    {
        $this->role = $role;
        $this->request = $request;
        $this->setParams( $request->get( 'params', array() ) );
        $this->response = $response;
    }

    public function setStaff( $staff )
    {
        $this->staff = $staff;
        $this->staff && Lib\Utils\Log::setAuthor( $staff->getFullName() );
    }

    public function setWpUser( $wp_user )
    {
        $this->wp_user = $wp_user;
        if ( $wp_user ) {
            Lib\Utils\Log::setAuthor( $wp_user->display_name );
        }
    }

    protected function init()
    {
        $me = array(
            'email' => '',
            'full_name' => '',
            'role' => $this->role,
            'img' => $this->staff ? Lib\Utils\Common::getAttachmentUrl( $this->staff->getAttachmentId(), 'thumbnail' ) : '',
        );

        switch ( $this->role ) {
            case self::ROLE_SUPERVISOR:
                $me['full_name'] = $this->wp_user->display_name;
                $me['email'] = $this->wp_user->user_email;
                if ( ! $me['img'] && get_option( 'show_avatars' ) ) {
                    $me['img'] = get_avatar_url( $this->wp_user->user_email ) ?: '';
                }
                break;
            case self::ROLE_STAFF:
                $me['full_name'] = $this->staff->getFullName();
                $me['email'] = $this->staff->getEmail();
                break;
        }

        $this->result = array(
            'me' => $me,
            'settings' => array(
//                'slots' => array(
//                    'server_side' => ! $pre_generated,
//                    'default' => $slots,
//                ),
//                'date' => array(
//                    'min' => date_create( $bounding['date_min'][0] . '-' . ( $bounding['date_min'][1] + 1 ) . '-' . $bounding['date_min'][2] )->format( 'Y-m-d' ),
//                    'max' => date_create( $bounding['date_max'][0] . '-' . ( $bounding['date_max'][1] + 1 ) . '-' . $bounding['date_max'][2] )->format( 'Y-m-d' ),
//                    'format' => array(
//                        'date' => get_option( 'date_format', 'Y-m-d' ),
//                        'time' => get_option( 'time_format', 'H:i' ),
//                    ),
//                ),
                'start_of_week' => (int) get_option( 'start_of_week' ),
                'notices' => array(
                    'date_interval_not_available' => __( 'The selected period is occupied by another appointment', 'bookly' ),
                    'date_interval_warning' => __( 'Selected period doesn\'t match service duration', 'bookly' ),
                    'interval_not_in_service_schedule' => __( 'Selected period doesn\'t match service schedule', 'bookly' ),
                    'interval_not_in_staff_schedule' => __( 'Selected period doesn\'t match provider\'s schedule', 'bookly' ),
                    'no_timeslots_available' => __( 'No timeslots available', 'bookly' ),
                    'overflow_capacity' => __( 'The number of customers should not be more than %d', 'bookly' ),
                    'service_required' => __( 'Please select a service', 'bookly' ),
                    'staff_reaches_working_time_limit' => __( 'Booking exceeds your working hours limit', 'bookly' ),
                    'minimum_capacity' => __( 'Minimum capacity', 'bookly' ),
                ),
            ),
        );
    }

    protected function appointment()
    {
        $response = array( 'data' => array() );
        $appointment = new Appointment();
        if ( $appointment->load( $this->param( 'id' ) ) ) {
            $query = Appointment::query( 'a' )
                ->select( 'SUM(ca.number_of_persons) AS total_number_of_persons,
                    a.staff_id,
                    a.staff_any,
                    a.service_id,
                    a.custom_service_name,
                    a.custom_service_price,
                    a.start_date,
                    a.end_date,
                    a.internal_note,
                    a.location_id,
                    a.online_meeting_provider,
                    a.online_meeting_id' )
                ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
                ->where( 'a.id', $appointment->getId() )
            ;

            // Determine display time zone
            $display_tz = Common::getStaffTimeZone( $this->staff );
            $wp_tz = Lib\Config::getWPTimeZone();

            // Fetch appointment,
            // and shift the dates to appropriate time zone if needed
            $info = $query->fetchRow();
            if ( $display_tz !== $wp_tz ) {
                $info['start_date'] = DateTime::convertTimeZone( $info['start_date'], $wp_tz, $display_tz );
                $info['end_date']   = DateTime::convertTimeZone( $info['end_date'], $wp_tz, $display_tz );
            }

            $response['data']['id'] = (int) $appointment->getId();
            $response['data']['staff_id'] = (int) $appointment->getStaffId();
            $response['data']['total_number_of_persons'] = (int) $info['total_number_of_persons'];
            $response['data']['start_date'] = $info['start_date'];
            $response['data']['end_date'] = $info['end_date'];
            $response['data']['service_id'] = (int) $info['service_id'];
            $response['data']['internal_note'] = $info['internal_note'];
            $response['data']['location_id'] = (int) $info['location_id'];
            $response['data']['custom_service_name'] = $info['custom_service_name'];
            $response['data']['custom_service_price'] = (float) $info['custom_service_price'];

            $customers = CustomerAppointment::query( 'ca' )
                ->select( 'ca.id,
                    ca.series_id,
                    ca.customer_id,
                    ca.package_id,
                    ca.custom_fields,
                    ca.extras,
                    ca.number_of_persons,
                    ca.notes,
                    ca.status,
                    ca.payment_id,
                    ca.collaborative_service_id,
                    ca.collaborative_token,
                    ca.compound_service_id,
                    ca.compound_token,
                    ca.time_zone,
                    ca.time_zone_offset,
                    p.paid    AS payment,
                    p.total   AS payment_total,
                    p.type    AS payment_type,
                    p.details AS payment_details,
                    p.status  AS payment_status,
                    c.full_name,
                    c.email,
                    c.phone,
                    c.group_id' )
                ->leftJoin( 'Payment', 'p', 'p.id = ca.payment_id' )
                ->leftJoin( 'Customer', 'c', 'c.id = ca.customer_id' )
                ->where( 'ca.appointment_id', $appointment->getId() )
                ->fetchArray();
            foreach ( $customers as $customer ) {
                $collaborative_service = '';
                if ( $customer['collaborative_service_id'] !== null ) {
                    $service = new Service();
                    if ( $service->load( $customer['collaborative_service_id'] ) ) {
                        $collaborative_service = $service->getTranslatedTitle();
                    }
                }
                $compound_service = '';
                if ( $customer['compound_service_id'] !== null ) {
                    $service = new Service();
                    if ( $service->load( $customer['compound_service_id'] ) ) {
                        $compound_service = $service->getTranslatedTitle();
                    }
                }
                $custom_fields = (array) json_decode( $customer['custom_fields'], true );
                $response['data']['customer_appointments'][] = array(
                    'id' => (int) $customer['id'],
                    'customer_id' => (int) $customer['customer_id'],
                    'series_id' => $customer['series_id'],
                    'package_id' => $customer['package_id'],
                    'collaborative_service' => $collaborative_service,
                    'collaborative_token' => $customer['collaborative_token'],
                    'compound_service' => $compound_service,
                    'compound_token' => $customer['compound_token'],
                    'custom_fields' => $custom_fields,
                    'files' => Lib\Proxy\Files::getFileNamesForCustomFields( $custom_fields ),
                    'extras' => (array) json_decode( $customer['extras'], true ),
                    'number_of_persons' => (int) $customer['number_of_persons'],
                    'notes' => $customer['notes'],
                    'payment_id' => $customer['payment_id'],
                    'payment_type' => $customer['payment_id']
                        ? ( $customer['payment'] != $customer['payment_total'] ? 'partial' : 'full' )
                        : null,
                    'status' => $customer['status'],
                    'timezone' => Lib\Proxy\Pro::getCustomerTimezone( $customer['time_zone'], $customer['time_zone_offset'] ),
                );
            }
        }

        $this->result = $response['data'];
    }

    protected function checkAppointmentTime()
    {
        $appointment_id = (int) $this->param( 'id', 0 );
        $service_id = (int) $this->param( 'service_id' );
        $customer_appointments = $this->param( 'customer_appointments', array() );
        $start_date = $this->param( 'start_date' );
        $location_id = (int) $this->param( 'location_id' );
        foreach ( $customer_appointments as &$ca ) {
            if ( isset( $ca['id'] ) ) {
                $ca['ca_id'] = $ca['id'];
            }
            $ca = $this->extendCustomerAppointment( $ca );
            $ca['id'] = $ca['customer_id'];
            unset( $ca['customer_id'] );
        }

        if ( $this->param( 'end_date' ) ) {
            $end_date = $this->getDateFormattedParameter( 'end_date', 'Y-m-d H:i:s' );
        } else {
            $appointment = $appointment_id
                ? Appointment::find( $appointment_id )
                : null;

            if ( $appointment ) {
                if ( ( $appointment->getStartDate() == $start_date )
                    && ( $appointment->getServiceId() == $service_id ) ) {
                    $end_date = $appointment->getEndDate();
                } else {
                    $service = Service::find( $service_id );
                    $end_date = DatePoint::fromStr( $start_date )->modify( $service->getDuration() )->format( 'Y-m-d H:i:s' );
                }
            } elseif ( $service_id ) {
                $service = Service::find( $service_id );
                $end_date = DatePoint::fromStr( $start_date )->modify( $service->getDuration() )->format( 'Y-m-d H:i:s' );
            }
        }
        if ( $this->role === self::ROLE_SUPERVISOR ) {
            $staff_id = $this->param( 'staff_id' );
            if ( ! $staff_id ) {
                throw new Exceptions\ParameterException( 'staff_id', $this->param( 'staff_id' ) );
            }
        } else {
            $staff_id = $this->staff->getId();
        }

        $result = UtilAppointment::checkTime( $appointment_id,
            $start_date,
            $end_date,
            (int) $staff_id,
            $service_id,
            $location_id,
            $customer_appointments
        );

        $result['date_interval_not_available'] = (bool) $result['date_interval_not_available'];

        $this->result = $result;
    }

    protected function saveAppointment()
    {
        $appointment_id = (int) $this->param( 'id', 0 );
        $location_id = (int) $this->param( 'location_id', 0 );
        $service_id = (int) $this->param( 'service_id' );
        $notification = $this->param( 'notification', false );
        $custom_service_name = trim( $this->param( 'custom_service_name', '' ) );
        $custom_service_price = trim( $this->param( 'custom_service_price', '' ) );
        if ( $this->role === self::ROLE_SUPERVISOR ) {
            $staff_id = $this->param( 'staff_id' );
            if ( ! $staff_id ) {
                throw new Exceptions\ParameterException( 'staff_id', $this->param( 'staff_id' ) );
            }
        } else {
            $staff_id = $this->staff->getId();
        }

        $skip_date = 0;
        $service = $service_id
            ? Service::find( $service_id )
            : null;
        if ( ! $service && ! $custom_service_name ) {
            throw new Exceptions\ParameterException( 'service_id', $this->param( 'service_id' ) );
        }

        $customer_appointments = $this->param( 'customer_appointments', array() );
        $start_date = $this->getDateFormattedParameter( 'start_date', 'Y-m-d H:i:s' );

        foreach ( $customer_appointments as &$ca ) {
            if ( isset( $ca['id'] ) ) {
                $ca['ca_id'] = $ca['id'];
            }
            $ca = $this->extendCustomerAppointment( $ca );
            $ca['id'] = $ca['customer_id'];
            unset( $ca['customer_id'] );
        }

        if ( $this->param( 'end_date' ) ) {
            $end_date = $this->getDateFormattedParameter( 'end_date', 'Y-m-d H:i:s' );
        } else {
            $appointment = $appointment_id
                ? Appointment::find( $appointment_id )
                : null;

            if ( $appointment && $appointment->getStartDate() == $start_date && $service_id == $appointment->getServiceId() ) {
                $end_date = $appointment->getEndDate();
            } else {
                $end_date = DatePoint::fromStr( $start_date )->modify( $service->getDuration() )->format( 'Y-m-d H:i:s' );
            }
        }

        if ( ! $skip_date && $this->staff ) {
            $display_tz = Common::getStaffTimeZone( $this->staff );
            // Determine display time zone,
            // and shift the dates to WP time zone if needed
            $wp_tz = Lib\Config::getWPTimeZone();
            if ( $display_tz !== $wp_tz ) {
                $start_date = DateTime::convertTimeZone( $start_date, $display_tz, $wp_tz );
                $end_date = DateTime::convertTimeZone( $end_date, $display_tz, $wp_tz );
            }
        }

        $response = UtilAppointment::save(
            $appointment_id,
            (int) $staff_id,
            $service_id,
            $custom_service_name,
            $custom_service_price,
            $location_id,
            $skip_date,
            $start_date,
            $end_date,
            array( 'enabled' => 0 ),
            array(),
            'current',
            $customer_appointments,
            $notification,
            $this->param( 'internal_note' ),
            'mobile'
        );
        if ( $response['success'] ) {
            unset( $response['data'] );

            $this->result = $response;
        } else {
            throw new Exceptions\ApiException( 'ERROR', 400, $response['errors'], $this->request );
        }
    }

    protected function deleteAppointment()
    {
        $appointment_id = (int) $this->param( 'id', 0 );
        $notification = $this->param( 'notification', false );
        $reason = $this->param( 'reason', '' );
        if ( $this->role === self::ROLE_STAFF ) {
            $staff_id = Appointment::query()->where( 'id', $appointment_id )->fetchVar( 'staff_id' );
            if ( $staff_id != $this->staff->getId() ) {
                throw new Exceptions\ApiException( 'FORBIDDEN', 403 );
            }
        }

        $response = UtilAppointment::delete( $appointment_id, $notification, $reason );
        if ( $response['success'] ) {
            $this->result = $response['data'];
        } else {
            throw new Exceptions\ApiException( 'ERROR', 400, $response['errors'], $this->request );
        }
    }

    protected function appointments()
    {
        $one_day = new \DateInterval( 'P1D' );
        $list = array();
        $start_date = $this->getDateTimeParameter( 'start_date' );
        $end_date = $this->getDateTimeParameter( 'end_date' );
        $location_ids = array();

        // Determine display time zone
        $display_tz = Common::getStaffTimeZone( $this->staff );

        // Due to possibly different time zones of staff members expand start and end dates
        // to provide 100% coverage of the requested date range
        $start_date->sub( $one_day );
        $end_date->add( $one_day );

        $staff_list = array( $this->staff );
        if ( $this->role === self::ROLE_SUPERVISOR ) {
            $staff_list = $this->getStaffListByRole( false, $this->param( 'staff_ids' ) ?: array() );
        }

        $query = \Bookly\Backend\Modules\Calendar\Ajax::getAppointmentsQueryForCalendar( $staff_list, $start_date, $end_date, $location_ids );
        $query->addSelect( 'a.service_id' );
        $records = \Bookly\Backend\Modules\Calendar\Ajax::getAppointmentsForCalendar( $query );

        $appointments = array();
        $wp_tz = Config::getWPTimeZone();
        $convert_tz = $display_tz !== $wp_tz;
        foreach ( $records as $appointment ) {
            if ( ! isset ( $appointments[ $appointment['id'] ] ) ) {
                if ( $convert_tz ) {
                    $appointment['start_date'] = DateTime::convertTimeZone( $appointment['start_date'], $wp_tz, $display_tz );
                    $appointment['end_date'] = DateTime::convertTimeZone( $appointment['end_date'], $wp_tz, $display_tz );
                }
                $appointments[ $appointment['id'] ] = $appointment;
                $appointments[ $appointment['id'] ]['customer_appointments'] = array();
            }

            $customer_id = (int) $appointment['customer_id'];
            if ( $customer_id !== 0 ) {
                $appointments[ $appointment['id'] ]['customer_appointments'][] = array(
                    'id' => (int) $appointment['ca_id'],
                    'customer_id' => $customer_id,
                    'full_name' => $appointment['client_name'],
                    'status' => $appointment['status'],
                );
            }
        }

        foreach ( $appointments as $appointment ) {
            $list[] = array(
                'id' => (int) $appointment['id'],
                'staff_id' => (int) $appointment['staff_id'],
                'start_date' => $appointment['start_date'],
                'end_date' => $appointment['end_date'],
                'service' => array(
                    'id' => (int) $appointment['service_id'],
                    'name' => $appointment['service_name'],
                    'service_price' => (float) $appointment['service_price'],
                ),
                'color' => $appointment['service_color'],
                'internal_note' => $appointment['internal_note'],
                'customer_appointments' => $appointment['customer_appointments'],
            );
        }

        $this->result = $list ?: array();
    }

    protected function customers()
    {
        $list = Entities\Customer::query()->select( 'id, first_name, last_name, email, phone, notes, group_id, attachment_id' )
            ->sortBy( 'full_name' )
            ->fetchArray() ?: array();

        array_walk( $list, function ( &$item ) {
            $item['id'] = (int) $item['id'];
            $item['group_id'] = (int) $item['group_id'];
            $item['timezone'] = Lib\Proxy\Pro::getLastCustomerTimezone( $item['id'] );
            $item['img'] = Lib\Utils\Common::getAttachmentUrl( $item['attachment_id'], 'full' );
            unset( $item['attachment_id'] );
        } );

        $this->result = $list;
    }

    protected function saveCustomer()
    {
        if ( $this->param( 'id' ) ) {
            $customer = Entities\Customer::find( $this->param( 'id' ) );
        } else {
            $customer = new Entities\Customer();
        }
        if ( Config::customerGroupsActive() ) {
            $customer->setGroupId( $this->param( 'group_id' ) );
        }
        $customer
            ->setFullName( trim( rtrim( $this->param( 'first_name' ) ) . ' ' . ltrim( $this->param( 'last_name' ) ) ) )
            ->setEmail( $this->param( 'email', '' ) )
            ->setPhone( $this->param( 'phone', '' ) )
            ->setNotes( $this->param( 'notes', '' ) )
            ->save();

        if ( $customer->isLoaded() ) {
            $this->result = array(
                'id' => (int) $customer->getId(),
                'first_name' => $customer->getFirstName(),
                'last_name' => $customer->getLastName(),
            );
        } else {
            global $wpdb;
            throw new Exceptions\BooklyException( $wpdb->last_error );
        }
    }

    protected function slots()
    {
        $service_id = $this->param( 'service_id' );
        $service = Service::find( $service_id );

        $displayed_time_slots = get_option( 'bookly_appointments_displayed_time_slots' );
        if ( $displayed_time_slots === 'appropriate' ) {
            // As at backend in appointment form
            $appointments_time_delimiter = 5 * MINUTE_IN_SECONDS;
        } else {
            $appointments_time_delimiter = get_option( 'bookly_appointments_time_delimiter', 0 ) * MINUTE_IN_SECONDS;
        }
        if ( ! $service ) {
            $service = new Service();
            $service->setDuration( Lib\Config::getTimeSlotLength() );
        }

        $date = $this->getDateFormattedParameter( 'date', 'Y-m-d' );
        if ( ! $appointments_time_delimiter && ( $ts_length = (int) $service->getSlotLength() ) ) {
            $time_end = max( $service->getUnitsMax() * $service->getDuration() + DAY_IN_SECONDS, DAY_IN_SECONDS * 2 );
        } else {
            $ts_length = $appointments_time_delimiter > 0 ? $appointments_time_delimiter : Lib\Config::getTimeSlotLength();
            $time_end = max( ( $service->getUnitsMax() * $service->getDuration() ) + DAY_IN_SECONDS, DAY_IN_SECONDS * 2 );
        }

        $this->result = array(
            'start' => $this->generateSlots( 0, $time_end, $ts_length, $date, true ),
            'end' => $this->generateSlots( 0, $time_end, $ts_length, $date, false ),
        );
    }

    protected function availableSlots()
    {
        $staff_ids = array( $this->staff->getId() );
        $service_id = $this->param( 'service_id' );
        $date = $this->param( 'date' );

        $appointment_id = $this->param( 'appointment_id' );
        $location_id = $this->param( 'location_id', 0 );
        $nop = max( 1, $this->param( 'nop', 1 ) );

        $chain_item = new Lib\ChainItem();
        $chain_item
            ->setStaffIds( $staff_ids )
            ->setServiceId( $service_id )
            ->setLocationId( $location_id )
            ->setNumberOfPersons( $nop )
            ->setQuantity( 1 )
            ->setLocationId( $location_id )
            ->setUnits( 1 )
            ->setExtras( array() );

        $chain = new Lib\Chain();
        $chain->add( $chain_item );

        $custom_slot = array();
        $ignore_appointments = array();
        if ( $appointment_id ) {
            $appointment = Appointment::find( $appointment_id );
            if ( date_create( $appointment->getStartDate() )->format( 'Y-m-d' ) === date_create( $date )->format( 'Y-m-d' ) ) {
                $custom_slot = array(
                    'title' => DatePoint::fromStr( $appointment->getStartDate() )->formatI18n( get_option( 'time_format' ) ),
                    'value' => date_create( $appointment->getStartDate() )->format( 'H:i' ),
                );
            }
            $ignore_appointments[] = $appointment_id;
        }

        $scheduler = new Lib\Scheduler( $chain, date_create( $date )->format( 'Y-m-d 00:00' ), date_create( $date )->format( 'Y-m-d' ), 'daily', array( 'every' => 1 ), array(), false, $ignore_appointments );
        $schedule = $scheduler->scheduleForFrontend( 1 );
        $result = array( 'start' => array() );
        foreach ( $schedule[0]['options'] as $slot ) {
            $value = json_decode( $slot['value'], true );
            $date = date_create( $value[0][2] );
            $value = $date->format( 'Y-m-d H:i:00' );
            if ( ! empty( $custom_slot ) && $value === $custom_slot['value'] ) {
                $custom_slot = array();
            }
            if ( ! empty( $custom_slot ) && strcmp( $value, $custom_slot['value'] ) > 0 ) {
                $result[] = $custom_slot;
                $custom_slot = array();
            }
            $result['start'][] = array(
                'title' => $slot['title'],
                'value' => $value,
            );
        }

        if ( ! empty( $custom_slot ) ) {
            $result[] = $custom_slot;
        }

        $this->result = $result;
    }

    /**
     * @deprecated Bookly 25.1
     */
    protected function services()
    {
        $this->staffList();
        $this->result = $this->result ? $this->result[0]['services'] : array();
    }

    protected function staffList()
    {
        /** @var Staff[] $list */
        $list = $this->getStaffListByRole();

        $type = Service::TYPE_SIMPLE;

        $staff_list = array();

        foreach ( $list as $staff ) {
            $staff_item = array(
                'id' => (int) $staff->getId(),
                'full_name' => $staff->getFullName(),
                'email' => $staff->getEmail(),
                'phone' => $staff->getPhone(),
                'img' => Lib\Utils\Common::getAttachmentUrl( $staff->getAttachmentId(), 'full' ),
                'services' => array(),
                'locations' => array(),
            );

            $staff_locations = Lib\Proxy\Locations::findByStaffId( $staff->getId() ) ?: array();
            foreach ( $staff_locations as $location ) {
                $staff_item['locations'][] = (int) $location->getId();
            }

            $services = array();
            foreach ( $staff->getServicesData( $type ) as $row ) {
                /** @var Lib\Entities\StaffService $staff_service */
                $staff_service = $row['staff_service'];
                /** @var Service $service */
                $service = $row['service'];

                $sub_services = $service->getSubServices();
                if ( $type == Service::TYPE_SIMPLE || ! empty( $sub_services ) ) {
                    if ( $staff_service->getLocationId() === null || Lib\Proxy\Locations::prepareStaffLocationId( $staff_service->getLocationId(), $staff_service->getStaffId() ) == $staff_service->getLocationId() ) {
                        if ( ! in_array( $service->getId(), array_map( function ( $service ) { return $service['id']; }, $services ) ) ) {
                            $service_data = array(
                                'id' => (int) $service->getId(),
                                'name' => $service->getTitle(),
                                'name_d' => $service->getTitle() . ' (' . DateTime::secondsToInterval( $service->getDuration() ) . ')',
                                'duration' => (int) $service->getDuration(),
                                'locations' => array(
                                    array(
                                        'id' => (int) $staff_service->getLocationId() ?: 0,
                                        'capacity_min' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMin() : 1,
                                        'capacity_max' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMax() : 1,
                                    ),
                                ),
                                'extras' => array(),
                            );
                            foreach ( Lib\Proxy\ServiceExtras::findByServiceId( $service->getId() ) ?: array() as $extras ) {
                                $service_data['extras'][] = array(
                                    'id' => (int) $extras->getId(),
                                    'name' => $extras->getTitle(),
                                    'price' => (float) $extras->getPrice(),
                                    'min_quantity' => (int) $extras->getMinQuantity(),
                                    'max_quantity' => (int) $extras->getMaxQuantity(),
                                    'img' => Lib\Utils\Common::getAttachmentUrl( $extras->getAttachmentId(), 'full' ),
                                );
                            }

//                        $max_duration = max( $max_duration, $service->getUnitsMax() * $service->getDuration() );
//                        // Prepare time slots if service has custom time slots length.
//                        if ( ! $appointments_time_delimiter && $pre_generated && ( $ts_length = (int) $service->getSlotLength() ) ) {
//                            $time_end = max( $service->getUnitsMax() * $service->getDuration() + DAY_IN_SECONDS, DAY_IN_SECONDS * 2 );
//                            $service_data['custom_time_slots'] = $this->generateSlots( 0, $time_end, $ts_length );
//                        } else {
//                            $service_data['custom_time_slots'] = false;
//                        }
                            $services[] = $service_data;
                        } else {
                            array_walk( $services, function ( &$item ) use ( $staff_service, $service ) {
                                if ( $item['id'] == $service->getId() ) {
                                    $item['locations'][] = array(
                                        'id' => (int) $staff_service->getLocationId() ?: 0,
                                        'capacity_min' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMin() : 1,
                                        'capacity_max' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMax() : 1,
                                    );
                                }
                            } );
                        }
                    }
                }
            }
            $staff_item['services'] = $services;
            $staff_list[] = $staff_item;
        }

        $this->result = $staff_list;
    }

    protected function notifications()
    {
        Lib\Notifications\Routine::sendNotificationsAssociatedWithQueue( $this->param( 'notifications', array() ), $this->param( 'type', 'all' ), $this->param( 'token' ) );
        $this->result = array( 'success' => true );
    }

    protected function settings()
    {
        $result = array();
        $keys = array( 'addons', 'customer_appointment', 'customer_groups', 'locations' );
        $request = $this->param( 'data', array() );
        if ( ! is_array( $request ) || ! $request ) {
            $request = $keys;
        }

        foreach ( $request as $key ) {
            if ( $key === 'addons' ) {
                $data = Lib\Proxy\Shared::prepareL10nGlobal( array(
                    'addons' => array(),
                ) );
                $result[ $key ] = $data['addons'];
                continue;
            }

            if ( $key === 'customer_appointment' ) {
                $customer_gr_def_app_status = array();
                foreach ( Lib\Proxy\CustomerGroups::prepareDefaultAppointmentStatuses( array( 0 => Lib\Config::getDefaultAppointmentStatus() ) ) as $group_id => $status ) {
                    $customer_gr_def_app_status[] = array( 'group_id' => $group_id, 'status' => $status );
                }
                $result[ $key ] = array(
                    'statuses' => Common::getAllStatuses(),
                    'customer_gr_def_app_status' => $customer_gr_def_app_status,
                );
                continue;
            }

            if ( $key === 'customer_groups' ) {
                $customer_groups = array();
                foreach ( Lib\Proxy\CustomerGroups::getGroups() as $group_id => $title ) {
                    $customer_groups[] = array( 'group_id' => $group_id, 'title' => $title );
                }
                $result[ $key ] = $customer_groups;
                continue;
            }

            if ( $key === 'locations' ) {
                $locations = array();
                $staff_list = $this->getStaffListByRole();
                $exist_locations = array();
                foreach ( $staff_list as $staff ) {
                    $list = Lib\Proxy\Locations::findByStaffId( $staff->getId() )?: array();
                    foreach ( $list as $location ) {
                        if ( ! in_array( $location->getId(), $exist_locations ) ) {
                            $exist_locations[] = $location->getId();
                            $locations[] = array(
                                'id' => (int) $location->getId(),
                                'name' => $location->getName(),
                            );
                        }
                    }
                }
                $result[ $key ] = $locations;
                continue;
            }
        }

        $this->result = $result;
    }

    protected function deleteAttachments()
    {
        /** @var Lib\Entities\NotificationQueue $queue */
        $queue = Lib\Entities\NotificationQueue::query()->where( 'token', $this->param( 'token' ) )->where( 'sent', 0 )->findOne();
        if ( $queue ) {
            $queue_data = json_decode( $queue->getData(), true );
            Lib\Notifications\Routine::deleteNotificationAttachmentFiles( $queue_data );
            $queue->setSent( 1 )->save();
        }

        $this->result = array( 'success' => true );
    }

    /**
     * @param integer $time_start
     * @param integer $time_end
     * @param integer $ts_length
     * @param string $date
     * @param bool $first_day
     * @return array
     */
    protected function generateSlots( $time_start, $time_end, $ts_length, $date, $first_day = true )
    {
        $slots = array();
        $date_start = date_create( $date )->modify( '+' . $time_start . ' seconds' );
        $date_end = date_create( $date )->modify( '+' . $time_end . ' seconds' );
        while ( $date_start < $date_end ) {
            if ( ! $first_day || $date == $date_start->format( 'Y-m-d' ) ) {
                $slots[] = array(
                    'value' => $date_start->format( 'Y-m-d H:i:s' ),
                );
            }
            $date_start->modify( '+' . $ts_length . ' seconds' );
        }

        return $slots;
    }

    protected function extendCustomerAppointment( array $ca )
    {
        $default = array(
            'created_from' => 'mobile',
            'notes' => '',
            'payment_id' => null,
            'created_at' => current_time( 'mysql' ),
            'time_zone' => null,
            'time_zone_offset' => null,
            'custom_fields' => array(),
            'extras' => array(),
        );
        $ca += $default;
        $customer_appointment = $ca['ca_id']
            ? CustomerAppointment::find( $ca['ca_id'] )
            : array();
        if ( $customer_appointment ) {
            $fields = $customer_appointment->getFields();
            foreach ( $default as $key => $value ) {
                if ( ! array_key_exists( $key, $ca ) ) {
                    $ca[ $key ] = $fields[ $key ];
                }
            }
            $json_values = array( 'custom_fields', 'extras' );
            foreach ( $json_values as $key ) {
                if ( ! is_array( $ca[ $key ] ) ) {
                    $ca[ $key ] = json_decode( $ca[ $key ], true );
                }
            }
        } else {
            unset( $ca['ca_id'] );
        }

        return $ca;
    }

    /**
     * @param string $key
     * @param string $format
     * @return string
     * @throws Exceptions\ParameterException
     */
    protected function getDateFormattedParameter( $key, $format )
    {
        return $this->getDateTimeParameter( $key )->format( $format );
    }

    /**
     * @param string $key
     * @return \DateTime
     * @throws Exceptions\ParameterException
     */
    protected function getDateTimeParameter( $key )
    {
        try {
            if ( $this->param( $key ) ) {
                $date_time = date_create( $this->param( $key ) );
                if ( $date_time ) {
                    return $date_time;
                }
            }
            throw new Exceptions\ParameterException( $key, $this->param( $key ) );
        } catch ( \Error $e ) {
            throw new Exceptions\ParameterException( $key, $this->param( $key ) );
        }
    }

    /**
     * @param bool $return_ids
     * @param array|null $filter_by_ids
     * @return array
     */
    protected function getStaffListByRole( $return_ids = false, $filter_by_ids = array() )
    {
        $staff_list = array();

        if ( $this->role === self::ROLE_SUPERVISOR ) {
            $query = Lib\Entities\Staff::query()
                ->whereNot( 'visibility', 'archive' );
            if ( $filter_by_ids && is_array( $filter_by_ids ) ) {
                $query->whereIn( 'id', $filter_by_ids );
            }

            if ( $return_ids ) {
                $staff_list = $query->fetchCol( 'id' );
            } else {
                $staff_list = $query->find();
            }
        } elseif ( $this->role === self::ROLE_STAFF ) {
            if ( $return_ids ) {
                $staff_list = array( $this->staff->getId() );
            } else {
                $staff_list = array( $this->staff );
            }
        }

        return $staff_list;
    }
}