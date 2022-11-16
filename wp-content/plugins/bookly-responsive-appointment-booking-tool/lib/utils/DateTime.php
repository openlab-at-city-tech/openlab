<?php
namespace Bookly\Lib\Utils;

use Bookly\Lib;
use Bookly\Lib\Base\Cache;

/**
 * Class DateTime
 *
 * @package Bookly\Lib\Utils
 */
class DateTime extends Cache
{
    const FORMAT_MOMENT_JS         = 1;
    const FORMAT_JQUERY_DATEPICKER = 2;
    const FORMAT_PICKADATE         = 3;

    private static $format_characters_day = array( 'd', 'D', 'j', 'l', 'N', 'S', 'w', 'z' );
    private static $format_characters_month = array( 'F', 'm', 'M', 'n' );
    private static $format_characters_year = array( 'o', 'Y', 'y' );
    private static $format_replacements = array(
        self::FORMAT_MOMENT_JS => array(
            'd' => 'DD',
            '\d' => '[d]',
            'D' => 'ddd',
            '\D' => '[D]',
            'j' => 'D',
            '\j' => 'j',
            'l' => 'dddd',
            '\l' => 'l',
            'N' => 'E',
            '\N' => 'N',
            'S' => 'o',
            '\S' => '[S]',
            'w' => 'e',
            '\w' => '[w]',
            'z' => 'DDD',
            '\z' => '[z]',
            'W' => 'W',
            '\W' => '[W]',
            'F' => 'MMMM',
            '\F' => 'F',
            'm' => 'MM',
            '\m' => '[m]',
            'M' => 'MMM',
            '\M' => '[M]',
            'n' => 'M',
            '\n' => 'n',
            't' => '',
            '\t' => 't',
            'L' => '',
            '\L' => 'L',
            'o' => 'YYYY',
            '\o' => 'o',
            'Y' => 'YYYY',
            '\Y' => 'Y',
            'y' => 'YY',
            '\y' => 'y',
            'a' => 'a',
            '\a' => '[a]',
            'A' => 'A',
            '\A' => '[A]',
            'B' => '',
            '\B' => 'B',
            'g' => 'h',
            '\g' => 'g',
            'G' => 'H',
            '\G' => 'G',
            'h' => 'hh',
            '\h' => '[h]',
            'H' => 'HH',
            '\H' => '[H]',
            'i' => 'mm',
            '\i' => 'i',
            's' => 'ss',
            '\s' => '[s]',
            'u' => 'SSS',
            '\u' => 'u',
            'e' => 'zz',
            '\e' => '[e]',
            'I' => '',
            '\I' => 'I',
            'O' => '',
            '\O' => 'O',
            'P' => '',
            '\P' => 'P',
            'T' => '',
            '\T' => 'T',
            'Z' => '',
            '\Z' => '[Z]',
            'c' => '',
            '\c' => 'c',
            'r' => '',
            '\r' => 'r',
            'U' => 'X',
            '\U' => 'U',
            '\\' => '',
        ),
        self::FORMAT_JQUERY_DATEPICKER => array(
            // Day
            'd' => 'dd',
            '\d' => '\'d\'',
            'j' => 'd',
            '\j' => 'j',
            'l' => 'DD',
            '\l' => 'l',
            'D' => 'D',
            '\D' => '\'D\'',
            'z' => 'o',
            '\z' => 'z',
            // Month
            'm' => 'mm',
            '\m' => '\'m\'',
            'n' => 'm',
            '\n' => 'n',
            'F' => 'MM',
            '\F' => 'F',
            // Year
            'Y' => 'yy',
            '\Y' => 'Y',
            'y' => 'y',
            '\y' => '\'y\'',
            // Others
            'S' => '',
            '\S' => 'S',
            'o' => 'yy',
            '\o' => '\'o\'',
            '\\' => '',
        ),
        self::FORMAT_PICKADATE => array(
            // Day
            'd' => 'dd',
            '\d' => '!d',
            'D' => 'ddd',
            '\D' => 'D',
            'l' => 'dddd',
            '\l' => 'l',
            'j' => 'd',
            '\j' => 'j',
            // Month
            'm' => 'mm',
            '\m' => '!m',
            'M' => 'mmm',
            '\M' => 'M',
            'F' => 'mmmm',
            '\F' => 'F',
            'n' => 'm',
            '\n' => 'n',
            // Year
            'y' => 'yy',
            '\y' => 'y',
            'Y' => 'yyyy',
            '\Y' => 'Y',
            // Others
            'S' => '',
            '\S' => 'S',
            '\\' => '',
        ),
    );

