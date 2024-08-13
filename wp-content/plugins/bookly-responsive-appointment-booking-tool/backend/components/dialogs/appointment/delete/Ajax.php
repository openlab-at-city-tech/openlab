<?php
namespace Bookly\Backend\Components\Dialogs\Appointment\Delete;

use Bookly\Lib;
use Bookly\Backend\Components\Dialogs\Queue\NotificationList;

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
     * Delete single appointment.
     */
    public static function deleteAppointment()
    {
        $appointment_id = self::parameter( 'appointment_id' );
        $reason = self::parameter( 'reason' );

        $queue = new NotificationList();

        if ( self::parameter( 'notify' ) ) {
            $ca_list = Lib\Entities\CustomerAppointment::query()
                ->where( 'appointment_id', $appointment_id )
                ->find();
            /** @var Lib\Entities\CustomerAppointment $ca */
            foreach ( $ca_list as $ca ) {
                switch ( $ca->getStatus() ) {
                    case Lib\Entities\CustomerAppointment::STATUS_CANCELLED:
                    case Lib\Entities\CustomerAppointment::STATUS_REJECTED:
                    case Lib\Entities\CustomerAppointment::STATUS_DONE:
                        continue 2;
                    case Lib\Entities\CustomerAppointment::STATUS_PENDING:
                    case Lib\Entities\CustomerAppointment::STATUS_WAITLISTED:
                        $ca->setStatus( Lib\Entities\CustomerAppointment::STATUS_REJECTED );
                        break;
                    case Lib\Entities\CustomerAppointment::STATUS_APPROVED:
                        $ca->setStatus( Lib\Entities\CustomerAppointment::STATUS_CANCELLED );
                        break;
                    default:
                        $busy_statuses = Lib\Proxy\CustomStatuses::prepareBusyStatuses( array() );
                        if ( in_array( $ca->getStatus(), $busy_statuses ) ) {
                            $ca->setStatus( Lib\Entities\CustomerAppointment::STATUS_CANCELLED );
                        } else {
                            $ca->setStatus( Lib\Entities\CustomerAppointment::STATUS_REJECTED );
                        }
                }
                Lib\Notifications\Booking\Sender::sendForCA( $ca, null, array( 'cancellation_reason' => $reason ), false, $queue );
            }
        }

        $appointment = Lib\Entities\Appointment::find( $appointment_id );
        if ( $appointment ) {
            $appointment->delete();
        }

        $response = array();
        $list = $queue->getList();
        if ( $list ) {
            $db_queue = new Lib\Entities\NotificationQueue();
            $db_queue
                ->setData( json_encode( array( 'all' => $list ) ) )
                ->save();

            $response['queue'] = array( 'token' => $db_queue->getToken(), 'all' => $list );
        }

        wp_send_json_success( $response );
    }
}