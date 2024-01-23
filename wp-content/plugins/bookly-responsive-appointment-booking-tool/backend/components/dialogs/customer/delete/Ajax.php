<?php
namespace Bookly\Backend\Components\Dialogs\Customer\Delete;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'supervisor' );
    }

    /**
     * Delete customers.
     */
    public static function deleteCustomers()
    {
        $customer_ids = array_unique( (array) self::parameter( 'customers' ) );
        if ( $customer_ids ) {
            update_user_meta(
                get_current_user_id(),
                'bookly_delete_customers_options',
                self::parameter( 'remember' ) ? array( 'with_wp_users' => self::parameter( 'with_wp_users' ), 'with_events' => self::parameter( 'with_events' ) ) : ''
            );
            /** @var Lib\Entities\Customer[] $customers */
            $customers = Lib\Entities\Customer::query()
                ->whereIn( 'id', $customer_ids )
                ->indexBy( 'id' )
                ->find();

            $appointments = Lib\Entities\Appointment::query( 'a' )
                ->select( 'a.id, ca.id as ca_id, ca.customer_id, a.internal_note' )
                ->leftJoin( 'CustomerAppointment', 'ca', 'a.id = ca.appointment_id' )
                ->whereIn( 'ca.customer_id', $customer_ids )
                ->fetchArray();

            if ( $appointments ) {
                /** @var Lib\Entities\CustomerAppointment[] $ca_list */
                $ca_list = Lib\Entities\CustomerAppointment::query()
                    ->whereIn( 'id', array_map( function ( $appointment ) { return $appointment['ca_id']; }, $appointments ) )
                    ->indexBy( 'id' )
                    ->find();

                $info = sprintf( '%s, %s: ',
                    Lib\Utils\DateTime::formatDateTime( Lib\Slots\DatePoint::now()->format( 'Y-m-d H:i:s' ) ),
                    __( 'Deleted Customer', 'bookly' ) );
                foreach ( $appointments as $appointment ) {
                    $note = Lib\Query::escape( $info . $customers[ $appointment['customer_id'] ]->getFullName() );
                    Lib\Entities\Appointment::query( 'a' )
                        ->update()
                        ->setRaw( 'internal_note = CONCAT_WS(%s,internal_note,%s)', array( $appointment['internal_note'] == '' ? '' : PHP_EOL, $note ) )
                        ->where( 'id', $appointment['id'] )
                        ->execute();
                    $ca_list[ $appointment['ca_id'] ]->delete();
                }
            }
            foreach ( $customers as $customer ) {
                $customer->deleteWithWPUser( (bool) self::parameter( 'with_wp_users' ) );
            }
        }

        wp_send_json_success();
    }

    public static function checkCustomers()
    {
        $customer_ids = array_unique( (array) self::parameter( 'customers' ) );
        $events       = Lib\Entities\Appointment::query( 'a' )
            ->leftJoin( 'CustomerAppointment', 'ca', 'a.id = ca.appointment_id' )
            ->whereIn( 'ca.customer_id', $customer_ids )
            ->whereIn( 'ca.status', Lib\Proxy\CustomStatuses::prepareBusyStatuses( array(
                Lib\Entities\CustomerAppointment::STATUS_PENDING,
                Lib\Entities\CustomerAppointment::STATUS_APPROVED,
            ) ) )
            ->whereGte( 'a.start_date', current_time( 'mysql' ) )
            ->count() > 0;
        $tasks        = Lib\Entities\Appointment::query( 'a' )
            ->leftJoin( 'CustomerAppointment', 'ca', 'a.id = ca.appointment_id' )
            ->whereIn( 'ca.customer_id', $customer_ids )
            ->whereIn( 'ca.status', Lib\Proxy\CustomStatuses::prepareBusyStatuses( array(
                Lib\Entities\CustomerAppointment::STATUS_PENDING,
                Lib\Entities\CustomerAppointment::STATUS_APPROVED,
            ) ) )
            ->where( 'a.start_date', null )
            ->count() > 0;
        $meta         = get_user_meta( get_current_user_id(), 'bookly_delete_customers_options', true );
        if ( $meta != '' ) {
            $remember      = true;
            $with_events   = isset( $meta['with_events'] ) && $meta['with_events'];
            $with_wp_users = isset( $meta['with_wp_users'] ) && $meta['with_wp_users'];

        } else {
            $remember      = false;
            $with_events   = false;
            $with_wp_users = false;
        }
        wp_send_json_success( array(
            'with_events'   => $with_events,
            'with_wp_users' => $with_wp_users,
            'remember'      => $remember,
            'exists_events' => $events || $tasks,
        ) );
    }

}