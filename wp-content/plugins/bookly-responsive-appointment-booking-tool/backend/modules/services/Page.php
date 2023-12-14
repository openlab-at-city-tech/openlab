<?php
namespace Bookly\Backend\Modules\Services;

use Bookly\Lib;

class Page extends Lib\Base\Ajax
{
    /**
     * Render page.
     */
    public static function render()
    {
        wp_enqueue_media();
        self::enqueueStyles( array(
            'wp' => array( 'wp-color-picker' ),
            'alias' => array( 'bookly-backend-globals' ),
        ) );

        self::enqueueScripts( array(
            'wp' => array( 'wp-color-picker' ),
            'backend' => array(
                'js/range-tools.js' => array( 'bookly-backend-globals' ),
                'js/sortable.min.js',
            ),
            'module' => array( 'js/services-list.js' => array( 'bookly-range-tools.js' ) ),
        ) );

        $categories = Lib\Entities\Category::query()->sortBy( 'position' )->fetchArray();
        foreach ( $categories as &$category ) {
            $category['attachment'] = Lib\Utils\Common::getAttachmentUrl( $category['attachment_id'], 'thumbnail' ) ?: null;
        }

        $datatables = Lib\Utils\Tables::getSettings( Lib\Utils\Tables::SERVICES );

        wp_localize_script( 'bookly-services-list.js', 'BooklyL10n', array(
            'are_you_sure' => esc_attr__( 'Are you sure?', 'bookly' ),
            'appointmentsUrl' => Lib\Utils\Common::escAdminUrl( \Bookly\Backend\Modules\Appointments\Ajax::pageSlug() ),
            'private_warning' => esc_attr__( 'The service will be created with the visibility of Private.', 'bookly' ),
            'edit' => esc_attr__( 'Edit', 'bookly' ),
            'duplicate' => esc_attr__( 'Duplicate', 'bookly' ),
            'reorder' => esc_attr__( 'Reorder', 'bookly' ),
            'categories' => $categories,
            'uncategorized' => esc_attr__( 'Uncategorized', 'bookly' ),
            'noResultFound' => esc_attr__( 'No result found', 'bookly' ),
            'zeroRecords' => esc_attr__( 'No records.', 'bookly' ),
            'processing' => esc_attr__( 'Processing...', 'bookly' ),
            'show_type' => count( Proxy\Shared::prepareServiceTypes( array() ) ) > 0,
            'datatables' => $datatables,
        ) );

        $data['categories'] = $categories;
        $data['datatable'] = $datatables[ Lib\Utils\Tables::SERVICES ];

        self::renderTemplate( 'index', $data );
    }

    /**
     * Get data for staff drop-down.
     *
     * @return array
     */
    public static function getStaffDropDownData()
    {
        if ( Lib\Config::proActive() ) {
            return Lib\Proxy\Pro::getStaffDataForDropDown();
        } else {
            $items = Lib\Entities\Staff::query()
                ->select( 'id, full_name' )
                ->whereNot( 'visibility', 'archive' )
                ->sortBy( 'position' )
                ->fetchArray();

            return array(
                0 => array(
                    'name' => '',
                    'items' => $items,
                ),
            );
        }
    }
}