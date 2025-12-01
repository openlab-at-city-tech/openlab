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
	array(
		'scroll_up_tabs' => array(
			'control_type' => 'kadence_tab_control',
			'section'      => 'scroll_up',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'kadence' ),
					'target' => 'scroll_up',
				),
				'design' => array(
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'scroll_up_design',
				),
				'active' => 'general',
			),
		),
		'scroll_up_tabs_design' => array(
			'control_type' => 'kadence_tab_control',
			'section'      => 'scroll_up_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'kadence' ),
					'target' => 'scroll_up',
				),
				'design' => array(
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'scroll_up_design',
				),
				'active' => 'design',
			),
		),
		'scroll_up' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'scroll_up',
			'default'      => kadence()->default( 'scroll_up' ),
			'label'        => esc_html__( 'Enable Scroll To Top', 'kadence' ),
			'transport'    => 'refresh',
		),
		'scroll_up_icon' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'scroll_up',
			'priority'     => 10,
			'default'      => kadence()->default( 'scroll_up_icon' ),
			'label'        => esc_html__( 'Scroll Up Icon', 'kadence' ),
			'partial'      => array(
				'selector'            => '.scroll-up-wrap',
				'container_inclusive' => true,
				'render_callback'     => 'Kadence\scroll_up',
			),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				),
			),
			'input_attrs'  => array(
				'layout' => array(
					'arrow-up' => array(
						'icon' => 'arrowUp',
					),
					'arrow-up2' => array(
						'icon' => 'arrowUp2',
					),
					'chevron-up' => array(
						'icon' => 'chevronUp',
					),
					'chevron-up2' => array(
						'icon' => 'chevronUp2',
					),
				),
				'responsive' => false,
			),
		),
		'scroll_up_icon_size' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'scroll_up',
			'label'        => esc_html__( 'Icon Size', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'font-size',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'scroll_up_icon_size' ),
			'input_attrs'  => array(
				'min'        => array(
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
				),
				'max'        => array(
					'px'  => 100,
					'em'  => 12,
					'rem' => 12,
				),
				'step'       => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
				),
				'units'      => array( 'px', 'em', 'rem' ),
				'responsive' => true,
			),
		),
		'scroll_up_side' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'scroll_up',
			'default'      => kadence()->default( 'scroll_up_side' ),
			'label'        => esc_html__( 'Align', 'kadence' ),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '.scroll-up-wrap',
					'pattern'  => 'scroll-up-side-$',
					'key'      => '',
				),
			),
			'input_attrs'  => array(
				'layout' => array(
					'left' => array(
						'name'    => __( 'Left', 'kadence' ),
						'icon'    => '',
					),
					'right' => array(
						'name'    => __( 'Right', 'kadence' ),
						'icon'    => '',
					),
				),
				'responsive' => false,
			),
		),
		'scroll_up_side_offset' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'scroll_up',
			'label'        => esc_html__( 'Side Offset', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_side_offset' ),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader.scroll-up-side-right, #kt-scroll-up.scroll-up-side-right',
					'pattern'  => '$',
					'property' => 'right',
					'key'      => 'size',
				),
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader.scroll-up-side-left, #kt-scroll-up.scroll-up-side-left',
					'pattern'  => '$',
					'property' => 'left',
					'key'      => 'size',
				),
			),
			'input_attrs'  => array(
				'min'        => array(
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
					'vw'  => 0,
				),
				'max'        => array(
					'px'  => 600,
					'em'  => 20,
					'rem' => 20,
					'vw'  => 100,
				),
				'step'       => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'vw' => 1,
				),
				'units'      => array( 'px', 'em', 'rem', 'vw' ),
				'responsive' => true,
			),
		),
		'scroll_up_bottom_offset' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'scroll_up',
			'label'        => esc_html__( 'Bottom Offset', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_bottom_offset' ),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'pattern'  => '$',
					'property' => 'bottom',
					'key'      => 'size',
				),
			),
			'input_attrs'  => array(
				'min'        => array(
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
					'vh'  => 0,
				),
				'max'        => array(
					'px'  => 600,
					'em'  => 20,
					'rem' => 20,
					'vh'  => 100,
				),
				'step'       => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'vh' => 1,
				),
				'units'      => array( 'px', 'em', 'rem', 'vh' ),
				'responsive' => true,
			),
		),
		'scroll_up_visiblity' => array(
			'control_type' => 'kadence_check_icon_control',
			'section'      => 'scroll_up',
			'default'      => kadence()->default( 'scroll_up_visiblity' ),
			'label'        => esc_html__( 'Visibility', 'kadence' ),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				),
			),
			'partial'      => array(
				'selector'            => '.scroll-up-wrap',
				'container_inclusive' => true,
				'render_callback'     => 'Kadence\scroll_up',
			),
			'input_attrs'  => array(
				'options' => array(
					'desktop' => array(
						'name' => __( 'Desktop', 'kadence' ),
						'icon' => 'desktop',
					),
					'tablet' => array(
						'name' => __( 'Tablet', 'kadence' ),
						'icon' => 'tablet',
					),
					'mobile' => array(
						'name' => __( 'Mobile', 'kadence' ),
						'icon' => 'smartphone',
					),
				),
			),
		),
		'scroll_up_style' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'scroll_up_design',
			'default'      => kadence()->default( 'scroll_up_style' ),
			'label'        => esc_html__( 'Scroll Button Style', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'pattern'  => 'scroll-up-style-$',
					'key'      => '',
				),
			),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				),
			),
			'input_attrs'  => array(
				'layout' => array(
					'filled' => array(
						'name'    => __( 'Filled', 'kadence' ),
					),
					'outline' => array(
						'name'    => __( 'Outline', 'kadence' ),
					),
					'secondary' => array(
						'name'    => __( 'Secondary', 'kadence' ),
					),
				),
				'responsive' => false,
			),
		),
		'scroll_up_color' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'scroll_up_design',
			'label'        => esc_html__( 'Colors', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_color' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader:hover, #kt-scroll-up:hover',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'hover',
				),
			),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
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
		'scroll_up_background' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'scroll_up_design',
			'label'        => esc_html__( 'Background Colors', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_background' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader:hover, #kt-scroll-up:hover',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'hover',
				),
			),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'    => 'scroll_up_style',
					'operator'   => '=',
					'value'      => 'filled',
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
		'scroll_up_border_colors' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'scroll_up_design',
			'label'        => esc_html__( 'Border Colors', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_border' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader:hover, #kt-scroll-up:hover',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'hover',
				),
			),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
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
		'scroll_up_border' => array(
			'control_type' => 'kadence_border_control',
			'section'      => 'scroll_up_design',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'scroll_up_border' ),
			'live_method'     => array(
				array(
					'type'     => 'css_border',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'border',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				),
			),
			'input_attrs'  => array(
				'responsive' => false,
				'color'      => false,
			),
		),
		'scroll_up_radius' => array(
			'control_type' => 'kadence_measure_control',
			'section'      => 'scroll_up_design',
			'priority'     => 10,
			'default'      => kadence()->default( 'scroll_up_radius' ),
			'label'        => esc_html__( 'Border Radius', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'measure',
				),
			),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				),
			),
			'input_attrs'  => array(
				'responsive' => false,
			),
		),
		'scroll_up_padding' => array(
			'control_type' => 'kadence_measure_control',
			'section'      => 'scroll_up_design',
			'priority'     => 10,
			'default'      => kadence()->default( 'scroll_up_padding' ),
			'label'        => esc_html__( 'Scroll Button Padding', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#kt-scroll-up-reader, #kt-scroll-up',
					'property' => 'padding',
					'pattern'  => '$',
					'key'      => 'measure',
				),
			),
			'context'      => array(
				array(
					'setting'  => 'scroll_up',
					'operator' => '==',
					'value'    => true,
				),
			),
			'input_attrs'  => array(
				'responsive' => true,
			),
		),
	)
);
