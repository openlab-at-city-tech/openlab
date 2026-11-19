<?php
/**
 * Secondary Button Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

Theme_Customizer::add_settings(
	[
		'buttons_secondary_color'         => [
			'control_type' => 'kadence_color_control',
			'section'      => 'secondary_button',
			'label'        => esc_html__( 'Text Colors', 'kadence' ),
			'default'      => kadence()->default( 'buttons_secondary_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.button.button-style-secondary',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.button.button-style-secondary:hover',
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
		'buttons_secondary_background'    => [
			'control_type' => 'kadence_color_control',
			'section'      => 'secondary_button',
			'label'        => esc_html__( 'Background Colors', 'kadence' ),
			'default'      => kadence()->default( 'buttons_secondary_background' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.button.button-style-secondary',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.button.button-style-secondary:hover',
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
		'buttons_secondary_border_colors' => [
			'control_type' => 'kadence_color_control',
			'section'      => 'secondary_button',
			'label'        => esc_html__( 'Border Colors', 'kadence' ),
			'default'      => kadence()->default( 'buttons_secondary_border' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.button.button-style-secondary',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.button.button-style-secondary:hover',
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
		'buttons_secondary_border'        => [
			'control_type' => 'kadence_border_control',
			'section'      => 'secondary_button',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'buttons_secondary_border' ),
			'live_method'  => [
				[
					'type'     => 'css_border',
					'selector' => '.button.button-style-secondary',
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
		'buttons_secondary_border_radius' => [
			'control_type' => 'kadence_range_control',
			'section'      => 'secondary_button',
			'label'        => esc_html__( 'Border Radius', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.button.button-style-secondary',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'buttons_secondary_border_radius' ),
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
		'buttons_secondary_typography'    => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'secondary_button',
			'label'        => esc_html__( 'Font', 'kadence' ),
			'default'      => kadence()->default( 'buttons_secondary_typography' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.button.button-style-secondary',
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
				'id'      => 'buttons_secondary_typography',
				'options' => 'no-color',
			],
		],
		'buttons_secondary_padding'       => [
			'control_type' => 'kadence_measure_control',
			'section'      => 'secondary_button',
			'priority'     => 10,
			'default'      => kadence()->default( 'buttons_secondary_padding' ),
			'label'        => esc_html__( 'Padding', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.button.button-style-secondary',
					'property' => 'padding',
					'pattern'  => '$',
					'key'      => 'measure',
				],
			],
			'input_attrs'  => [
				'responsive' => true,
			],
		],
		'buttons_secondary_shadow'        => [
			'control_type' => 'kadence_shadow_control',
			'section'      => 'secondary_button',
			'priority'     => 20,
			'label'        => esc_html__( 'Button Shadow', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css_boxshadow',
					'selector' => '.button.button-style-secondary',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				],
			],
			'default'      => kadence()->default( 'buttons_secondary_shadow' ),
		],
		'buttons_secondary_shadow_hover'  => [
			'control_type' => 'kadence_shadow_control',
			'section'      => 'secondary_button',
			'priority'     => 20,
			'label'        => esc_html__( 'Button Hover State Shadow', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css_boxshadow',
					'selector' => '.button.button-style-secondary:hover',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				],
			],
			'default'      => kadence()->default( 'buttons_secondary_shadow_hover' ),
		],
	]
);
