<?php
/**
 * Tutor Course Layout Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'courses_layout_tabs'          => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'courses_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'courses_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'courses_layout_design',
			],
			'active'  => 'general',
		],
	],
	'courses_layout_tabs_design'   => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'courses_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'courses_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'courses_layout_design',
			],
			'active'  => 'design',
		],
	],
	'info_course_title'            => [
		'control_type' => 'kadence_title_control',
		'section'      => 'courses_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Course Title', 'kadence' ),
		'settings'     => false,
	],
	'info_course_title_design'     => [
		'control_type' => 'kadence_title_control',
		'section'      => 'courses_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Course Title', 'kadence' ),
		'settings'     => false,
	],
	'courses_title_layout'         => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'courses_layout',
		'label'        => esc_html__( 'Course Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'courses_title_layout' ),
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
	'courses_title_inner_layout'   => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'courses_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'courses_title_inner_layout' ),
		'label'        => esc_html__( 'Title Container Width', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'courses_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.courses-hero-section',
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
	// 'courses_title_align' => array(
	// 'control_type' => 'kadence_radio_icon_control',
	// 'section'      => 'courses_layout',
	// 'label'        => esc_html__( 'Course Title Align', 'kadence' ),
	// 'priority'     => 4,
	// 'default'      => kadence()->default( 'courses_title_align' ),
	// 'context'      => array(
	// array(
	// 'setting'    => 'courses_title_layout',
	// 'operator'   => '=',
	// 'value'      => 'above',
	// ),
	// ),
	// 'live_method'     => array(
	// array(
	// 'type'     => 'class',
	// 'selector' => '.courses-title',
	// 'pattern'  => array(
	// 'desktop' => 'title-align-$',
	// 'tablet'  => 'title-tablet-align-$',
	// 'mobile'  => 'title-mobile-align-$',
	// ),
	// 'key'      => '',
	// ),
	// ),
	// 'input_attrs'  => array(
	// 'layout' => array(
	// 'left' => array(
	// 'tooltip'  => __( 'Left Align Title', 'kadence' ),
	// 'dashicon' => 'editor-alignleft',
	// ),
	// 'center' => array(
	// 'tooltip'  => __( 'Center Align Title', 'kadence' ),
	// 'dashicon' => 'editor-aligncenter',
	// ),
	// 'right' => array(
	// 'tooltip'  => __( 'Right Align Title', 'kadence' ),
	// 'dashicon' => 'editor-alignright',
	// ),
	// ),
	// 'responsive' => true,
	// ),
	// ),
	'courses_title_height'         => [
		'control_type' => 'kadence_range_control',
		'section'      => 'courses_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Title Container Min Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'courses_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#inner-wrap .courses-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'courses_title_height' ),
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
	'courses_enroll_overlay'       => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'courses_layout',
		'default'      => kadence()->default( 'courses_enroll_overlay' ),
		'label'        => esc_html__( 'Move sidebar up into header?', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'courses_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-courses',
				'pattern'  => 'courses-sidebar-overlay-$',
				'key'      => '',
			],
		],
	],
	// 'courses_title_elements' => array(
	// 'control_type' => 'kadence_sorter_control',
	// 'section'      => 'courses_layout',
	// 'priority'     => 6,
	// 'default'      => kadence()->default( 'courses_title_elements' ),
	// 'label'        => esc_html__( 'Title Elements', 'kadence' ),
	// 'transport'    => 'refresh',
	// 'context'      => array(
	// array(
	// 'setting'    => 'courses_title_layout',
	// 'operator'   => '=',
	// 'value'      => 'above',
	// ),
	// ),
	// 'settings'     => array(
	// 'elements'   => 'courses_title_elements',
	// 'title'      => 'courses_title_element_title',
	// 'breadcrumb' => 'courses_title_element_breadcrumb',
	// ),
	// 'input_attrs'  => array(
	// 'group' => 'courses_title_element',
	// ),
	// ),
	'courses_title_font'           => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'courses_layout_design',
		'label'        => esc_html__( 'Course Title Font', 'kadence' ),
		'default'      => kadence()->default( 'course_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.tutor-single-course-lead-info h1.tutor-course-header-h1, .tutor-course-details-title.tutor-fs-4',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'             => 'courses_title_font',
			'headingInherit' => true,
		],
	],
	// 'courses_title_breadcrumb_color' => array(
	// 'control_type' => 'kadence_color_control',
	// 'section'      => 'courses_layout_design',
	// 'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
	// 'default'      => kadence()->default( 'courses_title_breadcrumb_color' ),
	// 'live_method'     => array(
	// array(
	// 'type'     => 'css',
	// 'selector' => '.course-title .kadence-breadcrumbs',
	// 'property' => 'color',
	// 'pattern'  => '$',
	// 'key'      => 'color',
	// ),
	// array(
	// 'type'     => 'css',
	// 'selector' => '.course-title .kadence-breadcrumbs a:hover',
	// 'property' => 'color',
	// 'pattern'  => '$',
	// 'key'      => 'hover',
	// ),
	// ),
	// 'input_attrs'  => array(
	// 'colors' => array(
	// 'color' => array(
	// 'tooltip' => __( 'Initial Color', 'kadence' ),
	// 'palette' => true,
	// ),
	// 'hover' => array(
	// 'tooltip' => __( 'Link Hover Color', 'kadence' ),
	// 'palette' => true,
	// ),
	// ),
	// ),
	// ),
	// 'courses_title_breadcrumb_font' => array(
	// 'control_type' => 'kadence_typography_control',
	// 'section'      => 'courses_layout_design',
	// 'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
	// 'default'      => kadence()->default( 'course_title_breadcrumb_font' ),
	// 'live_method'     => array(
	// array(
	// 'type'     => 'css_typography',
	// 'selector' => '.courses-title .kadence-breadcrumbs',
	// 'property' => 'font',
	// 'key'      => 'typography',
	// ),
	// ),
	// 'input_attrs'  => array(
	// 'id'      => 'course_title_breadcrumb_font',
	// 'options' => 'no-color',
	// ),
	// ),
	'courses_title_background'     => [
		'control_type' => 'kadence_background_control',
		'section'      => 'courses_layout_design',
		'label'        => esc_html__( 'Course Above Area Background', 'kadence' ),
		'default'      => kadence()->default( 'courses_title_background' ),
		'context'      => [
			[
				'setting'  => 'courses_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#inner-wrap .courses-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Course Title Background', 'kadence' ),
		],
	],
	'courses_title_featured_image' => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'courses_layout_design',
		'default'      => kadence()->default( 'courses_title_featured_image' ),
		'label'        => esc_html__( 'Use Featured Image for Background?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'courses_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
	],
	'courses_title_overlay_color'  => [
		'control_type' => 'kadence_color_control',
		'section'      => 'courses_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'courses_title_overlay_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.courses-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting'  => 'courses_title_layout',
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
	'courses_title_border'         => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'courses_layout_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'courses_title_border' ),
		'context'      => [
			[
				'setting'  => 'courses_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'settings'     => [
			'border_top'    => 'course_title_top_border',
			'border_bottom' => 'course_title_bottom_border',
		],
		'live_method'  => [
			'course_title_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => '.courses-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				],
			],
			'course_title_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => '.courses-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'info_courses_layout'          => [
		'control_type' => 'kadence_title_control',
		'section'      => 'courses_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Course Layout', 'kadence' ),
		'settings'     => false,
	],
	'info_courses_layout_design'   => [
		'control_type' => 'kadence_title_control',
		'section'      => 'courses_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Course Layout', 'kadence' ),
		'settings'     => false,
	],
	'courses_layout'               => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'courses_layout',
		'label'        => esc_html__( 'Course Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'courses_layout' ),
		'input_attrs'  => [
			'layout'     => [
				'left'  => [
					'tooltip' => __( 'Left Sidebar', 'kadence' ),
					'icon'    => 'leftsidebar',
				],
				'right' => [
					'tooltip' => __( 'Right Sidebar', 'kadence' ),
					'icon'    => 'rightsidebar',
				],
			],
			'responsive' => false,
			'class'      => 'kadence-two-col',
		],
	],
	'courses_content_style'        => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'courses_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'course_content_style' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-courses',
				'pattern'  => 'content-style-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'boxed'   => [
					'tooltip' => __( 'Boxed', 'kadence' ),
					'icon'    => 'boxed',
				],
				'unboxed' => [
					'tooltip' => __( 'Unboxed', 'kadence' ),
					'icon'    => 'narrow',
				],
			],
			'responsive' => false,
			'class'      => 'kadence-two-col',
		],
	],
	'courses_vertical_padding'     => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'courses_layout',
		'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'courses_vertical_padding' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-courses',
				'pattern'  => 'content-vertical-padding-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'show'   => [
					'name' => __( 'Enable', 'kadence' ),
				],
				'hide'   => [
					'name' => __( 'Disable', 'kadence' ),
				],
				'top'    => [
					'name' => __( 'Top Only', 'kadence' ),
				],
				'bottom' => [
					'name' => __( 'Bottom Only', 'kadence' ),
				],
			],
			'responsive' => false,
			'class'      => 'kadence-two-grid',
		],
	],
	// 'course_feature' => array(
	// 'control_type' => 'kadence_switch_control',
	// 'section'      => 'courses_layout',
	// 'priority'     => 20,
	// 'default'      => kadence()->default( 'course_feature' ),
	// 'label'        => esc_html__( 'Show Featured Image?', 'kadence' ),
	// 'transport'    => 'refresh',
	// ),
	// 'course_feature_position' => array(
	// 'control_type' => 'kadence_radio_icon_control',
	// 'section'      => 'courses_layout',
	// 'label'        => esc_html__( 'Featured Image Position', 'kadence' ),
	// 'priority'     => 20,
	// 'transport'    => 'refresh',
	// 'default'      => kadence()->default( 'course_feature_position' ),
	// 'context'      => array(
	// array(
	// 'setting'    => 'course_feature',
	// 'operator'   => '=',
	// 'value'      => true,
	// ),
	// ),
	// 'input_attrs'  => array(
	// 'layout' => array(
	// 'above' => array(
	// 'name' => __( 'Above', 'kadence' ),
	// ),
	// 'behind' => array(
	// 'name' => __( 'Behind', 'kadence' ),
	// ),
	// 'below' => array(
	// 'name' => __( 'Below', 'kadence' ),
	// ),
	// ),
	// 'responsive' => false,
	// ),
	// ),
	// 'course_feature_ratio' => array(
	// 'control_type' => 'kadence_radio_icon_control',
	// 'section'      => 'courses_layout',
	// 'label'        => esc_html__( 'Featured Image Ratio', 'kadence' ),
	// 'priority'     => 20,
	// 'default'      => kadence()->default( 'course_feature_ratio' ),
	// 'context'      => array(
	// array(
	// 'setting'    => 'course_feature',
	// 'operator'   => '=',
	// 'value'      => true,
	// ),
	// ),
	// 'live_method'     => array(
	// array(
	// 'type'     => 'class',
	// 'selector' => 'body.single-course .article-post-thumbnail',
	// 'pattern'  => 'kadence-thumbnail-ratio-$',
	// 'key'      => '',
	// ),
	// ),
	// 'input_attrs'  => array(
	// 'layout' => array(
	// 'inherit' => array(
	// 'name' => __( 'Inherit', 'kadence' ),
	// ),
	// '1-1' => array(
	// 'name' => __( '1:1', 'kadence' ),
	// ),
	// '3-4' => array(
	// 'name' => __( '3:4', 'kadence' ),
	// ),
	// '2-3' => array(
	// 'name' => __( '2:3', 'kadence' ),
	// ),
	// '9-16' => array(
	// 'name' => __( '9:16', 'kadence' ),
	// ),
	// '1-2' => array(
	// 'name' => __( '1:2', 'kadence' ),
	// ),
	// ),
	// 'responsive' => false,
	// 'class' => 'kadence-three-col-short',
	// ),
	// ),
	// 'info_course_syllabus_layout' => array(
	// 'control_type' => 'kadence_title_control',
	// 'section'      => 'courses_layout',
	// 'priority'     => 20,
	// 'label'        => esc_html__( 'Course Syllabus Layout', 'kadence' ),
	// 'settings'     => false,
	// ),
	// 'course_syllabus_columns' => array(
	// 'control_type' => 'kadence_radio_icon_control',
	// 'section'      => 'courses_layout',
	// 'priority'     => 20,
	// 'label'        => esc_html__( 'Course Syllabus Columns', 'kadence' ),
	// 'transport'    => 'refresh',
	// 'default'      => kadence()->default( 'course_syllabus_columns' ),
	// 'input_attrs'  => array(
	// 'layout' => array(
	// '1' => array(
	// 'name' => __( '1', 'kadence' ),
	// ),
	// '2' => array(
	// 'name' => __( '2', 'kadence' ),
	// ),
	// '3' => array(
	// 'name' => __( '3', 'kadence' ),
	// ),
	// ),
	// 'responsive' => false,
	// ),
	// ),
	// 'course_syllabus_lesson_style' => array(
	// 'control_type' => 'kadence_radio_icon_control',
	// 'section'      => 'courses_layout',
	// 'label'        => esc_html__( 'Course Lesson Style', 'kadence' ),
	// 'priority'     => 20,
	// 'transport'    => 'refresh',
	// 'default'      => kadence()->default( 'course_syllabus_lesson_style' ),
	// 'context'      => array(
	// array(
	// 'setting'    => 'course_syllabus_columns',
	// 'operator'   => '=',
	// 'value'      => '1',
	// ),
	// ),
	// 'input_attrs'  => array(
	// 'layout' => array(
	// 'standard' => array(
	// 'name' => __( 'Standard', 'kadence' ),
	// ),
	// 'tiles' => array(
	// 'name' => __( 'Two Column Tiles', 'kadence' ),
	// ),
	// 'center' => array(
	// 'name' => __( 'One Column Center', 'kadence' ),
	// ),
	// ),
	// 'responsive' => false,
	// 'class'      => 'kadence-tiny-text',
	// ),
	// ),
	// 'course_syllabus_thumbs' => array(
	// 'control_type' => 'kadence_switch_control',
	// 'section'      => 'courses_layout',
	// 'priority'     => 20,
	// 'default'      => kadence()->default( 'course_syllabus_thumbs' ),
	// 'label'        => esc_html__( 'Show Lesson Thumbnail in Syllabus?', 'kadence' ),
	// 'transport'    => 'refresh',
	// ),
	// 'course_syllabus_thumbs_ratio' => array(
	// 'control_type' => 'kadence_radio_icon_control',
	// 'section'      => 'courses_layout',
	// 'label'        => esc_html__( 'Lesson Thumbnail Ratio', 'kadence' ),
	// 'priority'     => 20,
	// 'default'      => kadence()->default( 'course_syllabus_thumbs_ratio' ),
	// 'context'      => array(
	// array(
	// 'setting'    => 'course_syllabus_thumbs',
	// 'operator'   => '=',
	// 'value'      => true,
	// ),
	// ),
	// 'live_method'     => array(
	// array(
	// 'type'     => 'class',
	// 'selector' => 'body.single-courses .llms-lesson-thumbnail',
	// 'pattern'  => 'kadence-thumbnail-ratio-$',
	// 'key'      => '',
	// ),
	// ),
	// 'input_attrs'  => array(
	// 'layout' => array(
	// 'inherit' => array(
	// 'name' => __( 'Inherit', 'kadence' ),
	// ),
	// '1-1' => array(
	// 'name' => __( '1:1', 'kadence' ),
	// ),
	// '3-4' => array(
	// 'name' => __( '3:4', 'kadence' ),
	// ),
	// '2-3' => array(
	// 'name' => __( '2:3', 'kadence' ),
	// ),
	// '9-16' => array(
	// 'name' => __( '9:16', 'kadence' ),
	// ),
	// '1-2' => array(
	// 'name' => __( '1:2', 'kadence' ),
	// ),
	// ),
	// 'responsive' => false,
	// 'class' => 'kadence-three-col-short',
	// ),
	// ),
	'course_background'            => [
		'control_type' => 'kadence_background_control',
		'section'      => 'courses_layout_design',
		'priority'     => 20,
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'course_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-courses',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Course Background', 'kadence' ),
		],
	],
	'course_content_background'    => [
		'control_type' => 'kadence_background_control',
		'section'      => 'courses_layout_design',
		'priority'     => 20,
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'course_content_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-courses .content-bg, body.single-courses:not(.content-style-unboxed) .tutor-price-preview-box, body.single-courses.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Course Content Background', 'kadence' ),
		],
	],
];

Theme_Customizer::add_settings( $settings );
