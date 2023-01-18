<?php
namespace Bookly\Backend\Components\Dialogs\Mailing\AddRecipients;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Dialogs\Mailing\AddRecipients
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Add recipients
     */
    public static function addRecipientsToMailingList()
    {
        $mailing_list_id = self::parameter( 'mailing_list_id' );
        $exist_phone_numbers = Lib\Entities\MailingListRecipient::query()->where( 'mailing_list_id', $mailing_list_id )->fetchCol( 'phone' );
        $mode = self::parameter( 'mode' );
        if ( $mode == 'manual' ) {
            $recipients = array_unique( array_map( 'trim', explode( "\n", self::parameter( 'recipients' ) ) ) );
            $new_phone_numbers = array_diff( $recipients, $exist_phone_numbers );
            foreach ( $new_phone_numbers as $phone ) {
                $recipient = new Lib\Entities\MailingListRecipient();
                $recipient->setPhone( $phone )
                    ->setMailingListId( $mailing_list_id )
                    ->save();
            }
        } else {
            $sum_of_payments = self::parameter( 'sum_of_payments' );
            $count_of_appointments = self::parameter( 'count_of_appointments' );
            $services = self::parameter( 'services', array() );
            $providers = self::parameter( 'providers', array() );
            $last_appointment = self::parameter( 'last_appointment' );

            $query = Lib\Entities\Customer::query( 'c' );
            $select = 'c.full_name, c.phone';

            if ( empty ( $services ) && empty( $providers ) ) {
                $customers = $query
                    ->select( $select )
                    ->leftJoin( 'CustomerAppointment', 'ca', 'ca.customer_id = c.id' )
                    ->where( 'ca.customer_id', null )
                    ->fetchArray() ;
            } else {
                global $wpdb;

                $raw_where = array();
                if ( $sum_of_payments ) {
                    $select .= ', (
                    SELECT SUM(p.total) FROM ' . Lib\Entities\Payment::getTableName() . ' p
                        WHERE p.id IN (
                            SELECT DISTINCT ca.payment_id FROM ' . Lib\Entities\CustomerAppointment::getTableName() . ' ca
                                WHERE ca.customer_id = c.id
                        )
                    ) AS sum_of_payments';
                    $raw_where[] = 'sum_of_payments >= ' . (float) $sum_of_payments;
                }
                if ( $count_of_appointments ) {
                    $select .= ',(
                    SELECT COUNT(DISTINCT ca.appointment_id) FROM ' . Lib\Entities\CustomerAppointment::getTableName() . ' ca
                        WHERE ca.customer_id = c.id
                    ) AS count_of_appointments';
                    $raw_where[] = 'count_of_appointments >= ' . (float) $count_of_appointments;
                }
                if ( $last_appointment ) {
                    $select .= ',(
                    SELECT MAX(a.start_date) FROM ' . Lib\Entities\Appointment::getTableName() . ' a
                        LEFT JOIN ' . Lib\Entities\CustomerAppointment::getTableName() . ' ca ON ca.appointment_id = a.id
                            WHERE ca.customer_id = c.id
                    ) AS last_appointment';
                    $raw_where[] = 'last_appointment >= ' . (int) $last_appointment;
                }
                $query->select( $select );

                $ca = Lib\Entities\Appointment::query( 'a' )
                    ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' );

                if ( $services ) {
                    $ca_raw_where = array();
                    $position = array_search( 'custom', $services );
                    if ( $position !== false ) {
                        unset( $services[ $position ] );
                        $ca_raw_where[] = 'a.service_id IS NULL';
                    }

                    $services = array_filter( $services, 'is_numeric' );
                    if ( $services ) {
                        $ca_raw_where[] = 'a.service_id IN (' . implode( ',', $services ) . ')';
                    }

                    if ( $ca_raw_where ) {
                        $ca->whereRaw( implode( ' OR ', $ca_raw_where ), array() );
                    }
                }
                if ( $providers ) {
                    $ca->whereIn( 'a.staff_id', $providers );
                }
                $customers = $ca->groupBy( 'ca.customer_id' )->fetchCol( 'ca.customer_id' );

                $query->whereIn( 'id', $customers );

                $sql = 'SELECT result.full_name, result.phone FROM (' . $query . ') AS result';
                if ( $raw_where ) {
                    $sql .= ' WHERE ' . implode( ' AND ', $raw_where );
                }

                $customers = $wpdb->get_results( $sql, ARRAY_A );
            }

            foreach ( $customers as $data ) {
                if ( ! in_array( $data['phone'], $exist_phone_numbers ) ) {
                    $recipient = new Lib\Entities\MailingListRecipient();
                    $recipient->setPhone( $data['phone'] )
                        ->setName( $data['full_name'] )
                        ->setMailingListId( $mailing_list_id )
                        ->save();
                    $exist_phone_numbers[] = $data['phone'];
                }
            }
        }

        wp_send_json_success();
    }
}