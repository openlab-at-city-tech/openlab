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
		// 'load_font_pairing' => array(
		// 'control_type' => 'kadence_font_pairing',
		// 'section'      => 'general_typography',
		// 'label'        => esc_html__( 'Font Pairings', 'kadence' ),
		// 'settings'     => false,
		// ),
		'base_font'                   => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Base Font', 'kadence' ),
			'default'      => kadence()->default( 'base_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => 'body',
					'property' => 'font',
					'key'      => 'typography',
				],
				[
					'id'       => 'base_font_family',
					'type'     => 'css',
					'property' => '--global-body-font-family',
					'selector' => 'body',
					'pattern'  => '$',
					'key'      => 'family',
				],
			],
			'input_attrs'  => [
				'id'         => 'base_font',
				'canInherit' => false,
			],
		],
		'load_base_italic'            => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_typography',
			'default'      => kadence()->default( 'load_base_italic' ),
			'label'        => esc_html__( 'Load Italics Font Styles', 'kadence' ),
			'context'      => [
				[
					'setting'  => 'base_font',
					'operator' => 'load_italic',
					'value'    => 'true',
				],
			],
		],
		'info_heading'                => [
			'control_type' => 'kadence_title_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Headings', 'kadence' ),
			'settings'     => false,
		],
		'heading_font'                => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Heading Font Family', 'kadence' ),
			'default'      => kadence()->default( 'heading_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => 'h1,h2,h3,h4,h5,h6',
					'property' => 'font',
					'key'      => 'family',
				],
				[
					'type'     => 'css',
					'property' => '--global-heading-font-family',
					'selector' => 'body',
					'pattern'  => '$',
					'key'      => 'family',
				],
			],
			'input_attrs'  => [
				'id'      => 'heading_font',
				'options' => 'family',
			],
		],
		'h1_font'                     => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H1 Font', 'kadence' ),
			'default'      => kadence()->default( 'h1_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => 'h1',
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'input_attrs'  => [
				'id'             => 'h1_font',
				'headingInherit' => true,
			],
		],
		'h2_font'                     => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H2 Font', 'kadence' ),
			'default'      => kadence()->default( 'h2_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => 'h2',
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'input_attrs'  => [
				'id'             => 'h2_font',
				'headingInherit' => true,
			],
		],
		'h3_font'                     => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H3 Font', 'kadence' ),
			'default'      => kadence()->default( 'h3_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => 'h3',
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'input_attrs'  => [
				'id'             => 'h3_font',
				'headingInherit' => true,
			],
		],
		'h4_font'                     => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H4 Font', 'kadence' ),
			'default'      => kadence()->default( 'h4_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => 'h4',
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'input_attrs'  => [
				'id'             => 'h4_font',
				'headingInherit' => true,
			],
		],
		'h5_font'                     => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H5 Font', 'kadence' ),
			'default'      => kadence()->default( 'h5_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => 'h5',
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'input_attrs'  => [
				'id'             => 'h5_font',
				'headingInherit' => true,
			],
		],
		'h6_font'                     => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H6 Font', 'kadence' ),
			'default'      => kadence()->default( 'h6_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => 'h6',
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'input_attrs'  => [
				'id'             => 'h6_font',
				'headingInherit' => true,
			],
		],
		'info_above_title_heading'    => [
			'control_type' => 'kadence_title_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Title Above Content', 'kadence' ),
			'settings'     => false,
		],
		'title_above_font'            => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H1 Title', 'kadence' ),
			'default'      => kadence()->default( 'title_above_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.entry-hero h1',
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'input_attrs'  => [
				'id'             => 'title_above_font',
				'headingInherit' => true,
			],
		],
		'title_above_breadcrumb_font' => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Breadcrumbs', 'kadence' ),
			'default'      => kadence()->default( 'title_above_breadcrumb_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.entry-hero .kadence-breadcrumbs',
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'input_attrs'  => [
				'id' => 'title_above_breadcrumb_font',
			],
		],
		'font_rendering'              => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_typography',
			'transport'    => 'refresh',
			'default'      => kadence()->default( 'font_rendering' ),
			'label'        => esc_html__( 'Enable Font Smoothing', 'kadence' ),
		],
		'google_subsets'              => [
			'control_type' => 'kadence_check_icon_control',
			'section'      => 'general_typography',
			'sanitize'     => 'kadence_sanitize_google_subsets',
			'priority'     => 20,
			'default'      => [],
			'label'        => esc_html__( 'Google Font Subsets', 'kadence' ),
			'input_attrs'  => [
				'options' => [
					'latin-ext'          => [
						'name' => __( 'Latin Extended', 'kadence' ),
					],
					'cyrillic'           => [
						'name' => __( 'Cyrillic', 'kadence' ),
					],
					'cyrillic-ext'       => [
						'name' => __( 'Cyrillic Extended', 'kadence' ),
					],
					'greek'              => [
						'name' => __( 'Greek', 'kadence' ),
					],
					'greek-ext'          => [
						'name' => __( 'Greek Extended', 'kadence' ),
					],
					'vietnamese'         => [
						'name' => __( 'Vietnamese', 'kadence' ),
					],
					'arabic'             => [
						'name' => __( 'Arabic', 'kadence' ),
					],
					'khmer'              => [
						'name' => __( 'Khmer', 'kadence' ),
					],
					'chinese'            => [
						'name' => __( 'Chinese', 'kadence' ),
					],
					'chinese-simplified' => [
						'name' => __( 'Chinese Simplified', 'kadence' ),
					],
					'tamil'              => [
						'name' => __( 'Tamil', 'kadence' ),
					],
					'bengali'            => [
						'name' => __( 'Bengali', 'kadence' ),
					],
					'devanagari'         => [
						'name' => __( 'Devanagari', 'kadence' ),
					],
					'hebrew'             => [
						'name' => __( 'Hebrew', 'kadence' ),
					],
					'korean'             => [
						'name' => __( 'Korean', 'kadence' ),
					],
					'thai'               => [
						'name' => __( 'Thai', 'kadence' ),
					],
					'telugu'             => [
						'name' => __( 'Telugu', 'kadence' ),
					],
				],
			],
		],
	]
);
