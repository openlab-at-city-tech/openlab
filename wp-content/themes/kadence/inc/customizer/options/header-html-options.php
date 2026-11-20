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
	'header_html_tabs'        => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'header_html',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'header_html',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'header_html_design',
			],
			'active'  => 'general',
		],
	],
	'header_html_tabs_design' => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'header_html_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'header_html',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'header_html_design',
			],
			'active'  => 'design',
		],
	],
	'header_html_content'     => [
		'control_type' => 'kadence_editor_control',
		'section'      => 'header_html',
		'sanitize'     => 'wp_kses_post',
		'priority'     => 4,
		'default'      => kadence()->default( 'header_html_content' ),
		'partial'      => [
			'selector'            => '.header-html',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\header_html',
		],
		'input_attrs'  => [
			'id' => 'header_html',
		],
	],
	'header_html_wpautop'     => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'header_html',
		'default'      => kadence()->default( 'header_html_wpautop' ),
		'label'        => esc_html__( 'Automatically Add Paragraphs', 'kadence' ),
		'partial'      => [
			'selector'            => '.header-html',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\header_html',
		],
	],
	'header_html_typography'  => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'header_html_design',
		'label'        => esc_html__( 'Font', 'kadence' ),
		'default'      => kadence()->default( 'header_html_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '#main-header .header-html',
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
			'id' => 'header_html_typography',
		],
	],
	'header_html_link_style'  => [
		'control_type' => 'kadence_select_control',
		'section'      => 'header_html_design',
		'default'      => kadence()->default( 'header_html_link_style' ),
		'label'        => esc_html__( 'Link Style', 'kadence' ),
		'input_attrs'  => [
			'options' => [
				'normal' => [
					'name' => __( 'Underline', 'kadence' ),
				],
				'plain'  => [
					'name' => __( 'No Underline', 'kadence' ),
				],
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '#main-header .header-html',
				'pattern'  => 'inner-link-style-$',
				'key'      => '',
			],
		],
	],
	'header_html_link_color'  => [
		'control_type' => 'kadence_color_control',
		'section'      => 'header_html_design',
		'label'        => esc_html__( 'Link Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_html_link_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#main-header .header-html a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '#main-header .header-html a:hover',
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
	'header_html_margin'      => [
		'control_type' => 'kadence_measure_control',
		'section'      => 'header_html_design',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_html_margin' ),
		'label'        => esc_html__( 'Margin', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#main-header .header-html',
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
