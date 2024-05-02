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
		// 'load_font_pairing' => array(
		// 	'control_type' => 'kadence_font_pairing',
		// 	'section'      => 'general_typography',
		// 	'label'        => esc_html__( 'Font Pairings', 'kadence' ),
		// 	'settings'     => false,
		// ),
		'base_font' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Base Font', 'kadence' ),
			'default'      => kadence()->default( 'base_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'body',
					'property' => 'font',
					'key'      => 'typography',
				),
				array(
					'type'     => 'css',
					'property' => '--global-body-font-family',
					'selector' => 'body',
					'pattern'  => '$',
					'key'      => 'family',
				),
			),
			'input_attrs'  => array(
				'id'         => 'base_font',
				'canInherit' => false,
			),
		),
		'load_base_italic' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_typography',
			'default'      => kadence()->default( 'load_base_italic' ),
			'label'        => esc_html__( 'Load Italics Font Styles', 'kadence' ),
			'context'      => array(
				array(
					'setting' => 'base_font',
					'operator'   => 'load_italic',
					'value'   => 'true',
				),
			),
		),
		'info_heading' => array(
			'control_type' => 'kadence_title_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Headings', 'kadence' ),
			'settings'     => false,
		),
		'heading_font' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Heading Font Family', 'kadence' ),
			'default'      => kadence()->default( 'heading_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h1,h2,h3,h4,h5,h6',
					'property' => 'font',
					'key'      => 'family',
				),
				array(
					'type'     => 'css',
					'property' => '--global-heading-font-family',
					'selector' => 'body',
					'pattern'  => '$',
					'key'      => 'family',
				),
			),
			'input_attrs'  => array(
				'id'      => 'heading_font',
				'options' => 'family',
			),
		),
		'h1_font' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H1 Font', 'kadence' ),
			'default'      => kadence()->default( 'h1_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h1',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h1_font',
				'headingInherit' => true,
			),
		),
		'h2_font' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H2 Font', 'kadence' ),
			'default'      => kadence()->default( 'h2_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h2',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h2_font',
				'headingInherit' => true,
			),
		),
		'h3_font' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H3 Font', 'kadence' ),
			'default'      => kadence()->default( 'h3_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h3',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h3_font',
				'headingInherit' => true,
			),
		),
		'h4_font' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H4 Font', 'kadence' ),
			'default'      => kadence()->default( 'h4_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h4',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h4_font',
				'headingInherit' => true,
			),
		),
		'h5_font' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H5 Font', 'kadence' ),
			'default'      => kadence()->default( 'h5_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h5',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h5_font',
				'headingInherit' => true,
			),
		),
		'h6_font' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H6 Font', 'kadence' ),
			'default'      => kadence()->default( 'h6_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h6',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h6_font',
				'headingInherit' => true,
			),
		),
		'info_above_title_heading' => array(
			'control_type' => 'kadence_title_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Title Above Content', 'kadence' ),
			'settings'     => false,
		),
		'title_above_font' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H1 Title', 'kadence' ),
			'default'      => kadence()->default( 'title_above_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.entry-hero h1',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'title_above_font',
				'headingInherit' => true,
			),
		),
		'title_above_breadcrumb_font' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Breadcrumbs', 'kadence' ),
			'default'      => kadence()->default( 'title_above_breadcrumb_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.entry-hero .kadence-breadcrumbs',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'      => 'title_above_breadcrumb_font',
			),
		),
		'font_rendering' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_typography',
			'transport'    => 'refresh',
			'default'      => kadence()->default( 'font_rendering' ),
			'label'        => esc_html__( 'Enable Font Smoothing', 'kadence' ),
		),
		'google_subsets' => array(
			'control_type' => 'kadence_check_icon_control',
			'section'      => 'general_typography',
			'sanitize'     => 'kadence_sanitize_google_subsets',
			'priority'     => 20,
			'default'      => array(),
			'label'        => esc_html__( 'Google Font Subsets', 'kadence' ),
			'input_attrs'  => array(
				'options' => array(
					'latin-ext' => array(
						'name' => __( 'Latin Extended', 'kadence' ),
					),
					'cyrillic' => array(
						'name' => __( 'Cyrillic', 'kadence' ),
					),
					'cyrillic-ext' => array(
						'name' => __( 'Cyrillic Extended', 'kadence' ),
					),
					'greek' => array(
						'name' => __( 'Greek', 'kadence' ),
					),
					'greek-ext' => array(
						'name' => __( 'Greek Extended', 'kadence' ),
					),
					'vietnamese' => array(
						'name' => __( 'Vietnamese', 'kadence' ),
					),
					'arabic' => array(
						'name' => __( 'Arabic', 'kadence' ),
					),
					'khmer' => array(
						'name' => __( 'Khmer', 'kadence' ),
					),
					'chinese' => array(
						'name' => __( 'Chinese', 'kadence' ),
					),
					'chinese-simplified' => array(
						'name' => __( 'Chinese Simplified', 'kadence' ),
					),
					'tamil' => array(
						'name' => __( 'Tamil', 'kadence' ),
					),
					'bengali' => array(
						'name' => __( 'Bengali', 'kadence' ),
					),
					'devanagari' => array(
						'name' => __( 'Devanagari', 'kadence' ),
					),
					'hebrew' => array(
						'name' => __( 'Hebrew', 'kadence' ),
					),
					'korean' => array(
						'name' => __( 'Korean', 'kadence' ),
					),
					'thai' => array(
						'name' => __( 'Thai', 'kadence' ),
					),
					'telugu' => array(
						'name' => __( 'Telugu', 'kadence' ),
					),
				),
			),
		),
	)
);
