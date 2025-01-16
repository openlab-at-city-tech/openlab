<?php
namespace Bookly\Backend\Components\Dialogs\Customer\Edit;

use Bookly\Lib;

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

        $request = self::getRequest();
        // Check for errors.
        if ( get_option( 'bookly_cst_first_last_name' ) ) {
            if ( $request->get( 'first_name' ) == '' ) {
                $response['errors']['first_name'] = array( 'required' );
            }
            if ( $request->get( 'last_name' ) == '' ) {
                $response['errors']['last_name'] = array( 'required' );
            }
        } elseif ( $request->get( 'full_name' ) == '' ) {
            $response['errors']['full_name'] = array( 'required' );
        }
        if ( count( $response['errors'] ) === 0 ) {
            $customer = new Lib\Entities\Customer();
            if ( $request->get( 'id' ) ) {
                $customer->load( $request->get( 'id' ) );
            }
            $customer->setWpUserId( $request->get( 'wp_user_id' ) );
            $response = Proxy\Shared::prepareSaveCustomer( $response, $request, $customer );
            if ( count( $response['errors'] ) === 0 ) {
                $customer
                    ->setBirthday( $request->get( 'birthday' ) )
                    ->setGroupId( $request->get( 'group_id' ) )
                    ->setFirstName( $request->get( 'first_name' ) )
                    ->setLastName( $request->get( 'last_name' ) )
                    ->setFullName( $request->get( 'full_name' ) )
                    ->setPhone( $request->get( 'phone' ) ?: '' )
                    ->setEmail( $request->get( 'email' ) ?: '' )
                    ->setCountry( $request->get( 'country' ) )
                    ->setState( $request->get( 'state' ) )
                    ->setPostcode( $request->get( 'postcode' ) )
                    ->setCity( $request->get( 'city' ) )
                    ->setStreet( $request->get( 'street' ) )
                    ->setStreetNumber( $request->get( 'street_number' ) )
                    ->setAdditionalAddress( $request->get( 'additional_address' ) )
                    ->setNotes( $request->get( 'notes' ) ?: '' )
                    ->setAttachmentId( $request->get( 'attachment_id' ) )
                    ->save();

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
            $info_fields = json_decode( $customer['info_fields'], true ) ?: array();
            $files = $info_fields
                ? ( Lib\Proxy\Files::getFileNamesForCustomerInformationFields( $info_fields ) ?: array() )
                : array();
            $customer['info_fields'] = array();
            if ( $info_fields ) {
                foreach ( $info_fields as $field ) {
                    $customer['info_fields'][] = array(
                        'id' => (int) $field['id'],
                        'value' => $field['value'],
                    );
                }
            }

            $wp_user = $customer['wp_user_id']
                ? $wpdb->get_row( Dialog::getWPUsersQuery() . ( is_multisite() ? ' AND ' : ' WHERE ' ) . ' ID = ' . (int) $customer['wp_user_id'] )
                : null;
            $thumb = Lib\Utils\Common::getAttachmentUrl( $customer['attachment_id'], 'thumbnail' ) ?: null;

            wp_send_json_success( compact( 'customer', 'wp_user', 'files', 'thumb' ) );
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

    public static function checkWpUserIsAssigned()
    {
        $query = Lib\Entities\Customer::query()
            ->limit( 1 )
            ->where( 'wp_user_id', self::parameter( 'wp_user_id' ) );
        if ( self::hasParameter( 'customer_id' ) ) {
            $query->whereNot( 'id', self::parameter( 'customer_id' ) );
        }

        $full_name = $query->fetchVar( 'full_name' );
        $notices = array();
        if ( $full_name ) {
            $notices['wp_user_in_use'] = sprintf( __( 'This WP user is already connected to another customer: %s', 'bookly' ), $full_name );
        }

        wp_send_json_success( compact( 'notices' ) );
    }
}