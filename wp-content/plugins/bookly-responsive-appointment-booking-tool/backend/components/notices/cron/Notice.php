<?php
namespace Bookly\Backend\Components\Notices\Cron;

use Bookly\Lib;

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