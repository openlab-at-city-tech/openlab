<?php
namespace Bookly\Backend\Components\Notices\Cron;

use Bookly\Lib;

/**
 * Class Cron
 * @package Bookly\Backend\Components\Notices\Cron
 */
class Notice extends Lib\Base\Component
{
    /**
     * Render configure cron notice.
     */
    public static function render()
    {
        if ( ! Lib\Cloud\API::getInstance()->account->productActive( 'cron' ) ) {
            return self::renderTemplate( 'notice' );
        }
    }
}