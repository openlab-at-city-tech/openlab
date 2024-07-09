<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet;

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

class Response10
{
    /** @var Staff */
    protected $staff;
    /** @var array */
    protected $params;
    /** @var array */
    protected $result = array();
    /** @var int */
    protected $http_status = 200;
    /** @var bool */
    protected $error_code = 0;
    /** @var string */
    protected $error_message = 'ERROR';
    /** @var array */
    protected $error_data = array();

    /**
     * @param Entities\Staff $staff
     * @param array $params
     */
    public function __construct( $staff, $params )
    {
        $this->staff = $staff;
        $this->params = $params;
    }

    public function init()
    {
//        $pre_generated = get_option( 'bookly_appointments_displayed_time_slots' ) === 'all';
//        $type = Service::TYPE_SIMPLE;
//        $appointments_time_delimiter = get_option( 'bookly_appointments_time_delimiter', 0 ) * MINUTE_IN_SECONDS;
//        $max_duration  = 0;

//        $services = array();
//        foreach ( $this->staff->getServicesData( $type ) as $row ) {
//            /** @var Lib\Entities\StaffService $staff_service */
//            $staff_service = $row['staff_service'];
//            /** @var Service $service */
//            $service = $row['service'];
//
//            $sub_services = $service->getSubServices();
//            if ( $type == Service::TYPE_SIMPLE || ! empty( $sub_services ) ) {
//                if ( $staff_service->getLocationId() === null || Lib\Proxy\Locations::prepareStaffLocationId( $staff_service->getLocationId(), $staff_service->getStaffId() ) == $staff_service->getLocationId() ) {
//                    if ( ! in_array( $service->getId(), array_map( function ( $service ) { return $service['id']; }, $services ) ) ) {
//                        $service_data = array(
//                            'id' => (int) $service->getId(),
//                            'name' => $service->getTitle(),
//                            'name_d' => $service->getTitle() . ' (' . DateTime::secondsToInterval( $service->getDuration() ) . ')',
//                            'duration' => (int) $service->getDuration(),
//                            'locations' => (object) array(
//                                ( $staff_service->getLocationId() ?: 0 ) => array(
//                                    'capacity_min' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMin() : 1,
//                                    'capacity_max' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMax() : 1,
//                                ),
//                            ),
//                        );
//                        $max_duration = max( $max_duration, $service->getUnitsMax() * $service->getDuration() );
//                        // Prepare time slots if service has custom time slots length.
//                        if ( ! $appointments_time_delimiter && $pre_generated && ( $ts_length = (int) $service->getSlotLength() ) ) {
//                            $time_end = max( $service->getUnitsMax() * $service->getDuration() + DAY_IN_SECONDS, DAY_IN_SECONDS * 2 );
//                            $service_data['custom_time_slots'] = $this->generateSlots( 0, $time_end, $ts_length );
//                        } else {
//                            $service_data['custom_time_slots'] = false;
//                        }
//                        $services[] = $service_data;
//                    } else {
////                        array_walk( $services, function ( &$item ) use ( $staff_service, $service ) {
////                            if ( $item['id'] == $service->getId() ) {
////                                $item['locations'][ $staff_service->getLocationId() ?: 0 ] = array(
////                                    'capacity_min' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMin() : 1,
////                                    'capacity_max' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMax() : 1,
////                                );
////                            }
////                        } );
//                    }
//                }
//            }
//        }
//        $slots = array( 'start' => array() );
//
//        if ( $pre_generated ) {
//            $ts_length = $appointments_time_delimiter > 0 ? $appointments_time_delimiter : Lib\Config::getTimeSlotLength();
//            $time_end = max( $max_duration + DAY_IN_SECONDS, DAY_IN_SECONDS * 2 );
//
//            $slots = $this->generateSlots( 0, $time_end, $ts_length );
//        }
//        $bounding = Lib\Config::getBoundingDaysForPickadate();
        $this->result = array(
            'me' => array(
                'email' => $this->staff->getEmail(),
                'full_name' => $this->staff->getFullName(),
            ),
            'settings' => array(
//                'slots' => array(
//                    'server_side' => ! $pre_generated,
//                    'default' => $slots,
//                ),
//                'customer' => array(
//                    'default_status' => (object) Lib\Proxy\CustomerGroups::prepareDefaultAppointmentStatuses( array( 0 => Lib\Config::getDefaultAppointmentStatus() ) ),
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

    public function appointment()
    {
        $response = array( 'data' => array( 'customer_appointments' => array() ) );

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
                ->addSelect( sprintf( '%s AS min_capacity, %s AS max_capacity',
                    Lib\Proxy\Shared::prepareStatement( 1, 'MIN(ss.capacity_min)', 'StaffService' ),
                    Lib\Proxy\Shared::prepareStatement( 1, 'MAX(ss.capacity_max)', 'StaffService' )
                ) )
                ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
                ->leftJoin( 'StaffService', 'ss', 'ss.staff_id = a.staff_id AND ss.service_id = a.service_id AND ss.location_id <=> a.location_id' )
                ->where( 'a.id', $appointment->getId() )
            ;

            if ( ! Lib\Proxy\Locations::servicesPerLocationAllowed() ) {
                $query
                    ->addSelect( 'ss.location_id' )
                    ->where( 'ss.location_id', null );
            }

            // Determine display time zone
            $display_tz = Common::getCurrentUserTimeZone();
            $wp_tz = Lib\Config::getWPTimeZone();

            // Fetch appointment,
            // and shift the dates to appropriate time zone if needed
            $info = $query->fetchRow();
            if ( $display_tz !== $wp_tz ) {
                $info['start_date'] = DateTime::convertTimeZone( $info['start_date'], $wp_tz, $display_tz );
                $info['end_date']   = DateTime::convertTimeZone( $info['end_date'], $wp_tz, $display_tz );
            }

            $response['data']['id'] = (int) $appointment->getId();
            $response['data']['total_number_of_persons'] = (int) $info['total_number_of_persons'];
            $response['data']['min_capacity'] = (int) $info['min_capacity'];
            $response['data']['max_capacity'] = (int) $info['max_capacity'];
            $response['data']['start_date'] = $info['start_date'];
            $response['data']['end_date'] = $info['end_date'];
            $response['data']['service_id'] = (int) $info['service_id'];
            $response['data']['internal_note'] = $info['internal_note'];
            $response['data']['location_id'] = (int) $info['location_id'];

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
                    c.group_id')
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
                    'id' => (int) $customer['customer_id'],
                    'ca_id' => (int) $customer['id'],
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

    public function checkAppointmentTime()
    {
        $appointment_id = (int) $this->param( 'id', 0 );
        $service_id = (int) $this->param( 'service_id' );
        $customer_appointments = $this->param( 'customer_appointments', array() );
        $start_date = $this->param( 'start_date' );
        foreach ( $customer_appointments as &$ca ) {
            if ( isset( $ca['id'] ) ) {
                $ca['ca_id'] = $ca['id'];
                $ca = $this->extendCustomerAppointment( $ca );
            }
            $ca['id'] = $ca['customer_id'];
            unset( $ca['customer_id'] );
        }

        $appointment = $appointment_id
            ? Appointment::find( $appointment_id )
            : null;

        if ( $appointment ) {
            if ( ( $appointment->getStartDate() == $start_date )
                && ( $appointment->getServiceId() == $service_id ) )
            {
                $end_date = $appointment->getEndDate();
            } else {
                $service = Service::find( $service_id );
                $end_date = DatePoint::fromStr( $start_date )->modify( $service->getDuration() )->format( 'Y-m-d H:i:s' );
            }
        } elseif ( $service_id ) {
            $service = Service::find( $service_id );
            $end_date = DatePoint::fromStr( $start_date )->modify( $service->getDuration() )->format( 'Y-m-d H:i:s' );
        }

        $result = UtilAppointment::checkTime( $appointment_id,
            $start_date,
            $end_date,
            (int) $this->staff->getId(),
            (int) $this->param( 'service_id' ),
            null,
            $customer_appointments
        );

        $result['date_interval_not_available'] = (bool) $result['date_interval_not_available'];

        $this->result = $result;
    }

    public function saveAppointment()
    {
        $appointment_id = (int) $this->param( 'id', 0 );
        $service_id = (int) $this->param( 'service_id' );
        $service = $service_id
            ? Service::find( $service_id )
            : null;
        if ( ! $service ) {
            throw new ParameterException( 'service_id', $this->param( 'service_id' ) );
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
        $appointment = $appointment_id
            ? Appointment::find( $appointment_id )
            : null;

        if ( $appointment && $appointment->getStartDate() == $start_date && $service_id == $appointment->getServiceId() ) {
            $end_date = $appointment->getEndDate();
        } else {
            $end_date = DatePoint::fromStr( $start_date )->modify( $service->getDuration() )->format( 'Y-m-d H:i:s' );
        }

        $response = UtilAppointment::save(
            $appointment_id,
            (int) $this->staff->getId(),
            $service_id,
            '',
            '',
            0,
            0,
            $start_date,
            $end_date,
            array( 'enabled' => 0 ),
            array(),
            'current',
            $customer_appointments,
            false,
            $this->param( 'internal_note' ),
            'mobile'
        );
        if ( $response['success'] ) {
            unset( $response['data'], $response['queue'] );

            $this->result = $response;
        } else {
            $this->error_code = 400;
            $this->error_data = $response['errors'];
        }
    }

    public function appointments()
    {
        $one_day = new \DateInterval( 'P1D' );
        $list = array();
        $start_date = $this->getDateTimeParameter( 'start_date' );
        $end_date = $this->getDateTimeParameter( 'end_date' );
        $location_ids = array();

        // Determine display time zone
        $display_tz = Common::getCurrentUserTimeZone();

        // Due to possibly different time zones of staff members expand start and end dates
        // to provide 100% coverage of the requested date range
        $start_date->sub( $one_day );
        $end_date->add( $one_day );

        $query = \Bookly\Backend\Modules\Calendar\Ajax::getAppointmentsQueryForCalendar( array( $this->staff ), $start_date, $end_date, $location_ids );
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
            }
        }

        foreach ( $appointments as $appointment ) {
            $customer_id = (int) $appointment['customer_id'];
            $customer = $customer_id === 0
                ? null
                : array(
                    'id' => (int) $appointment['ca_id'],
                    'customer_id' => (int) $customer_id,
                    'full_name' => $appointment['client_name'],
                );

            $list[] = array(
                'id' => (int) $appointment['id'],
                'start_date' => $appointment['start_date'],
                'end_date' => $appointment['end_date'],
                'service' => array(
                    'id' => (int) $appointment['service_id'],
                    'name' => $appointment['service_name'],
                ),
                'color' => $appointment['service_color'],
                'internal_note' => trim( $appointment['internal_note'] ),
                'customer_appointments' => $customer ? array( $customer ) : array(),
            );
        }

        $this->result = $list ?: array();
    }

    public function customers()
    {
        $list = Entities\Customer::query()->select( 'id, first_name, last_name, email, phone, notes, group_id' )
            ->sortBy( 'full_name' )
            ->fetchArray() ?: array();

        array_walk( $list, function ( &$item ) {
            $item['id'] = (int) $item['id'];
            $item['group_id'] = (int) $item['group_id'];
            $item['timezone'] = Lib\Proxy\Pro::getLastCustomerTimezone( $item['id'] );
        } );

        $this->result = $list;
    }

    public function saveCustomer()
    {
        if ( $this->param( 'id' ) ) {
            $customer = Entities\Customer::find( $this->param( 'id' ) );
        } else {
            $customer = new Entities\Customer();
        }
        $customer
            ->setFullName( trim( rtrim( $this->param( 'first_name' ) ) . ' ' . ltrim( $this->param( 'last_name' ) ) ) )
            ->setEmail( $this->param( 'email', '' ) )
            ->setPhone( $this->param( 'phone', '' ) )
            ->setNotes( $this->param( 'notes', '' ) )
            ->save();

        $this->result = array(
            'id' => (int) $customer->getId(),
            'first_name' => $customer->getFirstName(),
            'last_name' => $customer->getLastName(),
        );
    }

    public function slots()
    {
        $service_id = $this->param( 'service_id' );
        $service = Service::find( $service_id );
        if ( $service ) {
            $appointments_time_delimiter = get_option( 'bookly_appointments_time_delimiter', 0 ) * MINUTE_IN_SECONDS;
            $date = $this->getDateFormattedParameter( 'date', 'Y-m-d' );
            if ( ! $appointments_time_delimiter && ( $ts_length = (int) $service->getSlotLength() ) ) {
                $time_end = max( $service->getUnitsMax() * $service->getDuration() + DAY_IN_SECONDS, DAY_IN_SECONDS * 2 );
            } else {
                $ts_length = $appointments_time_delimiter > 0 ? $appointments_time_delimiter : Lib\Config::getTimeSlotLength();
                $time_end = max( ( $service->getUnitsMax() * $service->getDuration() ) + DAY_IN_SECONDS, DAY_IN_SECONDS * 2 );
            }
            $this->result = $this->generateSlots( 0, $time_end, $ts_length, $date . ' ' );
        } else {
            throw new ParameterException( 'service_id', $service_id );
        }
    }

    public function availableSlots()
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

    public function services()
    {
//        $pre_generated = get_option( 'bookly_appointments_displayed_time_slots' ) === 'all';
        $type = Service::TYPE_SIMPLE;
//        $appointments_time_delimiter = get_option( 'bookly_appointments_time_delimiter', 0 ) * MINUTE_IN_SECONDS;
//        $max_duration  = 0;

        $services = array();
        foreach ( $this->staff->getServicesData( $type ) as $row ) {
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
//                            'locations' => (object) array(
//                                ( $staff_service->getLocationId() ?: 0 ) => array(
//                                    'capacity_min' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMin() : 1,
//                                    'capacity_max' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMax() : 1,
//                                ),
//                            ),
                        );
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
//                        array_walk( $services, function ( &$item ) use ( $staff_service, $service ) {
//                            if ( $item['id'] == $service->getId() ) {
//                                $item['locations'][ $staff_service->getLocationId() ?: 0 ] = array(
//                                    'capacity_min' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMin() : 1,
//                                    'capacity_max' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMax() : 1,
//                                );
//                            }
//                        } );
                    }
                }
            }
        }

