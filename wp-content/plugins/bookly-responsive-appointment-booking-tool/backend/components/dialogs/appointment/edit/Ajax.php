<?php
namespace Bookly\Backend\Components\Dialogs\Appointment\Edit;

use Bookly\Lib;
use Bookly\Lib\Entities\Appointment;
use Bookly\Lib\Entities\Category;
use Bookly\Lib\Entities\Customer;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Entities\Service;
use Bookly\Lib\Entities\Staff;
use Bookly\Lib\Slots\DatePoint;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Utils\DateTime;

class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => array( 'staff', 'supervisor' ) );
    }

    /**
     * Get data needed for appointment form initialisation.
     */
    public static function getDataForAppointmentForm()
    {
        $type = self::parameter( 'type', false ) == 'package' ? Service::TYPE_PACKAGE : Service::TYPE_SIMPLE;

        $result = array(
            'staff' => array(),
            'customers' => array(),
            'customers_loaded' => true,
            'start_time' => array(),
            'end_time' => array(),
            'week_days' => array(),
            'time_interval' => Lib\Config::getTimeSlotLength(),
            'customer_gr_def_app_status' => Lib\Proxy\CustomerGroups::prepareDefaultAppointmentStatuses( array( 0 => Lib\Config::getDefaultAppointmentStatus() ) ),
        );

        $appointments_time_delimiter = (int) get_option( 'bookly_appointments_time_delimiter', 0 ) * MINUTE_IN_SECONDS;

        // Staff list
        /** @var Staff[] $staff_members */
        if ( Lib\Config::proActive() ) {
            $staff_members = Common::isCurrentUserSupervisor()
                ? Staff::query()->sortBy( 'position' )->find()
                : Staff::query()->where( 'wp_user_id', get_current_user_id() )->find();
        } else {
            $staff_members = Staff::query()->limit( 1 )->find();
        }

        $postfix_archived = sprintf( ' (%s)', __( 'Archived', 'bookly' ) );
        $max_duration = 0;
        $has_categories = (bool) Category::query()->findOne();
        $appropriate_time_slots = get_option( 'bookly_appointments_displayed_time_slots', 'all' ) === 'appropriate';

        foreach ( $staff_members as $staff_member ) {
            $services = array();
            if ( $type == Service::TYPE_SIMPLE ) {
                $services = Proxy\Pro::addCustomService( $services );
            }
            foreach ( $staff_member->getServicesData( $type ) as $row ) {
                /** @var Lib\Entities\StaffService $staff_service */
                $staff_service = $row['staff_service'];
                /** @var Service $service */
                $service = $row['service'];
                /** @var Category $category */
                $category = $row['category'];

                $sub_services = $service->getSubServices();
                if ( $type == Service::TYPE_SIMPLE || ! empty( $sub_services ) ) {
                    if ( $staff_service->getLocationId() === null || Lib\Proxy\Locations::prepareStaffLocationId( $staff_service->getLocationId(), $staff_service->getStaffId() ) == $staff_service->getLocationId() ) {
                        if ( ! in_array( $service->getId(), array_map( function( $service ) {
                            return $service['id'];
                        }, $services ) ) ) {
                            $service_data = array(
                                'id' => (int) $service->getId(),
                                'name' => sprintf( '%s (%s)', $service->getTitle(), DateTime::secondsToInterval( $service->getDuration() ) ),
                                'category' => $category->getId() ? $category->getName() : ( $has_categories ? __( 'Uncategorized', 'bookly' ) : '' ),
                                'duration' => (int) $service->getDuration(),
                                'units_min' => (int) $service->getUnitsMin(),
                                'units_max' => (int) $service->getUnitsMax(),
                                'price' => $staff_service->getPrice(),
                                'locations' => array(
                                    ( $staff_service->getLocationId() ?: 0 ) => array(
                                        'capacity_min' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMin() : 1,
                                        'capacity_max' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMax() : 1,
                                    ),
                                ),
                                'online_meetings' => $service->getOnlineMeetings(),
                                'position' => (int) $service->getPosition(),
                            );
                            $max_duration = max( $max_duration, $service->getUnitsMax() * $service->getDuration() );
                            // Prepare time slots if service has custom time slots length.
                            if ( ! $appointments_time_delimiter && ! $appropriate_time_slots && ( $ts_length = (int) $service->getSlotLength() ) ) {
                                // Time list.
                                $service_data['custom_time_slots'] = array(
                                    'start_time' => array(),
                                    'end_time' => array(),
                                );
                                $time_start = 0;
                                $time_end = max( $service->getUnitsMax() * $service->getDuration() + DAY_IN_SECONDS, DAY_IN_SECONDS * 2 );

                                // Run the loop.
                                while ( $time_start <= $time_end ) {
                                    $slot = array(
                                        'value' => DateTime::buildTimeString( $time_start, false ),
                                        'title' => DateTime::formatTime( $time_start ),
                                    );
                                    if ( $time_start < DAY_IN_SECONDS ) {
                                        $service_data['custom_time_slots']['start_time'][] = $slot;
                                    }
                                    $slot['title_time'] = $slot['title'];
                                    unset( $slot['title'] );
                                    $service_data['custom_time_slots']['end_time'][] = $slot;
                                    $time_start += $ts_length;
                                }
                            }

                            $services[] = $service_data;
                        } else {
                            array_walk( $services, function( &$item ) use ( $staff_service, $service ) {
                                if ( $item['id'] == $service->getId() ) {
                                    $item['locations'][ $staff_service->getLocationId() ?: 0 ] = array(
                                        'capacity_min' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMin() : 1,
                                        'capacity_max' => Lib\Config::groupBookingActive() ? (int) $staff_service->getCapacityMax() : 1,
                                    );
                                }
                            } );
                        }
                    }
                }
            }
            $locations = array();
            foreach ( Lib\Proxy\Locations::findByStaffId( $staff_member->getId() ) ?: array() as $location ) {
                $locations[] = array(
                    'id' => (int) $location->getId(),
                    'name' => $location->getName(),
                );
            }
            $result['staff'][] = array(
                'id' => (int) $staff_member->getId(),
                'full_name' => $staff_member->getFullName() . ( $staff_member->getVisibility() == 'archive' ? $postfix_archived : '' ),
                'archived' => $staff_member->getVisibility() == 'archive',
                'services' => $services,
                'locations' => $locations,
                'category' => Lib\Proxy\Pro::getStaffCategoryName( $staff_member->getCategoryId() ),
            );
        }

        /** @var Customer $customer */
        // Customers list.
        $customers_count = Customer::query( 'c' )->count();
        if ( $customers_count < Customer::REMOTE_LIMIT ) {
            foreach ( Customer::query()->sortBy( 'full_name' )->find() as $customer ) {
                $name = $customer->getFullName();
                if ( $customer->getEmail() != '' || $customer->getPhone() != '' ) {
                    $name .= ' (' . trim( $customer->getEmail() . ', ' . $customer->getPhone(), ', ' ) . ')';
                }

                $result['customers'][] = array(
                    'id' => (int) $customer->getId(),
                    'name' => $name,
                    'group_id' => Lib\Config::customerGroupsActive() ? $customer->getGroupId() : 0,
                    'timezone' => Lib\Proxy\Pro::getLastCustomerTimezone( $customer->getId() ),
                );
            }
        } else {
            $result['customers_loaded'] = false;
        }

        // Time list.
        // For appropriate time slots use minimal time slot length (5 min)
        $ts_length = $appropriate_time_slots ? 5 * MINUTE_IN_SECONDS : ( $appointments_time_delimiter > 0 ? $appointments_time_delimiter : Lib\Config::getTimeSlotLength() );
        $time_start = 0;
        $time_end = max( $max_duration + DAY_IN_SECONDS, DAY_IN_SECONDS * 2 );

        // Run the loop.
        while ( $time_start <= $time_end ) {
            $slot = array(
                'value' => DateTime::buildTimeString( $time_start, false ),
                'title' => DateTime::formatTime( $time_start ),
            );
            if ( $time_start < DAY_IN_SECONDS ) {
                $result['start_time'][] = $slot;
            }
            $slot['title_time'] = $slot['title'];
            unset( $slot['title'] );
            $result['end_time'][] = $slot;
            $time_start += $ts_length;
        }

        $days_times = Lib\Config::getDaysAndTimes();
        $weekdays = array( 1 => 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', );
        foreach ( $days_times['days'] as $index => $abbrev ) {
            $result['week_days'][] = $weekdays[ $index ];
        }

        if ( $type == Service::TYPE_PACKAGE ) {
            $result = Proxy\Shared::prepareDataForPackage( $result );
        }

        wp_send_json( $result );
    }

    /**
     * Get appointment data when editing an appointment.
     */
    public static function getDataForAppointment()
    {
        $response = array( 'success' => false, 'data' => array( 'customers' => array() ) );

        $appointment = new Appointment();
        if ( $appointment->load( self::parameter( 'id' ) ) ) {
            $response['success'] = true;

            // Determine display time zone
            $display_tz = Common::getCurrentUserTimeZone();
            $wp_tz = Lib\Config::getWPTimeZone();

            $start_date = $appointment->getStartDate();
            $end_date = $appointment->getEndDate();
            if ( $display_tz !== $wp_tz ) {
                $start_date = DateTime::convertTimeZone( $start_date, $wp_tz, $display_tz );
                $end_date = DateTime::convertTimeZone( $end_date, $wp_tz, $display_tz );
            }

            $response['data']['start_date'] = $start_date;
            $response['data']['end_date'] = $end_date;
            $response['data']['start_time'] = $start_date
                ? array(
                    'value' => date( 'H:i', strtotime( $start_date ) ),
                    'title' => DateTime::formatTime( $start_date ),
                )
                : null;
            $response['data']['end_time'] = $end_date
                ? array(
                    'value' => date( 'H:i', strtotime( $end_date ) ),
                    'title' => DateTime::formatTime( $end_date ),
                )
                : null;
            $response['data']['staff_id'] = (int) $appointment->getStaffId();
            $response['data']['staff_any'] = (int) $appointment->getStaffAny();
            $response['data']['service_id'] = (int) $appointment->getServiceId();
            $response['data']['custom_service_name'] = $appointment->getCustomServiceName();
            $response['data']['custom_service_price'] = (float) $appointment->getCustomServicePrice();
            $response['data']['internal_note'] = $appointment->getInternalNote();
            $response['data']['location_id'] = (int) $appointment->getLocationId();
            $response['data']['online_meeting_start_url'] = Lib\Proxy\Shared::buildOnlineMeetingStartUrl( '', $appointment );

            $customers = CustomerAppointment::query( 'ca' )
                ->select( 'ca.id,
                    ca.series_id,
                    ca.customer_id,
                    ca.package_id,
                    ca.custom_fields,
                    ca.extras,
                    ca.extras_multiply_nop,
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
                $payment_title = $customer['payment'] !== null ? Lib\Entities\Payment::paymentInfo(
                    $customer['payment'],
                    $customer['payment_total'],
                    $customer['payment_type'],
                    $customer['payment_status']
                ) : '';
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
                $name = $customer['full_name'];
                if ( $customer['email'] != '' || $customer['phone'] != '' ) {
                    $name .= ' (' . trim( $customer['email'] . ', ' . $customer['phone'], ', ' ) . ')';
                }
                $response['data']['customers_data'][] = array(
                    'id' => (int) $customer['customer_id'],
                    'name' => $name,
                    'group_id' => $customer['group_id'],
                    'timezone' => Lib\Proxy\Pro::getLastCustomerTimezone( $customer['customer_id'] ),
                );
                $response['data']['customers'][] = array(
                    'id' => (int) $customer['customer_id'],
                    'ca_id' => $customer['id'],
                    'series_id' => $customer['series_id'],
                    'package_id' => $customer['package_id'],
                    'collaborative_service' => $collaborative_service,
                    'collaborative_token' => $customer['collaborative_token'],
                    'compound_service' => $compound_service,
                    'compound_token' => $customer['compound_token'],
                    'custom_fields' => $custom_fields,
                    'files' => Lib\Proxy\Files::getFileNamesForCustomFields( $custom_fields ),
                    'extras' => (array) json_decode( $customer['extras'], true ),
                    'extras_multiply_nop' => (int) $customer['extras_multiply_nop'],
                    'number_of_persons' => (int) $customer['number_of_persons'],
                    'notes' => $customer['notes'],
                    'payment_id' => $customer['payment_id'],
                    'payment_type' => $customer['payment_id']
                        ? ( $customer['payment'] != $customer['payment_total'] ? 'partial' : 'full' )
                        : null,
                    'payment_title' => $payment_title,
                    'group_id' => $customer['group_id'],
                    'status' => $customer['status'],
                    'timezone' => Lib\Proxy\Pro::getCustomerTimezone( $customer['time_zone'], $customer['time_zone_offset'] ),
                );
            }
            // Service data
            if ( $appointment->getServiceId() ) {
                $service = Service::find( $appointment->getServiceId() );
                if ( $service ) {
                    $category = '';
                    if ( $service->getCategoryId() ) {
                        $category = Category::find( $service->getCategoryId() );
                        $category = $category->getName();
                    }
                    $response['data']['service'] = array(
                        'id' => (int) $service->getId(),
                        'name' => sprintf( '%s (%s)', $service->getTitle(), DateTime::secondsToInterval( $service->getDuration() ) ),
                        'category' => $category,
                        'duration' => (int) $service->getDuration(),
                        'units_min' => (int) $service->getUnitsMin(),
                        'units_max' => (int) $service->getUnitsMax(),
                        'locations' => array(
                            array(
                                'capacity_min' => Lib\Config::groupBookingActive() ? (int) $service->getCapacityMin() : 1,
                                'capacity_max' => Lib\Config::groupBookingActive() ? (int) $service->getCapacityMax() : 1,
                            ),
                        ),
                        'online_meetings' => $service->getOnlineMeetings(),
                    );
                }
            }
        }

        wp_send_json( $response );
    }

    /**
     * Save appointment form (for both create and edit).
     */
    public static function saveAppointmentForm()
    {
        $appointment_id = (int) self::parameter( 'id', 0 );
        $staff_id = (int) self::parameter( 'staff_id', 0 );
        $service_id = (int) self::parameter( 'service_id', -1 );
        $custom_service_name = trim( self::parameter( 'custom_service_name', '' ) );
        $custom_service_price = trim( self::parameter( 'custom_service_price', '' ) );
        $location_id = (int) self::parameter( 'location_id', 0 );
        $skip_date = self::parameter( 'skip_date', 0 );
        $start_date = self::parameter( 'start_date' );
        $end_date = self::parameter( 'end_date' );
        $repeat = json_decode( self::parameter( 'repeat', '[]' ), true );
        $schedule = self::parameter( 'schedule', array() );
        $reschedule_type = self::parameter( 'reschedule_type', 'current' );
        $customers = json_decode( self::parameter( 'customers', '[]' ), true );
        $notification = self::parameter( 'notification', false );
        $internal_note = self::parameter( 'internal_note' );
        $created_from = self::parameter( 'created_from' );

        $response = Lib\Utils\Appointment::save(
            $appointment_id,
            $staff_id,
            $service_id,
            $custom_service_name,
            $custom_service_price,
            $location_id,
            $skip_date,
            $start_date,
            $end_date,
            $repeat,
            $schedule,
            $reschedule_type,
            $customers,
            $notification,
            $internal_note,
            $created_from
        );

        wp_send_json( $response );
    }

    /**
     * Check whether appointment settings produce errors.
     */
    public static function checkAppointmentErrors()
    {
        $result = Lib\Utils\Appointment::checkTime(
            (int) self::parameter( 'appointment_id' ),
            self::parameter( 'start_date' ),
            self::parameter( 'end_date' ),
            (int) self::parameter( 'staff_id' ),
            (int) self::parameter( 'service_id' ),
            (int) self::parameter( 'location_id' ),
            json_decode( self::parameter( 'customers', '[]' ), true )
        );

        wp_send_json( $result );
    }

    /**
     * Get day schedule for "reschedule" button
     */
    public static function getDaySchedule()
    {
        $staff_ids = array( self::parameter( 'staff_id' ) );
        $service_id = self::parameter( 'service_id' );
        $service = Service::find( $service_id );
        $date = self::parameter( 'date' );

        $appointment_id = self::parameter( 'appointment_id' );
        $location_id = self::parameter( 'location_id' );
        $nop = max( 1, self::parameter( 'nop', 1 ) );

        // Get array of extras with max duration
        $extras = Proxy\Extras::getMaxDurationExtras( self::parameter( 'extras', array() ) );

        $chain_item = new Lib\ChainItem();
        $chain_item
            ->setStaffIds( $staff_ids )
            ->setServiceId( $service_id )
            ->setLocationId( $location_id )
            ->setNumberOfPersons( $nop )
            ->setQuantity( 1 )
            ->setLocationId( $location_id )
            ->setUnits( 1 )
            ->setExtras( $extras );

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
        $result = array();
        $time_format = get_option( 'time_format' );
        if ( isset( $schedule[0]['options'] ) ) {
            foreach ( $schedule[0]['options'] as $slot ) {
                $value = json_decode( $slot['value'], true );
                $date = date_create( $value[0][2] );
                $value = $date->format( 'H:i' );
                if ( ! empty( $custom_slot ) && $value === $custom_slot['value'] ) {
                    $custom_slot = array();
                }
                if ( ! empty( $custom_slot ) && strcmp( $value, $custom_slot['value'] ) > 0 ) {
                    $result[] = $custom_slot;
                    $custom_slot = array();
                }
                $end_date = clone $date;
                $end_date = $end_date->modify( $service->getDuration() . ' seconds' );
                $result['start'][] = array(
                    'title' => $slot['title'],
                    'value' => $value,
                    'disabled' => $slot['disabled'],
                );

                $result['end'][] = array(
                    'title_time' => date_i18n( $time_format, $end_date->getTimestamp() ),
                    'value' => $end_date->getTimestamp() - $date->modify( 'midnight' )->getTimestamp() >= DAY_IN_SECONDS ? ( (int) $end_date->format( 'H' ) + 24 ) . ':' . $end_date->format( 'i' ) : $end_date->format( 'H:i' ),
                    'disabled' => $slot['disabled'],
                );
            }
        }

        if ( ! empty( $custom_slot ) ) {
            $result[] = $custom_slot;
        }

        wp_send_json_success( $result );
    }

}