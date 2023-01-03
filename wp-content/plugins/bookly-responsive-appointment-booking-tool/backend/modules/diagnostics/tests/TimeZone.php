<?php

namespace Bookly\Backend\Modules\Diagnostics\Tests;

use Bookly\Lib;

/**
 * Class TimeZone
 *
 * @package Bookly\Backend\Modules\Diagnostics\Tests
 */
class TimeZone extends Test
{
    protected $slug = 'time-zone';

    public function __construct()
    {
        $this->title = __( 'Time settings', 'bookly' );
        $this->description = __( 'We recommend to use timezones with geographic names (e.g., "Europe/London") instead of "UTC +1". Using numerical representation may cause errors with daylight saving time. You can modify your timezone in WP Settings > General > Timezone.', 'bookly' );
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        if ( 'UTC' !== date_default_timezone_get() ) {
            $this->addError( 'РНР default timezone is incorrect' );
        }

        $timezone_string = get_option( 'timezone_string' );
        if ( ! $timezone_string ) {
            $this->addError( 'You\'re using numerical representation of timezone in WP settings. Please change it to geographic.' );
        }

        if ( ! get_option( 'time_format' ) || ! trim( Lib\Utils\DateTime::formatTime( time() ) ) ) {
            $this->addError( 'You\'re using incorrect time format in WP settings.' );
        }

        if ( ! get_option( 'date_format' ) || ! trim( Lib\Utils\DateTime::formatDate( time() ) ) ) {
            $this->addError( 'You\'re using incorrect date format in WP settings.' );
        }

        return empty( $this->errors );
    }
}