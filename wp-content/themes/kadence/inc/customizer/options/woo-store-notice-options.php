<?php
/**
 * Header Main Row Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'woocommerce_store_notice_tabs'        => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'woocommerce_store_notice',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'woocommerce_store_notice',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'woocommerce_store_notice_design',
			],
			'active'  => 'general',
		],
	],
	'woocommerce_store_notice_tabs_design' => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'woocommerce_store_notice_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'woocommerce_store_notice',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'woocommerce_store_notice_design',
			],
			'active'  => 'design',
		],
	],
	'woo_store_notice_placement'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'woocommerce_store_notice',
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'woo_store_notice_placement' ),
		'label'        => esc_html__( 'Store Notice Placement', 'kadence' ),
		'input_attrs'  => [
			'layout'     => [
				'standard' => [
					'tooltip' => __( 'Hangs down over the top of the header', 'kadence' ),
					'name'    => __( 'Hang Over Top', 'kadence' ),
					'icon'    => '',
				],
				'above'    => [
					'tooltip' => __( 'Placed above the Header', 'kadence' ),
					'name'    => __( 'Above', 'kadence' ),
					'icon'    => '',
				],
				'bottom'   => [
					'tooltip' => __( 'Stuck to the Bottom of the screen', 'kadence' ),
					'name'    => __( 'Bottom', 'kadence' ),
					'icon'    => '',
				],
			],
			'responsive' => false,
		],
	],
	'woo_store_notice_hide_dismiss'        => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'woocommerce_store_notice',
		'default'      => kadence()->default( 'woo_store_notice_hide_dismiss' ),
		'label'        => esc_html__( 'Disable Dismiss Button?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'woo_store_notice_placement',
				'operator' => '=',
				'value'    => 'above',
			],
		],
	],
	'woo_store_notice_font'                => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'woocommerce_store_notice_design',
		'label'        => esc_html__( 'Notice Font', 'kadence' ),
		'default'      => kadence()->default( 'woo_store_notice_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.woocommerce-demo-store .woocommerce-store-notice, .woocommerce-demo-store .woocommerce-store-notice a',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id' => 'woo_store_notice_font',
		],
	],
	'woo_store_notice_background'          => [
		'control_type' => 'kadence_color_control',
		'section'      => 'woocommerce_store_notice_design',
		'label'        => esc_html__( 'Background Color', 'kadence' ),
		'default'      => kadence()->default( 'woo_store_notice_background' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.woocommerce-demo-store .woocommerce-store-notice',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'input_attrs'  => [
			'colors'        => [
				'color' => [
					'tooltip' => __( 'Overlay Color', 'kadence' ),
					'palette' => true,
				],
			],
			'allowGradient' => true,
		],
	],
];

Theme_Customizer::add_settings( $settings );
