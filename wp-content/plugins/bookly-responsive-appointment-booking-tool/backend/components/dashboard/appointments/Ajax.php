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
        $end = date_create( $end );
        $based_on = self::parameter( 'based_on', 'created_at' );
        $day = array(
            'total' => 0,
            'revenue' => 0,
        );
        $data = array(
            'totals' => array(
                'approved' => 0,
                'pending' => 0,
                'total' => 0,
                'revenue' => 0,
            ),
            'filters' => array(
                'created_at' => array(
                    'approved' => sprintf( '%s#created-date=%s-%s&appointment-date=any&status=%s', Lib\Utils\Common::escAdminUrl( Modules\Appointments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ), 'approved' ),
                    'pending' => sprintf( '%s#created-date=%s-%s&appointment-date=any&status=%s', Lib\Utils\Common::escAdminUrl( Modules\Appointments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ), 'pending' ),
                    'total' => sprintf( '%s#created-date=%s-%s&appointment-date=any&status=any', Lib\Utils\Common::escAdminUrl( Modules\Appointments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ) ),
                    'revenue' => sprintf( '%s#created-date=%s-%s&appointment-date=any', Lib\Utils\Common::escAdminUrl( Modules\Payments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ) ),
                ),
                'start_date' => array(
                    'approved' => sprintf( '%s#created-date=any&appointment-date=%s-%s&status=%s', Lib\Utils\Common::escAdminUrl( Modules\Appointments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ), 'approved' ),
                    'pending' => sprintf( '%s#created-date=any&appointment-date=%s-%s&status=%s', Lib\Utils\Common::escAdminUrl( Modules\Appointments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ), 'pending' ),
                    'total' => sprintf( '%s#created-date=any&appointment-date=%s-%s&status=any', Lib\Utils\Common::escAdminUrl( Modules\Appointments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ) ),
                    'revenue' => sprintf( '%s#created-date=any&appointment-date=%s-%s', Lib\Utils\Common::escAdminUrl( Modules\Payments\Page::pageSlug() ), $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ) ),
                ),
            ),
            'days' => array(),
            'labels' => array(),
        );
        $end->modify( '+1 day' );
        $period = new \DatePeriod( $start, \DateInterval::createFromDateString( '1 day' ), $end );
        /** @var \DateTime $dt */
        foreach ( $period as $dt ) {
            $data['labels'][] = date_i18n( 'M j', $dt->getTimestamp() );
            $data['days'][ $dt->format( 'Y-m-d' ) ] = $day;
        }

        $query = Lib\Entities\CustomerAppointment::query( 'ca' )
            ->select( 'COUNT(1) AS quantity, p.paid AS revenue, ca.status, p.id' )
            ->leftJoin( 'Payment', 'p', 'p.id = ca.payment_id' );

        switch ( $based_on ) {
            case 'start_date':
                update_option( 'bookly_dashboard_based_on_appointment', 'start_date' );
                $query->addSelect( 'DATE(a.start_date) AS group_date' )
                    ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
                    ->whereBetween( 'a.start_date', $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ) )
                    ->groupBy( 'DATE(a.start_date), p.id, ca.status' );
                break;
            case 'created_at':
            default:
                update_option( 'bookly_dashboard_based_on_appointment', 'created_at' );
                $query->addSelect( 'DATE(ca.created_at) AS group_date' )
                    ->whereBetween( 'ca.created_at', $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ) )
                    ->groupBy( 'DATE(ca.created_at), p.id, ca.status' );
        }

        $records = $query->fetchArray();

        $custom_statuses = Lib\Proxy\CustomStatuses::getAll() ?: array();
        // Consider payment for all appointments only 1 time
        $payment_ids = array();
        foreach ( $records as $record ) {
            $group_key = $record['group_date'];
            $quantity = $record['quantity'];
            $status = $record['status'];
            if ( in_array( $record['id'], $payment_ids ) ) {
                $revenue = 0;
            } else {
                $payment_ids[] = $record['id'];
                $revenue = $record['revenue'];
            }
            if ( array_key_exists( $status, $data['totals'] ) ) {
                $data['totals'][ $status ] += $quantity;
            } elseif ( isset ( $custom_statuses[ $status ] ) && $custom_statuses[ $status ]->getBusy() ) {
                // Consider as APPROVED.
                $data['totals']['approved'] += $quantity;
            }
            $data['totals']['total'] += $quantity;
            $data['totals']['revenue'] += $revenue;
            $data['days'][ $group_key ]['total'] += $quantity;
            $data['days'][ $group_key ]['revenue'] += $revenue;
        }
        $data['totals']['revenue'] = Lib\Utils\Price::format( $data['totals']['revenue'] );

        wp_send_json_success( $data );
    }
}