<?php

namespace Bookly\Backend\Modules\Diagnostics\Tests;

use Bookly\Lib;

/**
 * Class LogErrors
 *
 * @package Bookly\Backend\Modules\Diagnostics\Tests
 */
class LogErrors extends Test
{
    protected $slug = 'check-log-errors';
    public $error_type = 'error';
    
    public function __construct()
    {
        $this->title = __( 'Critical errors', 'bookly' );
        $this->description = __( 'This test checks for critical errors in Bookly.', 'bookly' );
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $errors = Lib\Entities\Log::query( 'l' )
            ->where( 'action', Lib\Utils\Log::ACTION_ERROR )
            ->whereGt( 'created_at', date_create( current_time( 'mysql' ) )->modify( '-1 day' )->format( 'Y-m-d H:i:s' ) )
            ->whereLike( 'target', '%bookly-%' )
            ->count();

        if ( $errors ) {
            $this->addError( __( 'Some critical errors in Bookly were found recently. Please check Settings > Logs and contact Bookly support.', 'bookly' ) );
        }

        return empty( $this->errors );
    }
}