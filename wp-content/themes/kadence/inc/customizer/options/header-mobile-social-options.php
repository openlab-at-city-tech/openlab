<?php
/**
 * Header Builder Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'mobile_social_tabs'                 => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'mobile_social',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'mobile_social',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'mobile_social_design',
			],
			'active'  => 'general',
		],
	],
	'mobile_social_tabs_design'          => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'mobile_social_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'mobile_social',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'mobile_social_design',
			],
			'active'  => 'design',
		],
	],
	'header_mobile_social_items'         => [
		'control_type' => 'kadence_social_control',
		'section'      => 'mobile_social',
		'priority'     => 6,
		'default'      => kadence()->default( 'header_mobile_social_items' ),
		'label'        => esc_html__( 'Social Items', 'kadence' ),
		'partial'      => [
			'selector'            => '.header-mobile-social-wrap',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\mobile_social',
		],
	],
	'header_mobile_social_show_label'    => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'mobile_social',
		'priority'     => 8,
		'default'      => kadence()->default( 'header_mobile_social_show_label' ),
		'label'        => esc_html__( 'Show Icon Label?', 'kadence' ),
		'partial'      => [
			'selector'            => '.header-mobile-social-wrap',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\mobile_social',
		],
	],
	'header_mobile_social_item_spacing'  => [
		'control_type' => 'kadence_range_control',
		'section'      => 'mobile_social',
		'label'        => esc_html__( 'Item Spacing', 'kadence' ),
		'default'      => kadence()->default( 'header_mobile_social_item_spacing' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.header-mobile-social-wrap .header-mobile-social-inner-wrap',
				'property' => 'gap',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'input_attrs'  => [
			'min'        => [
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			],
			'max'        => [
				'px'  => 50,
				'em'  => 3,
				'rem' => 3,
			],
			'step'       => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			],
			'units'      => [ 'px', 'em', 'rem' ],
			'responsive' => false,
		],
	],
	'header_mobile_social_style'         => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'mobile_social',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_mobile_social_style' ),
		'label'        => esc_html__( 'Social Style', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.header-mobile-social-inner-wrap',
				'pattern'  => 'social-style-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'filled'  => [
					'name' => __( 'Filled', 'kadence' ),
				],
				'outline' => [
					'name' => __( 'Outline', 'kadence' ),
				],
			],
			'responsive' => false,
		],
	],
	'header_mobile_social_icon_size'     => [
		'control_type' => 'kadence_range_control',
		'section'      => 'mobile_social_design',
		'label'        => esc_html__( 'Icon Size', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.header-mobile-social-wrap .header-mobile-social-inner-wrap',
				'property' => 'font-size',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'header_mobile_social_icon_size' ),
		'input_attrs'  => [
			'min'        => [
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			],
			'max'        => [
				'px'  => 100,
				'em'  => 12,
				'rem' => 12,
			],
			'step'       => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			],
			'units'      => [ 'px', 'em', 'rem' ],
			'responsive' => false,
		],
	],
	'header_mobile_social_brand'         => [
		'control_type' => 'kadence_select_control',
		'section'      => 'mobile_social_design',
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'header_mobile_social_brand' ),
		'label'        => esc_html__( 'Use Brand Colors?', 'kadence' ),
		'input_attrs'  => [
			'options' => [
				''           => [
					'name' => __( 'No', 'kadence' ),
				],
				'always'     => [
					'name' => __( 'Yes', 'kadence' ),
				],
				'onhover'    => [
					'name' => __( 'On Hover', 'kadence' ),
				],
				'untilhover' => [
					'name' => __( 'Until Hover', 'kadence' ),
				],
			],
		],
	],
	'header_mobile_social_color'         => [
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_social_design',
		'label'        => esc_html__( 'Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_mobile_social_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.header-mobile-social-wrap a.social-button',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.header-mobile-social-wrap a.social-button:hover',
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
	'header_mobile_social_background'    => [
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_social_design',
		'label'        => esc_html__( 'Background Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_mobile_social_background' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.header-mobile-social-wrap a.social-button',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.header-mobile-social-wrap a.social-button:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'context'      => [
			[
				'setting'  => 'header_mobile_social_style',
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
	'header_mobile_social_border_colors' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_social_design',
		'label'        => esc_html__( 'Border Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_mobile_social_border' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#mobile-header .header-mobile-social-wrap a.social-button, #mobile-drawer .header-mobile-social-wrap a.social-button',
				'property' => 'border-color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '#mobile-header .header-mobile-social-wrap a.social-button:hover, #mobile-drawer .header-mobile-social-wrap a.social-button:hover',
				'property' => 'border-color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color' => [
					'tooltip' => __( ' Color', 'kadence' ),
					'palette' => true,
				],
				'hover' => [
					'tooltip' => __( 'Hover Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'header_mobile_social_border'        => [
		'control_type' => 'kadence_border_control',
		'section'      => 'mobile_social_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'header_mobile_social_border' ),
		'live_method'  => [
			[
				'type'     => 'css_border',
				'selector' => '.header-mobile-social-wrap a.social-button',
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
	'header_mobile_social_border_radius' => [
		'control_type' => 'kadence_range_control',
		'section'      => 'mobile_social_design',
		'label'        => esc_html__( 'Border Radius', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.header-mobile-social-wrap a.social-button',
				'property' => 'border-radius',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'header_mobile_social_border_radius' ),
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
			'responsive' => false,
		],
	],
	'header_mobile_social_typography'    => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'mobile_social_design',
		'label'        => esc_html__( 'Font', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'header_mobile_social_show_label',
				'operator' => '=',
				'value'    => true,
			],
		],
		'default'      => kadence()->default( 'header_mobile_social_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.header-mobile-social-wrap a.social-button .social-label',
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
			'id'      => 'header_mobile_social_typography',
			'options' => 'no-color',
		],
	],
	'header_mobile_social_margin'        => [
		'control_type' => 'kadence_measure_control',
		'section'      => 'mobile_social_design',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_mobile_social_margin' ),
		'label'        => esc_html__( 'Margin', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#mobile-header .header-mobile-social-wrap, #mobile-drawer .header-mobile-social-wrap',
				'property' => 'margin',
				'pattern'  => '$',
				'key'      => 'measure',
			],
		],
		'input_attrs'  => [
			'responsive' => false,
		],
	],
];

Theme_Customizer::add_settings( $settings );
