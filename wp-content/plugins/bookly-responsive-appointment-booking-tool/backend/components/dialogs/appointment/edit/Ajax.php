<?php
namespace Bookly\Backend\Components\Dialogs\Appointment\Edit;

use Bookly\Backend\Modules\Calendar;
use Bookly\Lib;
use Bookly\Lib\DataHolders\Booking as DataHolders;
use Bookly\Lib\Entities\Appointment;
use Bookly\Lib\Entities\Category;
use Bookly\Lib\Entities\Customer;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Entities\Service;
use Bookly\Lib\Entities\Staff;
use Bookly\Lib\Slots\DatePoint;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Utils\DateTime;

/**
 * Class Ajax
 *
 * @package Bookly\Backend\Components\Dialogs\Appointment\Edit
 */
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

        $appointments_time_delimiter = get_option( 'bookly_appointments_time_delimiter', 0 ) * MINUTE_IN_SECONDS;

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
                        if ( ! in_array( $service->getId(), array_map( function ( $service ) { return $service['id']; }, $services ) ) ) {
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
                            array_walk( $services, function ( &$item ) use ( $staff_service, $service ) {
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
            foreach ( (array) Lib\Proxy\Locations::findByStaffId( $staff_member->getId() ) as $location ) {
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
                    'group_id' => $customer->getGroupId(),
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
        $response = array( 'success' => false );
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

        if ( ! $service_id ) {
            // Custom service.
            $service_id = null;
        }
        if ( $service_id || $custom_service_name == '' ) {
            $custom_service_name = null;
        }
        if ( $service_id || $custom_service_price == '' ) {
            $custom_service_price = null;
        }
        if ( ! $location_id ) {
            $location_id = null;
        }

        // Check for errors.
        if ( ! $skip_date ) {
            if ( ! $start_date ) {
                $response['errors']['time_interval'] = __( 'Start time must not be empty', 'bookly' );
            } elseif ( ! $end_date ) {
                $response['errors']['time_interval'] = __( 'End time must not be empty', 'bookly' );
            } elseif ( $start_date == $end_date ) {
                $response['errors']['time_interval'] = __( 'End time must not be equal to start time', 'bookly' );
            }
        }

        if ( $service_id == -1 ) {
            $response['errors']['service_required'] = true;
        } elseif ( $service_id === null && $custom_service_name === null ) {
            $response['errors']['custom_service_name_required'] = true;
        }

        if ( ! $staff_id ) {
            $response['errors']['provider_required'] = true;
        }

        $total_number_of_persons = 0;
        $max_extras_duration = 0;
        $extras_consider_duration = (bool) Lib\Proxy\ServiceExtras::considerDuration();
        $busy_statuses = Lib\Proxy\CustomStatuses::prepareBusyStatuses( array(
            CustomerAppointment::STATUS_PENDING,
            CustomerAppointment::STATUS_APPROVED,
        ) );
        foreach ( $customers as $i => $customer ) {
            if ( in_array( $customer['status'], $busy_statuses ) ) {
                $total_number_of_persons += $customer['number_of_persons'];
                if ( $extras_consider_duration ) {
                    $extras_duration = Lib\Proxy\ServiceExtras::getTotalDuration( $customer['extras'] );
                    if ( $extras_duration > $max_extras_duration ) {
                        $max_extras_duration = $extras_duration;
                    }
                }
            }
            $customers[ $i ]['created_from'] = ( $created_from == 'backend' ) ? 'backend' : 'frontend';
        }
        if ( $service_id ) {
            $staff_service = new Lib\Entities\StaffService();
            $staff_service->loadBy( array(
                'staff_id' => $staff_id,
                'service_id' => $service_id,
                'location_id' => $location_id ?: null,
            ) );
            if ( ! $staff_service->isLoaded() ) {
                $staff_service->loadBy( array(
                    'staff_id' => $staff_id,
                    'service_id' => $service_id,
                    'location_id' => null,
                ) );
            }
            if ( $total_number_of_persons > $staff_service->getCapacityMax() ) {
                $response['errors']['overflow_capacity'] = (int) $staff_service->getCapacityMax();
            }
        }

        // If no errors then try to save the appointment.
        if ( ! isset ( $response['errors'] ) ) {
            $display_tz = Common::getCurrentUserTimeZone();
            if ( $skip_date ) {
                $duration = 0;
            } else {
                // Determine display time zone,
                // and shift the dates to WP time zone if needed
                $wp_tz = Lib\Config::getWPTimeZone();
                if ( $display_tz !== $wp_tz ) {
                    $start_date = DateTime::convertTimeZone( $start_date, $display_tz, $wp_tz );
                    $end_date = DateTime::convertTimeZone( $end_date, $display_tz, $wp_tz );
                }
                $duration = Lib\Slots\DatePoint::fromStr( $end_date )->diff( Lib\Slots\DatePoint::fromStr( $start_date ) );
            }
            if ( ! $skip_date && $repeat['enabled'] ) {
                $queue = array();
                // Series.
                if ( ! empty ( $schedule ) ) {
                    /** @var DataHolders\Order[] $orders */
                    $orders = array();

                    if ( $service_id ) {
                        $service = Service::find( $service_id );
                    } else {
                        $service = new Service();
                        $service
                            ->setTitle( $custom_service_name )
                            ->setDuration( $duration )
                            ->setPrice( $custom_service_price );
                    }

                    foreach ( $customers as $customer ) {
                        // Create new series.
                        $series = new Lib\Entities\Series();
                        $series
                            ->setRepeat( self::parameter( 'repeat' ) )
                            ->setToken( Common::generateToken( get_class( $series ), 'token' ) )
                            ->save();

                        // Create order
                        if ( $notification ) {
                            $orders[ $customer['id'] ] = DataHolders\Order::create( Customer::find( $customer['id'] ) )
                                ->addItem( 0, DataHolders\Series::create( $series ) );
                        }

                        foreach ( $schedule as $i => $slot ) {
                            $slot = json_decode( $slot, true );
                            $start_date = $slot[0][2];
                            $end_date = Lib\Slots\DatePoint::fromStr( $start_date )->modify( $duration )->format( 'Y-m-d H:i:s' );
                            // Try to find existing appointment
                            /** @var Appointment $appointment */
                            $appointment = Appointment::query( 'a' )
                                ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
                                ->where( 'a.staff_id', $staff_id )
                                ->where( 'a.service_id', $service_id )
                                ->whereNotIn( 'ca.status', Lib\Proxy\CustomStatuses::prepareFreeStatuses( array(
                                    CustomerAppointment::STATUS_CANCELLED,
                                    CustomerAppointment::STATUS_REJECTED,
                                ) ) )
                                ->where( 'start_date', $start_date )
                                ->findOne();

                            $ca_customers = array();
                            if ( $appointment ) {
                                foreach ( $appointment->getCustomerAppointments( true ) as $ca ) {
                                    $ca_customer = $ca->getFields();
                                    $ca_customer['ca_id'] = $ca->getId();
                                    $ca_customer['extras'] = json_decode( $ca_customer['extras'], true );
                                    $ca_customer['custom_fields'] = json_decode( $ca_customer['custom_fields'], true );
                                    $ca_customers[] = $ca_customer;
                                }
                            } else {
                                // Create appointment.
                                $appointment = new Appointment();
                                $appointment
                                    ->setLocationId( $location_id )
                                    ->setStaffId( $staff_id )
                                    ->setServiceId( $service_id )
                                    ->setCustomServiceName( $custom_service_name )
                                    ->setCustomServicePrice( $custom_service_price )
                                    ->setStartDate( $start_date )
                                    ->setEndDate( $end_date )
                                    ->setInternalNote( $internal_note )
                                    ->setExtrasDuration( $max_extras_duration )
                                    ->save();
                            }

                            if ( $appointment->getId() ) {
                                // Online meeting
                                Lib\Proxy\Shared::syncOnlineMeeting( array(), $appointment, $service );
                                // Save customer appointments.
                                $ca_list = $appointment->saveCustomerAppointments( array_merge( $ca_customers, array( $customer ) ), $series->getId() );
                                if ( $customer['payment_for'] === 'current' ) {
                                    $customer['payment_action'] = $customer['payment_for'] = null;
                                }
                                // Google Calendar.
                                Lib\Proxy\Pro::syncGoogleCalendarEvent( $appointment );
                                // Outlook Calendar.
                                Lib\Proxy\OutlookCalendar::syncEvent( $appointment );

                                if ( $notification ) {
                                    // Waiting list.
                                    Lib\Proxy\WaitingList::handleParticipantsChange( $queue, $appointment );
                                    foreach ( $ca_list as $ca ) {
                                        $item = DataHolders\Simple::create( $ca )
                                            ->setService( $service )
                                            ->setAppointment( $appointment );
                                        $orders[ $ca->getCustomerId() ]->getItem( 0 )->addItem( $i, $item );
                                        $queue = Lib\Proxy\WaitingList::handleFreePlace( $queue, $ca );
                                    }
                                }
                            }
                        }
                        if ( $customer['payment_action'] == 'create' && $customer['payment_for'] == 'series' ) {
                            Proxy\RecurringAppointments::createBackendPayment( $series, $customer );
                        }
                    }
                    if ( $notification ) {
                        foreach ( $orders as $order ) {
                            Lib\Notifications\Booking\Sender::sendForOrder( $order, array(), $notification == 'all', $queue );
                        }
                    }
                }
                $response['success'] = true;
                if ( $queue ) {
                    $db_queue = new Lib\Entities\NotificationQueue();
                    $db_queue
                        ->setData( json_encode( array( 'all' => $queue ) ) )
                        ->save();

                    $response['queue'] = array( 'token' => $db_queue->getToken(), 'all' => $queue, 'changed_status' => array() );
                }

                $response['data'] = array( 'resourceId' => $staff_id );  // make EventCalendar refetch events
            } else {
                // Single appointment.
                $appointment = new Appointment();
                if ( $appointment_id ) {
                    // Edit.
                    $appointment->load( $appointment_id );
                    if ( $appointment->getStaffId() != $staff_id ) {
                        $appointment->setStaffAny( 0 );
                    }
                    if ( $reschedule_type != 'current' ) {
                        $start_date_timestamp = strtotime( $start_date );
                        $days_offset = floor( $start_date_timestamp / DAY_IN_SECONDS ) - floor( strtotime( $appointment->getStartDate() ) / DAY_IN_SECONDS );
                        $reschedule_start_time = $start_date_timestamp % DAY_IN_SECONDS;
                        $current_start_date = $appointment->getStartDate();
                    }
                }
                $appointment
                    ->setLocationId( $location_id )
                    ->setStaffId( $staff_id )
                    ->setServiceId( $service_id )
                    ->setCustomServiceName( $custom_service_name )
                    ->setCustomServicePrice( $custom_service_price )
                    ->setStartDate( $skip_date ? null : $start_date )
                    ->setEndDate( $skip_date ? null : $end_date )
                    ->setInternalNote( $internal_note )
                    ->setExtrasDuration( $max_extras_duration );

                $modified = $appointment->getModified();
                if ( $appointment->save() !== false ) {

                    $queue_changed_status = array();
                    $queue = array();

                    foreach ( $customers as &$customer ) {
                        if ( $customer['payment_action'] === 'create' ) {
                            // Set 'current', the employee can choose 'Create: Payment for the entire series',
                            // but series cannot be created here
                            $customer['payment_for'] = 'current';
                        }
                    }
                    // Save customer appointments.
                    $ca_status_changed = $appointment->saveCustomerAppointments( $customers );

                    foreach ( $customers as $customer ) {
                        // Reschedule all recurring appointments for $days_offset days and set it's time to $reschedule_start_time
                        $rescheduled_appointments = array( $appointment_id );
                        if ( $appointment_id && $reschedule_type != 'current' && $customer['series_id'] ) {
                            $query = Appointment::query( 'a' )
                                ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
                                ->where( 'ca.series_id', $customer['series_id'] )
                                ->whereNotIn( 'a.id', $rescheduled_appointments );
                            if ( $reschedule_type == 'next' ) {
                                $query->whereGt( 'a.start_date', $current_start_date );
                            }
                            $reschedule_appointments = $query->find();
                            /** @var Appointment $reschedule_appointment */
                            foreach ( $reschedule_appointments as $reschedule_appointment ) {
                                $start_timestamp = strtotime( $reschedule_appointment->getStartDate() );
                                $duration = strtotime( $reschedule_appointment->getEndDate() ) - $start_timestamp;
                                $new_start_timestamp = ( (int) ( $start_timestamp / DAY_IN_SECONDS ) + $days_offset ) * DAY_IN_SECONDS + $reschedule_start_time;
                                $reschedule_appointment
                                    ->setStartDate( date( 'Y-m-d H:i:s', $new_start_timestamp ) )
                                    ->setEndDate( date( 'Y-m-d H:i:s', $new_start_timestamp + $duration ) );
                                $reschedule_modified = $reschedule_appointment->getModified();

                                $reschedule_appointment->save();

                                $rescheduled_appointments[] = $reschedule_appointment->getId();
                                if ( $notification ) {
                                    foreach ( $reschedule_appointment->getCustomerAppointments( true ) as $ca ) {
                                        Lib\Notifications\Booking\Sender::sendForCA( $ca, $appointment, array(), true, $queue );
                                    }
                                }

                                self::_deleteSentReminders( $reschedule_appointment, $reschedule_modified );
                            }
                        }
                    }

                    // Online meeting.
                    if ( $service_id ) {
                        $service = Service::find( $service_id );
                        $response['alert_errors'] = Lib\Proxy\Shared::syncOnlineMeeting( array(), $appointment, $service );
                    }
                    // Google Calendar.
                    Lib\Proxy\Pro::syncGoogleCalendarEvent( $appointment );
                    // Outlook Calendar.
                    Lib\Proxy\OutlookCalendar::syncEvent( $appointment );

                    // Send notifications.
                    if ( $notification ) {
                        // Waiting list.
                        $queue = Lib\Proxy\WaitingList::handleParticipantsChange( $queue, $appointment );

                        $ca_list = $appointment->getCustomerAppointments( true );
                        foreach ( $ca_list as $ca ) {
                            $queue = Lib\Proxy\WaitingList::handleFreePlace( $queue, $ca );
                        }
                        foreach ( $ca_status_changed as $ca ) {
                            if ( $appointment_id ) {
                                Lib\Notifications\Booking\Sender::sendForCA( $ca, $appointment, array(), false, $queue_changed_status );
                            }
                            Lib\Notifications\Booking\Sender::sendForCA( $ca, $appointment, array(), true, $queue );
                            unset( $ca_list[ $ca->getId() ] );
                        }
                        foreach ( $ca_list as $ca ) {
                            Lib\Notifications\Booking\Sender::sendForCA( $ca, $appointment, array(), true, $queue );
                        }
                    }

                    $response['success'] = true;
                    $response['data'] = $skip_date
                        ? array()
                        : self::_getAppointmentForCalendar( $appointment->getId(), $display_tz );
                    $db_queue = new Lib\Entities\NotificationQueue();
                    $db_queue
                        ->setData( json_encode( array( 'all' => $queue, 'changed_status' => $queue_changed_status ) ) )
                        ->save();

                    $response['queue'] = array( 'token' => $db_queue->getToken(), 'all' => $queue, 'changed_status' => $queue_changed_status );

                    self::_deleteSentReminders( $appointment, $modified );
                } else {
                    $response['errors'] = array( 'db' => __( 'Could not save appointment in database.', 'bookly' ) );
                }
            }
        }
        update_user_meta( get_current_user_id(), 'bookly_appointment_form_send_notifications', $notification );

        wp_send_json( $response );
    }

    /**
     * Check whether appointment settings produce errors.
     */
    public static function checkAppointmentErrors()
    {
        $start_date = self::parameter( 'start_date' );
        $end_date = self::parameter( 'end_date' );
        $appointment_duration = isset ( $start_date, $end_date )
            ? strtotime( $end_date ) - strtotime( $start_date )
            : 0;
        $staff_id = (int) self::parameter( 'staff_id' );
        $service_id = (int) self::parameter( 'service_id' );
        $location_id = (int) self::parameter( 'location_id' );
        $appointment_id = (int) self::parameter( 'appointment_id' );
        $customers = json_decode( self::parameter( 'customers', '[]' ), true );
        $service = Service::find( $service_id );
        $service_duration = $service ? $service->getDuration() : 0;
        $result = array(
            'date_interval_not_available' => false,
            'date_interval_warning' => false,
            'interval_not_in_staff_schedule' => false,
            'interval_not_in_service_schedule' => false,
            'staff_reaches_working_time_limit' => false,
            'customers_appointments_limit' => array(),
        );
        if ( $start_date && $end_date ) {
            // Determine display time zone,
            // and shift the dates to WP time zone if needed
            $display_tz = Common::getCurrentUserTimeZone();
            $wp_tz = Lib\Config::getWPTimeZone();
            if ( $display_tz !== $wp_tz ) {
                $start_date = DateTime::convertTimeZone( $start_date, $display_tz, $wp_tz );
                $end_date = DateTime::convertTimeZone( $end_date, $display_tz, $wp_tz );
            }
            // Dates in staff time zone
            $staff_start_date = $start_date;
            $staff_end_date = $end_date;

            $busy_statuses = Lib\Proxy\CustomStatuses::prepareBusyStatuses( array(
                CustomerAppointment::STATUS_PENDING,
                CustomerAppointment::STATUS_APPROVED,
            ) );
            $max_extras_duration = 0;
            if ( Lib\Proxy\ServiceExtras::considerDuration() ) {
                foreach ( $customers as $customer ) {
                    if ( in_array( $customer['status'], $busy_statuses ) ) {
                        $extras_duration = Lib\Proxy\ServiceExtras::getTotalDuration( $customer['extras'] );
                        if ( $extras_duration > $max_extras_duration ) {
                            $max_extras_duration = $extras_duration;
                        }
                    }
                }
            }

            $total_end_date = $end_date;
            if ( $max_extras_duration > 0 ) {
                $total_end_date = date_create( $end_date )->modify( '+' . $max_extras_duration . ' sec' )->format( 'Y-m-d H:i:s' );
            }

            $result['date_interval_not_available'] = self::_dateIntervalIsIntersectWith( $start_date, $total_end_date, $staff_id, $appointment_id ) ?: false;

            // Check if selected interval fits into staff schedule
            if ( $staff_id ) {
                $interval_valid = true;
                $staff = Staff::find( $staff_id );

                // Shift dates to staff time zone if needed
                $staff_tz = $staff->getTimeZone();
                if ( $staff_tz ) {
                    $staff_start_date = DateTime::convertTimeZone( $start_date, $wp_tz, $staff_tz );
                    $staff_end_date = DateTime::convertTimeZone( $end_date, $wp_tz, $staff_tz );
                }

                // Check if interval is suitable for staff's hours limit
                $result['staff_reaches_working_time_limit'] = (bool) Lib\Proxy\Pro::getWorkingTimeLimitError(
                    $staff,
                    $staff_start_date,
                    $staff_end_date,
                    $appointment_duration + $max_extras_duration,
                    $appointment_id
                );

                $start = date_create( $staff_start_date );
                $end = date_create( $staff_end_date );
                $schedule_items = $staff->getScheduleItems( Lib\Proxy\Locations::prepareStaffScheduleLocationId( $location_id, $staff_id ) ?: null );
                $special_days = array();
                $special_days_location_id = Lib\Proxy\Locations::prepareStaffSpecialDaysLocationId( $location_id, $staff_id ) ?: null;
                $schedule = Lib\Proxy\SpecialDays::getSchedule( array( $staff_id ), $start, $end ) ?: array();
                foreach ( $schedule as $day ) {
                    if ( $special_days_location_id === ( Lib\Proxy\Locations::prepareStaffSpecialDaysLocationId( $day['location_id'], $staff_id ) ?: null ) ) {
                        $special_days[ $day['date'] ][] = $day;
                    }
                }

                // Check staff schedule for holidays and days off
                $date = clone $start;
                while ( $date < $end ) {
                    if (
                        ! isset ( $special_days[ $date->format( 'Y-m-d' ) ] ) && (
                            $staff->isOnHoliday( $date ) ||
                            ! $schedule_items[ $date->format( 'w' ) + 1 ]->getStartTime()
                        )
                    ) {
                        $interval_valid = false;
                        break;
                    }
                    $date->modify( '+1 day' );
                }

                if ( $interval_valid && $service_duration < DAY_IN_SECONDS ) {
                    // For services with duration not in days check staff working hours
                    $interval_valid = false;
                    // Check start and previous day to get night schedule
                    $date = clone $start;
                    $date->modify( '-1 day' );
                    while ( $date <= $start ) {
                        $Ymd = $date->format( 'Y-m-d' );
                        $Ymd_secs = strtotime( $Ymd );
                        if ( isset ( $special_days[ $Ymd ] ) ) {
                            // Special day
                            $day_start = $Ymd . ' ' . $special_days[ $Ymd ][0]['start_time'];
                            $day_end = date( 'Y-m-d H:i:s', $Ymd_secs + DateTime::timeToSeconds( $special_days[ $Ymd ][0]['end_time'] ) );
                            if ( $day_start <= $staff_start_date && $day_end >= $staff_end_date ) {
                                // Check if interval does not intersect with breaks
                                $intersects = false;
                                foreach ( $special_days[ $Ymd ] as $break ) {
                                    if ( $break['break_start'] ) {
                                        $break_start = date(
                                            'Y-m-d H:i:s',
                                            $Ymd_secs + DateTime::timeToSeconds( $break['break_start'] )
                                        );
                                        $break_end = date(
                                            'Y-m-d H:i:s',
                                            $Ymd_secs + DateTime::timeToSeconds( $break['break_end'] )
                                        );
                                        if ( $break_start < $staff_end_date && $break_end > $staff_start_date ) {
                                            $intersects = true;
                                            break;
                                        }
                                    }
                                }
                                if ( ! $intersects ) {
                                    $interval_valid = true;
                                    break;
                                }
                            }
                        } else {
                            // Regular schedule
                            $item = $schedule_items[ $date->format( 'w' ) + 1 ];
                            if ( $item->getStartTime() ) {
                                $day_start = $Ymd . ' ' . $item->getStartTime();
                                $day_end = date( 'Y-m-d H:i:s', $Ymd_secs + DateTime::timeToSeconds( $item->getEndTime() ) );
                                if ( $day_start <= $staff_start_date && $day_end >= $staff_end_date ) {
                                    // Check if interval does not intersect with breaks
                                    $intersects = false;
                                    foreach ( $item->getBreaksList() as $break ) {
                                        $break_start = date(
                                            'Y-m-d H:i:s',
                                            $Ymd_secs + DateTime::timeToSeconds( $break['start_time'] )
                                        );
                                        $break_end = date(
                                            'Y-m-d H:i:s',
                                            $Ymd_secs + DateTime::timeToSeconds( $break['end_time'] )
                                        );
                                        if ( $break_start < $staff_end_date && $break_end > $staff_start_date ) {
                                            $intersects = true;
                                            break;
                                        }
                                    }
                                    if ( ! $intersects ) {
                                        $interval_valid = true;
                                        break;
                                    }
                                }
                            }
                        }
                        $date->modify( '+1 day' );
                    }
                }

                if ( ! $interval_valid ) {
                    $result['interval_not_in_staff_schedule'] = true;
                }
            }

            if ( $service ) {
                $result = Proxy\ServiceSchedule::checkAppointmentErrors( $result, $staff_start_date, $staff_end_date, $service_id, $service_duration );

                // Service duration interval is not equal to.
                $result['date_interval_warning'] = ! ( $appointment_duration >= $service->getMinDuration()
                    && $appointment_duration <= $service->getMaxDuration()
                    && ( $service_duration == 0 || $appointment_duration % $service_duration == 0 ) );

                // Check customers for appointments limit
                foreach ( $customers as $index => $customer ) {
                    if ( $service->appointmentsLimitReached( $customer['id'], array( $start_date ) ) ) {
                        $customer_error = Customer::find( $customer['id'] );
                        $result['customers_appointments_limit'][] = sprintf( __( '%s has reached the limit of bookings for this service', 'bookly' ), $customer_error->getFullName() );
                    }
                }

                $result['customers_appointments_limit'] = array_unique( $result['customers_appointments_limit'] );
            }
        }

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
            $end_date = $date->modify( $service->getDuration() . ' seconds' );
            $result['start'][] = array(
                'title' => $slot['title'],
                'value' => $value,
                'disabled' => $slot['disabled'],
            );

            $result['end'][] = array(
                'title_time' => date_i18n( $time_format, $end_date->getTimestamp() ),
                'value' => $end_date->format( 'H:i' ),
                'disabled' => $slot['disabled'],
            );
        }

        if ( ! empty( $custom_slot ) ) {
            $result[] = $custom_slot;
        }

        wp_send_json_success( $result );
    }

    /**
     * Get appointment for Event Calendar
     *
     * @param int $appointment_id
     * @param string $display_tz
     * @return array
     */
    private static function _getAppointmentForCalendar( $appointment_id, $display_tz )
    {
        $query = Appointment::query( 'a' )
            ->where( 'a.id', $appointment_id );

        $appointments = Calendar\Page::buildAppointmentsForCalendar( $query, $display_tz );

        return $appointments[0];
    }

    /**
     * Check whether interval is intersected with another appointments.
     *
     * @param string $start_date
     * @param string $end_date
     * @param int $staff_id
     * @param int $appointment_id
     * @return array|null
     */
    private static function _dateIntervalIsIntersectWith( $start_date, $end_date, $staff_id, $appointment_id )
    {
        return Appointment::query( 'a' )
            ->select( 'a.id AS appointment_id, COALESCE(s.title, a.custom_service_name) AS service' )
            ->leftJoin( 'Service', 's', 's.id = a.service_id' )
            ->whereNot( 'a.id', $appointment_id )
            ->where( 'a.staff_id', $staff_id )
            ->whereLt( 'a.start_date', $end_date )
            ->whereRaw( 'DATE_ADD(a.end_date, INTERVAL a.extras_duration SECOND) > \'%s\'', array( $start_date ) )
            ->limit( 1 )
            ->fetchRow();
    }

    /**
     * Delete marks for sent reminders
     *
     * @param Appointment $appointment
     * @param array $modified
     */
    private static function _deleteSentReminders( Appointment $appointment, $modified )
    {
        // When changed start_date need resend the reminders
        if ( array_key_exists( 'start_date', $modified ) ) {
            $ca_ids = CustomerAppointment::query()
                ->select( 'id' )
                ->where( 'appointment_id', $appointment->getId() )
                ->fetchCol( 'id' );
            if ( $ca_ids ) {
                Lib\Entities\SentNotification::query( 'sn' )
                    ->delete( 'sn' )
                    ->leftJoin( 'Notification', 'n', 'n.id = sn.notification_id' )
                    ->whereIn( 'sn.ref_id', $ca_ids )
                    ->whereIn( 'n.type', array( Lib\Entities\Notification::TYPE_APPOINTMENT_REMINDER, Lib\Entities\Notification::TYPE_LAST_CUSTOMER_APPOINTMENT ) )
                    ->where( 'n.active', 1 )
                    ->execute();
            }
        }
    }
}