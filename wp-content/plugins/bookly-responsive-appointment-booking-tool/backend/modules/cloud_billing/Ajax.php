<?php
namespace Bookly\Backend\Modules\CloudBilling;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Modules\CloudBilling
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Get purchases list.
     */
    public static function getPurchasesList()
    {
        $dates = explode( ' - ', self::parameter( 'range' ), 2 );
        $start = Lib\Utils\DateTime::applyTimeZoneOffset( $dates[0], 0 );
        $end   = Lib\Utils\DateTime::applyTimeZoneOffset( date( 'Y-m-d', strtotime( '+1 day', strtotime( $dates[1] ) ) ), 0 );

        wp_send_json( Lib\Cloud\API::getInstance()->account->getPurchasesList( $start, $end ) );
    }
}