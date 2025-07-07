<?php
namespace Bookly\Backend\Modules\Staff;

use Bookly\Lib;

class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/staff-list.js' => array( 'bookly-backend-globals' ) ),
            'backend' => array( 'js/nav-scrollable.js' => array( 'bookly-backend-globals' ), ),
            'frontend' => array( 'js/intlTelInput.min.js' => array( 'jquery' ) ),
        ) );

        // Allow add-ons to enqueue their assets.
        Proxy\Shared::enqueueStaffProfileStyles();
        Proxy\Shared::enqueueStaffProfileScripts();
        Proxy\Shared::renderStaffPage( self::parameters() );

        $categories = Proxy\Pro::getCategoriesList() ?: array();
        foreach ( $categories as &$category ) {
            $category['attachment'] = Lib\Utils\Common::getAttachmentUrl( $category['attachment_id'], 'thumbnail' ) ?: null;
        }

        $datatables = Lib\Utils\Tables::getSettings( Lib\Utils\Tables::STAFF_MEMBERS );

        wp_localize_script( 'bookly-staff-list.js', 'BooklyL10n', array(
            'proRequired' => (int) ! Lib\Config::proActive(),
            'appointmentsUrl' => Lib\Utils\Common::escAdminUrl( \Bookly\Backend\Modules\Appointments\Ajax::pageSlug() ),
            'areYouSure' => esc_attr__( 'Are you sure?', 'bookly' ),
            'categories' => $categories,
            'uncategorized' => esc_attr__( 'Uncategorized', 'bookly' ),
            'edit' => esc_attr__( 'Edit', 'bookly' ),
            'reorder' => esc_attr__( 'Reorder', 'bookly' ),
            'noResultFound' => esc_attr__( 'No result found', 'bookly' ),
            'zeroRecords' => __( 'No matching records found', 'bookly' ),
            'processing' => esc_attr__( 'Processing...', 'bookly' ),
            'emptyTable' => __( 'No data available in table', 'bookly' ),
            'loadingRecords' => __( 'Loading...', 'bookly' ),
            'datatables' => $datatables,
        ) );

        self::renderTemplate( 'index', compact( 'categories', 'datatables' ) );
    }
}