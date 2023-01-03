<?php
namespace Bookly\Backend\Components\Dialogs\Customer\Edit;

use Bookly\Lib;

/**
 * Class Ajax
 *
 * @package Bookly\Backend\Components\Dialogs\Customer\Edit
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
     * Create or edit a customer.
     */
    public static function saveCustomer()
    {
        $response = array(
            'errors' => array(),
        );

        $params = self::parameters();

        // Check for errors.
        if ( get_option( 'bookly_cst_first_last_name' ) ) {
            if ( $params['first_name'] == '' ) {
                $response['errors']['first_name'] = array( 'required' );
            }
            if ( $params['last_name'] == '' ) {
                $response['errors']['last_name'] = array( 'required' );
            }
        } elseif ( $params['full_name'] == '' ) {
            $response['errors']['full_name'] = array( 'required' );
        }

        if ( count( $response['errors'] ) === 0 ) {
            if ( ! $params['wp_user_id'] ) {
                $params['wp_user_id'] = null;
            }
            if ( ! $params['birthday'] ) {
                $params['birthday'] = null;
            }
            if ( ! $params['group_id'] ) {
                $params['group_id'] = null;
            }
            $params = Proxy\CustomerInformation::prepareCustomerFormData( $params );
            if ( isset( $params['info_fields'] ) ) {
                $params['info_fields'] = json_encode( $params['info_fields'] );
            }
            $form = new Forms\Customer();
            $form->bind( $params );
            $customer = new Lib\Entities\Customer( $form->getData() );
            $response = Proxy\Shared::prepareSaveCustomer( $response, $params, $customer );
            if ( count( $response['errors'] ) === 0 ) {
                if ( $params['wp_user_id'] === 'create' && isset( $response['wp_user'] ) ) {
                    $customer->setWpUserId( $response['wp_user']['ID'] );
                }

                $customer->save();
                $response['success'] = true;
                $response['customer'] = array(
                    'id' => (int) $customer->getId(),
                    'wp_user_id' => (int) $customer->getWpUserId(),
                    'group_id' => (int) $customer->getGroupId(),
                    'full_name' => $customer->getFullName(),
                    'first_name' => $customer->getFirstName(),
                    'last_name' => $customer->getLastName(),
                    'phone' => $customer->getPhone(),
                    'email' => $customer->getEmail(),
                    'notes' => $customer->getNotes(),
                    'birthday' => $customer->getBirthday(),
                    'info_fields' => json_decode( $customer->getInfoFields() ),
                );
            }
        }

        if ( count( $response['errors'] ) > 0 ) {
            $response['success'] = false;
        }

        wp_send_json( $response );
    }

    /**
     * Get customer details.
     */
    public static function getCustomer()
    {
        global $wpdb;

        $customer_id = self::parameter( 'id' );
        $customer = Lib\Entities\Customer::find( $customer_id )->getFields();

        if ( $customer ) {
            $customer['id'] = (int) $customer['id'];
            if ( isset( $customer['info_fields'] ) && $customer['info_fields'] ) {
                $customer['info_fields'] = array_map( function( $item ) { return array( 'id' => (int) $item['id'], 'value' => $item['value'] ); }, json_decode( $customer['info_fields'], true ) );
            }

            $wp_user = $customer['wp_user_id']
                ? $wpdb->get_row( Dialog::getWPUsersQuery() . ( is_multisite() ? ' AND ' : ' WHERE ' ) . ' ID = ' . (int) $customer['wp_user_id'] )
                : null;
            wp_send_json_success( compact( 'customer', 'wp_user' ) );
        } else {
            wp_send_json_error();
        }
    }

    /**
     * Get list of WP users for select2
     */
    public static function getWPUsersList()
    {
        global $wpdb;

        $max_results = (int) self::parameter( 'max_results', 20 );
        $filter = self::parameter( 'filter' );
        $page = (int) self::parameter( 'page' );
        $query = Dialog::getWPUsersQuery();

        if ( $filter != '' ) {
            $search_value = Lib\Query::escape( $filter );
            $query .= is_multisite() ? ' AND' : ' WHERE';
            $query .= ' u.display_name like "%' . $search_value . '%"';
        }
        $query .= ' ORDER BY u.display_name LIMIT ' . $max_results . ' OFFSET ' . ( $page - 1 ) * $max_results;
        $wp_users = $wpdb->get_results( $query, ARRAY_A );

        $more = ( int ) $wpdb->get_var( 'SELECT FOUND_ROWS()' ) > $max_results * $page;

        wp_send_json( array(
            'results' => $wp_users,
            'pagination' => compact( 'more' ),
            'page' => $page,
        ) );
    }
}