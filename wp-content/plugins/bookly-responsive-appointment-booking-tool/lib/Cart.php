<?php
namespace Bookly\Lib;

use Bookly\Lib\DataHolders\Booking as DataHolders;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Entities\Payment;
use Bookly\Lib\Utils\Common;
use Bookly\Frontend\Modules\Booking\Proxy as BookingProxy;

/**
 * Class Cart
 *
 * @package Bookly\Lib
 */
class Cart
{
    /**
     * @var CartItem[]
     */
    private $items = array();

    /**
     * @var UserBookingData
     */
    private $userData;

    /**
     * Constructor.
     *
     * @param UserBookingData $userData
     */
    public function __construct( UserBookingData $userData )
    {
        $this->userData = $userData;
    }

    /**
     * Get cart item.
     *
     * @param integer $key
     * @return CartItem|false
     */
    public function get( $key )
    {
        if ( isset ( $this->items[ $key ] ) ) {
            return $this->items[ $key ];
        }

        return false;
    }

    /**
     * Add cart item.
     *
     * @param CartItem $item
     * @return integer
     */
    public function add( CartItem $item )
    {
        $this->items[] = $item;
        end( $this->items );

        return key( $this->items );
    }

    /**
     * Replace given item with other items.
     *
     * @param integer $key
     * @param CartItem[] $items
     * @return array
     */
    public function replace( $key, array $items )
    {
        $new_items = array();
        $new_keys = array();
        $new_key = 0;
        foreach ( $this->items as $cart_key => $cart_item ) {
            if ( $cart_key == $key ) {
                foreach ( $items as $item ) {
                    $new_items[ $new_key ] = $item;
                    $new_keys[] = $new_key;
                    ++$new_key;
                }
            } else {
                $new_items[ $new_key++ ] = $cart_item;
            }
        }
        $this->items = $new_items;

        return $new_keys;
    }

    /**
     * Drop cart item.
     *
     * @param integer $key
     */
    public function drop( $key )
    {
        unset ( $this->items[ $key ] );
    }

    /**
     * Get cart items.
     *
     * @return CartItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get items data as array.
     *
     * @return array
     */
    public function getItemsData()
    {
        $data = array();
        foreach ( $this->items as $key => $item ) {
            $data[ $key ] = $item->getData();
        }

        return $data;
    }

    /**
     * Set items data from array.
     *
     * @param array $data
     */
    public function setItemsData( array $data )
    {
        foreach ( $data as $key => $item_data ) {
            $item = new CartItem();
            $item->setData( $item_data );
            $this->items[ $key ] = $item;
        }
    }

