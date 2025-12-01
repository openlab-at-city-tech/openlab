<?php
/**
 * Outline Button Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

Theme_Customizer::add_settings(
	array(
		'buttons_outline_color' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'outline_button',
			'label'        => esc_html__( 'Text Colors', 'kadence' ),
			'default'      => kadence()->default( 'buttons_outline_color' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.button.button-style-outline:not(.has-text-color), .button.kb-btn-global-outline',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.button.button-style-outline:not(.has-text-color):hover, .button.kb-btn-global-outline:hover',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'hover',
				),
			),
			'input_attrs'  => array(
				'colors' => array(
					'color' => array(
						'tooltip' => __( 'Initial Color', 'kadence' ),
						'palette' => true,
					),
					'hover' => array(
						'tooltip' => __( 'Hover Color', 'kadence' ),
						'palette' => true,
					),
				),
			),
		),
		'buttons_outline_border_colors' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'outline_button',
			'label'        => esc_html__( 'Border Colors', 'kadence' ),
			'default'      => kadence()->default( 'buttons_outline_border' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.button.button-style-outline, .button.kb-btn-global-outline',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.button.button-style-outline:hover, .button.kb-btn-global-outline:hover',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'hover',
				),
			),
			'input_attrs'  => array(
				'colors' => array(
					'color' => array(
						'tooltip' => __( 'Initial Color', 'kadence' ),
						'palette' => true,
					),
					'hover' => array(
						'tooltip' => __( 'Hover Color', 'kadence' ),
						'palette' => true,
					),
				),
			),
		),
		'buttons_outline_border' => array(
			'control_type' => 'kadence_border_control',
			'section'      => 'outline_button',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'buttons_outline_border' ),
			'live_method'     => array(
				array(
					'type'     => 'css_border',
					'selector' => '.button.button-style-outline, .button.kb-btn-global-outline',
					'property' => 'border',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
			'input_attrs'  => array(
				'responsive' => true,
				'color'      => false,
			),
		),
		'buttons_outline_border_radius' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'outline_button',
			'label'        => esc_html__( 'Border Radius', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.button.button-style-outline, .button.kb-btn-global-outline',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'buttons_outline_border_radius' ),
			'input_attrs'  => array(
				'min'        => array(
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
					'%'   => 0,
				),
				'max'        => array(
					'px'  => 100,
					'em'  => 12,
					'rem' => 12,
					'%'   => 100,
				),
				'step'       => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'%'   => 1,
				),
				'units'      => array( 'px', 'em', 'rem', '%' ),
				'responsive' => true,
			),
		),
		'buttons_outline_typography' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'outline_button',
			'label'        => esc_html__( 'Font', 'kadence' ),
			'default'      => kadence()->default( 'buttons_outline_typography' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.button.button-style-outline, .button.kb-btn-global-outline',
					'pattern'  => array(
						'desktop' => '$',
						'tablet'  => '$',
						'mobile'  => '$',
					),
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id' => 'buttons_outline_typography',
				'options' => 'no-color',
			),
		),
		'buttons_outline_padding' => array(
			'control_type' => 'kadence_measure_control',
			'section'      => 'outline_button',
			'priority'     => 10,
			'default'      => kadence()->default( 'buttons_outline_padding' ),
			'label'        => esc_html__( 'Padding', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.button.button-style-outline, .button.kb-btn-global-outline',
					'property' => 'padding',
					'pattern'  => '$',
					'key'      => 'measure',
				),
			),
			'input_attrs'  => array(
				'responsive' => true,
			),
		),
		'buttons_outline_shadow' => array(
			'control_type' => 'kadence_shadow_control',
			'section'      => 'outline_button',
			'priority'     => 20,
			'label'        => esc_html__( 'Button Shadow', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css_boxshadow',
					'selector' => '.button.button-style-outline, .button.kb-btn-global-outline',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				),
			),
			'default'      => kadence()->default( 'buttons_outline_shadow' ),
		),
		'buttons_outline_shadow_hover' => array(
			'control_type' => 'kadence_shadow_control',
			'section'      => 'outline_button',
			'priority'     => 20,
			'label'        => esc_html__( 'Button Hover State Shadow', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css_boxshadow',
					'selector' => '.button.button-style-outline:hover, .button.kb-btn-global-outline:hover',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				),
			),
			'default'      => kadence()->default( 'buttons_outline_shadow_hover' ),
		),
	)
);

