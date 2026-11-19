<?php
/**
 * Primary Button Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

Theme_Customizer::add_settings(
	[
		'buttons_color'         => [
			'control_type' => 'kadence_color_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Text Colors', 'kadence' ),
			'default'      => kadence()->default( 'buttons_color' ),
			'live_method'  => [
				[
					'type'     => 'global',
					'selector' => '--global-palette-btn',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'global',
					'selector' => '--global-palette-btn-hover',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'hover',
				],
			],
			'input_attrs'  => [
				'colors' => [
					'color' => [
						'tooltip' => __( 'Initial Color', 'kadence' ),
						'palette' => true,
					],
					'hover' => [
						'tooltip' => __( 'Hover Color', 'kadence' ),
						'palette' => true,
					],
				],
			],
		],
		'buttons_background'    => [
			'control_type' => 'kadence_color_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Background Colors', 'kadence' ),
			'default'      => kadence()->default( 'buttons_background' ),
			'live_method'  => [
				[
					'type'     => 'global',
					'selector' => '--global-palette-btn-bg',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'global',
					'selector' => '--global-palette-btn-bg-hover',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'hover',
				],
			],
			'input_attrs'  => [
				'colors'        => [
					'color' => [
						'tooltip' => __( 'Initial Color', 'kadence' ),
						'palette' => true,
					],
					'hover' => [
						'tooltip' => __( 'Hover Color', 'kadence' ),
						'palette' => true,
					],
				],
				'allowGradient' => true,
			],
		],
		'buttons_border_colors' => [
			'control_type' => 'kadence_color_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Border Colors', 'kadence' ),
			'default'      => kadence()->default( 'buttons_border' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => 'button:hover, .button:hover, .wp-block-button__link:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'hover',
				],
			],
			'input_attrs'  => [
				'colors' => [
					'color' => [
						'tooltip' => __( 'Initial Color', 'kadence' ),
						'palette' => true,
					],
					'hover' => [
						'tooltip' => __( 'Hover Color', 'kadence' ),
						'palette' => true,
					],
				],
			],
		],
		'buttons_border'        => [
			'control_type' => 'kadence_border_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'buttons_border' ),
			'live_method'  => [
				[
					'type'     => 'css_border',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
					'property' => 'border',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
			'input_attrs'  => [
				'responsive' => true,
				'color'      => false,
			],
		],
		'buttons_border_radius' => [
			'control_type' => 'kadence_range_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Border Radius', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'buttons_border_radius' ),
			'input_attrs'  => [
				'min'        => [
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
					'%'   => 0,
				],
				'max'        => [
					'px'  => 100,
					'em'  => 12,
					'rem' => 12,
					'%'   => 100,
				],
				'step'       => [
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'%'   => 1,
				],
				'units'      => [ 'px', 'em', 'rem', '%' ],
				'responsive' => true,
			],
		],
		'buttons_typography'    => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Font', 'kadence' ),
			'default'      => kadence()->default( 'buttons_typography' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
					'pattern'  => [
						'desktop' => '$',
						'tablet'  => '$',
						'mobile'  => '$',
					],
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'input_attrs'  => [
				'id'      => 'buttons_typography',
				'options' => 'no-color',
			],
		],
		'buttons_padding'       => [
			'control_type' => 'kadence_measure_control',
			'section'      => 'general_buttons',
			'priority'     => 10,
			'default'      => kadence()->default( 'buttons_padding' ),
			'label'        => esc_html__( 'Padding', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
					'property' => 'padding',
					'pattern'  => '$',
					'key'      => 'measure',
				],
			],
			'input_attrs'  => [
				'responsive' => true,
			],
		],
		'buttons_shadow'        => [
			'control_type' => 'kadence_shadow_control',
			'section'      => 'general_buttons',
			'priority'     => 20,
			'label'        => esc_html__( 'Button Shadow', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css_boxshadow',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				],
			],
			'default'      => kadence()->default( 'buttons_shadow' ),
		],
		'buttons_shadow_hover'  => [
			'control_type' => 'kadence_shadow_control',
			'section'      => 'general_buttons',
			'priority'     => 20,
			'label'        => esc_html__( 'Button Hover State Shadow', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css_boxshadow',
					'selector' => 'button:hover, .button:hover, .wp-block-button__link:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				],
			],
			'default'      => kadence()->default( 'buttons_shadow_hover' ),
		],
	]
);
