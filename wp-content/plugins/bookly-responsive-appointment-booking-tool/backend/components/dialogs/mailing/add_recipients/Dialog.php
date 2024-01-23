<?php
namespace Bookly\Backend\Components\Dialogs\Mailing\AddRecipients;

use Bookly\Lib;
use Bookly\Backend\Components\Controls\Buttons;

class Dialog extends Lib\Base\Component
{
    /**
     * Render add recipients dialog.
     */
    public static function render()
    {
        self::enqueueScripts( array(
            'module' => array( 'js/add-recipients.js' => array( 'bookly-backend-globals' ), ),
        ) );

        $range = array(
            array( -365, __( 'year', 'bookly' ) . ' ' . __( 'ago', 'bookly' ) ),
            array( -122, sprintf( _n( '%d month', '%d months', 4, 'bookly' ), 4 ) . ' ' . __( 'ago', 'bookly' ) ),
            array( -92, sprintf( _n( '%d month', '%d months', 3, 'bookly' ), 3 ) . ' ' . __( 'ago', 'bookly' ) ),
            array( -61, sprintf( _n( '%d month', '%d months', 2, 'bookly' ), 2 ) . ' ' . __( 'ago', 'bookly' ) ),
        );
        foreach ( array_merge( array( - 28, - 21 ), range( - 14, - 1 ) ) as $days ) {
            $range[] = array( $days, Lib\Utils\DateTime::secondsToInterval( abs( $days ) * DAY_IN_SECONDS ) . ' ' . __( 'ago', 'bookly' ) );
        }
        $range[] = array( 0, __( 'Any', 'bookly' ) );
        foreach ( array_merge( range( 1, 14 ), array( 21, 28 ) ) as $days ) {
            $range[] = array( $days, __( 'in', 'bookly' ) . ' ' . Lib\Utils\DateTime::secondsToInterval( $days * DAY_IN_SECONDS ) );
        }
        $range[] = array( 61, sprintf( __( 'in', 'bookly' ) . ' ' . _n( '%d month', '%d months', 2, 'bookly' ), 2 ) );
        $range[] = array( 91, __( 'in', 'bookly' ) . ' ' . sprintf( _n( '%d month', '%d months', 3, 'bookly' ), 3 ) );
        $range[] = array( 122, __( 'in', 'bookly' ) . ' ' . sprintf( _n( '%d month', '%d months', 4, 'bookly' ), 4 ) );
        $range[] = array( 365, __( 'in', 'bookly' ) . ' ' . __( 'year', 'bookly' ) );

        wp_localize_script( 'bookly-add-recipients.js', 'BooklyL10nAddRecipientsDialog', array(
            'service' => Lib\Utils\Common::getServiceDataForDropDown( 's.type = "simple"' ),
            'staff' => Lib\Config::proActive() ? Lib\Proxy\Pro::getStaffDataForDropDown() : array( array( 'name' => '', 'items' => Lib\Entities\Staff::query()->select( 'id, full_name' )->whereNot( 'visibility', 'archive' )->sortBy( 'position, id' )->fetchArray(), ), ),
            'range' => $range,
            'l10n' => array(
                'recipients' => __( 'Recipients', 'bookly' ),
                'add_recipients' => __( 'Add recipients', 'bookly' ),
                'cancel' => __( 'Cancel', 'bookly' ),
                'automatic' => __( 'Automatic selection', 'bookly' ),
                'manual' => __( 'Manual selection', 'bookly' ),
                'recipients_placeholder' => __( 'Add phone numbers using international phone format, one number per line.', 'bookly' ) . PHP_EOL . __( 'E.g', 'bookly' ) . ':' . PHP_EOL . '+12021111111' . PHP_EOL . '+12021111112',
                'manual_help' => sprintf( __( 'You can add no more than %s contacts', 'bookly' ), 500 ),
                'automatic_help' => __( 'Please note that only customers who meet all of the conditions will be added to the list. You can find more information in our documentation', 'bookly' ),
                'sum_of_payments' => __( 'Total sum of payments, greater or equal than', 'bookly' ),
                'count_of_appointments' => __( 'Total number of appointments, greater or equal than', 'bookly' ),
                'providers' => __( 'Providers', 'bookly' ),
                'services' => __( 'Services', 'bookly' ),
                'last_appointment' => __( 'Last appointment', 'bookly' ),
                'all_services' => __( 'All services', 'bookly' ),
                'all_staff' => __( 'All staff', 'bookly' ),
                'no_service_selected' => __( 'No service selected', 'bookly' ),
                'no_staff_selected' => __( 'No staff selected', 'bookly' ),
                'custom' => __( 'Custom', 'bookly' ),
            ),
        ) );

        print '<div id="bookly-add-recipients-dialog"></div>';
    }

    /**
     * Render button
     */
    public static function renderAddRecipientsButton()
    {
        print '<div class="col-auto">';
        Buttons::renderAdd( 'bookly-js-add-recipients', 'btn-success', __( 'Add recipients', 'bookly' ) );
        print '</div>';
    }
}