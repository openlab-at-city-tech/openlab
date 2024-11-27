<?php
namespace Bookly\Lib\DataHolders\Notification;

use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Entities\Service;
use Bookly\Lib\Utils\Codes;

class Settings
{
    /** @var array */
    protected $settings;
    /** @var int */
    protected $offset_hours = 0;
    /** @var int */
    protected $at_hour;
    /** @var string  @see CustomerAppointment::STATUS_* or any */
    protected $status = 'any';
    /** @var bool */
    protected $instant = 0;
    /** @var mixed value 'any' or an array of service_ids */
    protected $services  = 'any';

    /**
     * Condition constructor.
     *
     * @param Notification $notification
     */
    public function __construct( Notification $notification )
    {
        $this->settings = (array) json_decode( $notification->getSettings(), true );
        $this->prepare( $notification->getType() );
    }

    /**
     * @param string $type
     */
    private function prepare( $type )
    {
        switch ( $type ) {
            case Notification::TYPE_NEW_BOOKING_COMBINED:
            case Notification::TYPE_NEW_PACKAGE:
            case Notification::TYPE_PACKAGE_DELETED:
            case Notification::TYPE_CUSTOMER_NEW_WP_USER:
            case Notification::TYPE_STAFF_NEW_WP_USER:
            case Notification::TYPE_STAFF_WAITING_LIST:
            case Notification::TYPE_FREE_PLACE_WAITING_LIST:
            case Notification::TYPE_VERIFY_PHONE:
            case Notification::TYPE_VERIFY_EMAIL:
            case Notification::TYPE_NEW_GIFT_CARD:
                $this->instant = 1;
                break;
            case Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED:
            case Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING:
            case Notification::TYPE_NEW_BOOKING:
            case Notification::TYPE_NEW_BOOKING_RECURRING:
            case Notification::TYPE_MOBILE_SC_GRANT_ACCESS_TOKEN:
                if ( isset( $this->settings['status'] ) ) {
                    $this->status = $this->settings['status'];
                }
                $this->instant  = 1;
                $this->services = $this->_handleService( $this->settings );
                break;
            case Notification::TYPE_APPOINTMENT_REMINDER:
                $this->status   = $this->settings['status'];
                $this->services = $this->_handleService( $this->settings );
                // no need to break here
            case Notification::TYPE_LAST_CUSTOMER_APPOINTMENT:
                if ( $this->settings['option'] == 1 ) {
                    // offset_hours [ 1h .. 30d ] & perform [ after | before ]
                    $this->offset_hours = $this->settings['offset_hours'];
                    if ( $this->settings['perform'] == 'before' ) {
                        $this->offset_hours *= - 1;
                    }
                } elseif ( $this->settings['option'] == 2 ) {
                    // at_hour [ 00:00 .. 23:00 ] & offset_bidirectional_hours [ -30d .. 30d ]
                    $this->at_hour      = $this->settings['at_hour'];
                    $this->offset_hours = $this->settings['offset_bidirectional_hours'];
                }
                break;
            case Notification::TYPE_STAFF_DAY_AGENDA:
                $this->at_hour      = $this->settings['before_at_hour'];
                $this->offset_hours = $this->settings['offset_before_hours'];
                break;
            case Notification::TYPE_CUSTOMER_BIRTHDAY:
                $this->at_hour      = $this->settings['at_hour'];
                $this->offset_hours = $this->settings['offset_bidirectional_hours'];
                break;
        }
    }

    /**
     * Get a message template for WhatsApp with variables
     *
     * @param array $codes
     * @return array
     */
    public function getWhatsAppMessage( $codes )
    {
        $data = $this->settings['whatsapp'];
        $message = array(
            'name' => $data['template'],
            'language' => array( 'code' => $data['language'] ),
            'components' => array(),
        );
        foreach ( array( 'header', 'body' ) as $part ) {
            if ( isset( $data[ $part ] ) ) {
                $component = array(
                    'type' => $part,
                    'parameters' => array(),
                );
                foreach ( $data[ $part ] as $value ) {
                    $component['parameters'][] = array(
                        'type' => 'text',
                        'text' => Codes::replace( $value, $codes, false ),
                    );
                }
                $message['components'][] = $component;
            }
        }

        return $message;
    }

    /**
     * @return int
     */
    public function getOffsetHours()
    {
        return (int) $this->offset_hours;
    }

    /**
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Gets at_hour
     *
     * @return int
     */
    public function getSendAtHour()
    {
        return (int) $this->at_hour;
    }

    /**
     * Gets at_hour
     *
     * @return int|null
     */
    public function getAtHour()
    {
        return $this->at_hour;
    }

    /**
     * Gets instant
     *
     * @return bool
     */
    public function getInstant()
    {
        return $this->instant;
    }

    /**
     * @return string|array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Default notification settings.
     * @return array
     */
    public static function getDefault()
    {
        return array(
            'status'   => 'any',
            'option'   => 2,
            'services' => array(
                'any' => 'any',
                'ids' => array(),
            ),
            'offset_hours'   => 2,
            'perform'        => 'before',
            'at_hour'        => 9,
            'before_at_hour' => 18,
            'offset_before_hours' => -24,
            'offset_bidirectional_hours' => 0,
        );
    }

    /**
     * @param Service $service
     * @param string  $status customer appointment status
     * @param Service|null $parent if set send staff notification for non simple service.
     * @return bool
     */
    public function allowedServiceWithStatus( Service $service, $status, $parent = null )
    {
        if ( in_array( $this->getStatus(), array( 'any', $status ) ) ) {
            if ( $this->services == 'any' ) {
                return true;
            } elseif ( $parent ) {
                return in_array( $service->getId(), isset( $this->services[ $parent->getType() ][ $parent->getId() ] ) ? $this->services[ $parent->getType() ][ $parent->getId() ] : array() );
            } else {
                return array_key_exists( $service->getId(), $this->services[ $service->getType() ] );
            }
        }

        return false;
    }

    /**
     * Get services ids or string 'any' for current notification
     *
     * @param array $settings
     * @return array|string
     */
    private function _handleService( $settings )
    {
        $services = array(
            Service::TYPE_SIMPLE => array(),
            Service::TYPE_COMPOUND => array(),
            Service::TYPE_COLLABORATIVE => array(),
            Service::TYPE_PACKAGE => array()
        );
        // value "any" or an array of service_ids
        if ( $settings && $settings['services']['any'] == 'selected' ) {
            if ( array_key_exists( 'ids', $settings['services'] ) ) {
                /* Example: We have 1 notification with checked service1 and service4
                 *
                 * service1,service2,service3 is simple services
                 * service4 is compound with [service2 and service3]
                 *
                 * $service is array like
                 *              Client  |   Staff
                 * [
                 *  'simple' =>   [1    =>  [1]   ],
                 *  'compound' => [4    =>  [2,3] ]
                 * ]
                 * finally:
                 * to client we to need send notification for services 1 or 4
                 * to staff 1 or 2,3 if client booked service1 or service4
                 * ----
                 * if client booked service2 or service3, no notification will be sent
                 */
                $rows = Service::query( 's' )
                    ->select( 's.id, s.`type`, COALESCE(ss.sub_service_id,s.id) AS sub_id' )
                    ->leftJoin( 'SubService', 'ss', 'ss.service_id = s.id' )
                    ->whereIn( 's.id', array_map( 'intval', (array) $settings['services']['ids'] ) )
                    ->fetchArray();
                foreach ( $rows as $row ) {
                    $services[ $row['type'] ][ $row['id'] ][] = $row['sub_id'];
                }
            }
        } else {
            $services = 'any';
        }

        return $services;
    }
}