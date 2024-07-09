<?php
namespace Bookly\Backend\Components\Dashboard\Appointments;

use Bookly\Lib;
use Bookly\Backend\Modules;

class Ajax extends Lib\Base\Ajax
{
    public static function getAppointmentsDataForDashboard()
    {
        list ( $start, $end ) = explode( ' - ', self::parameter( 'range' ) );
        $start = date_create( $start );
        $end   = date_create( $end );
        $day   = array(
            'total'   => 0,
            'revenue' => 0,
        );
        $data  = array(
            'totals' => array(
                'approved' => 0,
                'pending'  => 0,
                'total'    => 0,
                'revenue'  => 0,
            ),
            'filters' => array(
                'approved' => sprintf( '%s#created-date=%s-%s&appointment-date=any&status=%s', Lib\Utils\Common::escAdminUrl( Modules\Appointments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ), 'approved' ),
                'pending'  => sprintf( '%s#created-date=%s-%s&appointment-date=any&status=%s', Lib\Utils\Common::escAdminUrl( Modules\Appointments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ), 'pending' ),
                'total'    => sprintf( '%s#created-date=%s-%s&appointment-date=any&status=any', Lib\Utils\Common::escAdminUrl( Modules\Appointments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ) ),
                'revenue'  => sprintf( '%s#created-date=%s-%s', Lib\Utils\Common::escAdminUrl( Modules\Payments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ) ),
            ),
            'days'   => array(),
            'labels' => array(),
        );
        $end->modify( '+1 day' );
        $period = new \DatePeriod( $start, \DateInterval::createFromDateString( '1 day' ), $end );
        /** @var \DateTime $dt */
        foreach ( $period as $dt ) {
            $data['labels'][] = date_i18n( 'M j', $dt->getTimestamp() );
            $data['days'][ $dt->format( 'Y-m-d' ) ] = $day;
        }

        $records = Lib\Entities\CustomerAppointment::query( 'ca' )
            ->select( 'DATE(ca.created_at) AS created_at, COUNT(1) AS quantity, p.paid AS revenue, ca.status, p.id' )
            ->leftJoin( 'Payment', 'p', 'p.id = ca.payment_id' )
            ->whereBetween( 'ca.created_at', $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ) )
            ->groupBy( 'DATE(ca.created_at), p.id, ca.status' )
            ->fetchArray();

        $custom_statuses = Lib\Proxy\CustomStatuses::getAll() ?: array();
        // Consider payment for all appointments only 1 time
        $payment_ids = array();
        foreach ( $records as $record ) {
            $created_at = $record['created_at'];
            $quantity = $record['quantity'];
            $status   = $record['status'];
            if ( in_array( $record['id'], $payment_ids ) ) {
                $revenue = 0;
            } else {
                $payment_ids[] = $record['id'];
                $revenue       = $record['revenue'];
            }
            if ( array_key_exists( $status, $data['totals'] ) ) {
                $data['totals'][ $status ] += $quantity;
            } elseif ( isset ( $custom_statuses[ $status ] ) && $custom_statuses[ $status ]->getBusy() ) {
                // Consider as APPROVED.
                $data['totals']['approved'] += $quantity;
            }
            $data['totals']['total']   += $quantity;
            $data['totals']['revenue'] += $revenue;
            $data['days'][ $created_at ]['total']   += $quantity;
            $data['days'][ $created_at ]['revenue'] += $revenue;
        }
        $data['totals']['revenue'] = Lib\Utils\Price::format( $data['totals']['revenue'] );

        wp_send_json_success( $data );
    }
}