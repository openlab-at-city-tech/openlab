<?php
/**
 * Header Main Row Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

Theme_Customizer::add_settings(
	[
		'header_button_tabs'          => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'header_button',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'header_button',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'header_button_design',
				],
				'active'  => 'general',
			],
		],
		'header_button_tabs_design'   => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'header_button_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'header_button',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'header_button_design',
				],
				'active'  => 'design',
			],
		],
		'header_button_label'         => [
			'control_type' => 'kadence_text_control',
			'section'      => 'header_button',
			'sanitize'     => 'sanitize_text_field',
			'priority'     => 4,
			'label'        => esc_html__( 'Label', 'kadence' ),
			'default'      => kadence()->default( 'header_button_label' ),
			'live_method'  => [
				[
					'type'     => 'html',
					'selector' => '#main-header .header-button',
					'pattern'  => '$',
					'key'      => '',
				],
			],
		],
		'header_button_link'          => [
			'control_type' => 'kadence_text_control',
			'section'      => 'header_button',
			'sanitize'     => 'esc_url_raw',
			'label'        => esc_html__( 'URL', 'kadence' ),
			'priority'     => 4,
			'default'      => kadence()->default( 'header_button_link' ),
			'partial'      => [
				'selector'            => '.header-button-wrap',
				'container_inclusive' => true,
				'render_callback'     => 'Kadence\header_button',
			],
		],
		'header_button_target'        => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => kadence()->default( 'header_button_target' ),
			'label'        => esc_html__( 'Open in New Tab?', 'kadence' ),
		],
		'header_button_nofollow'      => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => kadence()->default( 'header_button_nofollow' ),
			'label'        => esc_html__( 'Set link to nofollow?', 'kadence' ),
		],
		'header_button_sponsored'     => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => kadence()->default( 'header_button_sponsored' ),
			'label'        => esc_html__( 'Set link attribute Sponsored?', 'kadence' ),
		],
		'header_button_download'      => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => kadence()->default( 'header_button_download' ),
			'label'        => esc_html__( 'Set link to Download?', 'kadence' ),
		],
		'header_button_style'         => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => kadence()->default( 'header_button_style' ),
			'label'        => esc_html__( 'Button Style', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '#main-header .header-button',
					'pattern'  => 'button-style-$',
					'key'      => '',
				],
			],
			'input_attrs'  => [
				'layout'     => [
					'filled'    => [
						'name' => __( 'Filled', 'kadence' ),
					],
					'outline'   => [
						'name' => __( 'Outline', 'kadence' ),
					],
					'secondary' => [
						'name' => __( 'Secondary', 'kadence' ),
					],
				],
				'responsive' => false,
			],
		],
		'header_button_visibility'    => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => kadence()->default( 'header_button_visibility' ),
			'label'        => esc_html__( 'Button Visibility', 'kadence' ),
			'partial'      => [
				'selector'            => '.header-button-wrap',
				'container_inclusive' => true,
				'render_callback'     => 'Kadence\header_button',
			],
			'input_attrs'  => [
				'layout'     => [
					'all'       => [
						'name' => __( 'Everyone', 'kadence' ),
					],
					'loggedout' => [
						'name' => __( 'Logged Out Only', 'kadence' ),
					],
					'loggedin'  => [
						'name' => __( 'Logged In Only', 'kadence' ),
					],
				],
				'responsive' => false,
			],
		],
		'header_button_size'          => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'header_button_design',
			'priority'     => 4,
			'default'      => kadence()->default( 'header_button_size' ),
			'label'        => esc_html__( 'Button Size', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '#main-header .header-button',
					'pattern'  => 'button-size-$',
					'key'      => '',
				],
			],
			'input_attrs'  => [
				'layout'     => [
					'small'  => [
						'name' => __( 'Sm', 'kadence' ),
					],
					'medium' => [
						'name' => __( 'Md', 'kadence' ),
						'icon' => '',
					],
					'large'  => [
						'name' => __( 'Lg', 'kadence' ),
						'icon' => '',
					],
					'custom' => [
						'name' => __( 'Custom', 'kadence' ),
						'icon' => '',
					],
				],
				'responsive' => false,
			],
		],
		'header_button_padding'       => [
			'control_type' => 'kadence_measure_control',
			'section'      => 'header_button_design',
			'priority'     => 10,
			'default'      => kadence()->default( 'header_button_padding' ),
			'label'        => esc_html__( 'Padding', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#main-header .button-size-custom.header-button',
					'property' => 'padding',
					'pattern'  => '$',
					'key'      => 'measure',
				],
			],
			'context'      => [
				[
					'setting'  => 'header_button_size',
					'operator' => '=',
					'value'    => 'custom',
				],
			],
			'input_attrs'  => [
				'responsive' => false,
			],
		],
		'header_button_color'         => [
			'control_type' => 'kadence_color_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Colors', 'kadence' ),
			'default'      => kadence()->default( 'header_button_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#main-header .header-button',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '#main-header .header-button:hover',
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
		'header_button_background'    => [
			'control_type' => 'kadence_color_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Background Colors', 'kadence' ),
			'default'      => kadence()->default( 'header_button_background' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#main-header .header-button',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '#main-header .header-button:hover',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'hover',
				],
			],
			'context'      => [
				[
					'setting'  => 'header_button_style',
					'operator' => '=',
					'value'    => 'filled',
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
		'header_button_border_colors' => [
			'control_type' => 'kadence_color_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Border Colors', 'kadence' ),
			'default'      => kadence()->default( 'header_button_border' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#main-header .header-button-wrap .header-button',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '#main-header .header-button-wrap .header-button:hover',
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
		'header_button_border'        => [
			'control_type' => 'kadence_border_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'header_button_border' ),
			'live_method'  => [
				[
					'type'     => 'css_border',
					'selector' => '#main-header .header-button',
					'property' => 'border',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
			'input_attrs'  => [
				'responsive' => false,
				'color'      => false,
			],
		],
		'header_button_radius'        => [
			'control_type' => 'kadence_measure_control',
			'section'      => 'header_button_design',
			'priority'     => 10,
			'default'      => kadence()->default( 'header_button_radius' ),
			'label'        => esc_html__( 'Border Radius', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#main-header .header-button',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'measure',
				],
			],
			'input_attrs'  => [
				'responsive' => false,
			],
		],
		'header_button_typography'    => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Font', 'kadence' ),
			'default'      => kadence()->default( 'header_button_typography' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '#main-header .header-button',
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
				'id'      => 'header_button_typography',
				'options' => 'no-color',
			],
		],
		'header_button_shadow'        => [
			'control_type' => 'kadence_shadow_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Button Shadow', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css_boxshadow',
					'selector' => '#main-header .header-button',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				],
			],
			'default'      => kadence()->default( 'header_button_shadow' ),
		],
		'header_button_shadow_hover'  => [
			'control_type' => 'kadence_shadow_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Button Hover State Shadow', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css_boxshadow',
					'selector' => '#main-header .header-button',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				],
			],
			'default'      => kadence()->default( 'header_button_shadow_hover' ),
		],
		'header_button_margin'        => [
			'control_type' => 'kadence_measure_control',
			'section'      => 'header_button_design',
			'priority'     => 10,
			'default'      => kadence()->default( 'header_button_margin' ),
			'label'        => esc_html__( 'Margin', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#main-header .header-button',
					'property' => 'margin',
					'pattern'  => '$',
					'key'      => 'measure',
				],
			],
			'input_attrs'  => [
				'responsive' => false,
			],
		],
	]
);
