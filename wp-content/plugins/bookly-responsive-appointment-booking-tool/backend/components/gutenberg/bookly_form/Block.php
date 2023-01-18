<?php
namespace Bookly\Backend\Components\Gutenberg\BooklyForm;

use Bookly\Lib;

/**
 * Class Block
 *
 * @package Bookly\Backend\Components\Gutenberg\BooklyForm
 */
class Block extends Lib\Base\Block
{
    /**
     * @inheritDoc
     */
    public static function registerBlockType()
    {
        self::enqueueScripts( array(
            'module' => array(
                'js/booking-form-block.js' => array( 'jquery', 'wp-blocks', 'wp-components', 'wp-element', 'wp-editor', 'bookly-backend-globals' ),
            ),
        ) );

        self::enqueueData( array(
            'casest',
            'custom_location_settings',
        ) );

        wp_localize_script( 'bookly-booking-form-block.js', 'BooklyFormL10n', array(
            'block' => array(
                'title' => 'Bookly - ' . __( 'Booking form', 'bookly' ),
                'description' => __( 'A custom block for displaying booking form', 'bookly' ),
            ),
            'selectLocation' => __( 'Select location', 'bookly' ),
            'selectCategory' => __( 'Select category', 'bookly' ),
            'selectService' => __( 'Select service', 'bookly' ),
            'any' => __( 'Any', 'bookly' ),
            'formFields' => __( 'Form fields', 'bookly' ),
            'location' => __( 'Default value for location', 'bookly' ),
            'category' => __( 'Default value for category', 'bookly' ),
            'service' => __( 'Default value for service', 'bookly' ),
            'staff' => __( 'Default value for employee', 'bookly' ),
            'nop' => __( 'Number of persons', 'bookly' ),
            'quantity' => __( 'Quantity', 'bookly' ),
            'date' => __( 'Date', 'bookly' ),
            'weekDays' => __( 'Week days', 'bookly' ),
            'timeRange' => __( 'Time range', 'bookly' ),
            'hide' => __( 'hide', 'bookly' ),
            'fields' => __( 'Fields', 'bookly' ),
            'duration' => __( 'Duration', 'bookly' ),
            'serviceHelp' => __( 'Please be aware that a value in this field is required in the frontend. If you choose to hide this field, please be sure to select a default value for it', 'bookly' ),
        ) );

        register_block_type( 'bookly/form-block', array(
            'editor_script' => 'bookly-booking-form-block.js',
        ) );
    }
}