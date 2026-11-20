<?php
/**
 * Product Layout Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'info_llms_dashboard_title'        => [
		'control_type' => 'kadence_title_control',
		'section'      => 'llms_dashboard_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Dashboard Navigation', 'kadence' ),
		'settings'     => false,
	],
	'llms_dashboard_navigation_layout' => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'llms_dashboard_layout',
		'label'        => esc_html__( 'Navigation Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'llms_dashboard_navigation_layout' ),
		'input_attrs'  => [
			'layout'     => [
				'left'  => [
					'tooltip' => __( 'Positioned on Left Content', 'kadence' ),
					'name'    => __( 'Left', 'kadence' ),
					'icon'    => '',
				],
				'above' => [
					'tooltip' => __( 'Positioned on Top Content', 'kadence' ),
					'name'    => __( 'Above', 'kadence' ),
					'icon'    => '',
				],
				'right' => [
					'tooltip' => __( 'Positioned on Right Content', 'kadence' ),
					'name'    => __( 'Right', 'kadence' ),
					'icon'    => '',
				],
			],
			'responsive' => false,
		],
	],
	'llms_dashboard_archive_columns'   => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'llms_dashboard_layout',
		'priority'     => 20,
		'label'        => esc_html__( 'Course and Membership Items Columns', 'kadence' ),
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'llms_dashboard_archive_columns' ),
		'input_attrs'  => [
			'layout'     => [
				'2' => [
					'name' => __( '2', 'kadence' ),
				],
				'3' => [
					'name' => __( '3', 'kadence' ),
				],
				'4' => [
					'name' => __( '4', 'kadence' ),
				],
			],
			'responsive' => false,
		],
	],
];

Theme_Customizer::add_settings( $settings );
