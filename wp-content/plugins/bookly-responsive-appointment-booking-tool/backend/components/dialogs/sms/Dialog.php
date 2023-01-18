<?php
namespace Bookly\Backend\Components\Dialogs\Sms;

use Bookly\Backend\Modules\Notifications\Lib\Codes;
use Bookly\Lib;
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Lib\Entities\Notification;

/**
 * Class Dialog
 * @package Bookly\Backend\Components\Dialogs\Sms
 */
class Dialog extends Lib\Base\Component
{
    /**
     * Render notification dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/notification-dialog.js' => array( 'bookly-backend-globals', 'bookly-editor.js' ) ),
        ) );

        $codes = new Codes( 'sms' );
        $codes_list = array();
        foreach ( Notification::getTypes() as $notification_type ) {
            $codes_list[ $notification_type ] = $codes->getCodes( $notification_type );
        }

        wp_localize_script( 'bookly-notification-dialog.js', 'BooklyNotificationDialogL10n', array(
            'recurringActive' => (int) Lib\Config::recurringAppointmentsActive(),
            'defaultNotification' => self::getDefaultNotification(),
            'codes' => $codes_list,
            'sms' => true,
            'title' => array(
                'container' => __( 'Sms', 'bookly' ),
                'new' => __( 'New sms notification', 'bookly' ),
                'edit' => __( 'Edit sms notification', 'bookly' ),
                'create' => __( 'Create notification', 'bookly' ),
                'save' => __( 'Save notification', 'bookly' ),
            ),
        ) );

        self::renderTemplate( 'dialog', array( 'gateway' => 'sms' ) );
    }

    public static function renderNewNotificationButton()
    {
        print '<div class="col-auto">';
        Buttons::renderAdd( 'bookly-js-new-notification', 'btn-success', __( 'New notification', 'bookly' ) );
        print '</div>';
    }

    /**
     * @return array
     */
    protected static function getDefaultNotification()
    {
        return array(
            'type' => Lib\Entities\Notification::TYPE_NEW_BOOKING,
            'active' => 1,
            'attach_ics' => 0,
            'attach_invoice' => 0,
            'message' => '',
            'name' => '',
            'subject' => '',
            'to_admin' => 0,
            'to_customer' => 1,
            'to_staff' => 0,
            'settings' => Lib\DataHolders\Notification\Settings::getDefault(),
        );
    }
}