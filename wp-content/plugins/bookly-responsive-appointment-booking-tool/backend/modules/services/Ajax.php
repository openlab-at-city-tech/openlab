<?php
namespace Bookly\Backend\Modules\Services;

use Bookly\Backend\Components\Dialogs\Service\Edit\Forms;
use Bookly\Lib;

/**
 * Class Ajax
 *
 * @package Bookly\Backend\Modules\Services
 */
class Ajax extends Page
{
    /**
     * Get services data for data tables
     */
    public static function getServices()
    {
        $columns = self::parameter( 'columns' );
        $order = self::parameter( 'order', array() );
        $filter = self::parameter( 'filter' );
        $limits = array(
            'length' => self::parameter( 'length' ),
            'start' => self::parameter( 'start' ),
        );

        $query = Lib\Entities\Service::query( 's' )
            ->select( 's.*, c.name AS category_name' )
            ->leftJoin( 'Category', 'c', 'c.id = s.category_id' );

        foreach ( $order as $sort_by ) {
            $query->sortBy( str_replace( '.', '_', $columns[ $sort_by['column'] ]['data'] ) )
                ->order( $sort_by['dir'] == 'desc' ? Lib\Query::ORDER_DESCENDING : Lib\Query::ORDER_ASCENDING );
        }

        $total = $query->count();

        if ( $filter['category'] != '' ) {
            $query->where( 's.category_id', $filter['category'] );
        }

        if ( $filter['search'] != '' ) {
            $fields = array();
            foreach ( $columns as $column ) {
                switch ( $column['data'] ) {
                    case 'category_name':
                        $fields[] = 'c.name';
                        break;
                    case 'id':
                    case 'title':
                        $fields[] = 's.' . $column['data'];
                        break;
                }
            }

            $search_columns = array();
            foreach ( $fields as $field ) {
                $search_columns[] = $field . ' LIKE "%%%s%"';
            }
            if ( ! empty( $search_columns ) ) {
                $query->whereRaw( implode( ' OR ', $search_columns ), array_fill( 0, count( $search_columns ), $filter['search'] ) );
            }
        }

        $filtered = $query->count();

        if ( ! empty( $limits ) ) {
            $query->limit( $limits['length'] )->offset( $limits['start'] );
        }

        $type_icons = Proxy\Shared::prepareServiceIcons( array( Lib\Entities\Service::TYPE_SIMPLE => 'far fa-calendar-check' ) );

        $data = array();
        foreach ( $query->fetchArray() as $service ) {
            $sub_services_count = count( Lib\Entities\Service::find( $service['id'] )->getSubServices() );
            $data[] = array(
                'id' => $service['id'],
                'title' => $service['title'],
                'position' => sprintf( '%05d-%05d', $service['position'], $service['id'] ),
                'category_name' => $service['category_name'],
                'color' => $service['color'],
                'type' => ucfirst( $service['type'] ),
                'type_icon' => isset( $type_icons[ $service['type'] ] ) ? $type_icons[ $service['type'] ] : 'far fa-question-circle',
                'disabled' => ! isset( $type_icons[ $service['type'] ] ),
                'price' => Lib\Utils\Price::format( $service['price'] ),
                'duration' => in_array( $service['type'], array( Lib\Entities\Service::TYPE_COLLABORATIVE, Lib\Entities\Service::TYPE_COMPOUND ) )
                    ? sprintf( _n( '%d service', '%d services', $sub_services_count, 'bookly' ), $sub_services_count )
                    : Lib\Utils\DateTime::secondsToInterval( $service['duration'] ),
                'online_meetings' => $service['online_meetings'],
            );
        }

        unset( $filter['search'] );

        Lib\Utils\Tables::updateSettings( 'services', $columns, $order, $filter );

        wp_send_json( array(
            'draw' => ( int ) self::parameter( 'draw' ),
            'data' => $data,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
        ) );
    }

    /**
     * Update services position.
     */
    public static function updateServicesPosition()
    {
        $services_sorts = self::parameter( 'positions' );
        foreach ( $services_sorts as $position => $service_id ) {
            $services_sort = new Lib\Entities\Service();
            $services_sort->load( $service_id );
            $services_sort->setPosition( $position );
            $services_sort->save();
        }

        wp_send_json_success();
    }

