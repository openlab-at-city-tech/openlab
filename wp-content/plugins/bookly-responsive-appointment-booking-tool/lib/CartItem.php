<?php
namespace Bookly\Lib;

/**
 * Class CartItem
 * @package Bookly\Lib
 */
class CartItem
{
    // Step service
    /** @var  int */
    protected $location_id;
    /** @var  int */
    protected $service_id;
    /** @var  array */
    protected $staff_ids;
    /** @var  int */
    protected $number_of_persons;
    /** @var  string Y-m-d */
    protected $date_from;
    /** @var  array */
    protected $days;
    /** @var  string H:i */
    protected $time_from;
    /** @var  string H:i */
    protected $time_to;
    /** @var  int */
    protected $units;

    // Step extras
    /** @var  array */
    protected $extras = array();
    /** @var  bool */
    protected $consider_extras_duration = true;

    // Step time
    /** @var  array */
    protected $slots;

    // Step details
    /** @var  array */
    protected $custom_fields = array();
    /** @var  int */
    protected $series_unique_id = 0;
    /** @var  bool */
    protected $first_in_series = false;

    // Step done
    /** @var  int */
    protected $appointment_id;
    /** @var  string */
    protected $booking_number;

    // Add here the properties that don't need to be returned in getData

    /**
     * Constructor.
     */
    public function __construct() { }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        return get_object_vars( $this );
    }

    /**
     * Set data.
     *
     * @param array $data
     * @return $this
     */
    public function setData( array $data )
    {
        foreach ( $data as $name => $value ) {
            $this->{$name} = $value;
        }

        return $this;
    }

    /**
     * Get appointment.
     *
     * @return Entities\Appointment|false
     */
    public function getAppointment()
    {
        return Entities\Appointment::find( $this->appointment_id );
    }

    /**
     * Get service.
     *
     * @return Entities\Service
     */
    public function getService()
    {
        return Entities\Service::find( $this->service_id );
    }

    /**
     * Get service price.
     *
     * @param int $nop
     * @return float
     */
    public function getServicePrice( $nop = 1 )
    {
        $price = $this->getServicePriceWithoutExtras();

        $price = Proxy\ServiceExtras::prepareServicePrice( $price * $nop, $price, $nop, $this->extras );

        return Proxy\Discounts::prepareServicePrice( $price, $this->service_id, $nop );
    }

    /**
     * Get service price.
     *
     * @return double
     */
    public function getServicePriceWithoutExtras()
    {
        static $service_prices_cache = array();

        $service = $this->getService();
        if ( $service->withSubServices() ) {
            $service_price = $service->getPrice();
        } else {
            $date_time = null;
            if ( $this->slots === null ) {
                $service_id = $this->service_id;
                $staff_id = current( $this->staff_ids );
                $location_id = $this->location_id;
            } else {
                list ( $service_id, $staff_id, $date_time, $location_id ) = $this->slots[0];
            }

            if ( Config::specialHoursActive() ) {
                $service_start = $this->slots === null || $date_time === null
                    ? 'unused'
                    : date( 'H:i:s', strtotime( $date_time ) );
            } else {
                $service_start = 'unused'; //the price is the same for all services in day
            }
            if ( isset ( $service_prices_cache[ $staff_id ][ $service_id ][ $location_id ][ $service_start ][ $this->getUnits() ] ) ) {
                $service_price = $service_prices_cache[ $staff_id ][ $service_id ][ $location_id ][ $service_start ][ $this->getUnits() ];
            } else {
                $staff_service = new Entities\StaffService();
                $location_id = Proxy\Locations::prepareStaffLocationId( $location_id, $staff_id ) ?: null;
                $staff_service->loadBy( compact( 'staff_id', 'service_id', 'location_id' ) );
                if ( ! $staff_service->isLoaded() ) {
                    $staff_service->loadBy( array( 'staff_id' => $staff_id, 'service_id' => $service_id, 'location_id' => null ) );
                }
                $service_price = $staff_service->getPrice() * $this->getUnits();
                if ( $this->slots && $date_time ) {
                    $service_price = Proxy\SpecialHours::adjustPrice( $service_price, $staff_id, $service_id, $location_id, $service_start, $this->getUnits(), date( 'w', strtotime( $date_time ) ) + 1 );
                }
                $service_prices_cache[ $staff_id ][ $service_id ][ $location_id ][ $service_start ][ $this->getUnits() ] = $service_price;
            }
        }

        return $service_price;
    }

    /**
     * Get service deposit.
     *
     * @return string
     */
    public function getDeposit()
    {
        $service = Entities\Service::find( $this->service_id );
        if ( $service && $service->withSubServices() ) {
            return $service->getDeposit();
        }
        list ( $service_id, $staff_id, , $location_id ) = $this->slots[0];
        $staff_service = new Entities\StaffService();
        $location_id = Proxy\Locations::prepareStaffLocationId( $location_id, $staff_id  ) ?: null;
        $staff_service->loadBy( compact( 'staff_id', 'service_id', 'location_id' ) );
        if ( ! $staff_service->isLoaded() ) {
            $staff_service->loadBy( array( 'staff_id' => $staff_id, 'service_id' => $service_id, 'location_id' => null ) );
        }

        return $staff_service->getDeposit();
    }

    /**
     * Get service deposit price.
     *
     * @return double
     */
    public function getDepositPrice()
    {
        $nop = $this->number_of_persons;

        return Proxy\DepositPayments::prepareAmount( $this->getServicePrice( $nop ), $this->getDeposit(), $nop );
    }

    /**
     * Get staff ID.
     *
     * @return int
     */
    public function getStaffId()
    {
        return (int) $this->slots[0][1];
    }

    /**
     * Get staff.
     *
     * @return Entities\Staff
     */
    public function getStaff()
    {
        return Entities\Staff::find( $this->getStaffId() );
    }

    /**
     * Get duration of service's extras.
     *
     * @return int
     * @todo The result may be incorrect for compound and collaborative services.
     */
    public function getExtrasDuration()
    {
        return $this->consider_extras_duration
            ? (int) Proxy\ServiceExtras::getTotalDuration( $this->extras )
            : 0;
    }

    /**
     * Distribute extras across slots.
     *
     * @return array
     */
    public function distributeExtrasAcrossSlots()
    {
        $result = array();

        $with_sub_services = $this->getService()->withSubServices();
        $extras = $this->getExtras();
        foreach ( $this->getSlots() as $key => $slot ) {
            list ( $service_id, $staff_id, $datetime ) = $slot;
            $service = Entities\Service::find( $service_id );

            if ( $with_sub_services ) {
                $result[ $key ] = array();
                foreach ( $service->getExtras() as $item ) {
                    $extras_id = $item->getId();
                    if ( isset ( $extras[ $extras_id ] ) ) {
                        $result[ $key ][ $extras_id ] = $extras[ $extras_id ];
                        // Extras are assigned only to one/unique service,
                        // and won't be multiplied across.
                        unset ( $extras[ $extras_id ] );
                    }
                }
            } else {
                $result[ $key ] = $extras;
            }
        }

        return $result;
    }

    /**
     * @param int $service_id
     * @return bool
     */
    public function isFirstSubService( $service_id )
    {
        return $this->slots[0][0] == $service_id;
    }

    /**
     * Tells whether this cart item is going to be put on waiting list.
     *
     * @return bool
     */
    public function toBePutOnWaitingList()
    {
        foreach ( $this->slots as $slot ) {
            if ( isset ( $slot[4] ) && $slot[4] == 'w' ) {

                return true;
            }
        }

        return false;
    }

    /**************************************************************************
     * Getters & Setters                                                      *
     **************************************************************************/

    /**
     * Gets appointment_id
     *
     * @return int
     */
    public function getAppointmentId()
    {
        return $this->appointment_id;
    }

    /**
     * Sets appointment_id
     *
     * @param int $appointment_id
     * @return $this
     */
    public function setAppointmentId( $appointment_id )
    {
        $this->appointment_id = $appointment_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getBookingNumber()
    {
        return $this->booking_number;
    }

    /**
     * @param string $booking_number
     * @return CartItem
     */
    public function setBookingNumber( $booking_number )
    {
        $this->booking_number = $booking_number;

        return $this;
    }

    /**
     * Gets location_id
     *
     * @return int
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * Sets location_id
     *
     * @param int $location_id
     * @return $this
     */
    public function setLocationId( $location_id )
    {
        $this->location_id = $location_id;

        return $this;
    }

    /**
     * Gets service_id
     *
     * @return int
     */
    public function getServiceId()
    {
        return $this->service_id;
    }

    /**
     * Sets service_id
     *
     * @param int $service_id
     * @return $this
     */
    public function setServiceId( $service_id )
    {
        $this->service_id = $service_id;

        return $this;
    }

    /**
     * Gets staff_ids
     *
     * @return array
     */
    public function getStaffIds()
    {
        return $this->staff_ids;
    }

    /**
     * Sets staff_ids
     *
     * @param array $staff_ids
     * @return $this
     */
    public function setStaffIds( $staff_ids )
    {
        $this->staff_ids = $staff_ids;

        return $this;
    }

    /**
     * Gets number_of_persons
     *
     * @return int
     */
    public function getNumberOfPersons()
    {
        return $this->number_of_persons;
    }

    /**
     * Sets number_of_persons
     *
     * @param int $number_of_persons
     * @return $this
     */
    public function setNumberOfPersons( $number_of_persons )
    {
        $this->number_of_persons = $number_of_persons;

        return $this;
    }

    /**
     * Gets units
     *
     * @return int
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Sets units
     *
     * @param int $units
     * @return $this
     */
    public function setUnits( $units )
    {
        $this->units = $units;

        return $this;
    }

    /**
     * Gets date_from
     *
     * @return string
     */
    public function getDateFrom()
    {
        return $this->date_from;
    }

    /**
     * Sets date_from
     *
     * @param string $date_from
     * @return $this
     */
    public function setDateFrom( $date_from )
    {
        $this->date_from = $date_from;

        return $this;
    }

    /**
     * Gets days
     *
     * @return array
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Sets days
     *
     * @param array $days
     * @return $this
     */
    public function setDays( $days )
    {
        $this->days = $days;

        return $this;
    }

    /**
     * Gets time_from
     *
     * @return string
     */
    public function getTimeFrom()
    {
        return $this->time_from;
    }

    /**
     * Sets time_from
     *
     * @param string $time_from
     * @return $this
     */
    public function setTimeFrom( $time_from )
    {
        $this->time_from = $time_from;

        return $this;
    }

    /**
     * Gets time_to
     *
     * @return string
     */
    public function getTimeTo()
    {
        return $this->time_to;
    }

    /**
     * Sets time_to
     *
     * @param string $time_to
     * @return $this
     */
    public function setTimeTo( $time_to )
    {
        $this->time_to = $time_to;

        return $this;
    }

    /**
     * Gets extras
     *
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Sets extras
     *
     * @param array $extras
     * @return $this
     */
    public function setExtras( $extras )
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Gets slots
     *
     * @return array
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * Sets slots
     *
     * @param array $slots
     * @return $this
     */
    public function setSlots( $slots )
    {
        $this->slots = $slots;

        return $this;
    }

    /**
     * Gets custom_fields
     *
     * @return array
     */
    public function getCustomFields()
    {
        return $this->custom_fields;
    }

    /**
     * Sets custom_fields
     *
     * @param array $custom_fields
     * @return $this
     */
    public function setCustomFields( $custom_fields )
    {
        $this->custom_fields = $custom_fields;

        return $this;
    }

    /**
     * Gets series_unique_id
     *
     * @return int
     */
    public function getSeriesUniqueId()
    {
        return (int) $this->series_unique_id;
    }

    /**
     * Sets series_unique_id
     *
     * @param int $series_unique_id
     * @return $this
     */
    public function setSeriesUniqueId( $series_unique_id )
    {
        $this->series_unique_id = $series_unique_id;

        return $this;
    }

    /**
     * Gets first_in_series
     *
     * @return bool
     */
    public function getFirstInSeries()
    {
        return $this->first_in_series;
    }

    /**
     * Sets first_in_series
     *
     * @param bool $first_in_series
     * @return $this
     */
    public function setFirstInSeries( $first_in_series )
    {
        $this->first_in_series = $first_in_series;

        return $this;
    }

    /**
     * Sets consider_extras_duration
     *
     * @param bool $consider_extras_duration
     * @return $this
     */
    public function setConsiderExtrasDuration( $consider_extras_duration )
    {
        $this->consider_extras_duration = $consider_extras_duration;

        return $this;
    }

}