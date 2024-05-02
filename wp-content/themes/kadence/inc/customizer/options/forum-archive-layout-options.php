<?php
/**
 * Header Main Row Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

Theme_Customizer::add_settings(
	array(
		'forum_archive_tabs' => array(
			'control_type' => 'kadence_tab_control',
			'section'      => 'forum_archive',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'kadence' ),
					'target' => 'forum_archive',
				),
				'design' => array(
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'forum_archive_design',
				),
				'active' => 'general',
			),
		),
		'forum_archive_tabs_design' => array(
			'control_type' => 'kadence_tab_control',
			'section'      => 'forum_archive_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'kadence' ),
					'target' => 'forum_archive',
				),
				'design' => array(
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'forum_archive_design',
				),
				'active' => 'design',
			),
		),
		'info_forum_archive_title' => array(
			'control_type' => 'kadence_title_control',
			'section'      => 'forum_archive',
			'priority'     => 2,
			'label'        => esc_html__( 'Archive Title', 'kadence' ),
			'settings'     => false,
		),
		'info_forum_archive_title_design' => array(
			'control_type' => 'kadence_title_control',
			'section'      => 'forum_archive_design',
			'priority'     => 2,
			'label'        => esc_html__( 'Archive Title', 'kadence' ),
			'settings'     => false,
		),
		'forum_archive_title' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'forum_archive',
			'priority'     => 3,
			'default'      => kadence()->default( 'forum_archive_title' ),
			'label'        => esc_html__( 'Show Archive Title?', 'kadence' ),
			'transport'    => 'refresh',
		),
		'forum_archive_title_layout' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'label'        => esc_html__( 'Archive Title Layout', 'kadence' ),
			'transport'    => 'refresh',
			'priority'     => 4,
			'default'      => kadence()->default( 'forum_archive_title_layout' ),
			'context'      => array(
				array(
					'setting'    => 'forum_archive_title',
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
		'forum_archive_title_inner_layout' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'priority'     => 4,
			'default'      => kadence()->default( 'forum_archive_title_inner_layout' ),
			'label'        => esc_html__( 'Container Width', 'kadence' ),
			'context'      => array(
				array(
					'setting'    => 'forum_archive_title',
					'operator'   => '=',
					'value'      => true,
				),
				array(
					'setting'    => 'forum_archive_title_layout',
					'operator'   => '=',
					'value'      => 'above',
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '.forum-archive-hero-section',
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
		'forum_archive_title_align' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'label'        => esc_html__( 'Course Title Align', 'kadence' ),
			'priority'     => 4,
			'default'      => kadence()->default( 'forum_archive_title_align' ),
			'context'      => array(
				array(
					'setting'    => 'forum_archive_title',
					'operator'   => '=',
					'value'      => true,
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '.forum-archive-title',
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
		'forum_archive_title_height' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'forum_archive',
			'priority'     => 5,
			'label'        => esc_html__( 'Container Min Height', 'kadence' ),
			'context'      => array(
				array(
					'setting'    => 'forum_archive_title',
					'operator'   => '=',
					'value'      => true,
				),
				array(
					'setting'    => 'forum_archive_title_layout',
					'operator'   => '=',
					'value'      => 'above',
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#inner-wrap .forum-archive-hero-section .entry-header',
					'property' => 'min-height',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'forum_archive_title_height' ),
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
		'forum_archive_title_elements' => array(
			'control_type' => 'kadence_sorter_control',
			'section'      => 'forum_archive',
			'priority'     => 6,
			'default'      => kadence()->default( 'forum_archive_title_elements' ),
			'label'        => esc_html__( 'Title Elements', 'kadence' ),
			'transport'    => 'refresh',
			'settings'     => array(
				'elements'    => 'forum_archive_title_elements',
				'title'       => 'forum_archive_title_element_title',
				'breadcrumb'  => 'forum_archive_title_element_breadcrumb',
				'search'      => 'forum_archive_title_element_search',
			),
		),
		'forum_archive_title_color' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Title Color', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_color' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-title h1',
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
		'forum_archive_title_search_width' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Search Bar Width', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form',
					'property' => 'width',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'forum_archive_title_search_width' ),
			'input_attrs'  => array(
				'min'        => array(
					'px'  => 100,
					'em'  => 4,
					'rem' => 4,
				),
				'max'        => array(
					'px'  => 600,
					'em'  => 12,
					'rem' => 12,
				),
				'step'       => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
				),
				'units'      => array( 'px', 'em', 'rem' ),
				'responsive' => false,
			),
		),
		'forum_archive_title_search_color' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Input Text Colors', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_search_color' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field, .forum-archive-title .bbp-search-form .kadence-search-icon-wrap',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field:focus, .forum-archive-title .bbp-search-form input.search-submit:hover ~ .kadence-search-icon-wrap',
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
						'tooltip' => __( 'Focus Color', 'kadence' ),
						'palette' => true,
					),
				),
			),
		),
		'forum_archive_title_search_background' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Input Background', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_search_background' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field:focus',
					'property' => 'background',
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
						'tooltip' => __( 'Focus Color', 'kadence' ),
						'palette' => true,
					),
				),
			),
		),
		'forum_archive_title_search_border' => array(
			'control_type' => 'kadence_border_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_search_border' ),
			'live_method'     => array(
				array(
					'type'     => 'css_border',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field',
					'pattern'  => '$',
					'property' => 'border',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
			'input_attrs'  => array(
				'responsive' => false,
				'color'      => false,
			),
		),
		'forum_archive_title_search_border_color' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Input Border Color', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_search_border_color' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field:focus',
					'property' => 'border-color',
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
						'tooltip' => __( 'Focus Color', 'kadence' ),
						'palette' => true,
					),
				),
			),
		),
		'forum_archive_title_search_typography' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Font', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_search_typography' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field',
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
				'id' => 'forum_archive_title_search_typography',
				'options' => 'no-color',
			),
		),
		'forum_archive_title_search_margin' => array(
			'control_type' => 'kadence_measure_control',
			'section'      => 'forum_archive_design',
			'default'      => kadence()->default( 'forum_archive_title_search_margin' ),
			'label'        => esc_html__( 'Margin', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form form',
					'property' => 'margin',
					'pattern'  => '$',
					'key'      => 'measure',
				),
			),
			'input_attrs'  => array(
				'responsive' => false,
			),
		),
		'forum_archive_title_breadcrumb_color' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_breadcrumb_color' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-title .kadence-breadcrumbs',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-title .kadence-breadcrumbs a:hover',
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
		'forum_archive_title_background' => array(
			'control_type' => 'kadence_background_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Archive Title Background', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_background' ),
			'context'      => array(
				array(
					'setting'    => 'forum_archive_title',
					'operator'   => '=',
					'value'      => true,
				),
				array(
					'setting'    => 'forum_archive_title_layout',
					'operator'   => '=',
					'value'      => 'above',
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'css_background',
					'selector' => '#inner-wrap .forum-archive-hero-section .entry-hero-container-inner',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				),
			),
			'input_attrs'  => array(
				'tooltip'  => __( 'Course Archive Title Background', 'kadence' ),
			),
		),
		'forum_archive_title_overlay_color' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_overlay_color' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.forum-archive-hero-section .hero-section-overlay',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				),
			),
			'context'      => array(
				array(
					'setting'    => 'forum_archive_title',
					'operator'   => '=',
					'value'      => true,
				),
				array(
					'setting'    => 'forum_archive_title_layout',
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
		'forum_archive_title_border' => array(
			'control_type' => 'kadence_borders_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_border' ),
			'context'      => array(
				array(
					'setting'    => 'forum_archive_title',
					'operator'   => '=',
					'value'      => true,
				),
				array(
					'setting'    => 'forum_archive_title_layout',
					'operator'   => '=',
					'value'      => 'above',
				),
			),
			'settings'     => array(
				'border_top'    => 'forum_archive_title_top_border',
				'border_bottom' => 'forum_archive_title_bottom_border',
			),
			'live_method'     => array(
				'forum_archive_title_top_border' => array(
					array(
						'type'     => 'css_border',
						'selector' => '.forum-archive-hero-section .entry-hero-container-inner',
						'pattern'  => '$',
						'property' => 'border-top',
						'key'      => 'border',
					),
				),
				'forum_archive_title_bottom_border' => array( 
					array(
						'type'     => 'css_border',
						'selector' => '.forum-archive-hero-section .entry-hero-container-inner',
						'property' => 'border-bottom',
						'pattern'  => '$',
						'key'      => 'border',
					),
				),
			),
		),
		'info_forum_archive_layout' => array(
			'control_type' => 'kadence_title_control',
			'section'      => 'forum_archive',
			'priority'     => 10,
			'label'        => esc_html__( 'Forum Archive Layout', 'kadence' ),
			'settings'     => false,
		),
		'info_forum_archive_layout_design' => array(
			'control_type' => 'kadence_title_control',
			'section'      => 'forum_archive_design',
			'priority'     => 10,
			'label'        => esc_html__( 'Forum Archive Layout', 'kadence' ),
			'settings'     => false,
		),
		'forum_archive_layout' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'label'        => esc_html__( 'Archive Layout', 'kadence' ),
			'transport'    => 'refresh',
			'priority'     => 10,
			'default'      => kadence()->default( 'forum_archive_layout' ),
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
		'forum_archive_content_style' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'label'        => esc_html__( 'Content Style', 'kadence' ),
			'priority'     => 10,
			'default'      => kadence()->default( 'forum_archive_content_style' ),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => 'body.post-type-archive-forum',
					'pattern'  => 'content-style-$',
					'key'      => '',
				),
				array(
					'type'     => 'class',
					'selector' => 'body.forum-archive',
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
		'forum_archive_vertical_padding' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
			'priority'     => 10,
			'default'      => kadence()->default( 'forum_archive_vertical_padding' ),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => 'body.post-type-archive-forum, body.forum-archive',
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
		'forum_archive_background' => array(
			'control_type' => 'kadence_background_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Site Background', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_background' ),
			'live_method'     => array(
				array(
					'type'     => 'css_background',
					'selector' => 'body.post-type-archive-forum, body.forum-archive',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				),
			),
			'input_attrs'  => array(
				'tooltip' => __( 'Course Archive Background', 'kadence' ),
			),
		),
		'forum_archive_content_background' => array(
			'control_type' => 'kadence_background_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Content Background', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_content_background' ),
			'live_method'  => array(
				array(
					'type'     => 'css_background',
					'selector' => 'body.post-type-archive-forum .content-bg, body.forum-archive .content-bg, body.forum-archive.content-style-unboxed .site, body.post-type-archive-forum.content-style-unboxed .site',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				),
			),
			'input_attrs'  => array(
				'tooltip' => __( 'Archive Content Background', 'kadence' ),
			),
		),
	)
);
