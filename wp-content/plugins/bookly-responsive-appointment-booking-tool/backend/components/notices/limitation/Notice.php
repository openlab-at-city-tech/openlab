<?php
namespace Bookly\Backend\Components\Notices\Limitation;

use Bookly\Lib;

/**
 * Class Notice
 * @package Bookly\Backend\Components\Notices\Limitation
 */
class Notice extends Lib\Base\Component
{
    /**
     * Render limitation notice.
     */
    public static function forNewService()
    {
        return self::renderTemplate( 'limitation_service', array(), false );
    }

    /**
     * Render limitation notice.
     */
    public static function forNewStaff()
    {
        return self::renderTemplate( 'limitation_staff', array(), false );
    }
}