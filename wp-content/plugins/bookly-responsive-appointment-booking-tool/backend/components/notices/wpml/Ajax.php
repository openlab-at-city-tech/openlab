<?php
namespace Bookly\Backend\Components\Notices\Wpml;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Notices\Wpml
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Dismiss 'WPML re save' notice.
     */
    public static function dismissWpmlResaveNotice()
    {
        update_option( 'bookly_show_wpml_resave_required_notice', '0' );

        wp_send_json_success();
    }
}