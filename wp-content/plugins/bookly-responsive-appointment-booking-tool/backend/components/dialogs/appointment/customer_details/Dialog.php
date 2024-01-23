<?php
namespace Bookly\Backend\Components\Dialogs\Appointment\CustomerDetails;

use Bookly\Lib;
use Bookly\Lib\Entities\CustomerAppointment;

class Dialog extends Lib\Base\Component
{
    /**
     * Render customer details dialog.
     */
    public static function render()
    {
        self::enqueueScripts( array(
            'module' => array( 'js/customer-details.js' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueData( array(
            'extras_list',
            'extras_multiply_nop'
        ), 'bookly-customer-details.js' );

        $statuses = array();
        foreach ( CustomerAppointment::getStatuses() as $status ) {
            $statuses[] = array(
                'id' => $status,
                'title' => CustomerAppointment::statusToString( $status ),
            );
        }

        wp_localize_script( 'bookly-customer-details.js', 'BooklyL10nCustomerDetailsDialog', Proxy\Shared::prepareL10n( array(
            'statuses' => $statuses,
            'showNotes' => Lib\Config::showNotes(),
            'l10n' => array(
                'customerDetails' => __( 'Edit booking details', 'bookly' ),
                'nop' => __( 'Number of persons', 'bookly' ),
                'status' => __( 'Status', 'bookly' ),
                'notes' => __( 'Appointment notes', 'bookly' ),
                'notes_help' => __( 'This text can be inserted into notifications with {appointment_notes} code', 'bookly' ),
                'timezone' => __( 'Timezone', 'bookly' ),
                'apply' => __( 'Apply', 'bookly' ),
                'cancel' => __( 'Cancel', 'bookly' ),
                'areYouSure' => __( 'Are you sure?', 'bookly' ),
            ),
        ) ) );

        print '<div id="bookly-customer-details-dialog"></div>';
    }
}