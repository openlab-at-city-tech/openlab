<?php
namespace Bookly\Backend\Modules\Customers;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Modules\Customers
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array(
            '_default'         => 'supervisor',
            'getCustomersList' => array( 'staff', 'supervisor' ),
        );
    }

    /**
     * Get list of customers.
     */
    public static function getCustomers()
    {
        global $wpdb;

        $columns = self::parameter( 'columns' );
        $order   = self::parameter( 'order', array() );
        $filter  = self::parameter( 'filter' );

        $query = Lib\Entities\Customer::query( 'c' );

        $total = $query->count();

        $select = 'SQL_CALC_FOUND_ROWS c.*,
                (
                    SELECT MAX(a.start_date) FROM ' . Lib\Entities\Appointment::getTableName() . ' a
                        LEFT JOIN ' . Lib\Entities\CustomerAppointment::getTableName() . ' ca ON ca.appointment_id = a.id
                            WHERE ca.customer_id = c.id
                ) AS last_appointment,
                (
                    SELECT COUNT(DISTINCT ca.appointment_id) FROM ' . Lib\Entities\CustomerAppointment::getTableName() . ' ca
                        WHERE ca.customer_id = c.id
                ) AS total_appointments,
                (
                    SELECT SUM(p.total) FROM ' . Lib\Entities\Payment::getTableName() . ' p
                        WHERE p.id IN (
                            SELECT DISTINCT ca.payment_id FROM ' . Lib\Entities\CustomerAppointment::getTableName() . ' ca
                                WHERE ca.customer_id = c.id
                        )
                ) AS payments,
                wpu.display_name AS wp_user';

        $select = Proxy\CustomerGroups::prepareCustomerSelect( $select );

        $query
            ->select( $select )
            ->tableJoin( $wpdb->users, 'wpu', 'wpu.ID = c.wp_user_id' )
            ->groupBy( 'c.id' );

        $query = Proxy\CustomerGroups::prepareCustomerQuery( $query );

        if ( $filter != '' ) {
            $search_value   = Lib\Query::escape( $filter );
            $search_columns = array( 'c.info_fields LIKE "%%%s%"' );
            foreach ( $columns as $column ) {
                if ( in_array( $column['data'], array( 'first_name', 'last_name', 'full_name', 'phone', 'email', 'id' ) ) ) {
                    $search_columns[] = 'c.' . $column['data'] . ' LIKE "%%%s%"';
                }
            }
            $query->whereRaw( implode( ' OR ', $search_columns ), array_fill( 0, count( $search_columns ), "%{$search_value}%" ) );
        }

        foreach ( $order as $sort_by ) {
            $query
                ->sortBy( str_replace( '.', '_', $columns[ $sort_by['column'] ]['data'] ) )
                ->order( $sort_by['dir'] == 'desc' ? Lib\Query::ORDER_DESCENDING : Lib\Query::ORDER_ASCENDING );
        }

        $query->limit( self::parameter( 'length' ) )->offset( self::parameter( 'start' ) );

        $data = array();
        $rows = $query->fetchArray();
        $records_filtered = ( int ) $wpdb->get_var( 'SELECT FOUND_ROWS()' );
        foreach ( $rows as $row ) {

            $address = Lib\Proxy\Pro::getFullAddressByCustomerData( $row );

            if ( $row['birthday'] !== null ) {
                $birthday = date_create( $row['birthday'] );
                if ( $birthday->format( 'Y' ) === '0000' ) {
                    $date_format = str_replace( array( 'y', 'Y' ), '', get_option( 'date_format' ) );
                    $birthday_formatted = date_i18n( $date_format, $birthday->getTimestamp() );
                    $birthday->modify( '+ 1900 years' );
                } else {
                    $birthday_formatted = Lib\Utils\DateTime::formatDate( $birthday->format( 'Y-m-d' ) );
                }
            } else {
                $birthday_formatted = $birthday = null;
            }

            $customer_data = array(
                'id'                 => $row['id'],
                'wp_user_id'         => $row['wp_user_id'],
                'wp_user'            => $row['wp_user'],
                'facebook_id'        => $row['facebook_id'],
                'group_id'           => $row['group_id'],
                'full_name'          => $row['full_name'],
                'first_name'         => $row['first_name'],
                'last_name'          => $row['last_name'],
                'phone'              => $row['phone'],
                'email'              => $row['email'],
                'country'            => $row['country'],
                'state'              => $row['state'],
                'postcode'           => $row['postcode'],
                'city'               => $row['city'],
                'street'             => $row['street'],
                'street_number'      => $row['street_number'],
                'additional_address' => $row['additional_address'],
                'address'            => $address,
                'notes'              => $row['notes'],
                'birthday'           => $birthday ? $birthday->format( 'Y-m-d' ) : null,
                'birthday_formatted' => $birthday_formatted,
                'last_appointment'   => $row['last_appointment'] ? Lib\Utils\DateTime::formatDateTime( $row['last_appointment'] ) : '',
                'total_appointments' => $row['total_appointments'],
                'payments'           => Lib\Utils\Price::format( $row['payments'] ),
            );

            $customer_data = Proxy\CustomerGroups::prepareCustomerListData( $customer_data, $row );
            $customer_data = Proxy\CustomerInformation::prepareCustomerListData( $customer_data, $row );

            $data[] = $customer_data;
        }

        Lib\Utils\Tables::updateSettings( 'customers', $columns, $order, $filter );

        wp_send_json( array(
            'draw'            => ( int ) self::parameter( 'draw' ),
            'recordsTotal'    => $total,
            'recordsFiltered' => $records_filtered,
            'data'            => $data,
        ) );
    }

    /**
     * Get list of customers.
     */
    public static function getCustomersList()
    {
        global $wpdb;

        $max_results = self::parameter( 'max_results', 20 );
        $filter      = self::parameter( 'filter' );
        $page        = self::parameter( 'page' );
        $query       = Lib\Entities\Customer::query( 'c' );

        $query->select( 'SQL_CALC_FOUND_ROWS c.id, c.group_id, c.full_name AS text, c.email, c.phone' );

        if ( $filter != '' ) {
            $search_value = Lib\Query::escape( $filter );
            $query
                ->whereLike( 'c.full_name', "%{$search_value}%" )
                ->whereLike( 'c.phone', "%{$search_value}%", 'OR' )
                ->whereLike( 'c.email', "%{$search_value}%", 'OR' )
            ;
        }

        $query->limit( $max_results )->offset( ( $page - 1 ) * $max_results );

        $rows = $query->fetchArray();
        $more = ( int ) $wpdb->get_var( 'SELECT FOUND_ROWS()' ) > $max_results * $page;

        $customers = array();
        foreach ( $rows as $customer ) {
            $name = $customer['text'];
            if ( $customer['email'] != '' || $customer['phone'] != '' ) {
                $name .= ' (' . trim( $customer['email'] . ', ' . $customer['phone'], ', ' ) . ')';
            }
            $customer['name'] = $name;
            if ( self::parameter( 'timezone' ) ) {
                $customer['timezone'] = Lib\Proxy\Pro::getLastCustomerTimezone( $customer['id'] );
            }
            $customers[] = $customer;
        }

        wp_send_json( array(
            'results'    => $customers,
            'pagination' => array(
                'more' => $more,
            ),
        ) );
    }

    /**
     * Merge customers.
     */
    public static function mergeCustomers()
    {
        $target_id = self::parameter( 'target_id' );
        $ids       = self::parameter( 'ids', array() );

        // Move appointments.
        Lib\Entities\CustomerAppointment::query()
            ->update()
            ->set( 'customer_id', $target_id )
            ->whereIn( 'customer_id', $ids )
            ->execute();

        // Let add-ons do their stuff.
        Proxy\Shared::mergeCustomers( $target_id, $ids );

        // Merge customer data.
        $target_customer = Lib\Entities\Customer::find( $target_id );
        foreach ( $ids as $id ) {
            if ( $id != $target_id ) {
                $customer = Lib\Entities\Customer::find( $id );
                if ( ! $target_customer->getWpUserId() && $customer->getWpUserId() ) {
                    $target_customer->setWpUserId( $customer->getWpUserId() );
                }
                if ( ! $target_customer->getGroupId() ) {
                    $target_customer->setGroupId( $customer->getGroupId() );
                }
                if ( ! $target_customer->getFacebookId() ) {
                    $target_customer->setFacebookId( $customer->getFacebookId() );
                }
                if ( $target_customer->getFullName() == '' ) {
                    $target_customer->setFullName( $customer->getFullName() );
                }
                if ( $target_customer->getFirstName() == '' ) {
                    $target_customer->setFirstName( $customer->getFirstName() );
                }
                if ( $target_customer->getLastName() == '' ) {
                    $target_customer->setLastName( $customer->getLastName() );
                }
                if ( $target_customer->getPhone() == '' ) {
                    $target_customer->setPhone( $customer->getPhone() );
                }
                if ( $target_customer->getEmail() == '' ) {
                    $target_customer->setEmail( $customer->getEmail() );
                }
                if ( $target_customer->getBirthday() == '' ) {
                    $target_customer->setBirthday( $customer->getBirthday() );
                }
                if ( $target_customer->getCountry() == '' ) {
                    $target_customer->setCountry( $customer->getCountry() );
                }
                if ( $target_customer->getState() == '' ) {
                    $target_customer->setState( $customer->getState() );
                }
                if ( $target_customer->getPostcode() == '' ) {
                    $target_customer->setPostcode( $customer->getPostcode() );
                }
                if ( $target_customer->getCity() == '' ) {
                    $target_customer->setCity( $customer->getCity() );
                }
                if ( $target_customer->getStreet() == '' ) {
                    $target_customer->setStreet( $customer->getStreet() );
                }
                if ( $target_customer->getAdditionalAddress() == '' ) {
                    $target_customer->setAdditionalAddress( $customer->getAdditionalAddress() );
                }
                if ( $target_customer->getNotes() == '' ) {
                    $target_customer->setNotes( $customer->getNotes() );
                }
                if ( $target_customer->getStripeAccount() == '' ) {
                    $target_customer->setStripeAccount( $customer->getStripeAccount() );
                }
                // Delete merged customer.
                $customer->delete();
            }
            $target_customer->save();
        }

        wp_send_json_success();
    }
}