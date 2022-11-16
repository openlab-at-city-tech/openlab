<?php
namespace Bookly\Backend\Components\Dialogs\Queue;

use Bookly\Lib;

/**
 * Class Dialog
 * @package Bookly\Backend\Components\Dialogs\Queue
 */
class Dialog extends Lib\Base\Component
{
    /**
     * Render notifications queue dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/queue-dialog.js' => array( 'bookly-backend-globals' ), ),
        ) );

        wp_localize_script( 'bookly-queue-dialog.js', 'BooklyL10nNotificationsQueueDialog', array(
            'csrf_token' => Lib\Utils\Common::getCsrfToken(),
            'l10n' => array(
                'title' => __( 'Send notifications', 'bookly' ),
                'send' => __( 'Send', 'bookly' ),
                'close' => __( 'Close', 'bookly' ),
            )
        ) );

        print '<div id="bookly-notifications-queue-dialog"></div>';
    }
}