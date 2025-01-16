<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;
use Bookly\Lib\DataHolders\Booking;

class CustomerAppointment extends Lib\Base\Entity
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REJECTED = 'rejected';
    const STATUS_WAITLISTED = 'waitlisted';
    const STATUS_DONE = 'done';

    /** @var int */
    protected $series_id;
    /** @var  int */
    protected $package_id;
    /** @var  int */
    protected $customer_id;
    /** @var  int */
    protected $appointment_id;
    /** @var  int */
    protected $payment_id;
    /** @var  int */
    protected $order_id;
    /** @var  int */
    protected $number_of_persons = 1;
    /** @var  int */
    protected $units = 1;
    /** @var  string */
    protected $notes;
    /** @var  string */
    protected $extras = '[]';
    /** @var  int */
    protected $extras_multiply_nop;
    /** @var  string */
    protected $custom_fields = '[]';
    /** @var  string */
    protected $status;
    /** @var  string Y-m-d H:i:s */
    protected $status_changed_at;
    /** @var  string */
    protected $token;
    /** @var  string */
    protected $time_zone;
    /** @var  int */
    protected $time_zone_offset;
    /** @var  int */
    protected $rating;
    /** @var  string */
    protected $rating_comment;
    /** @var  string */
    protected $locale;
    /** @var  int */
    protected $collaborative_service_id;
    /** @var  string */
    protected $collaborative_token;
    /** @var  int */
    protected $compound_service_id;
    /** @var  string */
    protected $compound_token;
    /** @var  string */
    protected $created_from = 'frontend';
    /** @var  string */
    protected $created_at;
    /** @var  string */
    protected $updated_at;

    protected static $table = 'bookly_customer_appointments';

    protected $loggable = true;

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'series_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Series' ) ),
        'package_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Package', 'namespace' => '\BooklyPackages\Lib\Entities', 'required' => 'bookly-addon-packages' ) ),
        'customer_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Customer' ) ),
        'appointment_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Appointment' ) ),
        'payment_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Payment' ) ),
        'order_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Order' ) ),
        'number_of_persons' => array( 'format' => '%d' ),
        'units' => array( 'format' => '%d' ),
        'notes' => array( 'format' => '%s' ),
        'extras' => array( 'format' => '%s' ),
        'extras_multiply_nop' => array( 'format' => '%d' ),
        'custom_fields' => array( 'format' => '%s' ),
        'status' => array( 'format' => '%s' ),
        'status_changed_at' => array( 'format' => '%s' ),
        'token' => array( 'format' => '%s' ),
        'time_zone' => array( 'format' => '%s' ),
        'time_zone_offset' => array( 'format' => '%d' ),
        'rating' => array( 'format' => '%d' ),
        'rating_comment' => array( 'format' => '%s' ),
        'locale' => array( 'format' => '%s' ),
        'collaborative_service_id' => array( 'format' => '%d' ),
        'collaborative_token' => array( 'format' => '%s' ),
        'compound_service_id' => array( 'format' => '%d' ),
        'compound_token' => array( 'format' => '%s' ),
        'created_from' => array( 'format' => '%s' ),
        'created_at' => array( 'format' => '%s' ),
        'updated_at' => array( 'format' => '%s' ),
    );

    /** @var Customer */
    public $customer;

    /** @var  string */
    private $last_status;
    /** @var bool */
    private $just_created = false;

    /**
     * Delete entity and appointment if there are no more customers.
     *
     * @param bool $compound_collaborative
     */
    public function deleteCascade( $compound_collaborative = false )
    {
        Lib\Proxy\Shared::deleteCustomerAppointment( $this );
        $this->delete();
        $appointment = new Appointment();
        if ( $appointment->load( $this->getAppointmentId() ) ) {
            // Check if there are any customers left.
            if ( self::query()->where( 'appointment_id', $appointment->getId() )->count() == 0 ) {
                // If no customers then delete the appointment.
                $appointment->delete();
            } else {
                // If there are customers then recalculate extras duration.
                if ( $this->getExtras() != '[]' ) {
                    $extras_duration = $appointment->getMaxExtrasDuration();
                    if ( $appointment->getExtrasDuration() != $extras_duration ) {
                        $appointment->setExtrasDuration( $extras_duration );
                        $appointment->save();
                    }
                }
                // Google Calendar.
                Lib\Proxy\Pro::syncGoogleCalendarEvent( $appointment );
                // Outlook Calendar.
                Lib\Proxy\OutlookCalendar::syncEvent( $appointment );
                // Waiting list.
                Lib\Proxy\WaitingList::handleParticipantsChange( false, $appointment );

                Lib\Proxy\WaitingList::handleAppointmentFreePlace( false, $appointment );
            }
            if ( $compound_collaborative ) {
                /** @var CustomerAppointment[] $ca_list */
                $ca_list = array();
                if ( $this->getCompoundToken() ) {
                    // Remove compound CustomerAppointments
                    $ca_list = self::query()
                        ->where( 'compound_token', $this->getCompoundToken() )
                        ->where( 'compound_service_id', $this->getCompoundServiceId() )
                        ->find();
                } elseif ( $this->getCollaborativeToken() ) {
                    $ca_list = self::query()
                        ->where( 'collaborative_token', $this->getCollaborativeToken() )
                        ->where( 'collaborative_service_id', $this->getCollaborativeServiceId() )
                        ->find();
                }
                foreach ( $ca_list as $ca ) {
                    $ca->deleteCascade();
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getStatusTitle()
    {
        return self::statusToString( $this->getStatus() );
    }

    /**
     * Check if cancel allowed
     *
     * @return bool
     */
    public function cancelAllowed()
    {
        $allow_cancel = true;
        $appointment = new Lib\Entities\Appointment();
        $minimum_time_prior_cancel = (int) Lib\Proxy\Pro::getMinimumTimePriorCancel( $appointment->getServiceId() );
        if ( $minimum_time_prior_cancel > 0
            && $appointment->load( $this->getAppointmentId() )
            && $appointment->getStartDate() !== null
        ) {
            $allow_cancel_time = strtotime( $appointment->getStartDate() ) - $minimum_time_prior_cancel;
            if ( current_time( 'timestamp' ) > $allow_cancel_time ) {
                $allow_cancel = false;
            }
        }
        if ( $this->getStatus() == self::STATUS_DONE ) {
            $allow_cancel = false;
        }

        return $allow_cancel;
    }

    /**
     * @param string $reason
     */
    public function cancel( $reason = '' )
    {
        $appointment = new Appointment();
        if ( $appointment->load( $this->getAppointmentId() ) ) {
            $item = Booking\Item::collect( $this, Lib\Proxy\CustomStatuses::prepareFreeStatuses( array(
                self::STATUS_CANCELLED,
                self::STATUS_REJECTED,
            ) ) );

            if ( $item ) {
                $item->setStatus( self::STATUS_CANCELLED );
                Lib\Notifications\Booking\Sender::send( $item, array( 'cancellation_reason' => $reason ) );
                if ( get_option( 'bookly_appointment_cancel_action' ) == 'delete' ) {
                    $this->deleteCascade( true );
                } else {
                    foreach ( $item->getItems() as $i ) {
                        if ( $i->getCA()->save() ) {
                            $appointment = $i->getAppointment();
                            if ( $i->getExtras() != '[]' ) {
                                $extras_duration = $appointment->getMaxExtrasDuration();
                                if ( $appointment->getExtrasDuration() != $extras_duration ) {
                                    $appointment->setExtrasDuration( $extras_duration );
                                    $appointment->save();
                                }
                            }
                            // Google Calendar.
                            Lib\Proxy\Pro::syncGoogleCalendarEvent( $appointment );
                            // Outlook Calendar.
                            Lib\Proxy\OutlookCalendar::syncEvent( $appointment );
                            // Waiting list.
                            Lib\Proxy\WaitingList::handleParticipantsChange( false, $appointment );

                            Lib\Proxy\WaitingList::handleAppointmentFreePlace( false, $appointment );
                        }
                    }
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function isJustCreated()
    {
        return $this->just_created;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setJustCreated( $value )
    {
        $this->just_created = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStatusChanged()
    {
        return $this->status != $this->last_status;
    }

    public static function statusToString( $status )
    {
        switch ( $status ) {
            case self::STATUS_PENDING:
                return __( 'Pending', 'bookly' );
            case self::STATUS_APPROVED:
                return __( 'Approved', 'bookly' );
            case self::STATUS_CANCELLED:
                return __( 'Cancelled', 'bookly' );
            case self::STATUS_REJECTED:
                return __( 'Rejected', 'bookly' );
            case self::STATUS_WAITLISTED:
                return __( 'On waiting list', 'bookly' );
            case self::STATUS_DONE:
                return __( 'Done', 'bookly' );
            case 'mixed':
                return __( 'Mixed', 'bookly' );
            default:
                return Lib\Proxy\CustomStatuses::statusToString( $status );
        }
    }

    public static function statusToIcon( $status )
    {
        switch ( $status ) {
            case self::STATUS_PENDING:
                return 'far fa-clock';
            case self::STATUS_APPROVED:
                return 'fas fa-check';
            case self::STATUS_CANCELLED:
                return 'fas fa-times';
            case self::STATUS_REJECTED:
                return 'fas fa-ban';
            case self::STATUS_WAITLISTED:
                return 'fas fa-list-ol';
            case self::STATUS_DONE:
                return 'far fa-check-square';
            default:
                return Lib\Proxy\CustomStatuses::statusToIcon( $status );
        }
    }

    /**
     * Get customer appointment statuses.
     *
     * @return array
     */
    public static function getStatuses()
    {
        if ( ! self::hasInCache( __FUNCTION__ ) ) {
            $statuses = array(
                self::STATUS_PENDING,
                self::STATUS_APPROVED,
                self::STATUS_CANCELLED,
                self::STATUS_REJECTED,
            );
            if ( Lib\Config::waitingListActive() ) {
                $statuses[] = self::STATUS_WAITLISTED;
            }
            $statuses[] = self::STATUS_DONE;

            $statuses = Lib\Proxy\CustomStatuses::prepareAllStatuses( $statuses );
            self::putInCache( __FUNCTION__, $statuses );
        }

        return self::getFromCache( __FUNCTION__ );
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets series_id
     *
     * @return int
     */
    public function getSeriesId()
    {
        return $this->series_id;
    }

    /**
     * Sets series_id
     *
     * @param Series $series
     * @return $this
     */
    public function setSeries( Series $series )
    {
        return $this->setSeriesId( $series->getId() );
    }

    /**
     * Sets series_id
     *
     * @param int $series_id
     * @return $this
     */
    public function setSeriesId( $series_id )
    {
        $this->series_id = $series_id;

        return $this;
    }

    /**
     * Gets customer_id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * Sets package
     *
     * @param \BooklyPackages\Lib\Entities\Package $package
     * @return $this
     */
    public function setPackage( \BooklyPackages\Lib\Entities\Package $package )
    {
        return $this->setPackageId( $package->getId() );
    }

    /**
     * Sets service_id
     *
     * @param int $package_id
     * @return $this
     */
    public function setPackageId( $package_id )
    {
        $this->package_id = $package_id;

        return $this;
    }

    /**
     * Gets service_id
     *
     * @return int
     */
    public function getPackageId()
    {
        return $this->package_id;
    }

    /**
     * Sets customer
     *
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer( Customer $customer )
    {
        return $this->setCustomerId( $customer->getId() );
    }

    /**
     * Sets customer_id
     *
     * @param int $customer_id
     * @return $this
     */
    public function setCustomerId( $customer_id )
    {
        $this->customer_id = $customer_id;

        return $this;
    }

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
     * @param Appointment $appointment
     * @return $this
     */
    public function setAppointment( Appointment $appointment )
    {
        return $this->setAppointmentId( $appointment->getId() );
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
     * Gets payment_id
     *
     * @return int
     */
    public function getPaymentId()
    {
        return $this->payment_id;
    }

    /**
     * Sets payment_id
     *
     * @param int $payment_id
     * @return $this
     */
    public function setPaymentId( $payment_id )
    {
        $this->payment_id = $payment_id;

        return $this;
    }

    /**
     * Gets order_id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Sets order_id
     *
     * @param int $order_id
     * @return $this
     */
    public function setOrderId( $order_id )
    {
        $this->order_id = $order_id;

        return $this;
    }

    /**
     * Gets notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Sets notes
     *
     * @param string $notes
     * @return $this
     */
    public function setNotes( $notes )
    {
        $this->notes = $notes;

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
     * Gets extras
     *
     * @return string
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Sets extras
     *
     * @param string $extras
     * @return $this
     */
    public function setExtras( $extras )
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Gets extras_multiply_nop
     *
     * @return string
     */
    public function getExtrasMultiplyNop()
    {
        return $this->extras_multiply_nop;
    }

    /**
     * Sets extras_multiply_nop
     *
     * @param string $extras_multiply_nop
     * @return $this
     */
    public function setExtrasMultiplyNop( $extras_multiply_nop )
    {
        $this->extras_multiply_nop = $extras_multiply_nop;

        return $this;
    }

    /**
     * Sets custom_fields
     *
     * @param string $custom_fields
     * @return $this
     */
    public function setCustomFields( $custom_fields )
    {
        $this->custom_fields = $custom_fields;

        return $this;
    }

    /**
     * Gets custom_fields
     *
     * @return string
     */
    public function getCustomFields()
    {
        return $this->custom_fields;
    }

    /**
     * Gets status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus( $status )
    {
        if ( $this->last_status === null ) {
            $this->last_status = $status;
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Gets status_changed_at
     *
     * @return string
     */
    public function getStatusChangedAt()
    {
        return $this->status_changed_at;
    }

    /**
     * Sets status_changed_at
     *
     * @param string $status_changed_at
     * @return $this
     */
    public function setStatusChangedAt( $status_changed_at )
    {
        $this->status_changed_at = $status_changed_at;

        return $this;
    }

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

    /**
     * Gets time_zone
     *
     * @return string
     */
    public function getTimeZone()
    {
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
     * Gets time_zone_offset
     *
     * @return int
     */
    public function getTimeZoneOffset()
    {
        return $this->time_zone_offset;
    }

    /**
     * Sets time_zone_offset
     *
     * @param int $time_zone_offset
     * @return $this
     */
    public function setTimeZoneOffset( $time_zone_offset )
    {
        $this->time_zone_offset = $time_zone_offset;

        return $this;
    }

    /**
     * Gets rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Sets rating
     *
     * @param int $rating
     * @return $this
     */
    public function setRating( $rating )
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Gets rating comment
     *
     * @return string
     */
    public function getRatingComment()
    {
        return $this->rating_comment;
    }

    /**
     * Sets rating comment
     *
     * @param string $rating_comment
     * @return $this
     */
    public function setRatingComment( $rating_comment )
    {
        $this->rating_comment = $rating_comment;

        return $this;
    }

    /**
     * Gets locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets locale
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale( $locale )
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Gets collaborative_service_id
     *
     * @return int
     */
    public function getCollaborativeServiceId()
    {
        return $this->collaborative_service_id;
    }

    /**
     * Sets collaborative_service_id
     *
     * @param int $collaborative_service_id
     * @return $this
     */
    public function setCollaborativeServiceId( $collaborative_service_id )
    {
        $this->collaborative_service_id = $collaborative_service_id;

        return $this;
    }

    /**
     * Gets compound_service_id
     *
     * @return int
     */
    public function getCompoundServiceId()
    {
        return $this->compound_service_id;
    }

    /**
     * Sets compound_service_id
     *
     * @param int $compound_service_id
     * @return $this
     */
    public function setCompoundServiceId( $compound_service_id )
    {
        $this->compound_service_id = $compound_service_id;

        return $this;
    }

    /**
     * Gets collaborative_token
     *
     * @return string
     */
    public function getCollaborativeToken()
    {
        return $this->collaborative_token;
    }

    /**
     * Sets collaborative_token
     *
     * @param string $collaborative_token
     * @return $this
     */
    public function setCollaborativeToken( $collaborative_token )
    {
        $this->collaborative_token = $collaborative_token;

        return $this;
    }

    /**
     * Gets compound_token
     *
     * @return string
     */
    public function getCompoundToken()
    {
        return $this->compound_token;
    }

    /**
     * Sets compound_token
     *
     * @param string $compound_token
     * @return $this
     */
    public function setCompoundToken( $compound_token )
    {
        $this->compound_token = $compound_token;

        return $this;
    }

    /**
     * Gets created_from
     *
     * @return string
     */
    public function getCreatedFrom()
    {
        return $this->created_from;
    }

    /**
     * Sets created_from
     *
     * @param string $created_from
     * @return $this
     */
    public function setCreatedFrom( $created_from )
    {
        $this->created_from = $created_from;

        return $this;
    }

    /**
     * Gets created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Sets created_at
     *
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt( $created_at )
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Gets last_status
     *
     * @return string
     */
    public function getLastStatus()
    {
        return $this->last_status;
    }

    /**
     * Gets updated_at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Sets updated_at
     *
     * @param string $updated_at
     * @return $this
     */
    public function setUpdatedAt( $updated_at )
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    /**
     * @param array|\stdClass $data
     * @param bool $overwrite_loaded_values
     * @return $this
     */
    public function setFields( $data, $overwrite_loaded_values = false )
    {
        if ( $data = (array) $data ) {
            if ( $this->last_status === null && array_key_exists( 'status', $data ) ) {
                $this->last_status = $data['status'];
            }
        }

        return parent::setFields( $data, $overwrite_loaded_values );
    }

    /**
     * Save entity to database.
     * Generate token before saving.
     *
     * @return int|false
     */
    public function save()
    {
        // Generate new token if it is not set.
        if ( $this->getToken() == '' ) {
            $this->setToken( Lib\Utils\Common::generateToken( get_class( $this ), 'token' ) );
        }
        if ( $this->getLocale() === null ) {
            $this->setLocale( apply_filters( 'wpml_current_language', null ) );
        }

        if ( $this->status != $this->last_status ) {
            $this->setStatusChangedAt( current_time( 'mysql' ) );
        }

        if ( $this->getExtrasMultiplyNop() === null ) {
            $this->setExtrasMultiplyNop( get_option( 'bookly_service_extras_multiply_nop', 1 ) );
        }

        $this->just_created = $this->getId() === null;

        if ( $this->getId() === null ) {
            $this->setUpdatedAt( current_time( 'mysql' ) );
        } elseif ( $this->getModified() ) {
            $this->setUpdatedAt( current_time( 'mysql' ) );
        }

        return parent::save();
    }

    /**
     * Delete entity from database
     *
     * @return false|int
     */
    public function delete()
    {
        $result = parent::delete();
        if ( $result && $this->getSeriesId() !== null ) {
            if ( self::query()->where( 'series_id', $this->getSeriesId() )->count() === 0 ) {
                Series::find( $this->getSeriesId() )->delete();
            }
        }

        return $result;
    }
}