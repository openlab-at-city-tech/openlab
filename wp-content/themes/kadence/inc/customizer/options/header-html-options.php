<?php
/**
 * Header Main Row Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = array(
	'header_html_tabs' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'header_html',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'header_html',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'header_html_design',
			),
			'active' => 'general',
		),
	),
	'header_html_tabs_design' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'header_html_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'header_html',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'header_html_design',
			),
			'active' => 'design',
		),
	),
	'header_html_content' => array(
		'control_type' => 'kadence_editor_control',
		'section'      => 'header_html',
		'sanitize'     => 'wp_kses_post',
		'priority'     => 4,
		'default'      => kadence()->default( 'header_html_content' ),
		'partial'      => array(
			'selector'            => '.header-html',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\header_html',
		),
		'input_attrs'  => array(
			'id' => 'header_html',
		),
	),
	'header_html_wpautop' => array(
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'header_html',
		'default'      => kadence()->default( 'header_html_wpautop' ),
		'label'        => esc_html__( 'Automatically Add Paragraphs', 'kadence' ),
		'partial'      => array(
			'selector'            => '.header-html',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\header_html',
		),
	),
	'header_html_typography' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'header_html_design',
		'label'        => esc_html__( 'Font', 'kadence' ),
		'default'      => kadence()->default( 'header_html_typography' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '#main-header .header-html',
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
			'id' => 'header_html_typography',
		),
	),
	'header_html_link_style' => array(
		'control_type' => 'kadence_select_control',
		'section'      => 'header_html_design',
		'default'      => kadence()->default( 'header_html_link_style' ),
		'label'        => esc_html__( 'Link Style', 'kadence' ),
		'input_attrs'  => array(
			'options' => array(
				'normal' => array(
					'name' => __( 'Underline', 'kadence' ),
				),
				'plain' => array(
					'name' => __( 'No Underline', 'kadence' ),
				),
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '#main-header .header-html',
				'pattern'  => 'inner-link-style-$',
				'key'      => '',
			),
		),
	),
	'header_html_link_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'header_html_design',
		'label'        => esc_html__( 'Link Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_html_link_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#main-header .header-html a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '#main-header .header-html a:hover',
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
	'header_html_margin' => array(
		'control_type' => 'kadence_measure_control',
		'section'      => 'header_html_design',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_html_margin' ),
		'label'        => esc_html__( 'Margin', 'kadence' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#main-header .header-html',
				'property' => 'margin',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'input_attrs'  => array(
			'responsive' => false,
		),
	),
);

Theme_Customizer::add_settings( $settings );