    /**
     * Format ISO date (or seconds) according to WP date format setting.
     *
     * @param string|integer $iso_date
     * @return string
     */
    public static function formatDate( $iso_date )
    {
        return date_i18n( get_option( 'date_format' ), is_numeric( $iso_date ) ? $iso_date : strtotime( $iso_date, current_time( 'timestamp' ) ) );
    }

    /**
     * Skip unsupported formatting options in js library
     *
     * @param string|integer $iso_date
     * @param int $for
     * @return string
     * @deprecated since 18.4
     */
    public static function formatDateFor( $iso_date, $for )
    {
        $replacements = array();
        switch ( $for ) {
            case self::FORMAT_JQUERY_DATEPICKER:
            case self::FORMAT_MOMENT_JS:
            case self::FORMAT_PICKADATE:
                foreach ( self::$format_replacements[ $for ] as $key => $value ) {
                    if ( $value === '' ) {
                        $replacements[ $key ] = $value;
                    }
                }
                break;
        }

        return date_i18n( strtr( get_option( 'date_format' ), $replacements ), is_numeric( $iso_date ) ? $iso_date : strtotime( $iso_date, current_time( 'timestamp' ) ) );
    }

    /**
     * Format ISO time (or seconds) according to WP time format setting.
     *
     * @param string|integer $iso_time
     * @return string
     */
    public static function formatTime( $iso_time )
    {
        return date_i18n( get_option( 'time_format' ), is_numeric( $iso_time ) ? $iso_time : strtotime( $iso_time, current_time( 'timestamp' ) ) );
    }

    /**
     * Format ISO datetime according to WP date and time format settings.
     *
     * @param string $iso_date_time
     * @return string
     */
    public static function formatDateTime( $iso_date_time )
    {
        return self::formatDate( $iso_date_time ) . ' ' . self::formatTime( $iso_date_time );
    }

    /**
     * Apply time zone or time zone offset (in JS format) to the given ISO date and time
     * which is considered to be in WP time zone.
     *
     * @param string $iso_date_time
     * @param string $time_zone
     * @param int $offset Offset in JS format (i.e. in minutes and +/- opposite to PHP format)
     * @param string $format Output format
     * @return false|string
     */
    public static function applyTimeZone( $iso_date_time, $time_zone, $offset, $format = 'Y-m-d H:i:s' )
    {
        $date = $iso_date_time;
        if ( $time_zone !== null ) {
            $date = self::convertTimeZone( $iso_date_time, Lib\Config::getWPTimeZone(), $time_zone, $format );
        } elseif ( $offset !== null ) {
            $date = self::applyTimeZoneOffset( $iso_date_time, $offset, $format );
        }

        return $date;
    }

    /**
     * Apply time zone offset (in JS format) to the given ISO date and time
     * which is considered to be in WP time zone.
     *
     * @param string $iso_date_time
     * @param int $offset Offset in JS format (i.e. in minutes and +/- opposite to PHP format)
     * @param string $format Output format
     * @return false|string
     */
    public static function applyTimeZoneOffset( $iso_date_time, $offset, $format = 'Y-m-d H:i:s' )
    {
        $client_diff = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS + $offset * 60;

        return date( $format, strtotime( $iso_date_time ) - $client_diff );
    }

