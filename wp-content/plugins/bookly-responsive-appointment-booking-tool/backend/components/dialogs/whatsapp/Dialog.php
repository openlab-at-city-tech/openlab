<?php
namespace Bookly\Backend\Components\Dialogs\Whatsapp;

use Bookly\Backend\Modules\Notifications\Lib\Codes;
use Bookly\Lib\Config;
use Bookly\Lib\Entities\Notification;
use Bookly\Backend\Components\Dialogs\Sms\Dialog as SmsDialog;

class Dialog extends SmsDialog
{
    /**
     * Render WhatsApp notification dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'bookly' => array( 'backend/components/dialogs/sms/resources/js/notification-dialog.js' => array( 'bookly-backend-globals' ) ),
        ) );

        $codes = new Codes( 'sms' );
        $codes_list = array();
        foreach ( Notification::getTypes() as $notification_type ) {
            $codes_list[ $notification_type ] = $codes->getCodes( $notification_type );
        }

        wp_localize_script( 'bookly-notification-dialog.js', 'BooklyNotificationDialogL10n', array(
            'recurringActive' => (int) Config::recurringAppointmentsActive(),
            'defaultNotification' => self::getDefaultNotification(),
            'codes' => $codes_list,
            'gateway' => 'whatsapp',
            'title' => array(
                'container' => __( 'Message', 'bookly' ),
                'new' => __( 'New WhatsApp notification', 'bookly' ),
                'edit' => __( 'Edit WhatsApp notification', 'bookly' ),
                'create' => __( 'Create notification', 'bookly' ),
                'save' => __( 'Save notification', 'bookly' ),
            ),
            'statuses' => array(
                'APPROVED' => __( 'Approved', 'bookly' ),
                'IN_APPEAL' => __( 'In appeal', 'bookly' ),
                'PENDING' => __( 'Pending', 'bookly' ),
                'REJECTED' => __( 'Rejected', 'bookly' ),
                'PENDING_DELETION' => __( 'Pending deletion', 'bookly' ),
                'DELETED' => __( 'Deleted', 'bookly' ),
                'DISABLED' => __( 'Disabled', 'bookly' ),
                'PAUSED' => __( 'Paused', 'bookly' ),
                'LIMIT_EXCEEDED' => __( 'Limit exceeded', 'bookly' ),
            ),
        ) );

        SmsDialog::renderTemplate( 'dialog', array( 'self' => __CLASS__, 'gateway' => 'whatsapp' ) );
    }
}