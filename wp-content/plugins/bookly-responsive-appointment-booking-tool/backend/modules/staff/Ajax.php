<?php
namespace Bookly\Backend\Modules\Staff;

use Bookly\Lib;

/**
 * Class Page
 * @package Bookly\Backend\Modules\Staff
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        $permissions = get_option( 'bookly_gen_allow_staff_edit_profile' ) ? array( '_default' => 'staff' ) : array();
        if ( Lib\Config::staffCabinetActive() ) {
            $permissions = array( '_default' => 'staff' );
        }

        return $permissions;
    }

    /**
     * Staff list
     */
    public static function getStaffList()
    {
        global $wpdb;

        $columns = self::parameter( 'columns' );
        $order   = self::parameter( 'order', array() );
        $filter  = self::parameter( 'filter' );
        $limits  = array(
            'length' => self::parameter( 'length' ),
            'start'  => self::parameter( 'start' ),
        );

        $query = Lib\Entities\Staff::query( 's' )
            ->select( 's.id, s.full_name, s.color' )
            ->tableJoin( $wpdb->users, 'wpu', 'wpu.ID = s.wp_user_id' );

        if ( ! Lib\Utils\Common::isCurrentUserAdmin() ) {
            $query->where( 's.wp_user_id', get_current_user_id() );
        }

        $query->addSelect( 's.category_id,  s.visibility, email, phone, wpu.display_name AS wp_user' );

        Proxy\Shared::prepareGetStaffQuery( $query );

        foreach ( $order as $sort_by ) {
            $query->sortBy( str_replace( '.', '_', $columns[ $sort_by['column'] ]['data'] ) )
                  ->order( $sort_by['dir'] == 'desc' ? Lib\Query::ORDER_DESCENDING : Lib\Query::ORDER_ASCENDING );
        }

        $total = $query->count();

        if ( $filter['archived'] ) {
            if ( isset( $filter['visibility'] ) && $filter['visibility'] != '' ) {
                $query->whereRaw( 's.visibility = %s OR s.visibility = %s', array( $filter['visibility'], 'archive' ) );
            }
        } elseif ( isset( $filter['visibility'] ) && $filter['visibility'] != '' ) {
            $query->where( 's.visibility', $filter['visibility'] );
        } else {
            $query->whereNot( 's.visibility', 'archive' );
        }

        if ( isset( $filter['category'] ) && $filter['category'] != '' ) {
            $query->where( 's.category_id', $filter['category'] );
        }

        if ( isset( $filter['search'] ) && $filter['search'] != '' ) {
            $fields = array();
            foreach ( $columns as $column ) {
                switch ( $column['data'] ) {
                    case 'user':
                        $fields[] = 'wpu.display_name';
                        break;
                    case 'email':
                    case 'full_name':
                    case 'id':
                    case 'phone':
                        $fields[] = 's.' . $column['data'];
                        break;
                }
            }

            $fields = Proxy\Shared::searchStaff( $fields, $columns, $query );

            $search_columns = array();
            foreach ( $fields as $field ) {
                $search_columns[] = $field . ' LIKE "%%%s%"';
            }
            if ( ! empty( $search_columns ) ) {
                $query->whereRaw( implode( ' OR ', $search_columns ), array_fill( 0, count( $search_columns ), $wpdb->esc_like( $filter['search'] ) ) );
            }
        }

        $filtered = $query->count();

        if ( ! empty( $limits ) ) {
            $query->limit( $limits['length'] )->offset( $limits['start'] );
        }

        $data = $query->fetchArray();

        unset( $filter['search'] );

        Lib\Utils\Tables::updateSettings( 'staff_members', $columns, $order, $filter );

        wp_send_json( array(
            'draw'            => ( int ) self::parameter( 'draw' ),
            'data'            => $data,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
        ) );
    }

    /**
     * 'Safely' remove staff (report if there are future appointments)
     */
    public static function removeStaff()
    {
        if ( Lib\Utils\Common::isCurrentUserAdmin() ) {
            $staff_ids = self::parameter( 'staff_ids', array() );
            if ( self::parameter( 'force_delete', false ) ) {
                foreach ( $staff_ids as $staff_id ) {
                    if ( $staff = Lib\Entities\Staff::find( $staff_id ) ) {
                        foreach ( Lib\Entities\Appointment::query( 'a' )->where( 'a.staff_id', $staff_id )->find() as $appointment ) {
                            Lib\Utils\Log::deleteEntity( $appointment, __METHOD__, 'Delete staff: ' . $staff->getFullName() );
                        }
                        $staff->delete();
                    }
                }
                $total = Lib\Entities\Staff::query()->count();

                wp_send_json_success( compact( 'total' ) );
            } else {
                $appointment = Lib\Entities\Appointment::query( 'a' )
                    ->select( 'a.staff_id, MAX(a.start_date) AS start_date' )
                    ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
                    ->whereIn( 'a.staff_id', $staff_ids )
                    ->whereGt( 'a.start_date', current_time( 'mysql' ) )
                    ->whereIn( 'ca.status', Lib\Proxy\CustomStatuses::prepareBusyStatuses( array(
                        Lib\Entities\CustomerAppointment::STATUS_PENDING,
                        Lib\Entities\CustomerAppointment::STATUS_APPROVED,
                    ) ) )
                    ->limit( 1 )
                    ->fetchRow();

                $filter_url  = '';
                if ( $appointment['start_date'] ) {
                    $last_month = date_create( $appointment['start_date'] )->modify( 'last day of' )->format( 'Y-m-d' );
                    $action     = 'show_modal';
                    $filter_url = sprintf( '%s#staff=%d&appointment-date=%s-%s',
                        Lib\Utils\Common::escAdminUrl( \Bookly\Backend\Modules\Appointments\Ajax::pageSlug() ),
                        $appointment['staff_id'],
                        date_create( current_time( 'mysql' ) )->format( 'Y-m-d' ),
                        $last_month );
                    wp_send_json_error( compact( 'action', 'filter_url' ) );
                }
                $filter_url = Proxy\Shared::getAffectedAppointmentsFilter( $filter_url, $staff_ids );
                if ( $filter_url ) {
                    $action = 'show_modal';
                    wp_send_json_error( compact( 'action', 'filter_url' ) );
                } else {
                    $action = 'confirm';
                    wp_send_json_error( compact( 'action' ) );
                }
            }
        }

        wp_send_json_success();
    }

     /**
     * Extend parent method to control access on staff member level.
     *
     * @param string $action
     * @return bool
     */
    protected static function hasAccess( $action )
    {
        if ( parent::hasAccess( $action ) ) {
            if ( ! Lib\Utils\Common::isCurrentUserAdmin() ) {
                $staff = new Lib\Entities\Staff();

                switch ( $action ) {
                    case 'getStaffList':
                        return $staff->loadBy( array( 'wp_user_id' => get_current_user_id() ) );
                    default:
                        return false;
                }
            }

            return true;
        }

        return false;
    }
}