    /**
     * From UTC0 datetime to WP timezone time
     *
     * @param string $iso_date_time UTC0 time
     * @param string $format Output format
     * @return string
     */
    public static function UTCToWPTimeZone( $iso_date_time, $format = 'Y-m-d H:i:s' )
    {
        return date( $format, strtotime( $iso_date_time ) + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
    }

    /**
     * Convert ISO date and time string from one time zone to another
     *
     * @param string $iso_date_time
     * @param string $from_time_zone
     * @param string $to_time_zone
     * @param string $format
     * @return false|string
     */
    public static function convertTimeZone( $iso_date_time, $from_time_zone, $to_time_zone, $format = 'Y-m-d H:i:s' )
    {
        return date_format( date_timestamp_set(
            date_create( $to_time_zone ),
            date_timestamp_get( date_create( $iso_date_time . ' ' . $from_time_zone ) )
        ), $format );
    }

    /**
     * Convert WordPress date and time format into requested JS format.
     *
     * @param string $source_format
     * @param int $to
     * @return string
     */
    public static function convertFormat( $source_format, $to )
    {
        switch ( $source_format ) {
            case 'date':
                $php_format = get_option( 'date_format', 'Y-m-d' );
                break;
            case 'time':
                $php_format = get_option( 'time_format', 'H:i' );
                break;
            default:
                $php_format = $source_format;
        }

        switch ( $to ) {
            case self::FORMAT_MOMENT_JS:
            case self::FORMAT_PICKADATE:
                return strtr( $php_format, self::$format_replacements[ $to ] );
            case self::FORMAT_JQUERY_DATEPICKER:
                return str_replace( '\'\'', '', strtr( $php_format, self::$format_replacements[ $to ] ) );
        }

        return $php_format;
    }

    /**
     * Build time string from seconds
     *
     * @param int $seconds
     * @param bool $show_seconds
     * @return string
     */
    public static function buildTimeString( $seconds, $show_seconds = true )
    {
        $sign = $seconds < 0 ? '-' : '';
        $seconds = abs( $seconds );
        $hours = (int) ( $seconds / 3600 );
        $seconds -= $hours * 3600;
        $minutes = (int) ( $seconds / 60 );
        $seconds -= $minutes * 60;

        return $show_seconds
            ? sprintf( '%s%02d:%02d:%02d', $sign, $hours, $minutes, $seconds )
            : sprintf( '%s%02d:%02d', $sign, $hours, $minutes );
    }

    /**
     * Convert time in format H:i:s to seconds.
     *
     * @param $str
     * @return int
     */
    public static function timeToSeconds( $str )
    {
        $result = 0;
        if ( $str ) {
            $seconds = 3600;
            if ( $str[0] === '-' ) {
                $sign = -1;
                $str = substr( $str, 1 );
            } else {
                $sign = 1;
            }
            foreach ( explode( ':', $str ) as $part ) {
                $result += $sign * (int) $part * $seconds;
                $seconds /= 60;
            }
        }

        return $result;
    }

    /**
     * Convert number of seconds into string "[XX year] [XX month] [XX week] [XX day] [XX h] XX min".
     *
     * @param int $duration
     * @return string
     */
    public static function secondsToInterval( $duration )
    {
        $key = __FUNCTION__ . '-' . $duration;
        if ( ! self::hasInCache( $key ) ) {
            $duration = (int) $duration;
            $month_in_seconds = 30 * DAY_IN_SECONDS;
            $years = (int) ( $duration / YEAR_IN_SECONDS );
            $months = (int) ( ( $duration % YEAR_IN_SECONDS ) / $month_in_seconds );
            $weeks = (int) ( ( ( $duration % YEAR_IN_SECONDS ) % $month_in_seconds ) / WEEK_IN_SECONDS );
            $days = (int) ( ( ( ( $duration % YEAR_IN_SECONDS ) % $month_in_seconds ) % WEEK_IN_SECONDS ) / DAY_IN_SECONDS );
            $hours = (int) ( ( ( ( $duration % YEAR_IN_SECONDS ) % $month_in_seconds ) % DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
            $minutes = (int) ( ( ( ( $duration % YEAR_IN_SECONDS ) % $month_in_seconds ) % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );

            $parts = array();

            if ( $years > 0 ) {
                $parts[] = sprintf( _n( '%d year', '%d years', $years, 'bookly' ), $years );
            }
            if ( $months > 0 ) {
                $parts[] = sprintf( _n( '%d month', '%d months', $months, 'bookly' ), $months );
            }
            if ( $weeks > 0 ) {
                $parts[] = sprintf( _n( '%d week', '%d weeks', $weeks, 'bookly' ), $weeks );
            }
            if ( $days > 0 ) {
                $parts[] = sprintf( _n( '%d day', '%d days', $days, 'bookly' ), $days );
            }
            if ( $hours > 0 ) {
                $parts[] = sprintf( __( '%d h', 'bookly' ), $hours );
            }
            if ( $minutes > 0 ) {
                $parts[] = sprintf( __( '%d min', 'bookly' ), $minutes );
            }

            self::putInCache( $key, implode( ' ', $parts ) );
        }

        return self::getFromCache( $key );
    }

    /**
     * Return formatted time interval
     *
     * @param string $start_time like 08:00:00
     * @param string $end_time like 18:45:00
     * @return string
     */
    public static function formatInterval( $start_time, $end_time )
    {
        return self::formatTime( self::timeToSeconds( $start_time ) ) . ' - ' . self::formatTime( self::timeToSeconds( $end_time ) );
    }

    /**
     * Format offset in seconds (e.g. 3600 => +01:00, -9000 => -02:30, etc.)
     *
     * @param int $offset
     * @return string
     */
    public static function formatOffset( $offset )
    {
        return sprintf(
            '%s%02d:%02d',
            $offset >= 0 ? '+' : '-',
            abs( $offset / HOUR_IN_SECONDS ),
            abs( $offset / MINUTE_IN_SECONDS ) % 60
        );
    }

    /**
     * Get offset in seconds for given time zone
     *
     * @param string $time_zone
     * @return int
     */
    public static function timeZoneOffset( $time_zone )
    {
        if ( preg_match( '/^UTC[+-]/', $time_zone ) ) {
            $offset = preg_replace( '/UTC\+?/', '', $time_zone );

            return $offset * HOUR_IN_SECONDS;
        } else {
            return timezone_offset_get( timezone_open( $time_zone ), new \DateTime() );
        }
    }

    /**
     * Get date parts order according to current date format.
     *
     * @return array
     */
    public static function getDatePartsOrder()
    {
        $order = array();
        $date_format = preg_replace( '/[^A-Za-z]/', '', get_option( 'date_format' ) );

        foreach ( str_split( $date_format ) as $character ) {
            switch ( true ) {
                case in_array( $character, self::$format_characters_day ):
                    $order[] = 'day';
                    break;
                case in_array( $character, self::$format_characters_month ):
                    $order[] = 'month';
                    break;
                case in_array( $character, self::$format_characters_year ):
                    $order[] = 'year';
                    break;
            }
        }

        $order = array_unique( $order );

        return count( $order ) == 3 ? $order : array( 'month', 'day', 'year' );
    }

    /**
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validateDate( $date, $format = 'Y-m-d' )
    {
        if ( $date === null ) {
            return false;
        }
        $d = \DateTime::createFromFormat( $format, $date );

        return $d && $d->format( $format ) === $date;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function dateRangeOptions( $array = array() )
    {
        return array_merge(
            array(
                'format' => self::convertFormat( 'date', self::FORMAT_MOMENT_JS ),
                'applyLabel' => __( 'Apply', 'bookly' ),
                'cancelLabel' => __( 'Cancel', 'bookly' ),
                'fromLabel' => __( 'From', 'bookly' ),
                'toLabel' => __( 'To', 'bookly' ),
                'customRangeLabel' => __( 'Custom range', 'bookly' ),
                'tomorrow' => __( 'Tomorrow', 'bookly' ),
                'today' => __( 'Today', 'bookly' ),
                'yesterday' => __( 'Yesterday', 'bookly' ),
                'last_7' => __( 'Last 7 days', 'bookly' ),
                'last_30' => __( 'Last 30 days', 'bookly' ),
                'next_7' => __( 'Next 7 days', 'bookly' ),
                'next_30' => __( 'Next 30 days', 'bookly' ),
                'thisMonth' => __( 'This month', 'bookly' ),
                'nextMonth' => __( 'Next month', 'bookly' ),
                'firstDay' => (int) get_option( 'start_of_week' ),
            ),
            $array
        );
    }

    /**
     * @param array $array
     * @return array
     */
    public static function datePickerOptions( $array = array() )
    {
        /** @var \WP_Locale $wp_locale */
        global $wp_locale;

        if ( ! $wp_locale ) {
            $wp_locale = new \WP_Locale();
        }

        if ( is_rtl() ) {
            $array['direction'] = 'rtl';
        }

        return array_merge(
            array(
                'format' => self::convertFormat( 'date', self::FORMAT_MOMENT_JS ),
                'monthNames' => array_values( $wp_locale->month ),
                'daysOfWeek' => array_values( $wp_locale->weekday_abbrev ),
                'firstDay' => (int) get_option( 'start_of_week' ),
                'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
                'dayNames' => array_values( $wp_locale->weekday ),
                'dayNamesShort' => array_values( $wp_locale->weekday_abbrev ),
                'meridiem' => $wp_locale->meridiem,
            ),
            $array
        );
    }

    /**
     * Get options for time zone switcher
     *
     * @return array
     */
    public static function getTimeZoneOptions()
    {
        $result = array();

        unload_textdomain( 'continents-cities' );
        load_textdomain( 'continents-cities', WP_LANG_DIR . '/continents-cities-' . get_locale() . '.mo' );

        $unsorted = array();
        foreach ( timezone_identifiers_list() as $zone_value ) {
            $zone = explode( '/', $zone_value, 2 );
            $key = translate( $zone[0], 'continents-cities' );
            if ( ! array_key_exists( $key, $unsorted ) ) {
                $unsorted[ $key ] = array();
            }
            $unsorted[ $key ][ $zone_value ] = translate( isset( $zone[1] ) ? implode( ' - ', array_map( function( $item ) { return translate( $item, 'continents-cities' ); }, explode( '/', str_replace( '_', ' ', $zone[1] ) ) ) ) : $zone[0], 'continents-cities' );
        }

        // Sort arrays
        unset( $unsorted['UTC'] );
        $sorted_continents = array_keys( $unsorted );
        asort( $sorted_continents );
        foreach ( $sorted_continents as $continent ) {
            $continent_data = $unsorted[ $continent ];
            asort( $continent_data );
            $result[ $continent ] = $continent_data;
        }
        $result['UTC'] = array( 'UTC' => 'UTC' );

        $offset_range = array(
            -12,
            -11.5,
            -11,
            -10.5,
            -10,
            -9.5,
            -9,
            -8.5,
            -8,
            -7.5,
            -7,
            -6.5,
            -6,
            -5.5,
            -5,
            -4.5,
            -4,
            -3.5,
            -3,
            -2.5,
            -2,
            -1.5,
            -1,
            -0.5,
            0,
            0.5,
            1,
            1.5,
            2,
            2.5,
            3,
            3.5,
            4,
            4.5,
            5,
            5.5,
            5.75,
            6,
            6.5,
            7,
            7.5,
            8,
            8.5,
            8.75,
            9,
            9.5,
            10,
            10.5,
            11,
            11.5,
            12,
            12.75,
            13,
            13.75,
            14,
        );

        foreach ( $offset_range as $offset ) {
            if ( 0 <= $offset ) {
                $offset_name = '+' . $offset;
            } else {
                $offset_name = (string) $offset;
            }

            $offset_value = $offset_name;
            $offset_name = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
            $offset_name = 'UTC' . $offset_name;
            $offset_value = 'UTC' . $offset_value;
            $result[ __( 'Manual Offsets' ) ][ $offset_value ] = $offset_name;
        }

        return $result;
    }

    /**
     * Convert date time from WP time zone to current staff time zone
     *
     * @param string $iso_date_time
     * @return string
     */
    public static function applyStaffTimeZone( $iso_date_time )
    {
        static $staff_tz;
        if ( $staff_tz === null ) {
            $staff = Lib\Entities\Staff::query()->where( 'wp_user_id', get_current_user_id() )->findOne();
            $staff_tz = ( $staff && $staff->getTimeZone() ) ? $staff->getTimeZone() : false;
        }

        if ( $staff_tz ) {
            $iso_date_time = self::convertTimeZone( $iso_date_time, Lib\Config::getWPTimeZone(), $staff_tz );
        }

        return $iso_date_time;
    }
}