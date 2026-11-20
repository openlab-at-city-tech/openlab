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
	'info_woo_account_title'        => [
		'control_type' => 'kadence_title_control',
		'section'      => 'account_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'My Account Navigation', 'kadence' ),
		'settings'     => false,
	],
	'woo_account_navigation_avatar' => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'account_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'woo_account_navigation_avatar' ),
		'label'        => esc_html__( 'Show User Name and Avatar?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'woo_account_navigation_layout' => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'account_layout',
		'label'        => esc_html__( 'Navigation Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'woo_account_navigation_layout' ),
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
];

Theme_Customizer::add_settings( $settings );
