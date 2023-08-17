<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

/**
 * Class Staff
 *
 * @package Bookly\Lib\Entities
 */
class Staff extends Lib\Base\Entity
{
    /** @var integer */
    protected $wp_user_id;
    /** @var int */
    protected $category_id;
    /** @var integer */
    protected $attachment_id;
    /** @var string */
    protected $full_name;
    /** @var string */
    protected $email;
    /** @var string */
    protected $phone;
    /** @var string */
    protected $time_zone;
    /** @var string */
    protected $info;
    /** @var int */
    protected $working_time_limit;
    /** @var string */
    protected $visibility = 'public';
    /** @var int */
    protected $position;
    /** @var string */
    protected $google_data;
    /** @var string */
    protected $outlook_data;
    /** @var string */
    protected $zoom_authentication = 'default';
    /** @var string */
    protected $zoom_jwt_api_key;
    /** @var string */
    protected $zoom_jwt_api_secret;
    /** @var string */
    protected $zoom_oauth_token;
    /** @var int */
    protected $icalendar = 0;
    /** @var string */
    protected $icalendar_token = '';
    /** @var int */
    protected $icalendar_days_before = 365;
    /** @var int */
    protected $icalendar_days_after = 365;
    /** @var string */
    protected $color;
    /** @var string */
    protected $gateways;

