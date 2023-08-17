<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

/**
 * Class Service
 *
 * @package Bookly\Lib\Entities
 */
class Service extends Lib\Base\Entity
{
    const TYPE_SIMPLE        = 'simple';
    const TYPE_COLLABORATIVE = 'collaborative';
    const TYPE_COMPOUND      = 'compound';
    const TYPE_PACKAGE       = 'package';

    const PREFERRED_ORDER                     = 'order';
    const PREFERRED_LEAST_OCCUPIED            = 'least_occupied';
    const PREFERRED_MOST_OCCUPIED             = 'most_occupied';
    const PREFERRED_LEAST_OCCUPIED_FOR_PERIOD = 'least_occupied_for_period';
    const PREFERRED_MOST_OCCUPIED_FOR_PERIOD  = 'most_occupied_for_period';
    const PREFERRED_LEAST_EXPENSIVE           = 'least_expensive';
    const PREFERRED_MOST_EXPENSIVE            = 'most_expensive';

    const VISIBILITY_PUBLIC      = 'public';
    const VISIBILITY_PRIVATE     = 'private';
    const VISIBILITY_GROUP_BASED = 'group';

    const START_TIME_REQUIRED = 'required';
    const START_TIME_OPTIONAL = 'optional';
    const START_TIME_OFF      = 'off';

    const SLOT_LENGTH_DEFAULT             = 'default';
    const SLOT_LENGTH_AS_SERVICE_DURATION = 'as_service_duration';

    /** @var  int */
    protected $category_id;
    /** @var  string */
    protected $type = 'simple';
    /** @var  string */
    protected $title;
    /** @var  int */
    protected $attachment_id;
    /** @var  int */
    protected $duration = 900;
    /** @var  string */
    protected $slot_length = 'default';
    /** @var  float */
    protected $price = 0;
    /** @var  string */
    protected $color;
    /** @var  string */
    protected $deposit = '100%';
    /** @var  int */
    protected $capacity_min = 1;
    /** @var  int */
    protected $capacity_max = 1;
    /** @var  int */
    protected $waiting_list_capacity;
    /** @var  int */
    protected $one_booking_per_slot = 0;
    /** @var  int */
    protected $padding_left = 0;
    /** @var  int */
    protected $padding_right = 0;
    /** @var  string */
    protected $info;
    /** @var  string */
    protected $start_time_info;
    /** @var  string */
    protected $end_time_info;
    /** @var  int */
    protected $package_life_time;
    /** @var  int */
    protected $package_size;
    /** @var  bool */
    protected $package_unassigned = 0;
    /** @var  int */
    protected $appointments_limit;
    /** @var  string */
    protected $limit_period = 'off';
    /** @var  string */
    protected $staff_preference = Service::PREFERRED_MOST_EXPENSIVE;
    /** @var  string */
    protected $staff_preference_settings = '{}';
    /** @var  bool */
    protected $recurrence_enabled = 1;
    /** @var  string */
    protected $recurrence_frequencies = 'daily,weekly,biweekly,monthly';
    /** @var  bool */
    protected $same_staff_for_subservices = 0;
    /** @var  int */
    protected $units_min = 1;
    /** @var  int */
    protected $units_max = 1;
    /** @var  int */
    protected $time_requirements = Service::START_TIME_REQUIRED;
    /** @var  bool */
    protected $collaborative_equal_duration = 0;
    /** @var  string */
    protected $online_meetings = 'off';
    /** @var  string */
    protected $final_step_url = '';
    /** @var  int */
    protected $wc_product_id = 0;
    /** @var  string */
    protected $wc_cart_info_name;
    /** @var  string */
    protected $wc_cart_info;
    /** @var  int */
    protected $min_time_prior_booking;
    /** @var  int */
    protected $min_time_prior_cancel;
    /** @var string */
    protected $gateways;
    /** @var  string */
    protected $visibility = Service::VISIBILITY_PUBLIC;
    /** @var  int */
    protected $position;

