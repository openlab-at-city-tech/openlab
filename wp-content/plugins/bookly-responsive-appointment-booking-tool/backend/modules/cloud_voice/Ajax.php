<?php
namespace Bookly\Backend\Modules\CloudVoice;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Modules\CloudVoice
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Save settings
     *
     * @return void
     */
    public static function cloudVoiceSaveSettings()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $cloud->voice->setSettings( self::parameter( 'language' ) )
            ? wp_send_json_success()
            : wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
    }

    /**
     * Make a test call
     *
     * @return void
     */
    public static function makeTestCall()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $phone_number = self::parameter( 'phone_number' );
        $cloud->voice->call( $phone_number, 'Hello, this is a test call from Bookly', 'Hello, this is a test call from Bookly' )
            ? wp_send_json_success( array( 'message' => sprintf( __( 'Calling %s', 'bookly' ), $phone_number ) . ' …' ) )
            : wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ?: __( 'Failed', 'bookly' ) ) );
    }

    /**
     * Get calls list
     *
     * @return void
     */
    public static function getCallsList()
    {
        $dates = explode( ' - ', self::parameter( 'range' ), 2 );
        $start = Lib\Utils\DateTime::applyTimeZoneOffset( $dates[0], 0 );
        $end   = Lib\Utils\DateTime::applyTimeZoneOffset( date( 'Y-m-d', strtotime( '+1 day', strtotime( $dates[1] ) ) ), 0 );

        wp_send_json( Lib\Cloud\API::getInstance()->voice->getCallsList( $start, $end ) );
    }

    /**
     * Get voice price-list.
     */
    public static function getVoicePriceList()
    {
        wp_send_json( Lib\Cloud\API::getInstance()->voice->getPriceList() );
    }
}