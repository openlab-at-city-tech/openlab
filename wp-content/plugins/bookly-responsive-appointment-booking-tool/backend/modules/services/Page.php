<?php
namespace Bookly\Backend\Modules\Services;

use Bookly\Lib;
use Bookly\Backend\Modules\Services\Proxy;

/**
 * Class Page
 * @package Bookly\Backend\Modules\Services
 */
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

        $staff = array();
        foreach ( self::getStaffDropDownData() as $category ) {
            foreach ( $category['items'] as $employee ) {
                $staff[ $employee['id'] ] = $employee['full_name'];
            }
        }

        $services = Lib\Entities\Service::query( 's' )
            ->whereIn( 's.type', array_keys( Proxy\Shared::prepareServiceTypes( array( Lib\Entities\Service::TYPE_SIMPLE => Lib\Entities\Service::TYPE_SIMPLE ) ) ) )
            ->sortBy( 'position' )
            ->fetchArray();
        $categories = Lib\Entities\Category::query()->sortBy( 'position' )->fetchArray();
        foreach ( $categories as &$category ) {
            $category['attachment'] = Lib\Utils\Common::getAttachmentUrl( $category['attachment_id'], 'thumbnail' ) ?: null;
        }

        $datatables = Lib\Utils\Tables::getSettings( 'services' );

        wp_localize_script( 'bookly-services-list.js', 'BooklyL10n', array(
            'are_you_sure' => esc_attr__( 'Are you sure?', 'bookly' ),
            'private_warning' => esc_attr__( 'The service will be created with the visibility of Private.', 'bookly' ),
            'edit' => esc_attr__( 'Edit', 'bookly' ),
            'duplicate' => esc_attr__( 'Duplicate', 'bookly' ),
            'reorder' => esc_attr__( 'Reorder', 'bookly' ),
            'staff' => $staff,
            'categories' => $categories,
            'uncategorized' => esc_attr__( 'Uncategorized', 'bookly' ),
            'services' => $services,
            'noResultFound' => esc_attr__( 'No result found', 'bookly' ),
            'zeroRecords' => esc_attr__( 'No records.', 'bookly' ),
            'processing' => esc_attr__( 'Processing...', 'bookly' ),
            'show_type' => count( Proxy\Shared::prepareServiceTypes( array() ) ) > 0,
            'datatables' => $datatables,
        ) );

        foreach ( $services as &$service ) {
            $service['title'] = esc_html( $service['title'] );
            $service['colors'] = Proxy\Shared::prepareServiceColors( array_fill( 0, 3, $service['color'] ), $service['id'], $service['type'] );
            $service['sub_services'] = Lib\Entities\SubService::query()
                ->where( 'service_id', $service['id'] )
                ->sortBy( 'position' )
                ->fetchArray();
            $service['sub_services_count'] = array_sum( array_map( function ( $sub_service ) {
                return (int) ( $sub_service['type'] == Lib\Entities\SubService::TYPE_SERVICE );
            }, $service['sub_services'] ) );
        }
        $data['services'] = $services;
        $data['service_types'] = Proxy\Shared::prepareServiceTypes( array( Lib\Entities\Service::TYPE_SIMPLE => __( 'Simple', 'bookly' ) ) );
        $data['categories'] = $categories;
        $data['datatables'] = $datatables;

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
                    'name'  => '',
                    'items' => $items,
                ),
            );
        }
    }
}