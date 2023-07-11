<?php
namespace Bookly\Backend\Modules\CloudWhatsapp;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Modules\CloudWhatsapp
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Save settings
     *
     * @return void
     */
    public static function cloudWhatsappSaveSettings()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $cloud->whatsapp->setSettings( self::parameter( 'access_token' ), self::parameter( 'phone_id' ), self::parameter( 'business_account_id' ) )
            ? wp_send_json_success()
            : wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
    }

    /**
     * Get messages list
     *
     * @return void
     */
    public static function getMessagesList()
    {
        $dates = explode( ' - ', self::parameter( 'range' ), 2 );
        $start = Lib\Utils\DateTime::applyTimeZoneOffset( $dates[0], 0 );
        $end   = Lib\Utils\DateTime::applyTimeZoneOffset( date( 'Y-m-d', strtotime( '+1 day', strtotime( $dates[1] ) ) ), 0 );

        wp_send_json( Lib\Cloud\API::getInstance()->whatsapp->getMessagesList( $start, $end ) );
    }
}