<?php
namespace Bookly\Backend\Components\Dialogs\Appointment\CustomerDetails;

use Bookly\Lib;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Backend\Components\Dialogs\Appointment\CustomerDetails\Proxy;

/**
 * Class Dialog
 *
 * @package Bookly\Backend\Components\Dialogs\Appointment\CustomerDetails
 */
class Dialog extends Lib\Base\Component
{
    /**
     * Render customer details dialog.
     */
    public static function render()
    {
        self::enqueueScripts( array(
            'module' => array( 'js/customer_details.js' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueData( array(
            'extras_list',
        ), 'bookly-customer_details.js' );

        $statuses = array();
        foreach ( CustomerAppointment::getStatuses() as $status ) {
            $statuses[] = array(
                'id' => $status,
                'title' => CustomerAppointment::statusToString( $status ),
            );
        }

        wp_localize_script( 'bookly-customer_details.js', 'BooklyL10nCustomerDetailsDialog', Proxy\Shared::prepareL10n( array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'statuses' => $statuses,
            'showNotes' => Lib\Config::showNotes(),
            'l10n' => array(
                'customerDetails' => __( 'Edit booking details', 'bookly' ),
                'nop' => __( 'Number of persons', 'bookly' ),
                'status' => __( 'Status', 'bookly' ),
                'notes' => __( 'Appointment notes', 'bookly' ),
                'timezone' => __( 'Timezone', 'bookly' ),
                'apply' => __( 'Apply', 'bookly' ),
                'cancel' => __( 'Cancel', 'bookly' ),
                'areYouSure' => __( 'Are you sure?', 'bookly' ),
            ),
        ) ) );

        print '<div id="bookly-customer-details-dialog"></div>';
    }
}