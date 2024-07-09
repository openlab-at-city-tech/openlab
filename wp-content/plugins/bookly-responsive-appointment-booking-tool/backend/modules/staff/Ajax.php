<?php
namespace Bookly\Backend\Modules\Staff;

use Bookly\Lib;

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

        $columns = Lib\Utils\Tables::filterColumns( self::parameter( 'columns' ), Lib\Utils\Tables::STAFF_MEMBERS );
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

        foreach ( $data as &$row ) {
            $row['color'] = esc_attr( $row['color'] );
        }

        unset( $filter['search'], $row );

        Lib\Utils\Tables::updateSettings( Lib\Utils\Tables::STAFF_MEMBERS, $columns, $order, $filter );

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
            foreach ( $staff_ids as $staff_id ) {
                if ( $staff = Lib\Entities\Staff::find( $staff_id ) ) {
                    $staff->delete();
                }
            }
            $total = Lib\Entities\Staff::query()->count();

            wp_send_json_success( compact( 'total' ) );
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