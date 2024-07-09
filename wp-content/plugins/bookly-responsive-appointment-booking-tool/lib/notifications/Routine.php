<?php
namespace Bookly\Lib\Notifications;

use Bookly\Lib;
use Bookly\Lib\DataHolders\Booking\Collaborative;
use Bookly\Lib\DataHolders\Booking\Compound;
use Bookly\Lib\DataHolders\Booking\Simple;
use Bookly\Lib\DataHolders\Notification\Settings;
use Bookly\Lib\Entities\Appointment;
use Bookly\Lib\Entities\Customer;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Entities\SentNotification;
use Bookly\Lib\Entities\Service;
use Bookly\Lib\Entities\Staff;
use Bookly\Lib\Entities\StaffService;
use Bookly\Lib\Utils\DateTime;

abstract class Routine
{
    /** @var Lib\Slots\DatePoint */
    private static $date_point;
    /** @var Lib\Slots\DatePoint */
    private static $today;
    /** @var string Format: YYYY-MM-DD */
    private static $mysql_today;
    /** @var int hours */
    private static $processing_interval;
    /** @var int */
    private static $hours;

    /**
     * Notification
     *
     * @param Notification $notification
     */
    public static function processNotification( Notification $notification )
    {
        $settings = new Settings( $notification );

        if ( ! $settings->getInstant() ) {
            $ca_list = array();
            $customers = array();
            $statuses = Lib\Proxy\CustomStatuses::prepareBusyStatuses( array(
                CustomerAppointment::STATUS_PENDING,
                CustomerAppointment::STATUS_APPROVED,
            ) );

            switch ( $notification->getType() ) {
                // Appointment start date add time.
                case Notification::TYPE_APPOINTMENT_REMINDER:
                    $ca_list = self::getCustomerAppointments( $notification, $settings );
                    if ( $settings->getStatus() === 'any' ) {
                        $statuses = array();
                    }
                    break;

                // Last appointment.
                case Notification::TYPE_LAST_CUSTOMER_APPOINTMENT:
                    $ca_list = self::getLastCustomerAppointments( $notification, $settings );
                    break;

                // Client birthday.
                case Notification::TYPE_CUSTOMER_BIRTHDAY:
                    if ( $notification->getToCustomer() || $notification->getToCustom() ) {
                        $customers = self::getCustomersWithBirthday( $notification, $settings );
                    }
                    break;

                // Staff Agenda.
                case Notification::TYPE_STAFF_DAY_AGENDA:
                    self::sendStaffAgenda( $notification, $settings );
                    break;
            }

            if ( $ca_list ) {
                $compounds = array();
                $collaboratives = array();
                foreach ( $ca_list as $ca ) {
                    if ( $token = $ca->getCompoundToken() ) {
                        if ( ! isset ( $compounds[ $token ] ) ) {
                            $compounds[ $token ] = Compound::createByToken( $token, $statuses );
                        }
                    } elseif ( $token = $ca->getCollaborativeToken() ) {
                        if ( ! isset ( $collaboratives[ $token ] ) ) {
                            $collaboratives[ $token ] = Collaborative::createByToken( $token, $statuses );
                        }
                    } else {
                        $simple = Simple::create( $ca );
                        if ( Booking\Reminder::send( $notification, $simple ) ) {
                            self::wasSent( $notification, $ca->getId() );
                        }
                    }
                }
                foreach ( $compounds as $compound ) {
                    if ( Booking\Reminder::send( $notification, $compound ) ) {
                        foreach ( $compound->getItems() as $item ) {
                            self::wasSent( $notification, $item->getCA()->getId() );
                        }
                    }
                }
                foreach ( $collaboratives as $collaborative ) {
                    if ( Booking\Reminder::send( $notification, $collaborative ) ) {
                        foreach ( $collaborative->getItems() as $item ) {
                            self::wasSent( $notification, $item->getCA()->getId() );
                        }
                    }
                }
            } else {
                foreach ( $customers as $customer ) {
                    $codes = new Assets\ClientBirthday\Codes( $customer );
                    if ( $notification->getToCustomer() && Base\Reminder::sendToClient( $customer, $notification, $codes ) ) {
                        self::wasSent( $notification, $customer->getId() );
                    }
                    if ( $notification->getToCustom() && Base\Reminder::sendToCustom( $notification, $codes ) ) {
                        self::wasSent( $notification, $customer->getId() );
                    }
                }
            }
        }
    }

