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
	'lesson_layout_tabs'            => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'lesson_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'lesson_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'lesson_layout_design',
			],
			'active'  => 'general',
		],
	],
	'lesson_layout_tabs_design'     => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'lesson_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'lesson_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'lesson_layout_design',
			],
			'active'  => 'design',
		],
	],
	'info_lesson_title'             => [
		'control_type' => 'kadence_title_control',
		'section'      => 'lesson_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Lesson Title', 'kadence' ),
		'settings'     => false,
	],
	'info_lesson_title_design'      => [
		'control_type' => 'kadence_title_control',
		'section'      => 'lesson_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Lesson Title', 'kadence' ),
		'settings'     => false,
	],
	'lesson_title'                  => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'lesson_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'lesson_title' ),
		'label'        => esc_html__( 'Show Lesson Title?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'lesson_title_layout'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'lesson_layout',
		'label'        => esc_html__( 'Lesson Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'lesson_title_layout' ),
		'context'      => [
			[
				'setting'  => 'lesson_title',
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
	'lesson_title_inner_layout'     => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'lesson_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'lesson_title_inner_layout' ),
		'label'        => esc_html__( 'Title Container Width', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'lesson_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'lesson_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.lesson-hero-section',
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
	'lesson_title_align'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'lesson_layout',
		'label'        => esc_html__( 'Lesson Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'lesson_title_align' ),
		'context'      => [
			[
				'setting'  => 'lesson_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.lesson-title',
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
	'lesson_title_height'           => [
		'control_type' => 'kadence_range_control',
		'section'      => 'lesson_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Title Container Min Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'lesson_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'lesson_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#inner-wrap .lesson-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'lesson_title_height' ),
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
	'lesson_title_elements'         => [
		'control_type' => 'kadence_sorter_control',
		'section'      => 'lesson_layout',
		'priority'     => 6,
		'default'      => kadence()->default( 'lesson_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => [
			'elements'   => 'lesson_title_elements',
			'title'      => 'lesson_title_element_title',
			'breadcrumb' => 'lesson_title_element_breadcrumb',
		],
		'context'      => [
			[
				'setting'  => 'lesson_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'group' => 'lesson_title_element',
		],
	],
	'lesson_title_font'             => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'lesson_layout_design',
		'label'        => esc_html__( 'Lesson Title Font', 'kadence' ),
		'default'      => kadence()->default( 'lesson_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.lesson-title h1',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting'  => 'lesson_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'id'             => 'lesson_title_font',
			'headingInherit' => true,
		],
	],
	'lesson_title_breadcrumb_color' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'lesson_layout_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'lesson_title_breadcrumb_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.lesson-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.lesson-title .kadence-breadcrumbs a:hover',
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
	'lesson_title_breadcrumb_font'  => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'lesson_layout_design',
		'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
		'default'      => kadence()->default( 'lesson_title_breadcrumb_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.lesson-title .kadence-breadcrumbs',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'lesson_title_breadcrumb_font',
			'options' => 'no-color',
		],
	],
	'lesson_title_background'       => [
		'control_type' => 'kadence_background_control',
		'section'      => 'lesson_layout_design',
		'label'        => esc_html__( 'Lesson Above Area Background', 'kadence' ),
		'default'      => kadence()->default( 'lesson_title_background' ),
		'context'      => [
			[
				'setting'  => 'lesson_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'lesson_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#inner-wrap .lesson-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Lesson Title Background', 'kadence' ),
		],
	],
	'lesson_title_featured_image'   => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'lesson_layout_design',
		'default'      => kadence()->default( 'lesson_title_featured_image' ),
		'label'        => esc_html__( 'Use Featured Image for Background?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'lesson_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'lesson_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
	],
	'lesson_title_overlay_color'    => [
		'control_type' => 'kadence_color_control',
		'section'      => 'lesson_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'lesson_title_overlay_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.lesson-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting'  => 'lesson_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'lesson_title_layout',
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
	'lesson_title_border'           => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'lesson_layout_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'lesson_title_border' ),
		'context'      => [
			[
				'setting'  => 'lesson_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'lesson_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'settings'     => [
			'border_top'    => 'lesson_title_top_border',
			'border_bottom' => 'lesson_title_bottom_border',
		],
		'live_method'  => [
			'lesson_title_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => '.lesson-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				],
			],
			'lesson_title_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => '.lesson-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'info_lesson_layout'            => [
		'control_type' => 'kadence_title_control',
		'section'      => 'lesson_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Lesson Layout', 'kadence' ),
		'settings'     => false,
	],
	'info_lesson_layout_design'     => [
		'control_type' => 'kadence_title_control',
		'section'      => 'lesson_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Lesson Layout', 'kadence' ),
		'settings'     => false,
	],
	'lesson_layout'                 => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'lesson_layout',
		'label'        => esc_html__( 'Lesson Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'lesson_layout' ),
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
	'lesson_sidebar_id'             => [
		'control_type' => 'kadence_select_control',
		'section'      => 'lesson_layout',
		'label'        => esc_html__( 'Lesson Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'lesson_sidebar_id' ),
		'input_attrs'  => [
			'options' => kadence()->sidebar_options(),
		],
	],
	'lesson_content_style'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'lesson_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'lesson_content_style' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-lesson',
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
	'lesson_vertical_padding'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'lesson_layout',
		'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'lesson_vertical_padding' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-lesson',
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
	'lesson_feature'                => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'lesson_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'lesson_feature' ),
		'label'        => esc_html__( 'Show Featured Image?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'lesson_feature_position'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'lesson_layout',
		'label'        => esc_html__( 'Featured Image Position', 'kadence' ),
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'lesson_feature_position' ),
		'context'      => [
			[
				'setting'  => 'lesson_feature',
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
	'lesson_feature_ratio'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'lesson_layout',
		'label'        => esc_html__( 'Featured Image Ratio', 'kadence' ),
		'priority'     => 20,
		'default'      => kadence()->default( 'lesson_feature_ratio' ),
		'context'      => [
			[
				'setting'  => 'lesson_feature',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-lesson .article-post-thumbnail',
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
	'lesson_comments'               => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'lesson_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'lesson_comments' ),
		'label'        => esc_html__( 'Show Comments?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'lesson_background'             => [
		'control_type' => 'kadence_background_control',
		'section'      => 'lesson_layout_design',
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'lesson_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-lesson',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Lesson Background', 'kadence' ),
		],
	],
	'lesson_content_background'     => [
		'control_type' => 'kadence_background_control',
		'section'      => 'lesson_layout_design',
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'lesson_content_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-lesson .content-bg, body.single-lesson.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Lesson Content Background', 'kadence' ),
		],
	],
];

Theme_Customizer::add_settings( $settings );
