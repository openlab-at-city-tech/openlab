<?php
namespace Bookly\Backend\Components\Dialogs\Notifications;

use Bookly\Lib;
use Bookly\Backend\Components\Dialogs\Sms\Dialog as SmsDialog;
use Bookly\Backend\Modules\Notifications\Lib\Codes;
use Bookly\Lib\Entities\Notification;

class Dialog extends SmsDialog
{
    /**
     * Render notification dialog.
     */
    public static function render()
    {
        // Add WP media button in tiny
        wp_enqueue_media();
        add_filter( 'mce_buttons', function ( $buttons ) {
            $mce_buttons = array(
                'array_unshift' => array( 'fontsizeselect', 'fontselect', ),
                'array_push' => array( 'wp_add_media', ),
            );

            foreach ( $mce_buttons as $method => $tools ) {
                foreach ( $tools as $tool ) {
                    if ( ! in_array( $tool, $buttons ) ) {
                        $method( $buttons, $tool );
                    }
                }
            }

            return $buttons;
        }, 10, 1 );

        add_filter( 'mce_buttons_2', function ( $buttons ) {
            $mce_buttons = array( 'backcolor', 'styleselect', );
            foreach ( $mce_buttons as $tool ) {
                if ( ! in_array( $tool, $buttons ) ) {
                    $buttons[] = $tool;
                }
            }

            return $buttons;
        }, 10, 1 );

        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'bookly' => array( 'backend/components/dialogs/sms/resources/js/notification-dialog.js' => array( 'bookly-backend-globals' ) ),
        ) );

        $codes = new Codes( 'email' );
        $codes_list = array();
        foreach ( Notification::getTypes() as $notification_type ) {
            $codes_list[ $notification_type ] = $codes->getCodes( $notification_type );
        }

        wp_localize_script( 'bookly-notification-dialog.js', 'BooklyNotificationDialogL10n', array(
            'recurringActive' => (int) Lib\Config::recurringAppointmentsActive(),
            'defaultNotification' => self::getDefaultNotification(),
            'codes' => $codes_list,
            'gateway' => 'email',
            'title' => array(
                'container' => __( 'Email', 'bookly' ),
                'new' => __( 'New email notification', 'bookly' ),
                'edit' => __( 'Edit email notification', 'bookly' ),
                'create' => __( 'Create notification', 'bookly' ),
                'save' => __( 'Save notification', 'bookly' ),
            ),
        ) );

        SmsDialog::renderTemplate( 'dialog', array( 'self' => __CLASS__, 'gateway' => 'email' ) );
    }
}