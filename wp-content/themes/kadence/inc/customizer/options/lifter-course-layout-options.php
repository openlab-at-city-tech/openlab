<?php
/**
 * Product Layout Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'course_layout_tabs'            => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'course_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'course_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'course_layout_design',
			],
			'active'  => 'general',
		],
	],
	'course_layout_tabs_design'     => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'course_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'course_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'course_layout_design',
			],
			'active'  => 'design',
		],
	],
	'info_course_title'             => [
		'control_type' => 'kadence_title_control',
		'section'      => 'course_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Course Title', 'kadence' ),
		'settings'     => false,
	],
	'info_course_title_design'      => [
		'control_type' => 'kadence_title_control',
		'section'      => 'course_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Course Title', 'kadence' ),
		'settings'     => false,
	],
	'course_title'                  => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'course_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'course_title' ),
		'label'        => esc_html__( 'Show Course Title?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'course_title_layout'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'course_layout',
		'label'        => esc_html__( 'Course Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'course_title_layout' ),
		'context'      => [
			[
				'setting'  => 'course_title',
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
	'course_title_inner_layout'     => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'course_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'course_title_inner_layout' ),
		'label'        => esc_html__( 'Title Container Width', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'course_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'course_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.course-hero-section',
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
	'course_title_align'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'course_layout',
		'label'        => esc_html__( 'Course Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'course_title_align' ),
		'context'      => [
			[
				'setting'  => 'course_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.course-title',
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
	'course_title_height'           => [
		'control_type' => 'kadence_range_control',
		'section'      => 'course_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Title Container Min Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'course_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'course_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#inner-wrap .course-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'course_title_height' ),
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
	'course_title_elements'         => [
		'control_type' => 'kadence_sorter_control',
		'section'      => 'course_layout',
		'priority'     => 6,
		'default'      => kadence()->default( 'course_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => [
			'elements'   => 'course_title_elements',
			'title'      => 'course_title_element_title',
			'breadcrumb' => 'course_title_element_breadcrumb',
		],
		'context'      => [
			[
				'setting'  => 'course_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'group' => 'course_title_element',
		],
	],
	'course_title_font'             => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'course_layout_design',
		'label'        => esc_html__( 'Course Title Font', 'kadence' ),
		'default'      => kadence()->default( 'course_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.course-title h1',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting'  => 'course_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'id'             => 'course_title_font',
			'headingInherit' => true,
		],
	],
	'course_title_breadcrumb_color' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'course_layout_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'course_title_breadcrumb_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.course-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.course-title .kadence-breadcrumbs a:hover',
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
	'course_title_breadcrumb_font'  => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'course_layout_design',
		'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
		'default'      => kadence()->default( 'course_title_breadcrumb_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.course-title .kadence-breadcrumbs',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'course_title_breadcrumb_font',
			'options' => 'no-color',
		],
	],
	'course_title_background'       => [
		'control_type' => 'kadence_background_control',
		'section'      => 'course_layout_design',
		'label'        => esc_html__( 'Course Above Area Background', 'kadence' ),
		'default'      => kadence()->default( 'course_title_background' ),
		'context'      => [
			[
				'setting'  => 'course_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'course_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#inner-wrap .course-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Course Title Background', 'kadence' ),
		],
	],
	'course_title_featured_image'   => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'course_layout_design',
		'default'      => kadence()->default( 'course_title_featured_image' ),
		'label'        => esc_html__( 'Use Featured Image for Background?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'course_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'course_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
	],
	'course_title_overlay_color'    => [
		'control_type' => 'kadence_color_control',
		'section'      => 'course_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'course_title_overlay_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.course-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting'  => 'course_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'course_title_layout',
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
	'course_title_border'           => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'course_layout_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'course_title_border' ),
		'context'      => [
			[
				'setting'  => 'course_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'course_title_layout',
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
					'selector' => '.course-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				],
			],
			'course_title_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => '.course-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'info_course_layout'            => [
		'control_type' => 'kadence_title_control',
		'section'      => 'course_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Course Layout', 'kadence' ),
		'settings'     => false,
	],
	'info_course_layout_design'     => [
		'control_type' => 'kadence_title_control',
		'section'      => 'course_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Course Layout', 'kadence' ),
		'settings'     => false,
	],
	'course_layout'                 => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'course_layout',
		'label'        => esc_html__( 'Course Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'course_layout' ),
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
			'responsive' => false,
			'class'      => 'kadence-three-col',
		],
	],
	'course_sidebar_id'             => [
		'control_type' => 'kadence_select_control',
		'section'      => 'course_layout',
		'label'        => esc_html__( 'Course Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'course_sidebar_id' ),
		'input_attrs'  => [
			'options' => kadence()->sidebar_options(),
		],
	],
	'course_content_style'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'course_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'course_content_style' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-course',
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
	'course_vertical_padding'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'course_layout',
		'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'course_vertical_padding' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-course',
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
	'course_feature'                => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'course_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'course_feature' ),
		'label'        => esc_html__( 'Show Featured Image?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'course_feature_position'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'course_layout',
		'label'        => esc_html__( 'Featured Image Position', 'kadence' ),
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'course_feature_position' ),
		'context'      => [
			[
				'setting'  => 'course_feature',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'above'  => [
					'name' => __( 'Above', 'kadence' ),
				],
				'behind' => [
					'name' => __( 'Behind', 'kadence' ),
				],
				'below'  => [
					'name' => __( 'Below', 'kadence' ),
				],
			],
			'responsive' => false,
		],
	],
	'course_feature_ratio'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'course_layout',
		'label'        => esc_html__( 'Featured Image Ratio', 'kadence' ),
		'priority'     => 20,
		'default'      => kadence()->default( 'course_feature_ratio' ),
		'context'      => [
			[
				'setting'  => 'course_feature',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-course .article-post-thumbnail',
				'pattern'  => 'kadence-thumbnail-ratio-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'inherit' => [
					'name' => __( 'Inherit', 'kadence' ),
				],
				'1-1'     => [
					'name' => __( '1:1', 'kadence' ),
				],
				'3-4'     => [
					'name' => __( '4:3', 'kadence' ),
				],
				'2-3'     => [
					'name' => __( '3:2', 'kadence' ),
				],
				'9-16'    => [
					'name' => __( '16:9', 'kadence' ),
				],
				'1-2'     => [
					'name' => __( '2:1', 'kadence' ),
				],
			],
			'responsive' => false,
			'class'      => 'kadence-three-col-short',
		],
	],
	'info_course_syllabus_layout'   => [
		'control_type' => 'kadence_title_control',
		'section'      => 'course_layout',
		'priority'     => 20,
		'label'        => esc_html__( 'Course Syllabus Layout', 'kadence' ),
		'settings'     => false,
	],
	'course_syllabus_columns'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'course_layout',
		'priority'     => 20,
		'label'        => esc_html__( 'Course Syllabus Columns', 'kadence' ),
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'course_syllabus_columns' ),
		'input_attrs'  => [
			'layout'     => [
				'1' => [
					'name' => __( '1', 'kadence' ),
				],
				'2' => [
					'name' => __( '2', 'kadence' ),
				],
				'3' => [
					'name' => __( '3', 'kadence' ),
				],
			],
			'responsive' => false,
		],
	],
	'course_syllabus_lesson_style'  => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'course_layout',
		'label'        => esc_html__( 'Course Lesson Style', 'kadence' ),
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'course_syllabus_lesson_style' ),
		'context'      => [
			[
				'setting'  => 'course_syllabus_columns',
				'operator' => '=',
				'value'    => '1',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'standard' => [
					'name' => __( 'Standard', 'kadence' ),
				],
				'tiles'    => [
					'name' => __( 'Two Column Tiles', 'kadence' ),
				],
				'center'   => [
					'name' => __( 'One Column Center', 'kadence' ),
				],
			],
			'responsive' => false,
			'class'      => 'kadence-tiny-text',
		],
	],
	'course_syllabus_thumbs'        => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'course_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'course_syllabus_thumbs' ),
		'label'        => esc_html__( 'Show Lesson Thumbnail in Syllabus?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'course_syllabus_thumbs_ratio'  => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'course_layout',
		'label'        => esc_html__( 'Lesson Thumbnail Ratio', 'kadence' ),
		'priority'     => 20,
		'default'      => kadence()->default( 'course_syllabus_thumbs_ratio' ),
		'context'      => [
			[
				'setting'  => 'course_syllabus_thumbs',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-course .llms-lesson-thumbnail',
				'pattern'  => 'kadence-thumbnail-ratio-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'inherit' => [
					'name' => __( 'Inherit', 'kadence' ),
				],
				'1-1'     => [
					'name' => __( '1:1', 'kadence' ),
				],
				'3-4'     => [
					'name' => __( '4:3', 'kadence' ),
				],
				'2-3'     => [
					'name' => __( '3:2', 'kadence' ),
				],
				'9-16'    => [
					'name' => __( '16:9', 'kadence' ),
				],
				'1-2'     => [
					'name' => __( '2:1', 'kadence' ),
				],
			],
			'responsive' => false,
			'class'      => 'kadence-three-col-short',
		],
	],
	'course_comments'               => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'course_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'course_comments' ),
		'label'        => esc_html__( 'Show Comments?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'course_background'             => [
		'control_type' => 'kadence_background_control',
		'section'      => 'course_layout_design',
		'priority'     => 20,
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'course_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-course',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Course Background', 'kadence' ),
		],
	],
	'course_content_background'     => [
		'control_type' => 'kadence_background_control',
		'section'      => 'course_layout_design',
		'priority'     => 20,
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'course_content_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-course .content-bg, body.single-course.content-style-unboxed .site',
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
