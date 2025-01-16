<?php
namespace Bookly\Backend\Modules\Customers;

use Bookly\Lib;

class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        if ( self::hasParameter( 'import-customers' ) ) {
            Proxy\Pro::importCustomers();
        }

        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/customers.js' => array( 'bookly-backend-globals' ), ),
            'frontend' => array( 'js/intlTelInput.min.js' => array( 'bookly-backend-globals' ) ),
        ) );

        $datatables = Lib\Utils\Tables::getSettings( Lib\Utils\Tables::CUSTOMERS );

        wp_localize_script( 'bookly-customers.js', 'BooklyL10n', array(
            'infoFields' => Lib\Proxy\CustomerInformation::getFieldsWhichMayHaveData() ?: array(),
            'tagsData' => Lib\Proxy\Pro::getTagsData() ?: array(),
            'edit' => __( 'Edit', 'bookly' ),
            'are_you_sure' => __( 'Are you sure?', 'bookly' ),
            'wp_users' => get_users( array( 'fields' => array( 'ID', 'display_name' ), 'orderby' => 'display_name' ) ),
            'zeroRecords' => __( 'No customers found.', 'bookly' ),
            'processing' => __( 'Processing...', 'bookly' ),
            'edit_customer' => __( 'Edit customer', 'bookly' ),
            'new_customer' => __( 'New customer', 'bookly' ),
            'create_customer' => __( 'Create customer', 'bookly' ),
            'save' => __( 'Save', 'bookly' ),
            'search' => __( 'Quick search customer', 'bookly' ),
            'download' => __( 'Download', 'bookly' ),
            'datatables' => $datatables,
        ) );

        self::renderTemplate( 'index', array( 'datatable' => $datatables[ Lib\Utils\Tables::CUSTOMERS ] ) );
    }
}