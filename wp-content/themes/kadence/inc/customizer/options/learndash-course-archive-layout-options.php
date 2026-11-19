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
	'sfwd-courses_archive_tabs'                    => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'sfwd_courses_archive_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'sfwd_courses_archive_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'sfwd_courses_archive_layout_design',
			],
			'active'  => 'general',
		],
	],
	'sfwd-courses_archive_tabs_design'             => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'sfwd_courses_archive_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'sfwd_courses_archive_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'sfwd_courses_archive_layout_design',
			],
			'active'  => 'design',
		],
	],
	'info_sfwd-courses_archive_title'              => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_courses_archive_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Archive Title', 'kadence' ),
		'settings'     => false,
	],
	'info_sfwd-courses_archive_title_design'       => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_courses_archive_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Archive Title', 'kadence' ),
		'settings'     => false,
	],
	'sfwd-courses_archive_title'                   => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_courses_archive_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'sfwd-courses_archive_title' ),
		'label'        => esc_html__( 'Show Archive Title?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'sfwd-courses_archive_title_layout'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_archive_layout',
		'label'        => esc_html__( 'Archive Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-courses_archive_title_layout' ),
		'context'      => [
			[
				'setting'  => 'sfwd-courses_archive_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'normal' => [
					'tooltip' => __( 'In Content', 'kadence' ),
					'icon'    => 'incontent',
				],
				'above'  => [
					'tooltip' => __( 'Above Content', 'kadence' ),
					'icon'    => 'abovecontent',
				],
			],
			'responsive' => false,
			'class'      => 'kadence-two-col',
		],
	],
	'sfwd-courses_archive_title_inner_layout'      => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_archive_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-courses_archive_title_inner_layout' ),
		'label'        => esc_html__( 'Container Width', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'sfwd-courses_archive_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-courses_archive_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.sfwd-courses-archive-hero-section',
				'pattern'  => 'entry-hero-layout-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'standard'  => [
					'tooltip' => __( 'Background Fullwidth, Content Contained', 'kadence' ),
					'name'    => __( 'Standard', 'kadence' ),
					'icon'    => '',
				],
				'fullwidth' => [
					'tooltip' => __( 'Background & Content Fullwidth', 'kadence' ),
					'name'    => __( 'Fullwidth', 'kadence' ),
					'icon'    => '',
				],
				'contained' => [
					'tooltip' => __( 'Background & Content Contained', 'kadence' ),
					'name'    => __( 'Contained', 'kadence' ),
					'icon'    => '',
				],
			],
			'responsive' => false,
		],
	],
	'sfwd-courses_archive_title_align'             => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_archive_layout',
		'label'        => esc_html__( 'Course Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-courses_archive_title_align' ),
		'context'      => [
			[
				'setting'  => 'sfwd-courses_archive_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.sfwd-courses-archive-title',
				'pattern'  => [
					'desktop' => 'title-align-$',
					'tablet'  => 'title-tablet-align-$',
					'mobile'  => 'title-mobile-align-$',
				],
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'left'   => [
					'tooltip'  => __( 'Left Align Title', 'kadence' ),
					'dashicon' => 'editor-alignleft',
				],
				'center' => [
					'tooltip'  => __( 'Center Align Title', 'kadence' ),
					'dashicon' => 'editor-aligncenter',
				],
				'right'  => [
					'tooltip'  => __( 'Right Align Title', 'kadence' ),
					'dashicon' => 'editor-alignright',
				],
			],
			'responsive' => true,
		],
	],
	'sfwd-courses_archive_title_height'            => [
		'control_type' => 'kadence_range_control',
		'section'      => 'sfwd_courses_archive_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Container Min Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'sfwd-courses_archive_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-courses_archive_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#inner-wrap .sfwd-courses-archive-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'sfwd-courses_archive_title_height' ),
		'input_attrs'  => [
			'min'   => [
				'px'  => 10,
				'em'  => 1,
				'rem' => 1,
				'vh'  => 2,
			],
			'max'   => [
				'px'  => 800,
				'em'  => 12,
				'rem' => 12,
				'vh'  => 100,
			],
			'step'  => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vh'  => 1,
			],
			'units' => [ 'px', 'em', 'rem', 'vh' ],
		],
	],
	'sfwd-courses_archive_title_elements'          => [
		'control_type' => 'kadence_sorter_control',
		'section'      => 'sfwd_courses_archive_layout',
		'priority'     => 6,
		'default'      => kadence()->default( 'sfwd-courses_archive_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'sfwd-courses_archive_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'settings'     => [
			'elements'    => 'sfwd-courses_archive_title_elements',
			'title'       => 'sfwd-courses_archive_title_element_title',
			'breadcrumb'  => 'sfwd-courses_archive_title_element_breadcrumb',
			'description' => 'sfwd-courses_archive_title_element_description',
		],
	],
	'sfwd-courses_archive_title_color'             => [
		'control_type' => 'kadence_color_control',
		'section'      => 'sfwd_courses_archive_layout_design',
		'label'        => esc_html__( 'Title Color', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_archive_title_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.sfwd-courses-archive-title h1',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color' => [
					'tooltip' => __( 'Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'sfwd-courses_archive_title_description_color' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'sfwd_courses_archive_layout_design',
		'label'        => esc_html__( 'Description Colors', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_archive_title_description_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.sfwd-courses-archive-title .archive-description',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.sfwd-courses-archive-title .archive-description a:hover',
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
					'tooltip' => __( 'Link Hover Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'sfwd-courses_archive_title_breadcrumb_color'  => [
		'control_type' => 'kadence_color_control',
		'section'      => 'sfwd_courses_archive_layout_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_archive_title_breadcrumb_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.sfwd-courses-archive-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.sfwd-courses-archive-title .kadence-breadcrumbs a:hover',
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
					'tooltip' => __( 'Link Hover Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'sfwd-courses_archive_title_background'        => [
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_courses_archive_layout_design',
		'label'        => esc_html__( 'Archive Title Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_archive_title_background' ),
		'context'      => [
			[
				'setting'  => 'sfwd-courses_archive_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-courses_archive_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#inner-wrap .sfwd-courses-archive-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Course Archive Title Background', 'kadence' ),
		],
	],
	'sfwd-courses_archive_title_overlay_color'     => [
		'control_type' => 'kadence_color_control',
		'section'      => 'sfwd_courses_archive_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_archive_title_overlay_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.sfwd-courses-archive-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting'  => 'sfwd-courses_archive_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-courses_archive_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'input_attrs'  => [
			'colors'        => [
				'color' => [
					'tooltip' => __( 'Overlay Color', 'kadence' ),
					'palette' => true,
				],
			],
			'allowGradient' => true,
		],
	],
	'sfwd-courses_archive_title_border'            => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'sfwd_courses_archive_layout_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_archive_title_border' ),
		'context'      => [
			[
				'setting'  => 'sfwd-courses_archive_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-courses_archive_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'settings'     => [
			'border_top'    => 'sfwd-courses_archive_title_top_border',
			'border_bottom' => 'sfwd-courses_archive_title_bottom_border',
		],
		'live_method'  => [
			'sfwd-courses_archive_title_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => '.sfwd-courses-archive-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				],
			],
			'sfwd-courses_archive_title_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => '.sfwd-courses-archive-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'info_sfwd-courses_archive_layout'             => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_courses_archive_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'settings'     => false,
	],
	'info_sfwd-courses_archive_layout_design'      => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_courses_archive_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'settings'     => false,
	],
	'sfwd-courses_archive_layout'                  => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_archive_layout',
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-courses_archive_layout' ),
		'input_attrs'  => [
			'layout'     => [
				'normal'    => [
					'tooltip' => __( 'Normal', 'kadence' ),
					'icon'    => 'normal',
				],
				'narrow'    => [
					'tooltip' => __( 'Narrow', 'kadence' ),
					'icon'    => 'narrow',
				],
				'fullwidth' => [
					'tooltip' => __( 'Fullwidth', 'kadence' ),
					'icon'    => 'fullwidth',
				],
				'left'      => [
					'tooltip' => __( 'Left Sidebar', 'kadence' ),
					'icon'    => 'leftsidebar',
				],
				'right'     => [
					'tooltip' => __( 'Right Sidebar', 'kadence' ),
					'icon'    => 'rightsidebar',
				],
			],
			'class'      => 'kadence-three-col',
			'responsive' => false,
		],
	],
	'sfwd-courses_archive_content_style'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_archive_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-courses_archive_content_style' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.post-type-archive-sfwd-courses',
				'pattern'  => 'content-style-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'boxed'   => [
					'tooltip' => __( 'Boxed', 'kadence' ),
					'icon'    => 'gridBoxed',
				],
				'unboxed' => [
					'tooltip' => __( 'Unboxed', 'kadence' ),
					'icon'    => 'gridUnboxed',
				],
			],
			'responsive' => false,
			'class'      => 'kadence-two-col',
		],
	],
	'sfwd-courses_archive_columns'                 => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_archive_layout',
		'priority'     => 20,
		'label'        => esc_html__( 'Course Archive Columns', 'kadence' ),
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'sfwd-courses_archive_columns' ),
		'input_attrs'  => [
			'layout'     => [
				'2' => [
					'name' => __( '2', 'kadence' ),
				],
				'3' => [
					'name' => __( '3', 'kadence' ),
				],
				'4' => [
					'name' => __( '4', 'kadence' ),
				],
			],
			'responsive' => false,
		],
	],
	'sfwd-courses_archive_background'              => [
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_courses_archive_layout_design',
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_archive_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.post-type-archive-sfwd-courses',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Course Archive Background', 'kadence' ),
		],
	],
	'sfwd-courses_archive_content_background'      => [
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_courses_archive_layout_design',
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_archive_content_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.post-type-archive-sfwd-courses .content-bg, body.post-type-archive-sfwd-courses.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Archive Content Background', 'kadence' ),
		],
	],
];

Theme_Customizer::add_settings( $settings );