    /**
     * Save all cart items (customer appointments).
     *
     * @param DataHolders\Order $order
     * @param string $time_zone
     * @param int $time_zone_offset
     * @return DataHolders\Order
     */
    public function save( DataHolders\Order $order, $time_zone, $time_zone_offset )
    {
        $item_key = 0;
        $orders_entity = new Entities\Order();
        $orders_entity
            ->setToken( Common::generateToken( get_class( $orders_entity ), 'token' ) )
            ->save();

        $this->userData->setOrderId( $orders_entity->getId() );

        foreach ( $this->getItems() as $cart_item ) {
            // Init.
            $payment_id = $order->hasPayment() ? $order->getPayment()->getId() : null;
            $service = $cart_item->getService();
            $series = null;
            $collaborative = null;
            $compound = null;

            // Whether to put this item on waiting list.
            $put_on_waiting_list = Config::waitingListActive() && get_option( 'bookly_waiting_list_enabled' ) && $cart_item->toBePutOnWaitingList();

            if ( $service->isCompound() ) {
                // Compound.
                $compound = DataHolders\Compound::create( $service )
                    ->setToken( Utils\Common::generateToken(
                        '\Bookly\Lib\Entities\CustomerAppointment',
                        'compound_token'
                    ) );
            } elseif ( $service->isCollaborative() ) {
                // Collaborative.
                $collaborative = DataHolders\Collaborative::create( $service )
                    ->setToken( Utils\Common::generateToken(
                        '\Bookly\Lib\Entities\CustomerAppointment',
                        'collaborative_token'
                    ) );
            } elseif ( $service->isPackage() ) {
                BookingProxy\Packages::createPackage( $order, $cart_item, $item_key );
                continue;
            }

            // Series.
            if ( $series_unique_id = $cart_item->getSeriesUniqueId() ) {
                if ( $order->hasItem( $series_unique_id ) ) {
                    $series = $order->getItem( $series_unique_id );
                } else {
                    $series_entity = new Entities\Series();
                    $series_entity
                        ->setRepeat( '{}' )
                        ->setToken( Common::generateToken( get_class( $series_entity ), 'token' ) )
                        ->save();

                    $series = DataHolders\Series::create( $series_entity );
                    $order->addItem( $series_unique_id, $series );
                }
                if ( get_option( 'bookly_recurring_appointments_payment' ) == 'first' && ! $cart_item->getFirstInSeries() ) {
                    // Link payment with the first item only.
                    $payment_id = null;
                }
            }

            $extras = $cart_item->distributeExtrasAcrossSlots();
            $custom_fields = $cart_item->getCustomFields();

            // For collaborative services we may need to find max duration to make all appointments of the same size.
            $collaborative_max_duration = null;
            $collaborative_extras_durations = array();
            if ( $collaborative && $service->getCollaborativeEqualDuration() ) {
                $consider_extras_duration = (bool) Proxy\ServiceExtras::considerDuration();
                foreach ( $cart_item->getSlots() as $key => $slot ) {
                    list ( $service_id ) = $slot;
                    $service = Entities\Service::find( $service_id );
                    $collaborative_extras_durations[ $key ] = $consider_extras_duration
                        ? (int) Proxy\ServiceExtras::getTotalDuration( $extras[ $key ] )
                        : 0;
                    $duration = $cart_item->getUnits() * $service->getDuration() + $collaborative_extras_durations[ $key ];
                    if ( $duration > $collaborative_max_duration ) {
                        $collaborative_max_duration = $duration;
                    }
                }
            }

            foreach ( $cart_item->getSlots() as $key => $slot ) {
                list ( $service_id, $staff_id, $start_datetime ) = $slot;

                $service = Entities\Service::find( $service_id );
                $item_duration = $collaborative_max_duration !== null
                    ? $collaborative_max_duration - $collaborative_extras_durations[ $key ]
                    : $cart_item->getUnits() * $service->getDuration();

                $end_datetime = $start_datetime !== null ? date( 'Y-m-d H:i:s', strtotime( $start_datetime ) + $item_duration ) : null;

                /*
                 * Get appointment with the same params.
                 * If it exists -> create connection to this appointment,
                 * otherwise create appointment and connect customer to new appointment
                 */
                $appointment = new Entities\Appointment();
                // Do not try to find appointment for tasks
                if ( $start_datetime !== null ) {
                    $appointment->loadBy( array(
                        'service_id' => $service_id,
                        'staff_id' => $staff_id,
                        'start_date' => $start_datetime,
                        'end_date' => $end_datetime,
                    ) );
                }
                if ( $appointment->isLoaded() ) {
                    $update = false;
                    if ( ! $appointment->getLocationId() && $cart_item->getLocationId() ) {
                        // Set location if it was not set previously.
                        $appointment->setLocationId( $cart_item->getLocationId() );
                        $update = true;
                    }
                    if ( $appointment->getStaffAny() == 1 && count( $cart_item->getStaffIds() ) == 1 ) {
                        // Remove marker Any for staff
                        $appointment->setStaffAny( 0 );
                        $update = true;
                    }
                    if ( $update ) {
                        $appointment->save();
                    }
                } else {
                    // Create new appointment.
                    $appointment
                        ->setLocationId( $cart_item->getLocationId() ?: null )
                        ->setServiceId( $service_id )
                        ->setStaffId( $staff_id )
                        ->setStaffAny( count( $cart_item->getStaffIds() ) > 1 )
                        ->setStartDate( $start_datetime )
                        ->setEndDate( $end_datetime )
                        ->save();
                }

                // Connect appointment with the cart item.
                $cart_item->setAppointmentId( $appointment->getId() );

                if ( $compound || $collaborative ) {
                    $service_custom_fields = Proxy\CustomFields::filterForService( $custom_fields, $service_id );
                } else {
                    $service_custom_fields = $custom_fields;
                }

                // Create CustomerAppointment record.
                $customer_appointment = new Entities\CustomerAppointment();
                $customer_appointment
                    ->setSeriesId( $series ? $series->getSeries()->getId() : null )
                    ->setCustomer( $order->getCustomer() )
                    ->setAppointment( $appointment )
                    ->setPaymentId( $payment_id )
                    ->setOrderId( $orders_entity->getId() )
                    ->setNumberOfPersons( $cart_item->getNumberOfPersons() )
                    ->setUnits( $cart_item->getUnits() )
                    ->setNotes( $this->userData->getNotes() )
                    ->setExtras( json_encode( $extras[ $key ] ) )
                    ->setCustomFields( json_encode( $service_custom_fields ) )
                    ->setStatus( $put_on_waiting_list
                        ? CustomerAppointment::STATUS_WAITLISTED
                        : Proxy\CustomerGroups::takeDefaultAppointmentStatus( Config::getDefaultAppointmentStatus(), $order->getCustomer()->getGroupId() ) )
                    ->setTimeZone( $time_zone )
                    ->setTimeZoneOffset( $time_zone_offset )
                    ->setCollaborativeServiceId( $collaborative ? $collaborative->getService()->getId() : null )
                    ->setCollaborativeToken( $collaborative ? $collaborative->getToken() : null )
                    ->setCompoundServiceId( $compound ? $compound->getService()->getId() : null )
                    ->setCompoundToken( $compound ? $compound->getToken() : null )
                    ->setCreatedFrom( 'frontend' )
                    ->setCreatedAt( current_time( 'mysql' ) )
                    ->save();

                $cart_item->setBookingNumber( Config::groupBookingActive() ? $appointment->getId() . '-' . $customer_appointment->getId() : $customer_appointment->getId() );
                Proxy\Files::attachFiles( $cart_item->getCustomFields(), $customer_appointment );

                // Handle extras duration.
                if ( Proxy\ServiceExtras::considerDuration() ) {
                    $appointment
                        ->setExtrasDuration( $appointment->getMaxExtrasDuration() )
                        ->save();
                }

                // Online meeting.
                Proxy\Shared::syncOnlineMeeting( array(), $appointment, $service );
                // Google Calendar.
                Proxy\Pro::syncGoogleCalendarEvent( $appointment );
                // Outlook Calendar.
                Proxy\OutlookCalendar::syncEvent( $appointment );

                // Add entities to result.
                $item = DataHolders\Simple::create( $customer_appointment )
                    ->setService( $service )
                    ->setAppointment( $appointment );

                if ( $compound ) {
                    $item = $compound->addItem( $item );
                } elseif ( $collaborative ) {
                    $item = $collaborative->addItem( $item );
                }
                if ( count( $item->getItems() ) === 1 ) {
                    if ( $series ) {
                        $series->addItem( $item_key++, $item );
                    } else {
                        $order->addItem( $item_key++, $item );
                    }
                }
            }
        }

        return $order;
    }

