<?php
namespace Bookly\Lib\Slots;

use Bookly\Lib\Utils\DateTime;

class TimePoint implements IPoint
{
    /** @var string */
    protected static $wp_timezone_offset;
    /** @var string */
    public static $client_timezone_offset;

    /** @var int */
    protected $time;

    /**
     * Constructor.
     *
     * @param int $time
     */
    public function __construct( $time )
    {
        $this->time = $time;
    }

    /**
     * Create TimePoint from string.
     *
     * @param string $time  Format H:i[:s]
     * @return self
     */
    public static function fromStr( $time )
    {
        return new static( DateTime::timeToSeconds( $time ) );
    }

    /**
     * Get value.
     *
     * @return int
     */
    public function value()
    {
        return $this->time;
    }

    /**
     * Tells whether two points are equal.
     *
     * @param IPoint $point
     * @return bool
     */
    public function eq( IPoint $point )
    {
        return $this->time == $point->value();
    }

    /**
     * Tells whether two points are not equal.
     *
     * @param IPoint $point
     * @return bool
     */
    public function neq( IPoint $point )
    {
        return $this->time != $point->value();
    }

    /**
     * Tells whether one point is less than another.
     *
     * @param IPoint $point
     * @return bool
     */
    public function lt( IPoint $point )
    {
        return $this->time < $point->value();
    }

    /**
     * Tells whether one point is less or equal than another.
     *
     * @param IPoint $point
     * @return bool
     */
    public function lte( IPoint $point )
    {
        return $this->time <= $point->value();
    }

    /**
     * Tells whether one point is greater than another.
     *
     * @param IPoint $point
     * @return bool
     */
    public function gt( IPoint $point )
    {
        return $this->time > $point->value();
    }

    /**
     * Tells whether one point is greater or equal than another.
     *
     * @param IPoint $point
     * @return bool
     */
    public function gte( IPoint $point )
    {
        return $this->time >= $point->value();
    }

    /**
     * Computes difference between two points.
     *
     * @param IPoint $point
     * @return int
     */
    public function diff( IPoint $point )
    {
        return $this->time - $point->value();
    }

    /**
     * Modify point.
     *
     * @param int $value
     * @return self
     */
    public function modify( $value )
    {
        if ( $value ) {
            return new static( $this->time + $value );
        }

        return $this;
    }

    /**
     * Returns time formatted with date_i18n.
     *
     * @return string
     */
    public function formatI18nTime()
    {
        return date_i18n( get_option( 'time_format' ), $this->time );
    }

    /**
     * Convert point from one time zone offset to another
     *
     * @param int $from_offset  Initial offset in seconds
     * @param int $to_offset  New offset in seconds
     * @return self
     */
    public function toTz( $from_offset, $to_offset )
    {
        return new static( $this->time - $from_offset + $to_offset );
    }

    /**
     * Convert point to WP time zone.
     *
     * @return self
     */
    public function toWpTz()
    {
        return $this->toTz( static::_clientTzOffset(), static::_wpTzOffset() );
    }

    /**
     * Convert point to client time zone.
     *
     * @return self
     */
    public function toClientTz()
    {
        return $this->toTz( static::_wpTzOffset(), static::_clientTzOffset() );
    }

    /**
     * Get WP time zone offset.
     *
     * @return int
     */
    protected static function _wpTzOffset()
    {
        if ( static::$wp_timezone_offset === null ) {
            static::$wp_timezone_offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
        }

        return static::$wp_timezone_offset;
    }

    /**
     * Get client time zone offset.
     *
     * @return int
     */
    protected static function _clientTzOffset()
    {
        if ( static::$client_timezone_offset === null ) {
            static::$client_timezone_offset = static::_wpTzOffset();
        }

        return static::$client_timezone_offset;
    }
}