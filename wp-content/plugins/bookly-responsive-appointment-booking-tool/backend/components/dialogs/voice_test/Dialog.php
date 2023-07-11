<?php
namespace Bookly\Backend\Components\Dialogs\VoiceTest;

use Bookly\Lib;

/**
 * Class Dialog
 * @package Bookly\Backend\Components\Dialogs\VoiceTest
 */
class Dialog extends Lib\Base\Component
{
    /**
     * Render voice test notification dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/testing-voice.js' => array( 'bookly-backend-globals' ) ),
        ) );

        wp_localize_script( 'bookly-testing-voice.js', 'BooklyL10nTestingVoiceDialog', array(
            'l10n' => array(
                'admin_phone' => get_option( 'bookly_sms_administrator_phone' ),
                'title' => __( 'Test voice notifications', 'bookly' ),
                'to_phone' => __( 'To phone', 'bookly' ),
                'notification' => __( 'Notification', 'bookly' ),
                'call' => __( 'Call', 'bookly' ),
                'close' => __( 'Close', 'bookly' ),
            ),
        ) );

        print '<div id="bookly-testing-voice-dialog"></div>';
    }
}