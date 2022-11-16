<?php
namespace Bookly\Backend\Modules\Notifications;

use Bookly\Lib;

/**
 * Class Page
 * @package Bookly\Backend\Modules\Notifications
 */
class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        $tab = self::parameter( 'tab', 'notifications' );

        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/email-notifications.js' => array( 'bookly-backend-globals' ) ),
            'bookly' => array( 'backend/modules/cloud_sms/resources/js/notifications-list.js' => array( 'bookly-backend-globals' ), ),
        ) );

        Proxy\Shared::enqueueAssets();
        
        $datatables = Lib\Utils\Tables::getSettings( array( 'email_notifications', 'email_logs' ) );

        wp_localize_script( 'bookly-email-notifications.js', 'BooklyL10n', array(
            'sentSuccessfully' => __( 'Sent successfully.', 'bookly' ),
            'settingsSaved' => __( 'Settings saved.', 'bookly' ),
            'areYouSure' => __( 'Are you sure?', 'bookly' ),
            'noResults' => __( 'No records.', 'bookly' ),
            'processing' => __( 'Processing...', 'bookly' ),
            'state' => array( __( 'Disabled', 'bookly' ), __( 'Enabled', 'bookly' ) ),
            'action' => array( __( 'enable', 'bookly' ), __( 'disable', 'bookly' ) ),
            'edit' => __( 'Edit', 'bookly' ),
            'gateway' => 'email',
            'tab' => $tab,
            'datatables' => $datatables,
        ) );

        self::renderTemplate( 'index', compact( 'tab' ) );
    }
}