    /**
     * @param string $gateway
     * @param bool $apply_discounts apply coupon/gift card
     * @return CartInfo
     */
    public function getInfo( $gateway = null, $apply_discounts = true )
    {
        $cart_info = new CartInfo( $this->userData, $apply_discounts );

        if ( $gateway === Payment::TYPE_CLOUD_STRIPE ) {
            $cart_info->setGateway( $gateway );
        } elseif ( $gateway !== null ) {
            // Set gateway when add-on enabled
            $cart_info = Proxy\Shared::applyGateway( $cart_info, $gateway );
        }

        return $cart_info;
    }

    /**
     * Generate title of cart items (used in payments).
     *
     * @param int $max_length
     * @param bool $multi_byte
     * @return string
     */
    public function getItemsTitle( $max_length = 255, $multi_byte = true )
    {
        reset( $this->items );
        $title = $this->get( key( $this->items ) )->getService()->getTranslatedTitle();
        $tail = '';
        $more = count( $this->items ) - 1;
        if ( $more > 0 ) {
            $tail = sprintf( _n( ' and %d more item', ' and %d more items', $more, 'bookly' ), $more );
        }

        if ( $multi_byte ) {
            if ( preg_match_all( '/./su', $title . $tail, $matches ) > $max_length ) {
                $length_tail = preg_match_all( '/./su', $tail, $matches );
                $title = preg_replace( '/^(.{' . ( $max_length - $length_tail - 3 ) . '}).*/su', '$1', $title ) . '...';
            }
        } else {
            if ( strlen( $title . $tail ) > $max_length ) {
                while ( strlen( $title . $tail ) + 3 > $max_length ) {
                    $title = preg_replace( '/.$/su', '', $title );
                }
                $title .= '...';
            }
        }

        return $title . $tail;
    }

