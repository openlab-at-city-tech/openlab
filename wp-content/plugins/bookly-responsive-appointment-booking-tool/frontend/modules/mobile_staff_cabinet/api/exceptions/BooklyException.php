<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api\Exceptions;

class BooklyException extends \Exception
{
    public function __construct( $message )
    {
        parent::__construct( get_option( 'bookly_dev' ) && $message ? $message : 'ERROR' );
    }
}