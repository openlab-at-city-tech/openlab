<?php
namespace Bookly\Backend\Modules\Notifications;

use Bookly\Lib;

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
        
        $datatables = Lib\Utils\Tables::getSettings( array( Lib\Utils\Tables::EMAIL_NOTIFICATIONS, Lib\Utils\Tables::EMAIL_LOGS ) );

        wp_localize_script( 'bookly-email-notifications.js', 'BooklyL10n', array(
            'sentSuccessfully' => __( 'Sent successfully.', 'bookly' ),
            'settingsSaved' => __( 'Settings saved.', 'bookly' ),
            'areYouSure' => __( 'Are you sure?', 'bookly' ),
            'noResults' => __( 'No records.', 'bookly' ),
            'processing' => __( 'Processing...', 'bookly' ),
            'emptyTable' => __( 'No data available in table', 'bookly' ),
            'zeroRecordsAlt' => __( 'No matching records found', 'bookly' ),
            'loadingRecords' => __( 'Loading...', 'bookly' ),
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