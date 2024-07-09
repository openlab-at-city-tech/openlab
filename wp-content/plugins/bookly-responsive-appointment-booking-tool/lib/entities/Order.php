<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;
use Bookly\Lib\DataHolders\Booking\Simple;
use Bookly\Lib\Utils\Common;

class Order extends Lib\Base\Entity
{
    /** @var  string */
    protected $token;

    protected static $table = 'bookly_orders';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'token' => array( 'format' => '%s' ),
    );

    /**
     * @return array
     */
    public function getCaItems()
    {
        $records = CustomerAppointment::query( 'ca' )
            ->select( 's.id AS service_id, MIN(a.start_date) AS start_date, MAX(a.end_date) AS end_date, a.custom_service_name, a.location_id, s.title AS service_title, ca.id AS ca_id' )
            ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
            ->leftJoin( 'Service', 's', 's.id = COALESCE(ca.compound_service_id, ca.collaborative_service_id, a.service_id)' )
            ->leftJoin( 'Order', 'o', 'o.id = ca.order_id', '\Bookly\Lib\Entities' )
            ->where( 'ca.order_id', $this->getId() )
            ->groupBy( 'COALESCE(ca.compound_token, ca.collaborative_token, ca.id)' )
            ->fetchArray();
        $data = array();
        foreach ( $records as $appointment ) {
            if ( $appointment['start_date'] !== null ) {
                $item = Simple::create( CustomerAppointment::find( $appointment['ca_id'] ) );
                $item->getAppointment()
                    ->setStartDate( $appointment['start_date'] )
                    ->setEndDate( $appointment['end_date'] )
                    ->setLocationId( $appointment['location_id'] );
                $data[] = array(
                    'title' => $appointment['service_id'] === null ? $appointment['custom_service_name'] : Common::getTranslatedString( 'service_' . $appointment['service_id'], $appointment['service_title'] ),
                    'item' => $item,
                );
            }
        }

        return $data;
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets token
     *
     * @param string $token
     * @return $this
     */
    public function setToken( $token )
    {
        $this->token = $token;

        return $this;
    }

}