    /**
     * Add service.
     */
    public static function createService()
    {
        ! Lib\Config::proActive() &&
        get_option( 'bookly_updated_from_legacy_version' ) != 'lite' &&
        Lib\Entities\Service::query()->count() > 4 &&
        wp_send_json_error();

        $form = new Forms\Service();
        $form->bind( self::parameters() );
        $form->getObject()->setDuration( Lib\Config::getTimeSlotLength() );
        $service = $form->save();

        Proxy\Shared::serviceCreated( $service );

        wp_send_json_success( array(
            'id' => $service->getId(),
            'title' => $service->getTitle(),
        ) );
    }

    /**
     * 'Safely' remove services (report if there are future appointments)
     */
    public static function removeServices()
    {
        $service_ids = self::parameter( 'service_ids', array() );
        if ( is_array( $service_ids ) && ! empty ( $service_ids ) ) {
            foreach ( $service_ids as $service_id ) {
                if ( $service = Lib\Entities\Service::find( $service_id ) ) {
                    Proxy\Shared::serviceDeleted( $service );
                    $service->delete();
                }
            }
        }

        wp_send_json_success();
    }

    /**
     * Update service categories
     */
    public static function updateServiceCategories()
    {
        $categories = self::parameter( 'categories', array() );
        $existing_categories = array();
        foreach ( $categories as $category ) {
            if ( strpos( $category['id'], 'new' ) === false ) {
                $existing_categories[] = $category['id'];
            }
        }
        // Delete categories
        Lib\Entities\Category::query()->delete()->whereNotIn( 'id', $existing_categories )->execute();
        foreach ( $categories as $position => $category_data ) {
            if ( strpos( $category_data['id'], 'new' ) !== false ) {
                $category = new Lib\Entities\Category();
            } else {
                $category = Lib\Entities\Category::find( $category_data['id'] );
            }
            $category
                ->setPosition( $position )
                ->setName( $category_data['name'] )
                ->setAttachmentId( $category_data['attachment_id'] ?: null )
                ->setInfo( $category_data['info'] )
                ->save();
        }
        $categories = Lib\Entities\Category::query()->sortBy( 'position' )->fetchArray();
        foreach ( $categories as &$category ) {
            $category['attachment'] = Lib\Utils\Common::getAttachmentUrl( $category['attachment_id'], 'thumbnail' ) ?: null;
        }
        wp_send_json_success( $categories );
    }

    /**
     * Duplicate service.
     */
    public static function duplicateService()
    {
        ! Lib\Config::proActive() &&
        get_option( 'bookly_updated_from_legacy_version' ) != 'lite' &&
        Lib\Entities\Service::query()->count() > 4 &&
        wp_send_json_error();
        $service_id = self::parameter( 'service_id' );
        $service = Lib\Entities\Service::find( $service_id );
        if ( $service ) {
            // Create copy of service
            $new_service = new Lib\Entities\Service( $service->getFields() );
            $new_service
                ->setId( null )
                ->setTitle( sprintf( __( 'Copy of %s', 'bookly' ), $new_service->getTitle() ) )
                ->setVisibility( Lib\Entities\Service::VISIBILITY_PRIVATE )
                ->save();

            foreach ( Lib\Entities\StaffService::query()->where( 'service_id', $service->getId() )->fetchArray() as $staff_service ) {
                $new_staff_service = new Lib\Entities\StaffService( $staff_service );
                $new_staff_service->setId( null )->setServiceId( $new_service->getId() )->save();
            }

            foreach ( Lib\Entities\SubService::query()->where( 'service_id', $service->getId() )->fetchArray() as $sub_service ) {
                $new_sub_service = new Lib\Entities\SubService( $sub_service );
                $new_sub_service->setId( null )->setServiceId( $new_service->getId() )->save();
            }

            Proxy\Shared::duplicateService( $service->getId(), $new_service->getId() );

            wp_send_json_success( array(
                'id' => $new_service->getId(),
                'title' => $new_service->getTitle(),
            ) );
        }

        wp_send_json_success();
    }
}