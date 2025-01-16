<?php
namespace Bookly\Backend\Modules\Setup;

use Bookly\Lib;
use Bookly\Backend\Modules\Services\Proxy;

class Ajax extends Lib\Base\Ajax
{
    /**
     * Get data for setup page.
     */
    public static function getSetupForm()
    {
        /** @global \WP_Locale $wp_locale */
        global $wp_locale;

        $services = Lib\Entities\Service::query( 's' )
            ->select( 'id, title, duration' )
            ->fetchArray();
        foreach ( $services as &$service ) {
            $service['duration'] = (int) $service['duration'];
        }
        $staff = Lib\Entities\Staff::query( 's' )
            ->select( 'id, full_name as name, email, phone' )
            ->fetchArray();

        // Business hours
        $week_day_ids = array(
            1 => 'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
        );
        $start_of_week = (int) get_option( 'start_of_week' );
        $business_hours = array();

        for ( $i = 1; $i <= 7; $i++ ) {
            $day_index = ( $start_of_week + $i ) < 8 ? $start_of_week + $i : $start_of_week + $i - 7;
            $day = $week_day_ids[ $day_index ];
            foreach ( array( 'start', 'end' ) as $var ) {
                $$var = get_option( 'bookly_bh_' . $day . '_' . $var, 'not-exists' );
                if ( 'not-exists' === $$var ) {
                    if ( $day === 'saturday' || $day === 'sunday' ) {
                        $$var = '';
                    } else {
                        $$var = $var === 'start' ? '8:00' : '18:00';
                    }
                }
            }
            $business_hours[] = array( 'index' => $day_index, 'title' => $wp_locale->weekday[ $day_index == 7 ? 6 : ( $day_index - 1 ) ], 'start' => $start, 'end' => $end );
        }

        wp_send_json_success( array(
            'company' => get_option( 'bookly_co_name', '' ),
            'business_hours' => $business_hours,
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
            case 2:
                // Save business hours
                $week_day_ids = array(
                    1 => 'sunday',
                    'monday',
                    'tuesday',
                    'wednesday',
                    'thursday',
                    'friday',
                    'saturday',
                );
                foreach ( self::parameter( 'business_hours', array() ) as $data ) {
                    foreach ( array( 'start', 'end' ) as $var ) {
                        $option = 'bookly_bh_' . $week_day_ids[ $data['index'] ] . '_' . $var;
                        update_option( $option, $data[ $var ] );
                    }
                }

                // Save timeslot length
                $bookly_gen_time_slot_length = self::parameter( 'timeslot_length' );
                if ( in_array( $bookly_gen_time_slot_length, Lib\Config::getTimeSlotLengthOptions() ) ) {
                    update_option( 'bookly_gen_time_slot_length', $bookly_gen_time_slot_length );
                }

                // Save currency
                update_option( 'bookly_pmt_currency', self::parameter( 'currency' ) );
                break;
            case 3:
                $existing_staff = array();
                foreach ( self::parameter( 'staff_members', array() ) as $staff_data ) {
                    $staff = new Lib\Entities\Staff();
                    if ( isset( $staff_data['id'] ) && $staff_data['id'] ) {
                        $staff->load( $staff_data['id'] );
                    }
                    $staff
                        ->setFullName( $staff_data['name'] ?: __( 'Staff', 'bookly' ) )
                        ->setPhone( $staff_data['phone'] )
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
            case 4:
                $existing_services = array();
                foreach ( self::parameter( 'services', array() ) as $service_data ) {
                    $service = new Lib\Entities\Service();
                    if ( isset( $service_data['id'] ) && $service_data['id'] ) {
                        $service->load( $service_data['id'] );
                    }
                    $service
                        ->setTitle( $service_data['title'] ?: __( 'Service', 'bookly' ) )
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
            update_option( 'bookly_setup_step', ++$step );
        }

        wp_send_json_success();
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