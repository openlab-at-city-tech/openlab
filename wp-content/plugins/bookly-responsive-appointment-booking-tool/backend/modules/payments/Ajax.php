<?php
namespace Bookly\Backend\Modules\Payments;

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
     * Get payments.
     */
    public static function getPayments()
    {
        $filter = self::parameter( 'filter' );
        $columns = Lib\Utils\Tables::filterColumns( self::parameter( 'columns' ), Lib\Utils\Tables::PAYMENTS );
        $order = self::parameter( 'order', array() );
        $limits = array(
            'length' => self::parameter( 'length' ),
            'start' => self::parameter( 'start' ),
        );

        $query = self::getPaymentQuery( $filter );

        $clone = clone $query;
        $counts = $clone->fetchCol( 'COUNT(p.id)' );

        foreach ( $order as $sort_by ) {
            $query->sortBy( str_replace( '.', '_', $columns[ $sort_by['column'] ]['data'] ) )
                ->order( $sort_by['dir'] === 'desc' ? Lib\Query::ORDER_DESCENDING : Lib\Query::ORDER_ASCENDING );
        }

        if ( ! empty( $limits ) ) {
            $query->limit( $limits['length'] )->offset( $limits['start'] );
        }
        $payments = $query->fetchArray();

        unset( $filter['created_at'] );

        Lib\Utils\Tables::updateSettings( Lib\Utils\Tables::PAYMENTS, null, null, $filter );

        $data = array();
        $total = 0;
        foreach ( $payments as $payment ) {
            $details = json_decode( $payment['details'], true );

            $paid_title = Lib\Utils\Price::format( $payment['paid'] );
            if ( $payment['paid'] != $payment['total'] ) {
                $paid_title = sprintf( __( '%s of %s', 'bookly' ), $paid_title, Lib\Utils\Price::format( $payment['total'] ) );
            }

            $data[] = array(
                'id' => $payment['id'],
                'created_at' => $payment['created_at'],
                'created_format' => Lib\Utils\DateTime::formatDateTime( $payment['created_at'] ),
                'type' => Lib\Entities\Payment::typeToString( $payment['type'] ),
                'multiple' => isset( $details['items'] ) && is_array( $details['items'] ) && count( $details['items'] ) > 1,
                'customer' => $payment['customer'] ?: $details['customer'],
                'provider' => $payment['provider'] ?: ( isset( $details['items'][0]['staff_name'] ) ? $details['items'][0]['staff_name'] : __( 'N/A', 'bookly' ) ),
                'service' => $payment['service'] ?: ( isset( $details['items'][0]['service_name'] ) ? $details['items'][0]['service_name'] : __( 'N/A', 'bookly' ) ),
                'start_date' => $payment['start_date'] ?: ( isset( $details['items'][0]['appointment_date'] ) && $details['items'][0]['appointment_date'] ? $details['items'][0]['appointment_date'] : __( 'N/A', 'bookly' ) ),
                'start_date_format' => $payment['start_date']
                    ? Lib\Utils\DateTime::formatDateTime( $payment['start_date'] )
                    : ( isset( $details['items'][0]['appointment_date'] ) && $details['items'][0]['appointment_date'] ? Lib\Utils\DateTime::formatDateTime( $details['items'][0]['appointment_date'] ) : __( 'N/A', 'bookly' ) ),
                'paid' => $paid_title,
                'status' => Lib\Entities\Payment::statusToString( $payment['status'] ),
                'subtotal' => Lib\Utils\Price::format( $details['subtotal']['price'] ),
            );

            $total += $payment['paid'];
        }

        wp_send_json( array(
            'draw' => ( int ) self::parameter( 'draw' ),
            'data' => $data,
            'recordsFiltered' => count( $counts ),
            'total' => Lib\Utils\Price::format( $total ),
        ) );
    }

    public static function getPaymentIds()
    {
        $filter = self::parameter( 'filter' );
        $query = self::getPaymentQuery( $filter );
        $ids = $query->fetchCol( 'p.id' );

        wp_send_json_success( compact( 'ids' ) );
    }

    /**
     * Delete payments.
     */
    public static function deletePayments()
    {
        $payment_ids = array_map( 'intval', self::parameter( 'data', array() ) );
        /** @var Lib\Entities\Payment $payment */
        foreach ( Lib\Entities\Payment::query()->whereIn( 'id', $payment_ids )->find() as $payment ) {
            $payment->delete();
        }
        wp_send_json_success();
    }

    private static function getPaymentQuery( $filter )
    {
        $query = Lib\Entities\Payment::query( 'p' )
            ->select( 'p.id, p.created_at, p.type, p.paid, p.total, p.status, p.details, c.full_name AS customer, st.full_name AS provider, s.title AS service, a.start_date' )
            ->leftJoin( 'CustomerAppointment', 'ca', 'ca.payment_id = p.id' )
            ->leftJoin( 'Customer', 'c', 'c.id = ca.customer_id' )
            ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
            ->leftJoin( 'Service', 's', 's.id = COALESCE(ca.compound_service_id, ca.collaborative_service_id, a.service_id)' )
            ->leftJoin( 'Staff', 'st', 'st.id = a.staff_id' )
            ->groupBy( 'p.id' );

        // Filters.
        list ( $start, $end ) = explode( ' - ', $filter['created_at'], 2 );
        $end = date( 'Y-m-d', strtotime( '+1 day', strtotime( $end ) ) );

        $query->whereBetween( 'p.created_at', $start, $end );

        if ( $filter['id'] != '' ) {
            $query->where( 'p.id', $filter['id'] );
        }

        if ( $filter['type'] != '' ) {
            $query->where( 'p.type', $filter['type'] );
        }

        if ( $filter['staff'] != '' ) {
            $query->where( 'st.id', $filter['staff'] );
        }

        if ( $filter['service'] != '' ) {
            $query->where( 's.id', $filter['service'] );
        }

        if ( $filter['status'] != '' ) {
            $query->where( 'p.status', $filter['status'] );
        }

        if ( $filter['customer'] != '' ) {
            $query->where( 'ca.customer_id', $filter['customer'] );
        }

        return $query;
    }
}