    /**
     * Get customer appointments for notification
     *
     * @param Notification $notification
     * @param Settings     $settings
     * @return CustomerAppointment[]
     */
    private static function getCustomerAppointments( Notification $notification, Settings $settings )
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        $ca_list = array();

        if ( $settings->getAtHour() !== null ) {
            // Send at time after start_date date (some day at 08:00)
            if ( self::isTimeToSend( $settings->getAtHour() ) ) {
                $query = sprintf(
                    'SELECT `ca`.* FROM `%s` `ca` LEFT JOIN `%s` `a` ON `a`.`id` = `ca`.`appointment_id`
                      WHERE DATE(`a`.`start_date`) = DATE("%s")',
                    CustomerAppointment::getTableName(),
                    Appointment::getTableName(),
                    self::$today->modify( - $settings->getOffsetHours() * HOUR_IN_SECONDS )->format( 'Y-m-d' )
                );
            } else {
                return $ca_list;
            }
        } else {
            $query = sprintf(
                'SELECT `ca`.* FROM `%s` `ca` LEFT JOIN `%s` `a` ON `a`.`id` = `ca`.`appointment_id`
                  WHERE `a`.`start_date` BETWEEN "%s" AND "%s"',
                CustomerAppointment::getTableName(),
                Appointment::getTableName(),
                self::$date_point->modify( - ( $settings->getOffsetHours() + self::$processing_interval ) * HOUR_IN_SECONDS )->format( 'Y-m-d H:i:s' ),
                self::$date_point->modify( - $settings->getOffsetHours() * HOUR_IN_SECONDS )->format( 'Y-m-d H:i:s' )
            );
        }

        // Select appointments for which reminders need to be sent today.
        $query .= sprintf( ' AND NOT EXISTS ( %s )',
            self::getQueryIfNotificationWasSent( $notification )
        );
        if ( $settings->getStatus() != 'any' ) {
            $query .= sprintf( ' AND `ca`.`status` = "%s"', $settings->getStatus() );
        }

        $query .= self::getAndWhereServiceType( $settings );

        foreach ( (array) $wpdb->get_results( $query, ARRAY_A ) as $fields ) {
            $ca_list[] = new CustomerAppointment( $fields );
        }

