<?php
/**
 * Course Layout Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = array(
	'sfwd_courses_layout_tabs' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'sfwd_courses_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'sfwd_courses_layout',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'sfwd_courses_layout_design',
			),
			'active' => 'general',
		),
	),
	'sfwd_courses_layout_tabs_design' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'sfwd_courses_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'sfwd_courses_layout',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'sfwd_courses_layout_design',
			),
			'active' => 'design',
		),
	),
	'info_sfwd-courses_title' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_courses_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Course Title', 'kadence' ),
		'settings'     => false,
	),
	'info_sfwd-courses_title_design' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_courses_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Course Title', 'kadence' ),
		'settings'     => false,
	),
	'sfwd-courses_title' => array(
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_courses_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'sfwd-courses_title' ),
		'label'        => esc_html__( 'Show Course Title?', 'kadence' ),
		'transport'    => 'refresh',
	),
	'sfwd-courses_title_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_layout',
		'label'        => esc_html__( 'Course Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-courses_title_layout' ),
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'normal' => array(
					'tooltip' => __( 'In Content', 'kadence' ),
					'icon'    => 'incontent',
				),
				'above' => array(
					'tooltip' => __( 'Above Content', 'kadence' ),
					'icon'    => 'abovecontent',
				),
			),
			'responsive' => false,
			'class'      => 'kadence-two-col',
		),
	),
	'sfwd-courses_title_inner_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-courses_title_inner_layout' ),
		'label'        => esc_html__( 'Title Container Width', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'sfwd-courses_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.sfwd-courses-hero-section',
				'pattern'  => 'entry-hero-layout-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'standard' => array(
					'tooltip' => __( 'Background Fullwidth, Content Contained', 'kadence' ),
					'name'    => __( 'Standard', 'kadence' ),
					'icon'    => '',
				),
				'fullwidth' => array(
					'tooltip' => __( 'Background & Content Fullwidth', 'kadence' ),
					'name'    => __( 'Fullwidth', 'kadence' ),
					'icon'    => '',
				),
				'contained' => array(
					'tooltip' => __( 'Background & Content Contained', 'kadence' ),
					'name'    => __( 'Contained', 'kadence' ),
					'icon'    => '',
				),
			),
			'responsive' => false,
		),
	),
	'sfwd-courses_title_align' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_layout',
		'label'        => esc_html__( 'Course Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-courses_title_align' ),
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.sfwd-courses-title',
				'pattern'  => array(
					'desktop' => 'title-align-$',
					'tablet'  => 'title-tablet-align-$',
					'mobile'  => 'title-mobile-align-$',
				),
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'left' => array(
					'tooltip'  => __( 'Left Align Title', 'kadence' ),
					'dashicon' => 'editor-alignleft',
				),
				'center' => array(
					'tooltip'  => __( 'Center Align Title', 'kadence' ),
					'dashicon' => 'editor-aligncenter',
				),
				'right' => array(
					'tooltip'  => __( 'Right Align Title', 'kadence' ),
					'dashicon' => 'editor-alignright',
				),
			),
			'responsive' => true,
		),
	),
	'sfwd-courses_title_height' => array(
		'control_type' => 'kadence_range_control',
		'section'      => 'sfwd_courses_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Title Container Min Height', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'sfwd-courses_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#inner-wrap .sfwd-courses-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => kadence()->default( 'sfwd-courses_title_height' ),
		'input_attrs'  => array(
			'min'     => array(
				'px'  => 10,
				'em'  => 1,
				'rem' => 1,
				'vh'  => 2,
			),
			'max'     => array(
				'px'  => 800,
				'em'  => 12,
				'rem' => 12,
				'vh'  => 100,
			),
			'step'    => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vh'  => 1,
			),
			'units'   => array( 'px', 'em', 'rem', 'vh' ),
		),
	),
	'sfwd-courses_title_elements' => array(
		'control_type' => 'kadence_sorter_control',
		'section'      => 'sfwd_courses_layout',
		'priority'     => 6,
		'default'      => kadence()->default( 'sfwd-courses_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => array(
			'elements'    => 'sfwd-courses_title_elements',
			'title' => 'sfwd-courses_title_element_title',
			'breadcrumb'  => 'sfwd-courses_title_element_breadcrumb',
		),
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'input_attrs'  => array(
			'group' => 'sfwd-courses_title_element',
		),
	),
	'sfwd-courses_title_font' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'sfwd_courses_layout_design',
		'label'        => esc_html__( 'Course Title Font', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_title_font' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.sfwd-courses-title h1',
				'property' => 'font',
				'key'      => 'typography',
			),
		),
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'input_attrs'  => array(
			'id'             => 'sfwd-courses_title_font',
			'headingInherit' => true,
		),
	),
	'sfwd-courses_title_breadcrumb_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'sfwd_courses_layout_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_title_breadcrumb_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.sfwd-courses-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.sfwd-courses-title .kadence-breadcrumbs a:hover',
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
					'tooltip' => __( 'Link Hover Color', 'kadence' ),
					'palette' => true,
				),
			),
		),
	),
	'sfwd-courses_title_breadcrumb_font' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'sfwd_courses_layout_design',
		'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_title_breadcrumb_font' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.sfwd-courses-title .kadence-breadcrumbs',
				'property' => 'font',
				'key'      => 'typography',
			),
		),
		'input_attrs'  => array(
			'id'      => 'sfwd-courses_title_breadcrumb_font',
			'options' => 'no-color',
		),
	),
	'sfwd-courses_title_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_courses_layout_design',
		'label'        => esc_html__( 'Course Above Area Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_title_background' ),
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'sfwd-courses_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '#inner-wrap .sfwd-courses-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Course Title Background', 'kadence' ),
		),
	),
	'sfwd-courses_title_featured_image' => array(
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_courses_layout_design',
		'default'      => kadence()->default( 'sfwd-courses_title_featured_image' ),
		'label'        => esc_html__( 'Use Featured Image for Background?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'sfwd-courses_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
	),
	'sfwd-courses_title_overlay_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'sfwd_courses_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_title_overlay_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.sfwd-courses-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
		),
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'sfwd-courses_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'input_attrs'  => array(
			'colors' => array(
				'color' => array(
					'tooltip' => __( 'Overlay Color', 'kadence' ),
					'palette' => true,
				),
			),
			'allowGradient' => true,
		),
	),
	'sfwd-courses_title_border' => array(
		'control_type' => 'kadence_borders_control',
		'section'      => 'sfwd_courses_layout_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_title_border' ),
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'sfwd-courses_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'settings'     => array(
			'border_top'    => 'sfwd-courses_title_top_border',
			'border_bottom' => 'sfwd-courses_title_bottom_border',
		),
		'live_method'     => array(
			'sfwd-courses_title_top_border' => array(
				array(
					'type'     => 'css_border',
					'selector' => '.sfwd-courses-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				),
			),
			'sfwd-courses_title_bottom_border' => array( 
				array(
					'type'     => 'css_border',
					'selector' => '.sfwd-courses-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
		),
	),
	'info_sfwd_courses_layout' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_courses_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Course Layout', 'kadence' ),
		'settings'     => false,
	),
	'info_sfwd_courses_layout_design' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_courses_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Course Layout', 'kadence' ),
		'settings'     => false,
	),
	'sfwd-courses_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_layout',
		'label'        => esc_html__( 'Course Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-courses_layout' ),
		'input_attrs'  => array(
			'layout' => array(
				'normal' => array(
					'tooltip' => __( 'Normal', 'kadence' ),
					'icon' => 'normal',
				),
				'narrow' => array(
					'tooltip' => __( 'Narrow', 'kadence' ),
					'icon' => 'narrow',
				),
				'fullwidth' => array(
					'tooltip' => __( 'Fullwidth', 'kadence' ),
					'icon' => 'fullwidth',
				),
				'left' => array(
					'tooltip' => __( 'Left Sidebar', 'kadence' ),
					'icon' => 'leftsidebar',
				),
				'right' => array(
					'tooltip' => __( 'Right Sidebar', 'kadence' ),
					'icon' => 'rightsidebar',
				),
			),
			'responsive' => false,
			'class'      => 'kadence-three-col',
		),
	),
	'sfwd-courses_sidebar_id' => array(
		'control_type' => 'kadence_select_control',
		'section'      => 'sfwd_courses_layout',
		'label'        => esc_html__( 'Course Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-courses_sidebar_id' ),
		'input_attrs'  => array(
			'options' => kadence()->sidebar_options(),
		),
	),
	'sfwd-courses_content_style' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-courses_content_style' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => 'body.single-sfwd-courses',
				'pattern'  => 'content-style-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'boxed' => array(
					'tooltip' => __( 'Boxed', 'kadence' ),
					'icon' => 'boxed',
				),
				'unboxed' => array(
					'tooltip' => __( 'Unboxed', 'kadence' ),
					'icon' => 'narrow',
				),
			),
			'responsive' => false,
			'class'      => 'kadence-two-col',
		),
	),
	'sfwd-courses_vertical_padding' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_layout',
		'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-courses_vertical_padding' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => 'body.single-sfwd-courses',
				'pattern'  => 'content-vertical-padding-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'show' => array(
					'name' => __( 'Enable', 'kadence' ),
				),
				'hide' => array(
					'name' => __( 'Disable', 'kadence' ),
				),
				'top' => array(
					'name' => __( 'Top Only', 'kadence' ),
				),
				'bottom' => array(
					'name' => __( 'Bottom Only', 'kadence' ),
				),
			),
			'responsive' => false,
			'class'      => 'kadence-two-grid',
		),
	),
	'sfwd-courses_feature' => array(
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_courses_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'sfwd-courses_feature' ),
		'label'        => esc_html__( 'Show Featured Image?', 'kadence' ),
		'transport'    => 'refresh',
	),
	'sfwd-courses_feature_position' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_layout',
		'label'        => esc_html__( 'Featured Image Position', 'kadence' ),
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'sfwd-courses_feature_position' ),
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_feature',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'above' => array(
					'name' => __( 'Above', 'kadence' ),
				),
				'behind' => array(
					'name' => __( 'Behind', 'kadence' ),
				),
				'below' => array(
					'name' => __( 'Below', 'kadence' ),
				),
			),
			'responsive' => false,
		),
	),
	'sfwd-courses_feature_ratio' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_courses_layout',
		'label'        => esc_html__( 'Featured Image Ratio', 'kadence' ),
		'priority'     => 20,
		'default'      => kadence()->default( 'sfwd-courses_feature_ratio' ),
		'context'      => array(
			array(
				'setting'    => 'sfwd-courses_feature',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => 'body.single-sfwd-courses .article-post-thumbnail',
				'pattern'  => 'kadence-thumbnail-ratio-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'inherit' => array(
					'name' => __( 'Inherit', 'kadence' ),
				),
				'1-1' => array(
					'name' => __( '1:1', 'kadence' ),
				),
				'3-4' => array(
					'name' => __( '4:3', 'kadence' ),
				),
				'2-3' => array(
					'name' => __( '3:2', 'kadence' ),
				),
				'9-16' => array(
					'name' => __( '16:9', 'kadence' ),
				),
				'1-2' => array(
					'name' => __( '2:1', 'kadence' ),
				),
			),
			'responsive' => false,
			'class' => 'kadence-three-col-short',
		),
	),
	'sfwd-courses_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_courses_layout_design',
		'priority'     => 20,
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.single-sfwd-courses',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip' => __( 'Course Background', 'kadence' ),
		),
	),
	'sfwd-courses_content_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_courses_layout_design',
		'priority'     => 20,
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-courses_content_background' ),
		'live_method'  => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.single-sfwd-courses .content-bg, body.single-sfwd-courses.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip' => __( 'Course Content Background', 'kadence' ),
		),
	),
);

Theme_Customizer::add_settings( $settings );

