<?php
namespace Bookly\Backend\Components\Dialogs\Service\Edit;

use Bookly\Lib;
use Bookly\Backend\Modules\Services\Proxy as ServicesProxy;
use Bookly\Backend\Modules\Services\Page as ServicesPage;

class Ajax extends Lib\Base\Ajax
{
    /**
     * Edit Service
     */
    public static function getServiceData()
    {
        $service_id = self::parameter( 'id' );
        $required_sub_services = max( Lib\Config::compoundServicesActive(), Lib\Config::collaborativeServicesActive(), Lib\Config::packagesActive() );
        $service = Lib\Entities\Service::query()->where( 'id', $service_id )->fetchRow();
        $staff_ids = Lib\Entities\StaffService::query()->where( 'service_id', $service_id )->fetchCol( 'staff_id' );
        if ( $required_sub_services ) {
            $simple_services = Lib\Entities\Service::query()
                ->select( 'id, title, duration, color, type' )
                ->where( 'units_max', 1 )
                ->where( 'type', Lib\Entities\Service::TYPE_SIMPLE )
                ->indexBy( 'id' )
                ->sortBy( 'position' )
                ->fetchArray();
            $service['sub_services'] = Lib\Entities\SubService::query()
                ->select( 'type, sub_service_id, duration' )
                ->where( 'service_id', $service['id'] )
                ->sortBy( 'position' )
                ->fetchArray();
            $sub_services_count = array_sum( array_map( function ( $sub_service ) {
                return (int) ( $sub_service['type'] == Lib\Entities\SubService::TYPE_SERVICE );
            }, $service['sub_services'] ) );
        } else {
            $simple_services = array();
        }

        $staff_dropdown_data = ServicesPage::getStaffDropDownData();

        $categories_collection = Lib\Entities\Category::query()->sortBy( 'position' )->fetchArray();
        $service_types = ServicesProxy\Shared::prepareServiceTypes( array( Lib\Entities\Service::TYPE_SIMPLE => __( 'Simple', 'bookly' ) ) );
        $result = array(
            'html' => array(
                'general' => self::renderTemplate( 'general', compact( 'service', 'service_types', 'simple_services', 'staff_dropdown_data', 'categories_collection', 'staff_ids' ), false ),
                'advanced' => Proxy\Pro::getAdvancedHtml( $service ),
                'time' => self::renderTemplate( 'time', compact( 'service' ), false ),
                'extras' => Proxy\ServiceExtras::getTabHtml( $service_id ),
                'schedule' => Proxy\ServiceSchedule::getTabHtml( $service_id ),
                'special_days' => Proxy\ServiceSpecialDays::getTabHtml( $service_id ),
                'additional' => Proxy\Shared::prepareAfterServiceList( '', $simple_services ),
                'wc' => ( get_option( 'bookly_wc_enabled' ) && get_option( 'bookly_wc_product' ) ) ? Proxy\Pro::getWCHtml( $service ) : '',
            ),
            'title' => $service['title'],
            'type' => $service['type'],
            'price' => Lib\Utils\Price::format( $service['price'] ),
            'duration' => $required_sub_services && in_array( $service['type'], array( Lib\Entities\Service::TYPE_COLLABORATIVE, Lib\Entities\Service::TYPE_COMPOUND, ) )
                ? sprintf( _n( '%d service', '%d services', $sub_services_count, 'bookly' ), $sub_services_count )
                : Lib\Utils\DateTime::secondsToInterval( $service['duration'] ),
            'staff' => $staff_dropdown_data,
        );

        wp_send_json_success( $result );
    }

    /**
     * Update service parameters and assign staff
     */
    public static function updateService()
    {
        $form = new Forms\Service();
        $form->bind( self::parameters() );
        $service = $form->save();

        $staff_ids = self::parameter( 'staff_ids', array() );
        if ( empty ( $staff_ids ) ) {
            Lib\Entities\StaffService::query()->delete()->where( 'service_id', $service->getId() )->execute();
        } else {
            Lib\Entities\StaffService::query()->delete()->where( 'service_id', $service->getId() )->whereNotIn( 'staff_id', $staff_ids )->execute();
            if ( $service->getType() == Lib\Entities\Service::TYPE_SIMPLE ) {
                if ( self::parameter( 'update_staff', false ) ) {
                    Lib\Entities\StaffService::query()
                        ->update()
                        ->set( 'price', self::parameter( 'price' ) )
                        ->set( 'capacity_min', $service->getCapacityMin() )
                        ->set( 'capacity_max', $service->getCapacityMax() )
                        ->where( 'service_id', self::parameter( 'id' ) )
                        ->execute();
                }
                // Create records for newly linked staff.
                $new_staff_ids = Lib\Entities\StaffService::query()
                    ->where( 'service_id', $service->getId() )
                    ->fetchColDiff( 'staff_id', $staff_ids );
                foreach ( $new_staff_ids as $staff_id ) {
                    $staff_service = new Lib\Entities\StaffService();
                    $staff_service->setStaffId( $staff_id )
                        ->setServiceId( $service->getId() )
                        ->setPrice( $service->getPrice() )
                        ->setCapacityMin( $service->getCapacityMin() )
                        ->setCapacityMax( $service->getCapacityMax() )
                        ->save();
                }
            }
        }

        // Update services in addons.
        $alert = Proxy\Shared::updateService( array( 'success' => array( __( 'Settings saved.', 'bookly' ) ) ), $service, self::parameters() );

        wp_send_json_success( Proxy\Shared::prepareUpdateServiceResponse( compact( 'alert' ), $service ) );
    }
}