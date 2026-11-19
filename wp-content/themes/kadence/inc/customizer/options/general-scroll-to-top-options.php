<?php
/**
 * Header Builder Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

Theme_Customizer::add_settings(
	[
		'scroll_up_tabs'          => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'scroll_up',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'scroll_up',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'scroll_up_design',
				],
				'active'  => 'general',
			],
		],
		'scroll_up_tabs_design'   => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'scroll_up_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'scroll_up',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'scroll_up_design',
				],
				'active'  => 'design',
			],
		],
		'scroll_up'               => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'scroll_up',
			'default'      => kadence()->default( 'scroll_up' ),
			'label'        => esc_html__( 'Enable Scroll To Top', 'kadence' ),
			'transport'    => 'refresh',
		],
		'scroll_up_icon'          => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'scroll_up',
			'priority'     => 10,
			'default'      => kadence()->default( 'scroll_up_icon' ),
			'label'        => esc_html__( 'Scroll Up Icon', 'kadence' ),
			'partial'      => [
				'selector'            => '.scroll-up-wrap',
				'container_inclusive' => true,
				'render_callback'     => 'Kadence\scroll_up',
			],
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				],
			],
			'input_attrs'  => [
				'layout'     => [
					'arrow-up'    => [
						'icon' => 'arrowUp',
					],
					'arrow-up2'   => [
						'icon' => 'arrowUp2',
					],
					'chevron-up'  => [
						'icon' => 'chevronUp',
					],
					'chevron-up2' => [
						'icon' => 'chevronUp2',
					],
				],
				'responsive' => false,
			],
		],
		'scroll_up_icon_size'     => [
			'control_type' => 'kadence_range_control',
			'section'      => 'scroll_up',
			'label'        => esc_html__( 'Icon Size', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'font-size',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'scroll_up_icon_size' ),
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
				'responsive' => true,
			],
		],
		'scroll_up_side'          => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'scroll_up',
			'default'      => kadence()->default( 'scroll_up_side' ),
			'label'        => esc_html__( 'Align', 'kadence' ),
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				],
			],
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '.scroll-up-wrap',
					'pattern'  => 'scroll-up-side-$',
					'key'      => '',
				],
			],
			'input_attrs'  => [
				'layout'     => [
					'left'  => [
						'name' => __( 'Left', 'kadence' ),
						'icon' => '',
					],
					'right' => [
						'name' => __( 'Right', 'kadence' ),
						'icon' => '',
					],
				],
				'responsive' => false,
			],
		],
		'scroll_up_side_offset'   => [
			'control_type' => 'kadence_range_control',
			'section'      => 'scroll_up',
			'label'        => esc_html__( 'Side Offset', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_side_offset' ),
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				],
			],
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader.scroll-up-side-right, #kt-scroll-up.scroll-up-side-right',
					'pattern'  => '$',
					'property' => 'right',
					'key'      => 'size',
				],
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader.scroll-up-side-left, #kt-scroll-up.scroll-up-side-left',
					'pattern'  => '$',
					'property' => 'left',
					'key'      => 'size',
				],
			],
			'input_attrs'  => [
				'min'        => [
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
					'vw'  => 0,
				],
				'max'        => [
					'px'  => 600,
					'em'  => 20,
					'rem' => 20,
					'vw'  => 100,
				],
				'step'       => [
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'vw'  => 1,
				],
				'units'      => [ 'px', 'em', 'rem', 'vw' ],
				'responsive' => true,
			],
		],
		'scroll_up_bottom_offset' => [
			'control_type' => 'kadence_range_control',
			'section'      => 'scroll_up',
			'label'        => esc_html__( 'Bottom Offset', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_bottom_offset' ),
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				],
			],
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'pattern'  => '$',
					'property' => 'bottom',
					'key'      => 'size',
				],
			],
			'input_attrs'  => [
				'min'        => [
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
					'vh'  => 0,
				],
				'max'        => [
					'px'  => 600,
					'em'  => 20,
					'rem' => 20,
					'vh'  => 100,
				],
				'step'       => [
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'vh'  => 1,
				],
				'units'      => [ 'px', 'em', 'rem', 'vh' ],
				'responsive' => true,
			],
		],
		'scroll_up_visiblity'     => [
			'control_type' => 'kadence_check_icon_control',
			'section'      => 'scroll_up',
			'default'      => kadence()->default( 'scroll_up_visiblity' ),
			'label'        => esc_html__( 'Visibility', 'kadence' ),
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				],
			],
			'partial'      => [
				'selector'            => '.scroll-up-wrap',
				'container_inclusive' => true,
				'render_callback'     => 'Kadence\scroll_up',
			],
			'input_attrs'  => [
				'options' => [
					'desktop' => [
						'name' => __( 'Desktop', 'kadence' ),
						'icon' => 'desktop',
					],
					'tablet'  => [
						'name' => __( 'Tablet', 'kadence' ),
						'icon' => 'tablet',
					],
					'mobile'  => [
						'name' => __( 'Mobile', 'kadence' ),
						'icon' => 'smartphone',
					],
				],
			],
		],
		'scroll_up_style'         => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'scroll_up_design',
			'default'      => kadence()->default( 'scroll_up_style' ),
			'label'        => esc_html__( 'Scroll Button Style', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'pattern'  => 'scroll-up-style-$',
					'key'      => '',
				],
			],
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
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
		'scroll_up_color'         => [
			'control_type' => 'kadence_color_control',
			'section'      => 'scroll_up_design',
			'label'        => esc_html__( 'Colors', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader:hover, #kt-scroll-up:hover',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'hover',
				],
			],
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
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
		'scroll_up_background'    => [
			'control_type' => 'kadence_color_control',
			'section'      => 'scroll_up_design',
			'label'        => esc_html__( 'Background Colors', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_background' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader:hover, #kt-scroll-up:hover',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'hover',
				],
			],
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				],
				[
					'setting'  => 'scroll_up_style',
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
		'scroll_up_border_colors' => [
			'control_type' => 'kadence_color_control',
			'section'      => 'scroll_up_design',
			'label'        => esc_html__( 'Border Colors', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_border' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader:hover, #kt-scroll-up:hover',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'hover',
				],
			],
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
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
		'scroll_up_border'        => [
			'control_type' => 'kadence_border_control',
			'section'      => 'scroll_up_design',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_border' ),
			'live_method'  => [
				[
					'type'     => 'css_border',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'border',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				],
			],
			'input_attrs'  => [
				'responsive' => false,
				'color'      => false,
			],
		],
		'scroll_up_radius'        => [
			'control_type' => 'kadence_measure_control',
			'section'      => 'scroll_up_design',
			'priority'     => 10,
			'default'      => kadence()->default( 'scroll_up_radius' ),
			'label'        => esc_html__( 'Border Radius', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'measure',
				],
			],
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				],
			],
			'input_attrs'  => [
				'responsive' => false,
			],
		],
		'scroll_up_padding'       => [
			'control_type' => 'kadence_measure_control',
			'section'      => 'scroll_up_design',
			'priority'     => 10,
			'default'      => kadence()->default( 'scroll_up_padding' ),
			'label'        => esc_html__( 'Scroll Button Padding', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'padding',
					'pattern'  => '$',
					'key'      => 'measure',
				],
			],
			'context'      => [
				[
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				],
			],
			'input_attrs'  => [
				'responsive' => true,
			],
		],
	]
);
