<?php

namespace Bookly\Backend\Modules\Diagnostics\Tools;

/**
 * Class ShortCodes
 *
 * @package Bookly\Backend\Modules\Diagnostics\Tools
 */
class ShortCodes extends Tool
{
    protected $slug = 'short-codes';
    protected $hidden = true;

    public function __construct()
    {
        $this->title = 'Bookly shortcodes';
    }

    public function render()
    {
        $shortcodes = array(
            array( 'name' => 'Booking form', 'code' => '[bookly-form' ),
            array( 'name' => 'Appointments list', 'code' => '[bookly-appointments-list' ),
            array( 'name' => 'Calendar', 'code' => '[bookly-calendar' ),
            array( 'name' => 'Cancellation confirmation', 'code' => '[bookly-cancellation-confirmation' ),
            array( 'name' => 'Customer Cabinet', 'code' => '[bookly-customer-cabinet' ),
            array( 'name' => 'Packages list', 'code' => '[bookly-packages-list' ),
            array( 'name' => 'Search form', 'code' => '[bookly-search-form' ),
            array( 'name' => 'Services form', 'code' => '[bookly-services-form' ),
            array( 'name' => 'Staff form', 'code' => '[bookly-staff-form' ),
            array( 'name' => 'Staff Cabinet - Advanced', 'code' => '[bookly-staff-advanced' ),
            array( 'name' => 'Staff Cabinet - Calendar', 'code' => '[bookly-staff-calendar' ),
            array( 'name' => 'Staff Cabinet - Days off', 'code' => '[bookly-staff-days-off' ),
            array( 'name' => 'Staff Cabinet - Details', 'code' => '[bookly-staff-details' ),
            array( 'name' => 'Staff Cabinet - Schedule', 'code' => '[bookly-staff-schedule' ),
            array( 'name' => 'Staff Cabinet - Services', 'code' => '[bookly-staff-services' ),
            array( 'name' => 'Staff Cabinet - Special days', 'code' => '[bookly-staff-special-days' ),
            array( 'name' => 'Staff ratings', 'code' => '[bookly-staff-rating' ),
        );

        return self::renderTemplate( '_short_codes', compact( 'shortcodes' ), false );
    }

    public function find()
    {
        global $wpdb;

        $shortcode = self::parameter( 'shortcode' );

        $row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->posts . ' WHERE post_content LIKE \'%%%s%\' AND post_type IN (\'page\',\'post\') AND post_status = \'publish\' ORDER BY ID DESC LIMIT 1', $shortcode ) );

        if ( $row ) {
            wp_send_json_success( array( 'url' => get_permalink( $row ) ) );
        }

        wp_send_json_error();
    }
}