        return $ca_list;
    }

    /**
     * Get last customer appointments for notification
     *
     * @param Notification $notification
     * @param Settings $settings
     * @return CustomerAppointment[]
     */
    private static function getLastCustomerAppointments( Notification $notification, Settings $settings )
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        $ca_list = array();

        $replace = array(
            '{bookly_appointments}' => Appointment::getTableName(),
            '{bookly_customer_appointments}' => CustomerAppointment::getTableName(),
            '{bookly_customers}' => Customer::getTableName(),
            '{bookly_sent_notifications}' => SentNotification::getTableName(),
        );

        if ( $settings->getAtHour() !== null ) {
            // Send at time after created date (some day at 08:00)
            if ( self::isTimeToSend( $settings->getAtHour() ) ) {
                $replace['{sent_time_interval}'] = sprintf( 'DATE("%s") = DATE(`start_date`)',
                    self::$today->modify( - $settings->getOffsetHours() * HOUR_IN_SECONDS )->format( 'Y-m-d' )
                );
            } else {
                return $ca_list;
            }
        } else {
            $replace['{sent_time_interval}'] = sprintf( '`start_date` BETWEEN "%s" AND "%s"',
                self::$date_point->modify( - ( $settings->getOffsetHours() + self::$processing_interval ) * HOUR_IN_SECONDS )->format( 'Y-m-d H:i:s' ),
                self::$date_point->modify( - $settings->getOffsetHours() * HOUR_IN_SECONDS )->format( 'Y-m-d H:i:s' )
            );
        }

        if ( self::$hours >= $settings->getSendAtHour() ) {
            $query = sprintf(
                'SELECT `ca`.*, `a`.`start_date` FROM `{bookly_customer_appointments}` `ca`
                    LEFT JOIN `{bookly_appointments}` `a` ON `a`.`id` = `ca`.`appointment_id`
                WHERE `ca`.`id` IN(
                    SELECT (
                        SELECT `ca2`.`id` FROM `{bookly_appointments}` `a`
                            INNER JOIN `{bookly_customer_appointments}` `ca2` ON `ca2`.`appointment_id` = `a`.`id` 
                        WHERE `ca2`.`customer_id` = `c`.`id`
                            AND {ca2_status_equal}
                            AND `a`.`start_date` = (
                                SELECT MAX(`a2`.`start_date`) FROM `{bookly_appointments}` `a2`
                                    INNER JOIN `{bookly_customer_appointments}` `ca3` ON `ca3`.`appointment_id` = `a2`.`id`
                                WHERE `ca3`.`customer_id` = `c`.`id` AND {ca3_status_equal}
                            ) LIMIT 1
                        ) `last_ca_id` FROM `{bookly_customers}` `c`
                    )
                    AND {sent_time_interval}
                    AND NOT EXISTS ( %s )',
                self::getQueryIfNotificationWasSent( $notification )
            );

            $busy = Lib\Proxy\CustomStatuses::prepareBusyStatuses( array(
                CustomerAppointment::STATUS_PENDING,
                CustomerAppointment::STATUS_APPROVED,
                CustomerAppointment::STATUS_DONE,
            ) );

            array_walk( $busy, array( $wpdb, 'escape_by_ref' ) );

            $statuses = implode( '", "', $busy );
            $replace['{ca2_status_equal}'] = '`ca2`.`status` IN ("' . $statuses . '")';
            $replace['{ca3_status_equal}'] = '`ca3`.`status` IN ("' . $statuses . '")';

            $query = strtr( $query, $replace );

            foreach ( (array) $wpdb->get_results( $query, ARRAY_A ) as $fields ) {
                $ca_list[] = new CustomerAppointment( $fields );
            }
        }

        return $ca_list;
    }

    /**
     * Customers for birthday congratulations
     *
     * @param Notification $notification
     * @param Settings $settings
     * @return Customer[]
     */
    private static function getCustomersWithBirthday( Notification $notification, Settings $settings )
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        $customers = array();

        if ( self::isTimeToSend( $settings->getAtHour() ) ) {
            $rows = (array) $wpdb->get_results( sprintf(
                'SELECT `c`.* FROM `%s` `c`
                WHERE `c`.`birthday` IS NOT NULL
                    AND DATE_FORMAT(`c`.`birthday`, "%%m-%%d") = "%s"
                    AND NOT EXISTS (
                        SELECT * FROM `%s` `sn`
                        WHERE DATE(`sn`.`created_at`) = DATE("%s")
                            AND `sn`.`notification_id` = %d
                            AND `sn`.`ref_id` = `c`.`id`
                    )',
                Customer::getTableName(),
                self::$today->modify( - $settings->getOffsetHours() * HOUR_IN_SECONDS )->format( 'm-d' ),
                SentNotification::getTableName(),
                self::$mysql_today,
                $notification->getId()
            ), ARRAY_A );

            foreach ( $rows as $fields ) {
                $customers[] = new Customer( $fields );
            }
        }

        return $customers;
    }

    /**
     * Send Staff Agenda
     *
     * @param Notification $notification
     * @param Settings $settings
     */
    private static function sendStaffAgenda( Notification $notification, Settings $settings )
    {
        if ( $notification->getToStaff() || $notification->getToAdmin() || $notification->getToCustom() ) {
            if ( self::isTimeToSend( $settings->getAtHour() ) ) {
                global $wpdb;

                $statuses = Lib\Proxy\CustomStatuses::prepareBusyStatuses( array(
                    CustomerAppointment::STATUS_PENDING,
                    CustomerAppointment::STATUS_APPROVED,
                ) );

                /** @var \stdClass[] $rows */
                $rows = $wpdb->get_results( sprintf(
                    'SELECT
                    `a`.*,
                    `ca`.`locale`,
                    `ca`.`extras`,
                    `ca`.`id`       AS `ca_id`,
                    `c`.`full_name` AS `customer_name`,
                    COALESCE(`s`.`title`, `a`.`custom_service_name`) AS `service_title`,
                    `s`.`info`      AS `service_info`,
                    `st`.`email`    AS `staff_email`,
                    `st`.`phone`    AS `staff_phone`,
                    `st`.`time_zone` AS `staff_timezone`
                FROM `%s` `ca`
                LEFT JOIN `%s` `a`  ON `a`.`id` = `ca`.`appointment_id`
                LEFT JOIN `%s` `c`  ON `c`.`id` = `ca`.`customer_id`
                LEFT JOIN `%s` `s`  ON `s`.`id`  = `a`.`service_id`
                LEFT JOIN `%s` `st` ON `st`.`id` = `a`.`staff_id`
                LEFT JOIN `%s` `ss` ON `ss`.`staff_id` = `a`.`staff_id` AND `ss`.`service_id` = `a`.`service_id` AND `ss`.`location_id` <=> `a`.`location_id`
                WHERE `st`.`visibility` != "archive"
                    AND `ca`.`status` IN("%s")
                    AND DATE("%s") = DATE(`a`.`start_date`)
                    AND NOT EXISTS (
                        SELECT * FROM `%s` `sn` 
                         WHERE DATE(`sn`.`created_at`) = DATE("%s")
                           AND `sn`.`notification_id` = %d
                           AND `sn`.`ref_id` = `a`.`staff_id`
                    )
                ORDER BY `a`.`start_date`',
                    CustomerAppointment::getTableName(),
                    Appointment::getTableName(),
                    Customer::getTableName(),
                    Service::getTableName(),
                    Staff::getTableName(),
                    StaffService::getTableName(),
                    implode( '","', $statuses ),
                    self::$today->modify( abs( $settings->getOffsetHours() ) * HOUR_IN_SECONDS )->format( 'Y-m-d' ),
                    SentNotification::getTableName(),
                    self::$mysql_today,
                    $notification->getId()
                ) );

                if ( $rows ) {
                    $appointments = array();
                    foreach ( $rows as $row ) {
                        $appointments[ $row->staff_id ][] = $row;
                    }

                    $columns = array(
                        '{10_time}' => __( 'Time', 'bookly' ),
                        '{30_service}' => __( 'Service', 'bookly' ),
                        '{40_customer}' => __( 'Customer', 'bookly' ),
                    );
                    if ( Lib\Config::locationsActive() ) {
                        $columns['{20_location}'] = __( 'Location', 'bookly' );
                    }
                    $columns_extended = $columns;
                    if ( Lib\Config::customFieldsActive() ) {
                        $columns_extended['{50_custom_fields}']  = __( 'Custom Fields', 'bookly' );
                        $columns_extended['{60_internal_notes}'] = __( 'Internal Notes', 'bookly' );
                    }
                    ksort( $columns );
                    ksort( $columns_extended );
                    $is_html = ( get_option( 'bookly_email_send_as' ) == 'html' && $notification->getGateway() != 'sms' );
                    if ( $is_html ) {
                        $table = '<table cellspacing="1" border="1" cellpadding="5"><thead><tr><td>'
                            . implode( '</td><td>', $columns )
                            . '</td></tr></thead><tbody>%s</tbody></table>';
                        $table_extended = '<table cellspacing="1" border="1" cellpadding="5"><thead><tr><td>'
                            . implode( '</td><td>', $columns_extended )
                            . '</td></tr></thead><tbody>%s</tbody></table>';
                        $tr = '<tr><td>' . implode( '</td><td>', array_keys( $columns ) ) . '</td></tr>';
                        $tr_extended = '<tr><td>' . implode( '</td><td>', array_keys( $columns_extended ) ) . '</td></tr>';
                    } else {
                        $table = '%s';
                        $table_extended = '%s';
                        $tr = implode( ', ', array_keys( $columns ) ) . PHP_EOL;
                        $tr_extended = implode( ', ', array_keys( $columns_extended ) ) . PHP_EOL;
                    }

                    foreach ( $appointments as $staff_id => $collection ) {
                        $sent = false;
                        $staff_email = null;
                        $staff_phone = null;
                        $agenda = '';
                        $agenda_extended = '';
                        foreach ( $collection as $appointment ) {
                            if ( ! Lib\Proxy\Pro::graceExpired() ) {
                                if ( $appointment->staff_timezone ) {
                                    // Convert date and time into staff time zone
                                    $staff_tz = $appointment->staff_timezone;
                                    if ( preg_match( '/^UTC[+-]/', $staff_tz ) ) {
                                        $offset = preg_replace( '/UTC\+?/', '', $staff_tz );
                                        $staff_tz = DateTime::formatOffset( $offset * HOUR_IN_SECONDS );
                                    }
                                    $appointment->start_date = DateTime::convertTimeZone( $appointment->start_date, Lib\Config::getWPTimeZone(), $staff_tz );
                                    $appointment->end_date = DateTime::convertTimeZone( $appointment->end_date, Lib\Config::getWPTimeZone(), $staff_tz );
                                }
                                $tr_data = array(
                                    '{10_time}' => DateTime::formatTime( $appointment->start_date ) . '-' . DateTime::formatTime( $appointment->end_date ),
                                    '{40_customer}' => $appointment->customer_name,
                                );

                                $location = Lib\Proxy\Locations::findById( $appointment->location_id );
                                $tr_data['{20_location}'] = $location ? $location->getName() : '';

                                // Extras
                                $extras = '';
                                $_extras = Lib\Proxy\ServiceExtras::getInfo( json_decode( $appointment->extras, true ), false ) ?: array();
                                if ( ! empty ( $_extras ) ) {
                                    foreach ( $_extras as $extra ) {
                                        if ( $is_html ) {
                                            $extras .= sprintf( '<li>%s</li>', $extra['title'] );
                                        } else {
                                            $extras .= sprintf( ', %s', str_replace( '&nbsp;&times;&nbsp;', ' x ', $extra['title'] ) );
                                        }
                                    }
                                    if ( $is_html ) {
                                        $extras = '<ul>' . $extras . '</ul>';
                                    }
                                }

                                $tr_data['{30_service}'] = $appointment->service_title . $extras;
                                $tr_data_extended = $tr_data;
                                if ( Lib\Config::customFieldsActive() ) {
                                    $ca = new CustomerAppointment();
                                    $ca->load( $appointment->ca_id );
                                    $custom_filed_str = '';
                                    foreach ( Lib\Proxy\CustomFields::getForCustomerAppointment( $ca ) ?: array() as $custom_field ) {
                                        if ( $is_html ) {
                                            $custom_filed_str .= sprintf( '%s: %s<br/>', $custom_field['label'], $custom_field['value'] );
                                        } else {
                                            $custom_filed_str .= sprintf( '%s: %s ', $custom_field['label'], $custom_field['value'] );
                                        }
                                    }
                                    $tr_data_extended['{50_custom_fields}']  = $custom_filed_str;
                                    $tr_data_extended['{60_internal_notes}'] = $appointment->internal_note;
                                }
                                $agenda .= strtr( $tr, $tr_data );
                                $agenda_extended .= strtr( $tr_extended, $tr_data_extended );
                            } else {
                                $agenda = __( 'To view the details of these appointments, please contact your website administrator in order to verify Bookly Pro license.', 'bookly' );
                                $agenda_extended = __( 'To view the details of these appointments, please contact your website administrator in order to verify Bookly Pro license.', 'bookly' );
                            }
                            $staff_email = $appointment->staff_email;
                            $staff_phone = $appointment->staff_phone;
                        }

                        if ( ( $notification->getGateway() == 'email' && $staff_email != '' )
                            || ( ( $notification->getGateway() != 'email' ) && $staff_phone != '' )
                        ) {
                            $codes = new Assets\StaffAgenda\Codes();
                            $codes->agenda_date = DateTime::formatDate( date( 'Y-m-d', current_time( 'timestamp' ) + abs( $settings->getOffsetHours() * HOUR_IN_SECONDS ) ) );
                            $codes->next_day_agenda = sprintf( $table, $agenda );
                            $codes->next_day_agenda_extended = sprintf( $table_extended, $agenda_extended );
                            $codes->staff = Staff::find( $appointment->staff_id ) ?: new Staff();
                            $sent = Base\Reminder::sendToStaff( $codes->staff, $notification, $codes );
                            if ( Base\Reminder::sendToAdmins( $notification, $codes ) ) {
                                $sent = true;
                            }
                            if ( Base\Reminder::sendToCustom( $notification, $codes ) ) {
                                $sent = true;
                            }
                        }

                        if ( $sent ) {
                            self::wasSent( $notification, $staff_id );
                        }
                    }
                }
            }
        }
    }

    /**
     * @param Notification $notification
     * @return string
     */
    private static function getQueryIfNotificationWasSent( Notification $notification )
    {
        return sprintf( '
                SELECT * FROM `%s` `sn` 
                WHERE `sn`.`ref_id` = `ca`.`id`
                  AND `sn`.`notification_id` = %d
            ',
            SentNotification::getTableName(),
            $notification->getId()
        );
    }

    /**
     * Get sql WHERE statement for service type.
     *
     * @param Settings $settings
     * @return string
     */
    private static function getAndWhereServiceType( Settings $settings )
    {
        $query = array();
        /* [
         *   'simple'   => [ 1 => [1] ],
         *   'compound' => [ 4 => [2,3]],
         *   ...
         * ] or 'any'
         */
        $services = $settings->getServices();
        if ( $services !== 'any' ) {
            foreach ( $services as $service_type => $service ) {
                $ids = array();
                foreach ( $service as $service_id => $simple_service_ids ) {
                    $ids[] = $service_id;
                }
                if ( $ids ) {
                    $ids = array_unique( $ids );
                    switch ( $service_type ) {
                        case Service::TYPE_SIMPLE:
                        case Service::TYPE_PACKAGE:
                            $query[] = sprintf( '`a`.`service_id` IN (%s)', implode( ', ', $ids ) );
                            break;
                        case Service::TYPE_COMPOUND:
                            $query[] = sprintf( '`ca`.`compound_service_id` IN (%s)', implode( ', ', $ids ) );
                            break;
                        case Service::TYPE_COLLABORATIVE:
                            $query[] = sprintf( '`ca`.`collaborative_service_id` IN (%s)', implode( ', ', $ids ) );
                            break;
                    }
                }
            }
        } else {
            return '';
        }

        return $query ? ' AND ( ' . implode( ' OR ', $query ) . ') ' : ' AND FALSE ';
    }

    /**
     * @param int $at_hour
     * @return bool
     */
    private static function isTimeToSend( $at_hour )
    {
        $range = Lib\Slots\Range::fromDates(
            sprintf( '%02d:00:00', $at_hour ),
            sprintf( '%02d:00:00 + %d hours', $at_hour, self::$processing_interval )
        );

        return $range->contains( self::$date_point );
    }

    /**
     * Mark notification as sent.
     *
     * @param Notification $notification
     * @param int          $ref_id
     */
    private static function wasSent( Notification $notification, $ref_id )
    {
        $sent_notification = new SentNotification();
        $sent_notification
            ->setRefId( $ref_id )
            ->setNotificationId( $notification->getId() )
            ->setCreatedAt( current_time( 'mysql' ) )
            ->save();
    }

    /**
     * Send notifications.
     */
    public static function sendNotifications()
    {
        // Disable caching.
        Lib\Utils\Common::noCache( true );

        $original_timezone = date_default_timezone_get();

        // @codingStandardsIgnoreStart
        date_default_timezone_set( 'UTC' );

        self::$date_point = Lib\Slots\DatePoint::now();
        self::$today = Lib\Slots\DatePoint::fromStr( 'today' );
        self::$mysql_today = self::$today->format( 'Y-m-d' );
        self::$hours = self::$date_point->format( 'H' );
        self::$processing_interval = (int) get_option( 'bookly_ntf_processing_interval' );

        // Custom notifications.
        $custom_notifications = Notification::query()
            ->where( 'active', 1 )
            ->find();

        $notifications = Notification::getAssociated();

        /** @var Notification $notification */
        foreach ( $custom_notifications as $notification ) {
            if ( in_array( $notification->getType(), $notifications[ $notification->getGateway() ] ) ) {
                self::processNotification( $notification );
            }
        }

        date_default_timezone_set( $original_timezone );
        // @codingStandardsIgnoreEnd
    }
}