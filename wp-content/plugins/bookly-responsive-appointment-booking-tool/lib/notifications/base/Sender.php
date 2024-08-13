<?php
namespace Bookly\Lib\Notifications\Base;

use Bookly\Lib\Entities\Notification;

abstract class Sender extends Reminder
{
    /**
     * Get instant notifications of given type.
     *
     * @param string $type
     * @return array
     */
    public static function getNotifications( $type )
    {
        $result = array(
            'client' => array(),
            'staff'  => array(),
        );

        $query = Notification::query( 'n' )
            ->where( 'n.type', $type )
            ->where( 'n.active', '1' )
        ;

        $notifications = Notification::getAssociated();

        /** @var Notification $notification */
        foreach ( $query->find() as $notification ) {
            if ( in_array( $notification->getType(), $notifications[ $notification->getGateway() ] ) ) {
                $settings = $notification->getSettingsObject();
                if ( $settings->getInstant() ) {
                    if ( $notification->getToCustomer() ) {
                        $result['client'][] = $notification;
                    }
                    if ( $notification->getToStaff() || $notification->getToAdmin() || $notification->getToCustom() ) {
                        $result['staff'][] = $notification;
                    }
                }
            }
        }

        return $result;
    }
}