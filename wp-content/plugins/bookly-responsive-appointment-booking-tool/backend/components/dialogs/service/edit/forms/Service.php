<?php
namespace Bookly\Backend\Components\Dialogs\Service\Edit\Forms;

use Bookly\Lib;
use Bookly\Backend\Components\Dialogs\Service\Edit\Proxy;

/**
 * Class Service
 * @method Lib\Entities\Service getObject
 *
 * @package Bookly\Backend\Components\Dialogs\Service\Edit\Forms
 */
class Service extends Lib\Base\Form
{
    protected static $entity_class = 'Service';

    public function configure()
    {
        $fields = array(
            'category_id',
            'title',
            'attachment_id',
            'duration',
            'slot_length',
            'price',
            'color',
            'deposit',
            'capacity_min',
            'capacity_max',
            'waiting_list_capacity',
            'one_booking_per_slot',
            'padding_left',
            'padding_right',
            'package_life_time',
            'package_size',
            'package_unassigned',
            'appointments_limit',
            'limit_period',
            'info',
            'start_time_info',
            'end_time_info',
            'type',
            'sub_services',
            'staff_preference',
            'staff_preferred_period_before',
            'staff_preferred_period_after',
            'staff_preferred_random',
            'recurrence_enabled',
            'recurrence_frequencies',
            'visibility',
            'positions',
            'taxes',
            'discounts',
            'same_staff_for_subservices',
            'unit_duration',
            'units_min',
            'units_max',
            'time_requirements',
            'collaborative_equal_duration',
            'online_meetings',
            'final_step_url',
            'wc_product_id',
            'wc_cart_info_name',
            'wc_cart_info',
            'min_time_prior_booking',
            'min_time_prior_cancel',
            'gateways',
            'gateways_list',
        );

        $this->setFields( $fields );
    }

    /**
     * Bind values to form.
     *
     * @param array $params
     * @param array $files
     */
    public function bind( array $params, array $files = array() )
    {
        // Field with NULL
        if ( array_key_exists( 'category_id', $params ) && ! $params['category_id'] ) {
            $params['category_id'] = null;
        }

        if ( ! array_key_exists( 'id', $params ) ) {
            // Set the default values for the new service
            if ( ! isset( $params['wc_cart_info_name'] ) || $params['wc_cart_info_name'] == '' ) {
                $params['wc_cart_info_name'] = get_option( 'bookly_l10n_wc_cart_info_name' );
            }
            if ( ! isset( $params['wc_cart_info'] ) || $params['wc_cart_info'] == '' ) {
                $params['wc_cart_info'] = get_option( 'bookly_l10n_wc_cart_info_value' );
            }
        }

        parent::bind( $params, $files );
    }

    /**
     * @return Lib\Entities\Service
     */
    public function save()
    {
        if ( $this->isNew() ) {
            // When adding new service - set its color randomly.
            $this->data['color'] = sprintf( '#%06X', mt_rand( 0, 0x64FFFF ) );
        } else {
            if ( $this->data['type'] === Lib\Entities\Service::TYPE_SIMPLE ) {
                Lib\Entities\SubService::query()->delete()->where( 'service_id', $this->data['id'] )->execute();
            }

            if ( ! isset( $this->data['limit_period'], $this->data['appointments_limit'] ) || $this->data['limit_period'] === 'off' || ! $this->data['appointments_limit'] ) {
                $this->data['appointments_limit'] = null;
            }

            if ( array_key_exists( 'deposit', $this->data ) && $this->data['deposit'] ) {
                $this->data['deposit'] = preg_replace( '/[^0-9%.]/', '', str_replace( ',', '.', $this->data['deposit'] ) );
            }

            $this->data = Proxy\Shared::prepareUpdateService( $this->data );
        }

        return parent::save();
    }

}