<?php
namespace Bookly\Backend\Components\Notices\Limitation;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    public static function requiredBooklyPro()
    {
        wp_send_json_success( array(
            'image' => plugins_url( 'backend/components/notices/limitation/resources/images/bookly-pro-required.png', Lib\Plugin::getMainFile() ),
            'caption' => __( 'This is a Pro version feature', 'bookly' ),
            'body' => __( 'To get access to more features, lifetime free updates and 24/7 support, upgrade to the Pro version of Bookly.', 'bookly' ),
            'features' => array(
                __( 'Compatibility with Bookly add-ons', 'bookly' ),
                __( 'Unlimited staff members', 'bookly' ),
                __( 'Unlimited services', 'bookly' ),
                __( 'Modern booking forms', 'bookly' ),
                __( 'Online meetings', 'bookly' ) . ' (Zoom, Google Meet, Jitsi, BigBlueButton)',
                __( 'WooCommerce compatibility', 'bookly' ),
                __( 'Google Calendar integration', 'bookly' ),
                __( 'Advanced service and staff management', 'bookly' ),
            ),
            'upgrade' => __( 'Upgrade', 'bookly' ),
            'close' => __( 'Close', 'bookly' ),
        ) );
    }
}