    protected static $table = 'bookly_services';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'category_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Category' ) ),
        'type' => array( 'format' => '%s' ),
        'title' => array( 'format' => '%s' ),
        'attachment_id' => array( 'format' => '%d' ),
        'duration' => array( 'format' => '%d' ),
        'slot_length' => array( 'format' => '%s' ),
        'price' => array( 'format' => '%f' ),
        'color' => array( 'format' => '%s' ),
        'deposit' => array( 'format' => '%s' ),
        'capacity_min' => array( 'format' => '%d' ),
        'capacity_max' => array( 'format' => '%d' ),
        'waiting_list_capacity' => array( 'format' => '%d' ),
        'one_booking_per_slot' => array( 'format' => '%d' ),
        'padding_left' => array( 'format' => '%d' ),
        'padding_right' => array( 'format' => '%d' ),
        'info' => array( 'format' => '%s' ),
        'start_time_info' => array( 'format' => '%s' ),
        'end_time_info' => array( 'format' => '%s' ),
        'package_life_time' => array( 'format' => '%d' ),
        'package_size' => array( 'format' => '%d' ),
        'package_unassigned' => array( 'format' => '%d' ),
        'appointments_limit' => array( 'format' => '%d' ),
        'limit_period' => array( 'format' => '%s' ),
        'staff_preference' => array( 'format' => '%s' ),
        'staff_preference_settings' => array( 'format' => '%s' ),
        'recurrence_enabled' => array( 'format' => '%d' ),
        'recurrence_frequencies' => array( 'format' => '%s' ),
        'same_staff_for_subservices' => array( 'format' => '%d' ),
        'units_min' => array( 'format' => '%d' ),
        'units_max' => array( 'format' => '%d' ),
        'time_requirements' => array( 'format' => '%s' ),
        'collaborative_equal_duration' => array( 'format' => '%d' ),
        'online_meetings' => array( 'format' => '%s' ),
        'final_step_url' => array( 'format' => '%s' ),
        'wc_product_id' => array( 'format' => '%d' ),
        'wc_cart_info_name' => array( 'format' => '%s' ),
        'wc_cart_info' => array( 'format' => '%s' ),
        'min_time_prior_booking' => array( 'format' => '%d' ),
        'min_time_prior_cancel' => array( 'format' => '%d' ),
        'gateways' => array( 'format' => '%s' ),
        'visibility' => array( 'format' => '%s' ),
        'position' => array( 'format' => '%d', 'sequent' => true ),
    );

    /** @var \BooklyServiceExtras\Lib\Entities\ServiceExtra[] */
    protected $extras;

    /**
     * Get translated title (if empty returns "Untitled").
     *
     * @param string $locale
     * @return string
     */
    public function getTranslatedTitle( $locale = null )
    {
        return $this->getTitle() != ''
            ? Lib\Utils\Common::getTranslatedString( 'service_' . $this->getId(), $this->getTitle(), $locale )
            : __( 'Untitled', 'bookly' );
    }

    /**
     * Get category name.
     *
     * @param string $locale
     * @return string
     */
    public function getTranslatedCategoryName( $locale = null )
    {
        if ( $this->getCategoryId() ) {
            return Category::find( $this->getCategoryId() )->getTranslatedName( $locale );
        }

        return __( 'Uncategorized', 'bookly' );
    }

    /**
     * Get translated info.
     *
     * @param string $locale
     * @return string
     */
    public function getTranslatedInfo( $locale = null )
    {
        return Lib\Utils\Common::getTranslatedString( 'service_' . $this->getId() . '_info', $this->getInfo(), $locale );
    }

    /**
     * Get sub services.
     *
     * @return Service[]
     */
    public function getSubServices()
    {
        return self::query( 's' )
            ->select( 's.*' )
            ->innerJoin( 'SubService', 'ss', 'ss.sub_service_id = s.id' )
            ->where( 'ss.service_id', $this->getId() )
            ->sortBy( 'ss.position' )
            ->find();
    }

    /**
     * Get min duration for service.
     *
     * @return float|int
     */
    public function getMinDuration()
    {
        return $this->duration * $this->units_min;
    }

    /**
     * Get max duration for service.
     *
     * @return float|int
     */
    public function getMaxDuration()
    {
        return $this->duration * $this->units_max;
    }

    /**
     * Get extras associated with service.
     *
     * @return \BooklyServiceExtras\Lib\Entities\ServiceExtra[]
     */
    public function getExtras()
    {
        if ( $this->extras === null ) {
            $this->extras = (array) Lib\Proxy\ServiceExtras::findByServiceId( $this->getId() );
        }

        return $this->extras;
    }

    /**
     * Check if given customer has reached the appointments limit for this service.
     *
     * @param int $customer_id
     * @param array $appointment_dates format( 'Y-m-d H:i:s' )
     * @return bool
     */
    public function appointmentsLimitReached( $customer_id, array $appointment_dates )
    {
        if ( Lib\Config::proActive() && $this->getLimitPeriod() != 'off' && $this->getAppointmentsLimit() > 0 ) {
            if ( $this->isCompound() ) {
                // Compound service.
                $sub_services = $this->getSubServices();
                $compound_service_id = $this->getId();
                $collaborative_service_id = null;
                $service_id = $sub_services[0]->getId();
            } elseif ( $this->isCollaborative() ) {
                // Collaborative service.
                $sub_services = $this->getSubServices();
                $compound_service_id = null;
                $collaborative_service_id = $this->getId();
                $service_id = $sub_services[0]->getId();
            } else {
                // Simple service.
                $compound_service_id = null;
                $collaborative_service_id = null;
                $service_id = $this->getId();
            }
            $statuses = get_option( 'bookly_cst_limit_statuses', array() );
            switch ( $this->getLimitPeriod() ) {
                case 'upcoming':
                    $db_count = CustomerAppointment::query( 'ca' )
                        ->leftJoin( 'Appointment', 'a', 'ca.appointment_id = a.id' )
                        ->where( 'a.service_id', $service_id )
                        ->where( 'ca.compound_service_id', $compound_service_id )
                        ->where( 'ca.collaborative_service_id', $collaborative_service_id )
                        ->where( 'ca.customer_id', $customer_id )
                        ->whereGt( 'a.start_date', current_time( 'mysql' ) )
                        ->whereNotIn( 'ca.status', $statuses )
                        ->count();
                    if ( $db_count + count( $appointment_dates ) > $this->getAppointmentsLimit() ) {
                        return true;
                    }
                    break;
                default:
                    foreach ( $appointment_dates as $appointment_date ) {
                        $regarding_appointment = false;
                        switch ( $this->getLimitPeriod() ) {
                            case 'calendar_day':
                                $bound_start = date_create( $appointment_date )->format( 'Y-m-d 00:00:00' );
                                $bound_end = date_create( $appointment_date )->format( 'Y-m-d 23:59:59' );
                                break;
                            case 'calendar_week':
                                $week_day = date_create( $appointment_date )->format( 'w' );
                                $start_week = (int) get_option( 'start_of_week' );
                                $delta = $week_day < $start_week ? $start_week + $week_day - 7 : $start_week - $week_day;
                                $start_date = date_create( $appointment_date )->modify( $delta . ' day' );
                                $bound_start = $start_date->format( 'Y-m-d 00:00:00' );
                                $bound_end = $start_date->modify( '+6 day' )->format( 'Y-m-d 23:59:59' );
                                break;
                            case 'calendar_month':
                                $bound_start = date_create( $appointment_date )->modify( 'first day of this month' )->format( 'Y-m-d 00:00:00' );
                                $bound_end = date_create( $appointment_date )->modify( 'last day of this month' )->format( 'Y-m-d 23:59:59' );
                                break;
                            case 'calendar_year':
                                $bound_start = date_create( $appointment_date )->modify( 'first day of January' )->format( 'Y-m-d 00:00:00' );
                                $bound_end = date_create( $appointment_date )->modify( 'last day of December' )->format( 'Y-m-d 23:59:59' );
                                break;

                            case 'day':
                                $bound_start = date_create( $appointment_date )->modify( '-1 day' )->format( 'Y-m-d H:i:s' );
                                $bound_end = $appointment_date;
                                $regarding_appointment = true;
                                break;
                            case 'week':
                                $bound_start = date_create( $appointment_date )->modify( '-1 week' )->format( 'Y-m-d H:i:s' );
                                $bound_end = $appointment_date;
                                $regarding_appointment = true;
                                break;
                            case 'month':
                                $bound_start = date_create( $appointment_date )->modify( '-30 days' )->format( 'Y-m-d H:i:s' );
                                $bound_end = $appointment_date;
                                $regarding_appointment = true;
                                break;
                            case 'year':
                                $bound_start = date_create( $appointment_date )->modify( '-365 days' )->format( 'Y-m-d H:i:s' );
                                $bound_end = $appointment_date;
                                $regarding_appointment = true;
                                break;
                        }
                        $query = CustomerAppointment::query( 'ca' )
                            ->leftJoin( 'Appointment', 'a', 'ca.appointment_id = a.id' )
                            ->where( 'a.service_id', $service_id )
                            ->where( 'ca.compound_service_id', $compound_service_id )
                            ->where( 'ca.customer_id', $customer_id )
                            ->whereNotIn( 'ca.status', $statuses );

                        if ( $regarding_appointment ) {
                            $query
                                ->whereGt( 'a.start_date', $bound_start )
                                ->whereLte( 'a.start_date', $bound_end );
                        } else {
                            $query
                                ->whereGte( 'a.start_date', $bound_start )
                                ->whereLt( 'a.start_date', $bound_end );
                        }

                        $db_count = $query->count();
                        $cart_count = 0;
                        $bound_start = strtotime( $bound_start );
                        $bound_end = strtotime( $bound_end );
                        foreach ( $appointment_dates as $date ) {
                            $cur_date = strtotime( $date );
                            if ( $cur_date <= $bound_end && $cur_date >= $bound_start ) {
                                $cart_count++;
                            }
                        }
                        if ( $db_count + $cart_count > $this->getAppointmentsLimit() ) {
                            return true;
                        }
                    }
                    break;
            }
        }

        return false;
    }

    /**
     * Check if service is a collaborative service.
     *
     * @return bool
     */
    public function isCollaborative()
    {
        return $this->getType() == self::TYPE_COLLABORATIVE;
    }

    /**
     * Check if service is a compound service.
     *
     * @return bool
     */
    public function isCompound()
    {
        return $this->getType() == self::TYPE_COMPOUND;
    }

    /**
     * Check if service is a package.
     *
     * @return bool
     */
    public function isPackage()
    {
        return $this->getType() == self::TYPE_PACKAGE;
    }

    /**
     * Check whether service should have sub services or not.
     *
     * @return bool
     */
    public function withSubServices()
    {
        return $this->isCompound() || $this->isCollaborative();
    }

    /**
     * Get service image url
     *
     * @param string $size
     *
     * @return string
     */
    public function getImageUrl( $size = 'full' )
    {
        return $this->attachment_id
            ? Lib\Utils\Common::getAttachmentUrl( $this->attachment_id, $size )
            : '';
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets category_id
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * Sets category
     *
     * @param Lib\Entities\Category $category
     * @return $this
     */
    public function setCategory( Lib\Entities\Category $category )
    {
        return $this->setCategoryId( $category->getId() );
    }

    /**
     * Sets category_id
     *
     * @param int $category_id
     * @return $this
     */
    public function setCategoryId( $category_id )
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * Gets type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets type
     *
     * @param string $type
     * @return $this
     */
    public function setType( $type )
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle( $title )
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets attachment_id
     *
     * @return int
     */
    public function getAttachmentId()
    {
        return $this->attachment_id;
    }

    /**
     * Sets attachment_id
     *
     * @param int $attachment_id
     * @return $this
     */
    public function setAttachmentId( $attachment_id )
    {
        $this->attachment_id = $attachment_id;

        return $this;
    }

    /**
     * Gets duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Sets duration
     *
     * @param int $duration
     * @return $this
     */
    public function setDuration( $duration )
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Gets slot length
     *
     * @return string
     */
    public function getSlotLength()
    {
        return $this->slot_length;
    }

    /**
     * Sets slot length
     *
     * @param string $slot_length
     * @return $this
     */
    public function setSlotLength( $slot_length )
    {
        $this->slot_length = $slot_length;

        return $this;
    }

    /**
     * Gets price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice( $price )
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Gets color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Sets color
     *
     * @param string $color
     * @return $this
     */
    public function setColor( $color )
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Gets deposit
     *
     * @return string
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * Sets deposit
     *
     * @param string $deposit
     * @return $this
     */
    public function setDeposit( $deposit )
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * Gets capacity_min
     *
     * @return int
     */
    public function getCapacityMin()
    {
        return $this->capacity_min;
    }

    /**
     * Sets capacity_min
     *
     * @param int $capacity_min
     * @return $this
     */
    public function setCapacityMin( $capacity_min )
    {
        $this->capacity_min = $capacity_min;

        return $this;
    }

    /**
     * Gets capacity_max
     *
     * @return int
     */
    public function getCapacityMax()
    {
        return $this->capacity_max;
    }

    /**
     * Sets capacity_max
     *
     * @param int $capacity_max
     * @return $this
     */
    public function setCapacityMax( $capacity_max )
    {
        $this->capacity_max = $capacity_max;

        return $this;
    }

    /**
     * Gets waiting_list_capacity
     *
     * @return int
     */
    public function getWaitingListCapacity()
    {
        return $this->waiting_list_capacity;
    }

    /**
     * Sets waiting_list_capacity
     *
     * @param int $waiting_list_capacity
     * @return $this
     */
    public function setWaitingListCapacity( $waiting_list_capacity )
    {
        $this->waiting_list_capacity = $waiting_list_capacity;

        return $this;
    }

    /**
     * Gets one_booking_per_slot
     *
     * @return int
     */
    public function getOneBookingPerSlot()
    {
        return $this->one_booking_per_slot;
    }

    /**
     * Sets one_booking_per_slot
     *
     * @param int $one_booking_per_slot
     * @return $this
     */
    public function setOneBookingPerSlot( $one_booking_per_slot )
    {
        $this->one_booking_per_slot = $one_booking_per_slot;

        return $this;
    }

    /**
     * Gets padding_left
     *
     * @return int
     */
    public function getPaddingLeft()
    {
        return $this->padding_left;
    }

    /**
     * Sets padding_left
     *
     * @param int $padding_left
     * @return $this
     */
    public function setPaddingLeft( $padding_left )
    {
        $this->padding_left = $padding_left;

        return $this;
    }

    /**
     * Gets padding_right
     *
     * @return int
     */
    public function getPaddingRight()
    {
        return $this->padding_right;
    }

    /**
     * Sets padding_right
     *
     * @param int $padding_right
     * @return $this
     */
    public function setPaddingRight( $padding_right )
    {
        $this->padding_right = $padding_right;

        return $this;
    }

    /**
     * Gets info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Sets info
     *
     * @param string $info
     * @return $this
     */
    public function setInfo( $info )
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Gets start time info
     *
     * @return string
     */
    public function getStartTimeInfo()
    {
        return $this->start_time_info;
    }

    /**
     * Sets start time info
     *
     * @param string $start_time_info
     * @return $this
     */
    public function setStartTimeInfo( $start_time_info )
    {
        $this->start_time_info = $start_time_info;

        return $this;
    }

    /**
     * Gets end time info
     *
     * @return string
     */
    public function getEndTimeInfo()
    {
        return $this->end_time_info;
    }

    /**
     * Sets end time info
     *
     * @param string $end_time_info
     * @return $this
     */
    public function setEndTimeInfo( $end_time_info )
    {
        $this->end_time_info = $end_time_info;

        return $this;
    }

    /**
     * Gets package_life_time
     *
     * @return int
     */
    public function getPackageLifeTime()
    {
        return $this->package_life_time;
    }

    /**
     * Sets package_life_time
     *
     * @param int $package_life_time
     * @return $this
     */
    public function setPackageLifeTime( $package_life_time )
    {
        $this->package_life_time = $package_life_time;

        return $this;
    }

    /**
     * Gets package_size
     *
     * @return int
     */
    public function getPackageSize()
    {
        return $this->package_size;
    }

    /**
     * Sets package_size
     *
     * @param int $package_size
     * @return $this
     */
    public function setPackageSize( $package_size )
    {
        $this->package_size = $package_size;

        return $this;
    }

    /**
     * Gets package_unassigned
     *
     * @return int
     */
    public function getPackageUnassigned()
    {
        return $this->package_unassigned;
    }

    /**
     * Sets package_unassigned
     *
     * @param int $package_unassigned
     * @return $this
     */
    public function setPackageUnassigned( $package_unassigned )
    {
        $this->package_unassigned = $package_unassigned;

        return $this;
    }

    /**
     * Gets appointments_limit
     *
     * @return int
     */
    public function getAppointmentsLimit()
    {
        return $this->appointments_limit;
    }

    /**
     * Sets appointments_limit
     *
     * @param int $appointments_limit
     * @return $this
     */
    public function setAppointmentsLimit( $appointments_limit )
    {
        $this->appointments_limit = $appointments_limit;

        return $this;
    }

    /**
     * Gets limit_period
     *
     * @return string
     */
    public function getLimitPeriod()
    {
        return $this->limit_period;
    }

    /**
     * Sets limit_period
     *
     * @param string $limit_period
     * @return $this
     */
    public function setLimitPeriod( $limit_period )
    {
        $this->limit_period = $limit_period;

        return $this;
    }

    /**
     * Gets staff_preference
     *
     * @return string
     */
    public function getStaffPreference()
    {
        return $this->staff_preference;
    }

    /**
     * Sets staff_preference
     *
     * @param string $staff_preference
     * @return $this
     */
    public function setStaffPreference( $staff_preference )
    {
        $this->staff_preference = $staff_preference;

        return $this;
    }

    /**
     * Gets staff_preference_settings
     *
     * @return string
     */
    public function getStaffPreferenceSettings()
    {
        return $this->staff_preference_settings;
    }

    /**
     * Sets staff_preference_settings
     *
     * @param string $staff_preference_settings
     * @return $this
     */
    public function setStaffPreferenceSettings( $staff_preference_settings )
    {
        $this->staff_preference_settings = $staff_preference_settings;

        return $this;
    }

    /**
     * @return int
     */
    public function getUnitsMin()
    {
        return $this->units_min;
    }

    /**
     * @param int $units_min
     * @return $this
     */
    public function setUnitsMin( $units_min )
    {
        $this->units_min = $units_min;

        return $this;
    }

    /**
     * @return int
     */
    public function getUnitsMax()
    {
        return $this->units_max;
    }

    /**
     * @param int $units_max
     * @return $this
     */
    public function setUnitsMax( $units_max )
    {
        $this->units_max = $units_max;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeRequirements()
    {
        return $this->time_requirements;
    }

    /**
     * @param int $time_requirements
     * @return $this
     */
    public function setTimeRequirements( $time_requirements )
    {
        $this->time_requirements = $time_requirements;

        return $this;
    }

    /**
     * Gets recurrence_enabled
     *
     * @return string
     */
    public function getRecurrenceEnabled()
    {
        return $this->recurrence_enabled;
    }

    /**
     * Sets recurrence_enabled
     *
     * @param string $recurrence_enabled
     * @return $this
     */
    public function setRecurrenceEnabled( $recurrence_enabled )
    {
        $this->recurrence_enabled = $recurrence_enabled;

        return $this;
    }

    /**
     * Gets same_staff_for_subservices
     *
     * @return string
     */
    public function getSameStaffForSubservices()
    {
        return $this->same_staff_for_subservices;
    }

    /**
     * Sets same_staff_for_subservices
     *
     * @param string $same_staff_for_subservices
     * @return $this
     */
    public function setSameStaffForSubservices( $same_staff_for_subservices )
    {
        $this->same_staff_for_subservices = $same_staff_for_subservices;

        return $this;
    }

    /**
     * Gets recurrence_frequencies
     *
     * @return string
     */
    public function getRecurrenceFrequencies()
    {
        return $this->recurrence_frequencies;
    }

    /**
     * Sets recurrence_frequencies
     *
     * @param string $recurrence_frequencies
     * @return $this
     */
    public function setRecurrenceFrequencies( $recurrence_frequencies )
    {
        $this->recurrence_frequencies = $recurrence_frequencies;

        return $this;
    }

    /**
     * Gets collaborative_equal_duration
     *
     * @return bool
     */
    public function getCollaborativeEqualDuration()
    {
        return $this->collaborative_equal_duration;
    }

    /**
     * Sets collaborative_equal_duration
     *
     * @param bool $collaborative_equal_duration
     * @return $this
     */
    public function setCollaborativeEqualDuration( $collaborative_equal_duration )
    {
        $this->collaborative_equal_duration = $collaborative_equal_duration;

        return $this;
    }

    /**
     * Gets online_meetings
     *
     * @return string
     */
    public function getOnlineMeetings()
    {
        return $this->online_meetings;
    }

    /**
     * Sets online_meetings
     *
     * @param string $online_meetings
     * @return $this
     */
    public function setOnlineMeetings( $online_meetings )
    {
        $this->online_meetings = $online_meetings;

        return $this;
    }

    /**
     * Gets final_step_url
     *
     * @return string
     */
    public function getFinalStepUrl()
    {
        return $this->final_step_url;
    }

    /**
     * Sets final_step_url
     *
     * @param string $final_step_url
     * @return $this
     */
    public function setFinalStepUrl( $final_step_url )
    {
        $this->final_step_url = $final_step_url;

        return $this;
    }

    /**
     * Gets wc_product_id
     *
     * @return int
     */
    public function getWCProductId()
    {
        return $this->wc_product_id;
    }

    /**
     * Sets wc_product_id
     *
     * @param int $wc_product_id
     * @return $this
     */
    public function setWCProductId( $wc_product_id )
    {
        $this->wc_product_id = $wc_product_id;

        return $this;
    }

    /**
     * Gets wc_cart_info_name
     *
     * @return string
     */
    public function getWCCartInfoName()
    {
        return $this->wc_cart_info_name;
    }

    /**
     * Get translated Cart info name
     *
     * @param string $locale
     * @return string
     */
    public function getTranslatedWCCartInfoName( $locale = null )
    {
        return Lib\Utils\Common::getTranslatedString( 'service_' . $this->getId() . '_wc_cart_info_name', $this->getWCCartInfoName(), $locale );
    }

    /**
     * Sets wc_cart_info_name
     *
     * @param string $wc_cart_info_name
     * @return $this
     */
    public function setWCCartInfoName( $wc_cart_info_name )
    {
        $this->wc_cart_info_name = $wc_cart_info_name;

        return $this;
    }

    /**
     * Gets wc_cart_info
     *
     * @return string
     */
    public function getWCCartInfo()
    {
        return $this->wc_cart_info;
    }

    /**
     * Get translated Cart info
     *
     * @param string $locale
     * @return string
     */
    public function getTranslatedWCCartInfo( $locale = null )
    {
        return Lib\Utils\Common::getTranslatedString( 'service_' . $this->getId() . '_wc_cart_info', $this->getWCCartInfo(), $locale );
    }

    /**
     * Sets wc_cart_info
     *
     * @param string $wc_cart_info
     * @return $this
     */
    public function setWCCartInfo( $wc_cart_info )
    {
        $this->wc_cart_info = $wc_cart_info;

        return $this;
    }

    /**
     * @return int
     */
    public function getMinTimePriorBooking()
    {
        return $this->min_time_prior_booking;
    }

    /**
     * @param $min_time_prior_booking
     * @return $this
     */
    public function setMinTimePriorBooking( $min_time_prior_booking )
    {
        $this->min_time_prior_booking = $min_time_prior_booking;

        return $this;
    }

    /**
     * @return int
     */
    public function getMinTimePriorCancel()
    {
        return $this->min_time_prior_cancel;
    }

    /**
     * @param $min_time_prior_cancel
     * @return $this
     */
    public function setMinTimePriorCancel( $min_time_prior_cancel )
    {
        $this->min_time_prior_cancel = $min_time_prior_cancel;

        return $this;
    }

    /**
     * Gets gateways
     *
     * @return string
     */
    public function getGateways()
    {
        return $this->gateways;
    }

    /**
     * Sets gateways
     *
     * @param string $gateways
     * @return $this
     */
    public function setGateways( $gateways )
    {
        $this->gateways = $gateways;

        return $this;
    }

    /**
     * Gets visibility
     *
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Sets visibility
     *
     * @param string $visibility
     * @return $this
     */
    public function setVisibility( $visibility )
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Gets position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition( $position )
    {
        $this->position = $position;

        return $this;
    }

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    /**
     * Save service.
     *
     * @return false|int
     */
    public function save()
    {
        if ( is_array( $this->recurrence_frequencies ) ) {
            $this->recurrence_frequencies = implode( ',', $this->recurrence_frequencies );
        }

        if ( $this->color === null ) {
            $this->color = sprintf( '#%06X', mt_rand( 0, 0x64FFFF ) );
        }

        $return = parent::save();
        if ( $this->isLoaded() ) {
            // Register string for translate in WPML.
            do_action( 'wpml_register_single_string', 'bookly', 'service_' . $this->getId(), $this->getTitle() );
            do_action( 'wpml_register_single_string', 'bookly', 'service_' . $this->getId() . '_info', $this->getInfo() );
            do_action( 'wpml_register_single_string', 'bookly', 'service_' . $this->getId() . '_wc_cart_info_name', $this->getWCCartInfoName() );
            do_action( 'wpml_register_single_string', 'bookly', 'service_' . $this->getId() . '_wc_cart_info', $this->getWCCartInfo() );
        }

        return $return;
    }
}