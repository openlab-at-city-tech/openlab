<?php
/**
 * Tutor Course Layout
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = array(
	'courses_archive_layout_tabs' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'courses_archive_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'courses_archive_layout',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'courses_archive_layout_design',
			),
			'active' => 'general',
		),
	),
	'courses_archive_layout_tabs_design' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'courses_archive_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'courses_archive_layout',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'courses_archive_layout_design',
			),
			'active' => 'design',
		),
	),
	'info_courses_archive_title' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'courses_archive_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Archive Title', 'kadence' ),
		'settings'     => false,
	),
	'info_courses_archive_title_design' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'courses_archive_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Archive Title', 'kadence' ),
		'settings'     => false,
	),
	'courses_archive_title' => array(
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'courses_archive_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'courses_archive_title' ),
		'label'        => esc_html__( 'Show Archive Title?', 'kadence' ),
		'transport'    => 'refresh',
	),
	'courses_archive_title_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'courses_archive_layout',
		'label'        => esc_html__( 'Archive Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'courses_archive_title_layout' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
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
	'courses_archive_title_inner_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'courses_archive_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'courses_archive_title_inner_layout' ),
		'label'        => esc_html__( 'Container Width', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'courses_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.course-archive-hero-section',
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
	'courses_archive_title_align' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'courses_archive_layout',
		'label'        => esc_html__( 'Course Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'courses_archive_title_align' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.course-archive-title',
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
	'courses_archive_title_height' => array(
		'control_type' => 'kadence_range_control',
		'section'      => 'courses_archive_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Container Min Height', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'courses_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#inner-wrap .course-archive-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => kadence()->default( 'courses_archive_title_height' ),
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
	'courses_archive_title_elements' => array(
		'control_type' => 'kadence_sorter_control',
		'section'      => 'courses_archive_layout',
		'priority'     => 6,
		'default'      => kadence()->default( 'courses_archive_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => array(
			'elements'    => 'courses_archive_title_elements',
			'title'       => 'courses_archive_title_element_title',
			'breadcrumb'  => 'courses_archive_title_element_breadcrumb',
		),
	),
	'courses_archive_title_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Title Color', 'kadence' ),
		'default'      => kadence()->default( 'courses_archive_title_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.courses-archive-title h1',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
		),
		'input_attrs'  => array(
			'colors' => array(
				'color' => array(
					'tooltip' => __( 'Color', 'kadence' ),
					'palette' => true,
				),
			),
		),
	),
	'courses_archive_title_breadcrumb_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'courses_archive_title_breadcrumb_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.courses-archive-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.courses-archive-title .kadence-breadcrumbs a:hover',
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
	'courses_archive_title_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Archive Title Background', 'kadence' ),
		'default'      => kadence()->default( 'courses_archive_title_background' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'courses_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '#inner-wrap .courses-archive-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Course Archive Title Background', 'kadence' ),
		),
	),
	'courses_archive_title_overlay_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'courses_archive_title_overlay_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.courses-archive-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
		),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'courses_archive_title_layout',
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
	'courses_archive_title_border' => array(
		'control_type' => 'kadence_borders_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'courses_archive_title_border' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'courses_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'settings'     => array(
			'border_top'    => 'courses_archive_title_top_border',
			'border_bottom' => 'courses_archive_title_bottom_border',
		),
		'live_method'     => array(
			'courses_archive_title_top_border' => array(
				array(
					'type'     => 'css_border',
					'selector' => '.courses-archive-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				),
			),
			'courses_archive_title_bottom_border' => array( 
				array(
					'type'     => 'css_border',
					'selector' => '.courses-archive-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
		),
	),
	'info_courses_archive_layout' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'courses_archive_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'settings'     => false,
	),
	'info_courses_archive_layout_design' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'courses_archive_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'settings'     => false,
	),
	'courses_archive_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'courses_archive_layout',
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'courses_archive_layout' ),
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
			'class'      => 'kadence-three-col',
			'responsive' => false,
		),
	),
	'courses_archive_sidebar_id' => array(
		'control_type' => 'kadence_select_control',
		'section'      => 'courses_archive_layout',
		'label'        => esc_html__( 'Archive Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'courses_archive_sidebar_id' ),
		'input_attrs'  => array(
			'options' => kadence()->sidebar_options(),
		),
	),
	'courses_archive_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'courses_archive_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.post-type-archive-courses, body.tax-courses_cat',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip' => __( 'Course Archive Background', 'kadence' ),
		),
	),
	'courses_archive_content_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'courses_archive_content_background' ),
		'live_method'  => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.post-type-archive-courses .content-bg, body.tax-courses_cat .content-bg, body.tax-courses_cat.content-style-unboxed .site, body.post-type-archive-courses.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip' => __( 'Archive Content Background', 'kadence' ),
		),
	),
);

Theme_Customizer::add_settings( $settings );

