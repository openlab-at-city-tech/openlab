<?php
namespace Bookly\Backend\Modules\Setup;

use Bookly\Lib;
use Bookly\Backend\Modules\Services\Proxy;

/**
 * Class Ajax
 *
 * @package Bookly\Backend\Modules\Setup
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Get data for setup page.
     */
    public static function getSetupForm()
    {
        $services = Lib\Entities\Service::query( 's' )
            ->select( 'id, title, duration' )
            ->fetchArray();
        foreach ( $services as &$service ) {
            $service['duration'] = (int) $service['duration'];
        }
        $staff = Lib\Entities\Staff::query( 's' )
            ->select( 'id, full_name as name, email, phone' )
            ->fetchArray();

        wp_send_json_success( array(
            'company' => get_option( 'bookly_co_name', '' ),
            'industry' => get_option( 'bookly_co_industry', false ),
            'size' => get_option( 'bookly_co_size', '' ),
            'email' => get_option( 'bookly_co_email', '' ),
            'staff_members' => $staff,
            'services' => $services,
        ) );
    }

    /**
     * Save setup form data.
     */
    public static function saveSetupForm()
    {
        $step = self::parameter( 'step', 1 );
        switch ( $step ) {
            case 1:
                update_option( 'bookly_co_name', self::parameter( 'company' ) );
                update_option( 'bookly_co_industry', self::parameter( 'industry' ) );
                update_option( 'bookly_co_size', self::parameter( 'size' ) );
                update_option( 'bookly_co_email', self::parameter( 'email' ) );
                break;
            case 2:
                $existing_staff = array();
                foreach ( self::parameter( 'staff_members', array() ) as $staff_data ) {
                    $staff = new Lib\Entities\Staff();
                    if ( isset( $staff_data['id'] ) && $staff_data['id'] ) {
                        $staff->load( $staff_data['id'] );
                    }
                    $staff
                        ->setFullName( $staff_data['name'] )
                        ->setPhone( $staff_data['phone_formatted'] ?: $staff_data['phone'] )
                        ->setEmail( $staff_data['email'] )
                        ->save();
                    $existing_staff[] = $staff->getId();
                    foreach ( Lib\Entities\Service::query()->find() as $service ) {
                        $staff_service = new Lib\Entities\StaffService();
                        $staff_service->loadBy( array( 'staff_id' => $staff->getId(), 'service_id' => $service->getId() ) );
                        if ( ! $staff_service->isLoaded() ) {
                            $staff_service
                                ->setStaffId( $staff->getId() )
                                ->setServiceId( $service->getId() )
                                ->save();
                        }
                    }
                }
                Lib\Entities\Staff::query()->delete()->whereNotIn( 'id', $existing_staff )->execute();
                break;
            case 3:
                $existing_services = array();
                foreach ( self::parameter( 'services', array() ) as $service_data ) {
                    $service = new Lib\Entities\Service();
                    if ( isset( $service_data['id'] ) && $service_data['id'] ) {
                        $service->load( $service_data['id'] );
                    }
                    $service
                        ->setTitle( $service_data['title'] )
                        ->setDuration( $service_data['duration'] )
                        ->save();
                    Proxy\Shared::serviceCreated( $service );
                    $existing_services[] = $service->getId();
                    foreach ( Lib\Entities\Staff::query()->find() as $staff ) {
                        $staff_service = new Lib\Entities\StaffService();
                        $staff_service->loadBy( array( 'staff_id' => $staff->getId(), 'service_id' => $service->getId() ) );
                        if ( ! $staff_service->isLoaded() ) {
                            $staff_service
                                ->setStaffId( $staff->getId() )
                                ->setServiceId( $service->getId() )
                                ->save();
                        }
                    }
                }
                Lib\Entities\Service::query()->delete()->whereNotIn( 'id', $existing_services )->execute();
                break;
        }
        if ( $step < 4 ) {
            update_option( 'bookly_setup_step', ++ $step );
        }
    }

    /**
     * Finish initial setup.
     */
    public static function finishSetupForm()
    {
        delete_option( 'bookly_setup_step' );

        wp_send_json_success();
    }
}