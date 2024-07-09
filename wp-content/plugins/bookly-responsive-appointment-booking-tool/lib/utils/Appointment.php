<?php
namespace Bookly\Lib\Utils;

use Bookly\Lib\Entities;
use Bookly\Lib\Entities\Customer;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Entities\Service;
use Bookly\Lib\Entities\Staff;
use Bookly\Lib;
use Bookly\Lib\DataHolders\Booking as DataHolders;
use Bookly\Backend\Components\Dialogs\Appointment\Edit\Proxy;
use Bookly\Backend\Components\Dialogs\Queue\NotificationList;
use Bookly\Backend\Modules\Calendar;

class Appointment
{
    /**
     * @param $appointment_id
     * @param $staff_id
     * @param $service_id
     * @param $custom_service_name
     * @param $custom_service_price
     * @param $location_id
     * @param $skip_date
     * @param $start_date
     * @param $end_date
     * @param $repeat
     * @param $schedule
     * @param $reschedule_type
     * @param $customers
     * @param $notification
     * @param $internal_note
     * @param $created_from
     * @return array
     */
    public static function save(
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
    )
    {
        $response = array( 'success' => false, 'alert_errors' => array() );
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
        } else if ( $service_id === null && $custom_service_name === null ) {
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
            CustomerAppointment::STATUS_APPROVED
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
            if ( ! $skip_date && isset( $repeat['enabled'] ) && $repeat['enabled'] ) {
                $queue = new NotificationList();
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
                            ->setRepeat( json_encode( $repeat ) )
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
                            /** @var Entities\Appointment $appointment */
                            $appointment = Entities\Appointment::query( 'a' )
                                ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
                                ->where( 'a.staff_id', $staff_id )
                                ->where( 'a.service_id', $service_id )
                                ->whereNotIn( 'ca.status', Lib\Proxy\CustomStatuses::prepareFreeStatuses( array(
                                    CustomerAppointment::STATUS_CANCELLED,
                                    CustomerAppointment::STATUS_REJECTED
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
                                $appointment = new Entities\Appointment();
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

                                Common::syncWithCalendars( $appointment );

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
                $list = $queue->getList();
                if ( $list ) {
                    $db_queue = new Lib\Entities\NotificationQueue();
                    $db_queue
                        ->setData( json_encode( array( 'all' => $list ) ) )
                        ->save();

                    $response['queue'] = array( 'token' => $db_queue->getToken(), 'all' => $list, 'changed_status' => array() );
                }

                $response['data'] = array( 'resourceId' => $staff_id );  // make EventCalendar refetch events
            } else {
                // Single appointment.
                $appointment = new Entities\Appointment();
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

                    $queue_changed_status = new NotificationList();
                    $queue = new NotificationList();

                    foreach ( $customers as &$customer ) {
                        if ( $customer['payment_for'] === 'series' ) {
                            $series = Lib\Entities\Series::find( $customer['series_id'] );
                            if ( $series ) {
                                if ( $customer['payment_action'] === 'create' ) {
                                    Proxy\RecurringAppointments::createBackendPayment( $series, $customer );
                                } elseif ( $customer['payment_action'] === 'attach' ) {
                                    Proxy\RecurringAppointments::attachBackendPayment( $series, $customer );
                                }
                            }
                        }
                        // Set 'current', the employee can choose 'Create: Payment for the entire series',
                        // but series cannot be created here
                        $customer['payment_for'] = 'current';
                    }
                    // Save customer appointments.
                    $ca_status_changed = $appointment->saveCustomerAppointments( $customers );

                    foreach ( $customers as $customer ) {
                        // Reschedule all recurring appointments for $days_offset days and set it's time to $reschedule_start_time
                        $rescheduled_appointments = array( $appointment_id );
                        if ( $appointment_id && $reschedule_type != 'current' && $customer['series_id'] ) {
                            $query = Entities\Appointment::query( 'a' )
                                ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
                                ->where( 'ca.series_id', $customer['series_id'] )
                                ->whereNotIn( 'a.id', $rescheduled_appointments );
                            if ( $reschedule_type == 'next' ) {
                                $query->whereGt( 'a.start_date', $current_start_date );
                            }
                            $reschedule_appointments = $query->find();
                            /** @var Entities\Appointment $reschedule_appointment */
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
                                if ( $appointment->getStartDate() ) {
                                    if ( $service_id ) {
                                        $service = Service::find( $service_id );
                                        $response['alert_errors'] = Lib\Proxy\Shared::syncOnlineMeeting( $response['alert_errors'], $appointment, $service );
                                    }
                                    Common::syncWithCalendars( $reschedule_appointment );
                                }
                            }
                        }
                    }
                    if ( $appointment->getStartDate() ) {
                        if ( $service_id ) {
                            $service = Service::find( $service_id );
                            $response['alert_errors'] = Lib\Proxy\Shared::syncOnlineMeeting( $response['alert_errors'], $appointment, $service );
                        }
                        Common::syncWithCalendars( $appointment );
                    }

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
                    $list = $queue->getList();
                    $changed_status = $queue_changed_status->getList();
                    if ( $list || $changed_status ) {
                        $db_queue = new Lib\Entities\NotificationQueue();
                        $db_queue
                            ->setData( json_encode( array( 'all' => $list, 'changed_status' => $changed_status ) ) )
                            ->save();

                        $response['queue'] = array( 'token' => $db_queue->getToken(), 'all' => $list, 'changed_status' => $changed_status );
                    }

                    self::_deleteSentReminders( $appointment, $modified );
                } else {
                    $response['errors'] = array( 'db' => __( 'Could not save appointment in database.', 'bookly' ) );
                }
            }
        }
        update_user_meta( get_current_user_id(), 'bookly_appointment_form_send_notifications', $notification );

        return $response;
    }

    /**
     * @param $appointment_id
     * @param $start_date
     * @param $end_date
     * @param $staff_id
     * @param $service_id
     * @param $location_id
     * @param $customers
     * @return array
     */
    public static function checkTime(
        $appointment_id,
        $start_date,
        $end_date,
        $staff_id,
        $service_id,
        $location_id,
        $customers
    )
    {
        $appointment_duration = isset ( $start_date, $end_date )
            ? strtotime( $end_date ) - strtotime( $start_date )
            : 0;
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
                foreach ( $customers as $customer ) {
                    if ( $service->appointmentsLimitReached( $customer['id'], array( $start_date ) ) ) {
                        $customer_error = Customer::find( $customer['id'] );
                        $result['customers_appointments_limit'][] = sprintf( __( '%s has reached the limit of bookings for this service', 'bookly' ), $customer_error->getFullName() );
                    }
                }
                $result['customers_appointments_limit'] = array_unique( $result['customers_appointments_limit'] );
            }
        }

        return $result;
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
        $query = Entities\Appointment::query( 'a' )
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
        return Entities\Appointment::query( 'a' )
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
     * @param Entities\Appointment $appointment
     * @param array $modified
     */
    private static function _deleteSentReminders( Entities\Appointment $appointment, $modified )
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