        $this->result = $services;
    }

    public function setError( $code, $message = null, $http_status = null, $error_data = null )
    {
        $this->error_code = $code;
        if ( $message ) {
            $this->error_message = $message;
        }
        if ( $http_status ) {
            $this->http_status = $http_status;
        }
        if ( $error_data ) {
            $this->error_data = $error_data;
        }
    }

    public function render()
    {
        if ( $this->error_code > 0 ) {
            $data = array(
                'error' => array(
                    'code' => $this->error_code,
                    'message' => $this->error_message,
                ),
            );
            if ( $this->error_data ) {
                $data['error']['data'] = $this->error_data;
            }
        } else {
            $data = array( 'result' => $this->result );
        }
        wp_send_json( $data, $this->http_status );
    }

    protected function param( $name, $default = null )
    {
        return array_key_exists( $name, $this->params ) ? stripslashes_deep( $this->params[ $name ] ) : $default;
    }

    protected function generateSlots( $time_start, $time_end, $ts_length, $prefix = '' )
    {
        $slots = array( 'start' => array() );
        // Run the loop.
        while ( $time_start <= $time_end ) {
            $slot = array(
                'value' => $prefix . DateTime::buildTimeString( $time_start ),
            );
            if ( $time_start < DAY_IN_SECONDS ) {
                $slots['start'][] = $slot;
            }
            $time_start += $ts_length;
        }

        return $slots;
    }

    protected function extendCustomerAppointment( array $ca )
    {
        $default = array(
            'status' => Lib\Config::getDefaultAppointmentStatus(),
            'number_of_persons' => 1,
            'notes' => '',
            'created_from' => 'mobile',
            'payment_id' => null,
            'created_at' => current_time( 'mysql' ),
            'time_zone' => null,
            'time_zone_offset' => null,
            'custom_fields' => array(),
            'extras' => array(),
        );
        $ca = array_merge( $ca, $default );
        $customer_appointment = CustomerAppointment::find( $ca['ca_id'] );
        if ( $customer_appointment ) {
            $fields = $customer_appointment->getFields();
            foreach ( $default as $key => $value ) {
                $ca[ $key ] = $fields[ $key ];
            }
            $json_values = array( 'custom_fields', 'extras', );
            foreach ( $json_values as $key ) {
                $ca[ $key ] = json_decode( $ca[ $key ], true );
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
     * @throws ParameterException
     */
    protected function getDateFormattedParameter( $key, $format )
    {
        return $this->getDateTimeParameter( $key )->format( $format );
    }

    /**
     * @param $key
     * @return \DateTime
     * @throws ParameterException
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
            throw new ParameterException( $key, $this->param( $key ) );
        } catch ( \Error $e ) {
            throw new ParameterException( $key, $this->param( $key ) );
        }
    }
}