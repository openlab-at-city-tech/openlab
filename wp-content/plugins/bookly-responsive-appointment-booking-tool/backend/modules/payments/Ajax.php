<?php
namespace Bookly\Backend\Modules\Payments;

use Bookly\Lib;

/**
 * Class Ajax
 *
 * @package Bookly\Backend\Modules\Payments
 */
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

        $query = Lib\Entities\Payment::query( 'p' )
            ->select( 'p.id, p.created_at, p.type, p.paid, p.total, p.status, p.details, p.target, c.full_name customer, st.full_name provider, s.title service, a.start_date' )
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

        $payments = $query->fetchArray();

        unset( $filter['created_at'] );

        Lib\Utils\Tables::updateSettings( 'payments', null, null, $filter );

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
                'provider' => $payment['target'] === Lib\Entities\Payment::TARGET_GIFT_CARDS ? __( 'N/A', 'bookly' ) : ( $payment['provider'] ?: $details['items'][0]['staff_name'] ),
                'service' => $payment['target'] === Lib\Entities\Payment::TARGET_GIFT_CARDS ? __( 'N/A', 'bookly' ) : ( $payment['service'] ?: $details['items'][0]['service_name'] ),
                'start_date' => $payment['start_date'] ?: ( isset( $details['items'][0]['appointment_date'] ) && $details['items'][0]['appointment_date'] ? $details['items'][0]['appointment_date'] : __( 'N/A', 'bookly' ) ),
                'start_date_format' => $payment['start_date']
                    ? Lib\Utils\DateTime::formatDateTime( $payment['start_date'] )
                    : ( isset( $details['items'][0]['appointment_date'] ) && $details['items'][0]['appointment_date'] ? Lib\Utils\DateTime::formatDateTime( $details['items'][0]['appointment_date'] ) : __( 'N/A', 'bookly' ) ),
                'paid' => $paid_title,
                'status' => Lib\Entities\Payment::statusToString( $payment['status'] ),
            );

            $total += $payment['paid'];
        }

        wp_send_json( array(
            'draw' => ( int ) self::parameter( 'draw' ),
            'recordsTotal' => count( $data ),
            'recordsFiltered' => count( $data ),
            'data' => $data,
            'total' => Lib\Utils\Price::format( $total ),
        ) );
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

}