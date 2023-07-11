<?php
namespace Bookly\Frontend\Modules\Zapier;

use Bookly\Lib;
use Bookly\Lib\Entities\Customer;

/**
 * Class Ajax
 * @package Bookly\Frontend\Modules\Zapier
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'anonymous' );
    }

    /**
     * Get resources
     */
    public static function cloudZapier()
    {
        switch ( self::parameter( 'resource' ) ) {
            case 'customers':
                wp_send_json( self::getCustomers() );
                break;
            case 'appointments':
                wp_send_json( self::getAppointments() );
                break;
            default:
                wp_send_json( array() );
        }
    }

    /**
     * Get customers
     *
     * @return array
     */
    private static function getCustomers()
    {
        $query = Customer::query()
            ->select( 'id,
             wp_user_id,
             facebook_id,
             group_id,
             full_name,
             first_name,
             last_name,
             phone,
             email,
             birthday,
             country,
             state,
             postcode,
             city,
             street,
             street_number,
             additional_address,
             notes,
             info_fields,
             created_at' )
            ->whereGte( 'created_at', date_create( current_time( 'mysql' ) )->modify( '-1 hours' )->format( 'Y-m-d H:i:s' ) )
            ->sortBy( 'created_at' )
            ->order( 'DESC' );

        $fields_name = array();
        foreach ( Lib\Proxy\CustomerInformation::getFieldsWhichMayHaveData() ?: array() as $field ) {
            $fields_name[ $field->id ] = $field->label;
        }
        return array_map( function ( $customer ) use ( $fields_name ) {
            $info_fields = array();

            $customer['created_at'] = Lib\Slots\DatePoint::fromStr( $customer['created_at'] )->format( 'Y-m-d\TH:i:sO' );

            foreach ( json_decode( $customer['info_fields'], true ) as $field ) {
                $info_fields[] = $fields_name[ $field['id'] ] . ': ' .
                    ( is_array( $field['value'] )
                        ? implode( ', ', $field['value'] )
                        : $field['value'] );
            }
            $customer['info_fields'] = implode( '; ', $info_fields );

            return $customer;
        }, $query->fetchArray() );

    }

    /**
     * Get appointments
     *
     * @return array
     */
    private static function getAppointments()
    {
        $records = 'new';
        if ( self::parameter( 'with-updated' ) ) {
            $records = 'new_or_updated';
        } elseif ( self::parameter( 'only-updated' ) ) {
            $records = 'updated';
        }

        $query = self::getAppointmentsQuery( $records );

        $all_extras = array();
        foreach ( Lib\Proxy\ServiceExtras::findAll() as $item ) {
            $all_extras[ $item->getId() ] = $item->getTitle();
        }

        $appointments = $query->fetchArray();
        foreach ( $appointments as &$appointment ) {
            $custom_fields = array();
            $extras = array();
            $appointment['custom_fields_'] = null;
            $appointment['extras_'] = null;
            $app = new Lib\Entities\Appointment();
            $app
                ->setId( $appointment['id'] )
                ->setOnlineMeetingProvider( $appointment['online_meeting_provider'] )
                ->setOnlineMeetingId( $appointment['online_meeting_id'] )
                ->setOnlineMeetingData( $appointment['online_meeting_data'] )
            ;

            // Extras
            $add_nop = $appointment['extras_multiply_nop'] && $appointment['number_of_persons'] > 1;
            foreach ( (array) json_decode( $appointment['extras'], true ) as $extras_id => $quantity ) {
                $title = ( $quantity > 1 ) ? $quantity . ' × ' . $all_extras[ $extras_id ] : $all_extras[ $extras_id ];
                if ( $add_nop ) {
                    $title = $appointment['number_of_persons'] . ' × ' . $title;
                }
                $extras[] = $title;
                $appointment['extras_'][ $extras_id ] = array( 'title' => $all_extras[ $extras_id ], 'quantity' => $quantity );
            }

            // Custom fields
            foreach ( Lib\Proxy\CustomFields::getForCustomerAppointment( new Lib\Entities\CustomerAppointment( array( 'id' => $appointment['ca_id'], 'custom_fields' => $appointment['custom_fields'] ) ) ) as $cf ) {
                $custom_fields[] = $cf['label'] . ': ' . $cf['value'];
                $appointment['custom_fields_'][ $cf['id'] ] = array( 'label' => $cf['label'], 'value' => $cf['value'] );
            }

            if ( $appointment['payment_id'] === null ) {
                $total = 0;
                if ( $appointment['order_id'] === null ) {
                    $total = Lib\DataHolders\Booking\Simple::create( Lib\Entities\CustomerAppointment::find( $appointment['ca_id'] ) )->getTotalPrice();
                } else {
                    foreach ( Lib\DataHolders\Booking\Order::createFromOrderId( $appointment['order_id'] )->getItems() as $item ) {
                        $total += $item->getTotalPrice();
                    }
                }
                $appointment['total'] = $total;
            }

            // Money
            foreach ( array( 'service_price', 'total', 'paid' ) as $key ) {
                $appointment[ $key ] = Lib\Utils\Price::format( $appointment[ $key ] );
            }

            // Date time
            $appointment['start_date']  = Lib\Slots\DatePoint::fromStr( $appointment['start_date'] )->format( 'Y-m-d\TH:i:sO' );
            $appointment['end_date']    = Lib\Slots\DatePoint::fromStr( $appointment['end_date'] )->format( 'Y-m-d\TH:i:sO' );
            $appointment['updated_at']  = Lib\Slots\DatePoint::fromStr( $appointment['updated_at'] )->format( 'Y-m-d\TH:i:sO' );

            $customer = new Customer();
            $customer->setFullName( $appointment['client_name'] );
            // Online meeting
            $appointment['online_meeting_url']       = Lib\Proxy\Shared::buildOnlineMeetingUrl( '', $app, $customer );
            $appointment['online_meeting_password']  = Lib\Proxy\Shared::buildOnlineMeetingPassword( '', $app );
            $appointment['online_meeting_start_url'] = Lib\Proxy\Shared::buildOnlineMeetingStartUrl( '', $app );
            $appointment['online_meeting_join_url']  = Lib\Proxy\Shared::buildOnlineMeetingJoinUrl( '', $app, $customer );

            $appointment['client_time_zone'] = Lib\Proxy\Pro::getCustomerTimezone( $appointment['time_zone'], $appointment['time_zone_offset'] );
            $appointment['custom_fields']    = implode( '; ', $custom_fields );
            $appointment['extras']           = implode( '; ', $extras );

            unset( $appointment['extras_multiply_nop'], $appointment['extras_multiply_nop'], $appointment['time_zone'], $appointment['time_zone_offset'], $appointment['online_meeting_provider'], $appointment['online_meeting_id'], $appointment['online_meeting_data'], $appointment['ca_id'], $appointment['order_id'] );
        }

        return $appointments;
    }

    /**
     * Get query for appointments
     *
     * @param string $records
     * @return Lib\Query
     */
    private static function getAppointmentsQuery( $records )
    {
        $date  = date_create( current_time( 'mysql' ) )->modify( '-1 hours' )->format( 'Y-m-d H:i:s' );
        $query = Lib\Entities\Appointment::query( 'a' );
        switch ( $records ) {
            case 'new':
                $query
                    ->select( 'CONCAT(ca.id,\'-\',UNIX_TIMESTAMP(ca.created_at)) AS id' )
                    ->whereGte( 'ca.created_at', $date );
                break;
            case 'new_or_updated':
                $query
                    ->select( 'CONCAT(ca.id,\'-\',UNIX_TIMESTAMP(GREATEST(ca.updated_at,a.updated_at,COALESCE(p.updated_at,0)))) AS id' )
                    ->whereRaw(
                        'ca.updated_at >= \'%s\' 
                        OR a.updated_at >= \'%s\'
                        OR p.updated_at >= \'%s\'',
                        array( $date, $date, $date )
                    );
                break;
            case 'updated':
                $query
                    ->select( 'CONCAT(ca.id,\'-\',UNIX_TIMESTAMP(GREATEST(ca.updated_at,a.updated_at,COALESCE(p.updated_at,0)))) AS id' )
                    ->whereRaw(
                        '(ca.updated_at > ca.created_at AND ca.updated_at >= \'%s\') 
                        OR (a.updated_at > a.created_at AND a.updated_at >= \'%s\')
                        OR (p.updated_at > p.created_at AND p.updated_at >= \'%s\')',
                        array( $date, $date, $date )
                    );
        }
        $query->addSelect( 'a.internal_note, a.start_date, DATE_ADD(a.end_date, INTERVAL a.extras_duration SECOND) AS end_date,
            COALESCE(s.title,a.custom_service_name) AS service_name, s.info AS service_info,
            COALESCE(ss.price,a.custom_service_price) AS service_price,
            st.full_name AS staff_name, st.email AS staff_email, st.info AS staff_info, st.phone AS staff_phone,
            ca.id AS ca_id,
            ca.order_id,
            ca.number_of_persons,
            ca.units,
            ca.custom_fields,
            ca.status AS appointment_status,
            ca.extras,
            ca.extras_multiply_nop,
            ca.time_zone,
            ca.time_zone_offset,
            ca.notes AS client_notes,
            a.online_meeting_provider,
            a.online_meeting_id,
            a.online_meeting_data,
            ct.name AS category_name,
            c.full_name AS client_name, c.first_name AS client_first_name, c.last_name AS client_last_name, c.phone AS client_phone, c.email AS client_email,
            p.total, p.type AS payment_gateway, p.status AS payment_status, p.paid, p.id AS payment_id,
            GREATEST(ca.updated_at,a.updated_at,COALESCE(p.updated_at,0)) AS updated_at' )
        ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
        ->leftJoin( 'Customer', 'c', 'c.id = ca.customer_id' )
        ->leftJoin( 'Payment', 'p', 'p.id = ca.payment_id' )
        ->leftJoin( 'Service', 's', 's.id = a.service_id' )
        ->leftJoin( 'Category', 'ct', 'ct.id = s.category_id' )
        ->leftJoin( 'Staff', 'st', 'st.id = a.staff_id' )
        ->leftJoin( 'StaffService', 'ss', 'ss.staff_id = a.staff_id AND ss.service_id = a.service_id' )
        ->groupBy( 'a.id' )
        ->sortBy( 'updated_at' )
        ->order( 'DESC' );

        if ( Lib\Config::proActive() ) {
            $query->addSelect( 'c.country AS client_country, c.state AS client_state, c.postcode AS client_postcode, c.city AS client_city, c.street AS client_street, c.street_number AS client_street_number, c.additional_address AS client_additional_address, gc.code AS gift_card_code' )
                ->leftJoin( 'GiftCard', 'gc', 'gc.id = p.gift_card_id', '\BooklyPro\Lib\Entities' );
        } else {
            $query->addSelect( 'null AS client_country, null AS client_state, null AS client_postcode, null AS client_city, null AS client_street, null AS client_street_number, null AS client_additional_address, null AS gift_card_code' );
        }

        if ( Lib\Config::locationsActive() ) {
            $query
                ->addSelect( 'l.name AS location' )
                ->leftJoin( 'Location', 'l', 'l.id = a.location_id', '\BooklyLocations\Lib\Entities' );
        } else {
            $query->addSelect( 'null AS location' );
        }

        if ( Lib\Config::couponsActive() ) {
            $query
                ->addSelect( 'coupon.code AS coupon_code' )
                ->leftJoin( 'Coupon', 'coupon', 'coupon.id = p.coupon_id', '\BooklyCoupons\Lib\Entities' );
        } else {
            $query->addSelect( 'null AS coupon_code' );
        }

        return $query;
    }

    /**
     * @inheritDoc
     */
    protected static function hasAccess( $action )
    {
        return Lib\Cloud\Zapier::checkApiKey( self::parameter( 'api_key' ) );
    }

    /**
     * Override parent method to exclude actions from CSRF token verification.
     *
     * @param string $action
     * @return bool
     */
    protected static function csrfTokenValid( $action = null )
    {
        return true;
    }
}