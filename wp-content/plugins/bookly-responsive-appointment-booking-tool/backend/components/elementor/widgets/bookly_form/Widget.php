<?php
namespace Bookly\Backend\Components\Elementor\Widgets\BooklyForm;

use Bookly\Lib;
use Bookly\Backend\Components\Elementor\Base;
use Bookly\Lib\Config;
use Elementor\Controls_Manager;

/**
 * Class Widget
 * @package Bookly\Backend\Components\Elementor\Widgets\BooklyForm
 */
class Widget extends Base\Widget
{
    protected $name = 'bookly-form';
    protected $icon = 'bookly';

    /**
     * @inheritDoc
     */
    public function get_title()
    {
        return __( 'Booking form', 'bookly' );
    }

    /**
     * @inheritDoc
     */
    protected function register_controls()
    {
        $this->start_controls_section(
            'bookly_booking_section',
            array(
                'label' => '<div class="bookly-elementor-section"><p>Bookly</p><br><p class="bookly-elementor-section-description">'
                    . esc_html__( 'A custom block for displaying booking form', 'bookly' ) . '</p></div>',
            )
        );

        $data = $this->getControlsData();
        if ( Config::locationsActive() ) {
            $this->add_control(
                'location_id',
                array(
                    'label' => __( 'Location', 'bookly' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => $data['locations'],
                    'default' => '0',
                )
            );
            $this->add_control(
                'hide_locations',
                array(
                    'label' => __( 'hide', 'bookly' ),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => false,
                )
            );
            $this->add_control( 'hr-location', array( 'type' => Controls_Manager::DIVIDER, ) );
        }
        $this->add_control(
            'category_id',
            array(
                'label' => __( 'Category', 'bookly' ),
                'type' => Controls_Manager::SELECT,
                'options' => $data['categories'],
                'default' => '0',
            )
        );
        $this->add_control(
            'hide_categories',
            array(
                'label' => __( 'hide', 'bookly' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => false,
            )
        );
        $this->add_control( 'hr-category', array( 'type' => Controls_Manager::DIVIDER, ) );

        $this->add_control(
            'service_id',
            array(
                'label' => __( 'Service', 'bookly' ),
                'type' => Controls_Manager::SELECT,
                'options' => $data['services'],
                'default' => '0',
            )
        );
        $this->add_control(
            'hide_services',
            array(
                'label' => __( 'hide', 'bookly' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => false,
            )
        );
        $this->add_control( 'hr-service', array( 'type' => Controls_Manager::DIVIDER, ) );

        $this->add_control(
            'staff_member_id',
            array(
                'label' => __( 'Employee', 'bookly' ),
                'type' => Controls_Manager::SELECT,
                'options' => $data['staff'],
                'default' => '0',
            )
        );
        $this->add_control(
            'hide_staff_members',
            array(
                'label' => __( 'hide', 'bookly' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => false,
            )
        );

        $this->add_control(
            '_heading_buttons',
            array(
                'raw' => '<strong>' . esc_html__( 'Fields', 'bookly' ) . '</strong><span class="bookly-elementor-right">' . esc_html__( 'hide', 'bookly' ) . '</span>',
                'type' => Controls_Manager::RAW_HTML,
                'separator' => 'before',
            )
        );
        if ( Config::customDurationActive() ) {
            $this->add_control(
                'hide_service_duration',
                array(
                    'label' => __( 'Duration', 'bookly' ),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => false,
                )
            );
        }
        if ( Config::groupBookingActive() ) {
            $this->add_control(
                'hide_nop',
                array(
                    'label' => __( 'Number of persons', 'bookly' ),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                )
            );
        }
        if ( Config::multiplyAppointmentsActive() ) {
            $this->add_control(
                'hide_quantity',
                array(
                    'label' => __( 'Quantity', 'bookly' ),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => false,
                )
            );
        }
        $this->add_control(
            'hide_date',
            array(
                'label' => __( 'Date', 'bookly' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => false,
            )
        );
        $this->add_control(
            'hide_week_days',
            array(
                'label' => __( 'Week days', 'bookly' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => false,
            )
        );
        $this->add_control(
            'hide_time_range',
            array(
                'label' => __( 'Time range', 'bookly' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => false,
            )
        );

        $this->end_controls_section();
    }

    /**
     * @inheritDoc
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $short_code = '[bookly-form';
        $hide = array();

        if ( Config::locationsActive() ) {
            if ( isset( $settings['location_id'] ) && $settings['location_id'] != 0 ) {
                $short_code .= ' location_id="' . $settings['location_id'] . '"';
            }
            if ( isset( $settings['hide_locations'] ) && $settings['hide_locations'] == 'yes' ) {
                $hide[] = 'locations';
            }
        }
        if ( isset( $settings['category_id'] ) && $settings['category_id'] != 0 ) {
            $short_code .= ' category_id="' . $settings['category_id'] . '"';
        }
        if ( isset( $settings['hide_categories'] ) && $settings['hide_categories'] == 'yes' ) {
            $hide[] = 'categories';
        }
        if ( isset( $settings['service_id'] ) && $settings['service_id'] != 0 ) {
            $short_code .= ' service_id="' . $settings['service_id'] . '"';
        }
        if ( isset( $settings['hide_services'] ) && $settings['hide_services'] == 'yes' ) {
            $hide[] = 'services';
        }
        if ( Config::customDurationActive() ) {
            if ( isset( $settings['hide_service_duration'] ) && $settings['hide_service_duration'] == 'yes' ) {
                $hide[] = 'service_duration';
            }
        }
        if ( isset( $settings['staff_member_id'] ) && $settings['staff_member_id'] != 0 ) {
            $short_code .= ' staff_member_id="' . $settings['staff_member_id'] . '"';
        }
        if ( Config::groupBookingActive() ) {
            if ( isset( $settings['hide_nop'] ) && $settings['hide_nop'] == '' ) {
                $short_code .= ' show_number_of_persons="1"';
            }
        }
        if ( Config::multiplyAppointmentsActive() ) {
            if ( isset( $settings['hide_quantity'] ) && $settings['hide_quantity'] == 'yes' ) {
                $hide[] = 'quantity';
            }
        }
        if ( isset( $settings['hide_staff_members'] ) && $settings['hide_staff_members'] == 'yes' ) {
            $hide[] = 'staff_members';
        }
        if ( isset( $settings['hide_date'] ) && $settings['hide_date'] == 'yes' ) {
            $hide[] = 'date';
        }
        if ( isset( $settings['hide_week_days'] ) && $settings['hide_week_days'] == 'yes' ) {
            $hide[] = 'week_days';
        }
        if ( isset( $settings['hide_time_range'] ) && $settings['hide_time_range'] == 'yes' ) {
            $hide[] = 'time_range';
        }
        if ( $hide ) {
            $short_code .= ' hide="' . implode( ',', $hide ) . '"';
        }

        $short_code .= ']';

        echo $short_code;
    }

    /**
     * @return array
     */
    private function getControlsData()
    {
        $casest = Lib\Config::getCaSeSt();
        $locations = array( 0 => __( 'Select location', 'bookly' ), );
        $categories = array( 0 => __( 'Select category', 'bookly' ) );
        $services = array( 0 => __( 'Select service', 'bookly' ) );
        $staff = array( 0 => __( 'Any', 'bookly' ) );
        foreach ( $casest['locations'] as $location ) {
            $locations[ $location['id'] ] = $location['name'];
        }
        foreach ( $casest['categories'] as $category ) {
            $categories[ $category['id'] ] = $category['name'];
        }
        foreach ( $casest['services'] as $service ) {
            $services[ $service['id'] ] = $service['name'];
        }
        foreach ( $casest['staff'] as $value ) {
            $staff[ $value['id'] ] = $value['name'];
        }

        return compact( 'locations', 'categories', 'services', 'staff' );
    }

}