    /**
     * Return cart_key for not available appointment or NULL.
     *
     * @return int|null
     */
    public function getFailedKey()
    {
        $waiting_list_enabled = Config::waitingListActive() && get_option( 'bookly_waiting_list_enabled' );

        $max_date = Slots\DatePoint::now()
            ->modify( ( 1 + Config::getMaximumAvailableDaysForBooking() ) * DAY_IN_SECONDS )
            ->modify( '00:00:00' );

        foreach ( $this->items as $cart_key => $cart_item ) {
            if ( $cart_item->getService() ) {
                $service = $cart_item->getService();
                $with_sub_services = $service->withSubServices();
                foreach ( $cart_item->getSlots() as $slot ) {
                    if ( $waiting_list_enabled && isset ( $slot[4] ) && $slot[4] == 'w' ) {
                        // Booking is always available for slots being placed on waiting list.
                        continue;
                    }
                    list ( $service_id, $staff_id, $datetime ) = $slot;
                    if ( $datetime === null ) {
                        // Booking is always available for tasks.
                        continue;
                    }
                    if ( $with_sub_services ) {
                        $service = Entities\Service::find( $service_id );
                    }

                    $bound_start = Slots\DatePoint::fromStr( $datetime );
                    $bound_end = Slots\DatePoint::fromStr( $datetime )->modify( ( (int) $service->getDuration() * $cart_item->getUnits() ) . ' sec' );
                    if ( Config::proActive() ) {
                        $bound_start->modify( '-' . (int) $service->getPaddingLeft() . ' sec' );
                        $bound_end->modify( ( (int) $service->getPaddingRight() + $cart_item->getExtrasDuration() ) . ' sec' );
                    }

                    if ( $bound_end->lte( $max_date ) ) {
                        $query = Entities\CustomerAppointment::query( 'ca' )
                            ->select(
                                sprintf(
                                    'ss.capacity_max, SUM(ca.number_of_persons) AS total_number_of_persons, s.one_booking_per_slot,
                                DATE_SUB(a.start_date, INTERVAL %s SECOND) AS bound_left,
                                DATE_ADD(a.end_date, INTERVAL (%s + a.extras_duration) SECOND) AS bound_right',
                                    Proxy\Shared::prepareStatement( 0, 'COALESCE(s.padding_left,0)', 'Service' ),
                                    Proxy\Shared::prepareStatement( 0, 'COALESCE(s.padding_right,0)', 'Service' )
                                )
                            )
                            ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
                            ->leftJoin( 'StaffService', 'ss', 'ss.staff_id = a.staff_id AND ss.service_id = a.service_id' )
                            ->leftJoin( 'Service', 's', 's.id = a.service_id' )
                            ->where( 'a.staff_id', $staff_id )
                            ->whereIn(
                                'ca.status', Proxy\CustomStatuses::prepareBusyStatuses( array(
                                Entities\CustomerAppointment::STATUS_PENDING,
                                Entities\CustomerAppointment::STATUS_APPROVED,
                            ) )
                            )
                            ->groupBy( 'a.service_id, a.start_date' )
                            ->havingRaw( '%s > bound_left AND bound_right > %s AND IF ( one_booking_per_slot = 0, ( total_number_of_persons + %d ) > ss.capacity_max, total_number_of_persons > 0 )',
                                array( $bound_end->format( 'Y-m-d H:i:s' ), $bound_start->format( 'Y-m-d H:i:s' ), $cart_item->getNumberOfPersons() ) )
                            ->limit( 1 );
                        if ( Config::locationsActive() && get_option( 'bookly_locations_allow_services_per_location' ) ) {
                            $location_id = isset( $slot[3] ) ? $slot[3] : 0;
                            $query
                                ->leftJoin( 'StaffLocation', 'sl', 'sl.staff_id = ss.staff_id', '\BooklyLocations\Lib\Entities' )
                                ->where( 'sl.location_id', $location_id )
                                ->whereRaw( '( ss.location_id IS NULL AND sl.custom_services = 0 ) OR ( ss.location_id IS NOT NULL AND sl.custom_services = 1 AND sl.location_id = ss.location_id )', array() );
                        } else {
                            $query->where( 'ss.location_id', null );
                        }
                        $rows = $query->execute( Query::HYDRATE_NONE );

                        if ( $rows != 0 ) {
                            // Intersection of appointments exists, time is not available.
                            return $cart_key;
                        }
                    } else {
                        return $cart_key;
                    }
                }
            }
        }

        return null;
    }

}