    protected static $table = 'bookly_staff';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'wp_user_id' => array( 'format' => '%d' ),
        'category_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'StaffCategory', 'namespace' => '\BooklyPro\Lib\Entities', 'required' => 'bookly-addon-pro' ) ),
        'attachment_id' => array( 'format' => '%d' ),
        'full_name' => array( 'format' => '%s' ),
        'email' => array( 'format' => '%s' ),
        'phone' => array( 'format' => '%s' ),
        'info' => array( 'format' => '%s' ),
        'working_time_limit' => array( 'format' => '%d' ),
        'visibility' => array( 'format' => '%s' ),
        'position' => array( 'format' => '%d', 'sequent' => true ),
        'google_data' => array( 'format' => '%s' ),
        'outlook_data' => array( 'format' => '%s' ),
        'time_zone' => array( 'format' => '%s' ),
        'zoom_authentication' => array( 'format' => '%s' ),
        'zoom_jwt_api_key' => array( 'format' => '%s' ),
        'zoom_jwt_api_secret' => array( 'format' => '%s' ),
        'zoom_oauth_token' => array( 'format' => '%s' ),
        'icalendar' => array( 'format' => '%d' ),
        'icalendar_token' => array( 'format' => '%s' ),
        'icalendar_days_before' => array( 'format' => '%d' ),
        'icalendar_days_after' => array( 'format' => '%d' ),
        'color' => array( 'format' => '%s' ),
        'gateways'  => array( 'format' => '%s' ),
    );

    /**
     * Get schedule items of staff member.
     *
     * @param int $location_id
     * @return StaffScheduleItem[]
     */
    public function getScheduleItems( $location_id = null )
    {
        $staff_id = $this->getId();
        $start_of_week = (int) get_option( 'start_of_week' );
        // Start of week affects the sorting.
        // If it is 0(Sun) then the result should be 1,2,3,4,5,6,7.
        // If it is 1(Mon) then the result should be 2,3,4,5,6,7,1.
        // If it is 2(Tue) then the result should be 3,4,5,6,7,1,2. Etc.
        $schedule = StaffScheduleItem::query()
            ->where( 'staff_id', $staff_id )
            ->sortBy( "IF(r.day_index + 10 - {$start_of_week} > 10, r.day_index + 10 - {$start_of_week}, 16 + r.day_index)" )
            ->indexBy( 'day_index' )
            ->where( 'location_id', $location_id )
            ->find();

        if ( count( $schedule ) === 7 ) {
            return $schedule;
        }

        $week = array( 1 => 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' );
        foreach ( $week as $day_index => $day ) {
            $sch_item = new Lib\Entities\StaffScheduleItem();
            if ( ! $sch_item->loadBy( compact( 'day_index', 'staff_id', 'location_id' ) ) ) {
                $sch_item
                    ->setStaffId( $staff_id )
                    ->setLocationId( $location_id )
                    ->setDayIndex( $day_index )
                    ->setStartTime( get_option( 'bookly_bh_' . $day . '_start' ) ?: null )
                    ->setEndTime( get_option( 'bookly_bh_' . $day . '_end' ) ?: null )
                    ->save();
            }
        }

        return $this->getScheduleItems( $location_id );
    }

    /**
     * Get data for services associated with this staff member.
     *
     * @param $type
     * @return array
     */
    public function getServicesData( $type = Service::TYPE_SIMPLE )
    {
        $result = array();

        if ( $this->getId() ) {
            $query = StaffService::query( 'ss' )
                ->select( 'ss.*,
                    s.category_id,
                    s.title,
                    s.duration,
                    s.price AS service_price,
                    s.color,
                    s.online_meetings,
                    s.slot_length,
                    s.position,
                    c.name' )
                ->addSelect( sprintf( '%s AS service_capacity_min, %s AS service_capacity_max, %s AS units_min, %s AS units_max',
                    Lib\Proxy\Shared::prepareStatement( 1, 's.capacity_min', 'Service' ),
                    Lib\Proxy\Shared::prepareStatement( 1, 's.capacity_max', 'Service' ),
                    Lib\Proxy\Shared::prepareStatement( 1, 's.units_min', 'Service' ),
                    Lib\Proxy\Shared::prepareStatement( 1, 's.units_max', 'Service' )
                ) )
                ->leftJoin( 'Service', 's', 's.id = ss.service_id' )
                ->leftJoin( 'Category', 'c', 'c.id = s.category_id' )
                ->where( 'ss.staff_id', $this->getId() )
                ->where( 's.type', $type );
            if ( ! Lib\Proxy\Locations::servicesPerLocationAllowed() ) {
                $query->where( 'ss.location_id', null );
            }
            $staff_services = $query->sortBy( 'c.position, s.position' )->fetchArray();

            foreach ( $staff_services as $data ) {
                $staff_service = new StaffService( $data );

                $service = new Service();
                $service
                    ->setId( $data['service_id'] )
                    ->setTitle( $data['title'] )
                    ->setColor( $data['color'] )
                    ->setDuration( $data['duration'] )
                    ->setPrice( $data['service_price'] )
                    ->setUnitsMin( $data['units_min'] )
                    ->setUnitsMax( $data['units_max'] )
                    ->setPosition( $data['position'] )
                    ->setCapacityMin( $data['service_capacity_min'] )
                    ->setCapacityMax( $data['service_capacity_max'] )
                    ->setOnlineMeetings( $data['online_meetings'] )
                    ->setSlotLength( $data['slot_length'] );

                $category = new Category();
                $category
                    ->setId( $data['category_id'] )
                    ->setName( $data['name'] );

                $result[] = compact( 'staff_service', 'service', 'category' );
            }
        }

        return $result;
    }

    /**
     * Check whether staff is archived or not.
     *
     * @return bool
     */
    public function isArchived()
    {
        return $this->getVisibility() == 'archive';
    }

    /**
     * Check whether staff is on holiday on given day.
     *
     * @param \DateTime $day
     * @return bool
     */
    public function isOnHoliday( \DateTime $day )
    {
        $query = Holiday::query()
            ->whereRaw( '( DATE_FORMAT( date, %s ) = %s AND repeat_event = 1 ) OR date = %s', array( '%m-%d', $day->format( 'm-d' ), $day->format( 'Y-m-d' ) ) )
            ->whereRaw( 'staff_id = %d OR staff_id IS NULL', array( $this->getId() ) )
            ->limit( 1 );
        $rows = $query->execute( Lib\Query::HYDRATE_NONE );

        return $rows != 0;
    }

    /**
     * @param string $locale
     * @return string
     */
    public function getTranslatedName( $locale = null )
    {
        return Lib\Utils\Common::getTranslatedString( 'staff_' . $this->getId(), $this->getFullName(), $locale );
    }

    /**
     * @param string $locale
     * @return string
     */
    public function getTranslatedInfo( $locale = null )
    {
        return Lib\Utils\Common::getTranslatedString( 'staff_' . $this->getId() . '_info', $this->getInfo(), $locale );
    }

    /**
     * Get workload for given date.
     *
     * @param string $date 'Y-m-d'
     * @param array $exclude list of appointment id's to exclude
     * @return int
     */
    public function getWorkload( $date, $exclude = array() )
    {
        $start_date = $date . ' 00:00:00';
        $end_date = date_create( $date )->modify( '+1 day' )->format( 'Y-m-d H:i:s' );
        $appointments = Lib\Entities\CustomerAppointment::query( 'ca' )
            ->select( 'a.start_date, DATE_ADD(`a`.`end_date`, INTERVAL `a`.`extras_duration` SECOND) as end_date' )
            ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
            ->where( 'a.staff_id', $this->getId() )
            ->whereLt( 'a.start_date', $end_date )
            ->whereGt( 'a.end_date', $start_date )
            ->whereIn( 'ca.status', Lib\Proxy\CustomStatuses::prepareBusyStatuses( array(
                Lib\Entities\CustomerAppointment::STATUS_PENDING,
                Lib\Entities\CustomerAppointment::STATUS_APPROVED,
                Lib\Entities\CustomerAppointment::STATUS_WAITLISTED,
            ) ) )
            ->whereNotIn( 'a.id', $exclude )
            ->fetchArray();

        $workload = 0;
        foreach ( $appointments as $appointment ) {
            $workload += min( strtotime( $end_date ), strtotime( $appointment['end_date'] ) ) - max( strtotime( $start_date ), strtotime( $appointment['start_date'] ) );
        }

        return $workload;
    }

    /**
     * Get holidays.
     *
     * @return array
     */
    public function getHolidays()
    {
        $collection = Lib\Entities\Holiday::query( 'h' )->where( 'h.staff_id', $this->getId() )->fetchArray();
        $holidays = array();
        foreach ( $collection as $holiday ) {
            list ( $Y, $m, $d ) = explode( '-', $holiday['date'] );
            $holidays[ $holiday['id'] ] = array(
                'm' => (int) $m,
                'd' => (int) $d,
            );
            // if not repeated holiday, add the year
            if ( ! $holiday['repeat_event'] ) {
                $holidays[ $holiday['id'] ]['y'] = (int) $Y;
            }
        }

        return $holidays;
    }

    /**
     * Get staff image url
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
     * Gets wp_user_id
     *
     * @return int
     */
    public function getWpUserId()
    {
        return $this->wp_user_id;
    }

    /**
     * Sets wp_user_id
     *
     * @param int $wp_user_id
     * @return $this
     */
    public function setWpUserId( $wp_user_id )
    {
        $this->wp_user_id = $wp_user_id;

        return $this;
    }

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
     * Gets full name
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * Sets full name
     *
     * @param string $full_name
     * @return $this
     */
    public function setFullName( $full_name )
    {
        $this->full_name = $full_name;

        return $this;
    }

    /**
     * Gets email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail( $email )
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets phone
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone( $phone )
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Gets time_zone
     *
     * @param bool $convert_utc_values Whether to convert values like UTC+1 into +01:00
     * @return string
     */
    public function getTimeZone( $convert_utc_values = true )
    {
        if ( $convert_utc_values && $this->time_zone && preg_match( '/^UTC[+-]/', $this->time_zone ) ) {
            $offset = preg_replace( '/UTC\+?/', '', $this->time_zone );

            return Lib\Utils\DateTime::formatOffset( $offset * HOUR_IN_SECONDS );
        }

        return $this->time_zone;
    }

    /**
     * Sets time_zone
     *
     * @param string $time_zone
     * @return $this
     */
    public function setTimeZone( $time_zone )
    {
        $this->time_zone = $time_zone;

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
     * Gets working_time_limit
     *
     * @return int
     */
    public function getWorkingTimeLimit()
    {
        return $this->working_time_limit;
    }

    /**
     * Sets working_time_limit
     *
     * @param int $working_time_limit
     * @return $this
     */
    public function setWorkingTimeLimit( $working_time_limit )
    {
        $this->working_time_limit = $working_time_limit;

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

    /**
     * Gets Google data
     *
     * @return string
     */
    public function getGoogleData()
    {
        return $this->google_data;
    }

    /**
     * Sets Google data
     *
     * @param string $google_data
     * @return $this
     */
    public function setGoogleData( $google_data )
    {
        $this->google_data = $google_data;

        return $this;
    }

    /**
     * Gets Microsoft data
     *
     * @return string
     */
    public function getOutlookData()
    {
        return $this->outlook_data;
    }

    /**
     * Sets Microsoft data
     *
     * @param string $outlook_data
     * @return $this
     */
    public function setOutlookData( $outlook_data )
    {
        $this->outlook_data = $outlook_data;

        return $this;
    }

    /**
     * Gets zoom_authentication
     *
     * @return string
     */
    public function getZoomAuthentication()
    {
        return $this->zoom_authentication;
    }

    /**
     * Sets zoom_authentication
     *
     * @param string $zoom_authentication
     * @return $this
     */
    public function setZoomAuthentication( $zoom_authentication )
    {
        $this->zoom_authentication = $zoom_authentication;

        return $this;
    }

    /**
     * Gets zoom_jwt_api_key
     *
     * @return string
     */
    public function getZoomJwtApiKey()
    {
        return $this->zoom_jwt_api_key;
    }

    /**
     * Sets zoom_jwt_api_key
     *
     * @param string $zoom_jwt_api_key
     * @return $this
     */
    public function setZoomJwtApiKey( $zoom_jwt_api_key )
    {
        $this->zoom_jwt_api_key = $zoom_jwt_api_key;

        return $this;
    }

    /**
     * Gets zoom_jwt_api_secret
     *
     * @return string
     */
    public function getZoomJwtApiSecret()
    {
        return $this->zoom_jwt_api_secret;
    }

    /**
     * Gets zoom_oauth_token
     *
     * @return string
     */
    public function getZoomOAuthToken()
    {
        return $this->zoom_oauth_token;
    }

    /**
     * Sets zoom_oauth_token
     *
     * @param string $zoom_oauth_token
     * @return $this
     */
    public function setZoomOAuthToken( $zoom_oauth_token )
    {
        $this->zoom_oauth_token = $zoom_oauth_token;

        return $this;
    }

    /**
     * Gets icalendar
     *
     * @return int
     */
    public function getICalendar()
    {
        return $this->icalendar;
    }

    /**
     * Sets icalendar
     *
     * @param int $icalendar
     * @return $this
     */
    public function setICalendar( $icalendar )
    {
        $this->icalendar = $icalendar;

        return $this;
    }

    /**
     * Gets icalendar_token
     *
     * @return string
     */
    public function getICalendarToken()
    {
        return $this->icalendar_token;
    }

    /**
     * Sets icalendar_token
     *
     * @param string $icalendar_token
     * @return $this
     */
    public function setICalendarToken( $icalendar_token )
    {
        $this->icalendar_token = $icalendar_token;

        return $this;
    }

    /**
     * Gets icalendar_days_before
     *
     * @return string
     */
    public function getICalendarDaysBefore()
    {
        return $this->icalendar_days_before;
    }

    /**
     * Sets icalendar_days_before
     *
     * @param string $icalendar_days_before
     * @return $this
     */
    public function setICalendarDaysBefore( $icalendar_days_before )
    {
        $this->icalendar_days_before = $icalendar_days_before;

        return $this;
    }

    /**
     * Gets icalendar_days_after
     *
     * @return string
     */
    public function getICalendarDaysAfter()
    {
        return $this->icalendar_days_after;
    }

    /**
     * Sets icalendar_days_after
     *
     * @param string $icalendar_days_after
     * @return $this
     */
    public function setICalendarDaysAfter( $icalendar_days_after )
    {
        $this->icalendar_days_after = $icalendar_days_after;

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

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    /**
     * Delete staff member.
     */
    public function delete()
    {
        Lib\Proxy\Pro::revokeGoogleCalendarToken( $this );

        parent::delete();
    }

    /**
     * @return false|int
     */
    public function save()
    {
        $is_new = ! $this->getId();

        if ( $is_new && $this->getWpUserId() ) {
            $user = get_user_by( 'id', $this->getWpUserId() );
            if ( $user ) {
                $this->setEmail( $user->get( 'user_email' ) );
            }
        }
        if ( $this->icalendar_token == '' ) {
            $this->setICalendarToken( Lib\Utils\Common::generateToken( get_class( $this ), 'icalendar_token' ) );
        }

        if ( $this->color === null ) {
            $this->color = sprintf( '#%06X', mt_rand( 0, 0x64FFFF ) );
        }

        $saved = parent::save();

        if ( $saved ) {
            // Register string for translate in WPML.
            do_action( 'wpml_register_single_string', 'bookly', 'staff_' . $this->getId(), $this->getFullName() );
            do_action( 'wpml_register_single_string', 'bookly', 'staff_' . $this->getId() . '_info', $this->getInfo() );

            if ( $is_new ) {
                // Schedule items.
                $staff_id = $this->getId();
                foreach ( Lib\Config::getBusinessHours() as $day_index => $bh ) {
                    $item = new StaffScheduleItem();
                    $item
                        ->setStaffId( $staff_id )
                        ->setDayIndex( $day_index + 1 )
                        ->setStartTime( $bh['start'] )
                        ->setEndTime( $bh['end'] )
                        ->save();
                }

                // Create holidays for staff
                self::$wpdb->query( sprintf(
                    'INSERT INTO `' . Holiday::getTableName() . '` (`parent_id`, `staff_id`, `date`, `repeat_event`)
                SELECT `id`, %d, `date`, `repeat_event` FROM `' . Holiday::getTableName() . '` WHERE `staff_id` IS NULL',
                    $staff_id
                ) );
            }
        }

        return $saved;
    }

}