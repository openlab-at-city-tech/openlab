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
	[
		'forum_archive_tabs'                      => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'forum_archive',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'forum_archive',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'forum_archive_design',
				],
				'active'  => 'general',
			],
		],
		'forum_archive_tabs_design'               => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'forum_archive_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'forum_archive',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'forum_archive_design',
				],
				'active'  => 'design',
			],
		],
		'info_forum_archive_title'                => [
			'control_type' => 'kadence_title_control',
			'section'      => 'forum_archive',
			'priority'     => 2,
			'label'        => esc_html__( 'Archive Title', 'kadence' ),
			'settings'     => false,
		],
		'info_forum_archive_title_design'         => [
			'control_type' => 'kadence_title_control',
			'section'      => 'forum_archive_design',
			'priority'     => 2,
			'label'        => esc_html__( 'Archive Title', 'kadence' ),
			'settings'     => false,
		],
		'forum_archive_title'                     => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'forum_archive',
			'priority'     => 3,
			'default'      => kadence()->default( 'forum_archive_title' ),
			'label'        => esc_html__( 'Show Archive Title?', 'kadence' ),
			'transport'    => 'refresh',
		],
		'forum_archive_title_layout'              => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'label'        => esc_html__( 'Archive Title Layout', 'kadence' ),
			'transport'    => 'refresh',
			'priority'     => 4,
			'default'      => kadence()->default( 'forum_archive_title_layout' ),
			'context'      => [
				[
					'setting'  => 'forum_archive_title',
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
		'forum_archive_title_inner_layout'        => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'priority'     => 4,
			'default'      => kadence()->default( 'forum_archive_title_inner_layout' ),
			'label'        => esc_html__( 'Container Width', 'kadence' ),
			'context'      => [
				[
					'setting'  => 'forum_archive_title',
					'operator' => '=',
					'value'    => true,
				],
				[
					'setting'  => 'forum_archive_title_layout',
					'operator' => '=',
					'value'    => 'above',
				],
			],
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '.forum-archive-hero-section',
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
		'forum_archive_title_align'               => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'label'        => esc_html__( 'Course Title Align', 'kadence' ),
			'priority'     => 4,
			'default'      => kadence()->default( 'forum_archive_title_align' ),
			'context'      => [
				[
					'setting'  => 'forum_archive_title',
					'operator' => '=',
					'value'    => true,
				],
			],
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '.forum-archive-title',
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
		'forum_archive_title_height'              => [
			'control_type' => 'kadence_range_control',
			'section'      => 'forum_archive',
			'priority'     => 5,
			'label'        => esc_html__( 'Container Min Height', 'kadence' ),
			'context'      => [
				[
					'setting'  => 'forum_archive_title',
					'operator' => '=',
					'value'    => true,
				],
				[
					'setting'  => 'forum_archive_title_layout',
					'operator' => '=',
					'value'    => 'above',
				],
			],
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#inner-wrap .forum-archive-hero-section .entry-header',
					'property' => 'min-height',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'forum_archive_title_height' ),
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
		'forum_archive_title_elements'            => [
			'control_type' => 'kadence_sorter_control',
			'section'      => 'forum_archive',
			'priority'     => 6,
			'default'      => kadence()->default( 'forum_archive_title_elements' ),
			'label'        => esc_html__( 'Title Elements', 'kadence' ),
			'transport'    => 'refresh',
			'settings'     => [
				'elements'   => 'forum_archive_title_elements',
				'title'      => 'forum_archive_title_element_title',
				'breadcrumb' => 'forum_archive_title_element_breadcrumb',
				'search'     => 'forum_archive_title_element_search',
			],
		],
		'forum_archive_title_color'               => [
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Title Color', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.forum-archive-title h1',
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
		'forum_archive_title_search_width'        => [
			'control_type' => 'kadence_range_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Search Bar Width', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form',
					'property' => 'width',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'forum_archive_title_search_width' ),
			'input_attrs'  => [
				'min'        => [
					'px'  => 100,
					'em'  => 4,
					'rem' => 4,
				],
				'max'        => [
					'px'  => 600,
					'em'  => 12,
					'rem' => 12,
				],
				'step'       => [
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
				],
				'units'      => [ 'px', 'em', 'rem' ],
				'responsive' => false,
			],
		],
		'forum_archive_title_search_color'        => [
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Input Text Colors', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_search_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field, .forum-archive-title .bbp-search-form .kadence-search-icon-wrap',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field:focus, .forum-archive-title .bbp-search-form input.search-submit:hover ~ .kadence-search-icon-wrap',
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
						'tooltip' => __( 'Focus Color', 'kadence' ),
						'palette' => true,
					],
				],
			],
		],
		'forum_archive_title_search_background'   => [
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Input Background', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_search_background' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field:focus',
					'property' => 'background',
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
						'tooltip' => __( 'Focus Color', 'kadence' ),
						'palette' => true,
					],
				],
			],
		],
		'forum_archive_title_search_border'       => [
			'control_type' => 'kadence_border_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_search_border' ),
			'live_method'  => [
				[
					'type'     => 'css_border',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field',
					'pattern'  => '$',
					'property' => 'border',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
			'input_attrs'  => [
				'responsive' => false,
				'color'      => false,
			],
		],
		'forum_archive_title_search_border_color' => [
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Input Border Color', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_search_border_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field:focus',
					'property' => 'border-color',
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
						'tooltip' => __( 'Focus Color', 'kadence' ),
						'palette' => true,
					],
				],
			],
		],
		'forum_archive_title_search_typography'   => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Font', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_search_typography' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.forum-archive-title .bbp-search-form input.search-field',
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
				'id'      => 'forum_archive_title_search_typography',
				'options' => 'no-color',
			],
		],
		'forum_archive_title_search_margin'       => [
			'control_type' => 'kadence_measure_control',
			'section'      => 'forum_archive_design',
			'default'      => kadence()->default( 'forum_archive_title_search_margin' ),
			'label'        => esc_html__( 'Margin', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.forum-archive-title .bbp-search-form form',
					'property' => 'margin',
					'pattern'  => '$',
					'key'      => 'measure',
				],
			],
			'input_attrs'  => [
				'responsive' => false,
			],
		],
		'forum_archive_title_breadcrumb_color'    => [
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_breadcrumb_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.forum-archive-title .kadence-breadcrumbs',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.forum-archive-title .kadence-breadcrumbs a:hover',
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
		'forum_archive_title_background'          => [
			'control_type' => 'kadence_background_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Archive Title Background', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_background' ),
			'context'      => [
				[
					'setting'  => 'forum_archive_title',
					'operator' => '=',
					'value'    => true,
				],
				[
					'setting'  => 'forum_archive_title_layout',
					'operator' => '=',
					'value'    => 'above',
				],
			],
			'live_method'  => [
				[
					'type'     => 'css_background',
					'selector' => '#inner-wrap .forum-archive-hero-section .entry-hero-container-inner',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				],
			],
			'input_attrs'  => [
				'tooltip' => __( 'Course Archive Title Background', 'kadence' ),
			],
		],
		'forum_archive_title_overlay_color'       => [
			'control_type' => 'kadence_color_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_overlay_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.forum-archive-hero-section .hero-section-overlay',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				],
			],
			'context'      => [
				[
					'setting'  => 'forum_archive_title',
					'operator' => '=',
					'value'    => true,
				],
				[
					'setting'  => 'forum_archive_title_layout',
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
		'forum_archive_title_border'              => [
			'control_type' => 'kadence_borders_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_title_border' ),
			'context'      => [
				[
					'setting'  => 'forum_archive_title',
					'operator' => '=',
					'value'    => true,
				],
				[
					'setting'  => 'forum_archive_title_layout',
					'operator' => '=',
					'value'    => 'above',
				],
			],
			'settings'     => [
				'border_top'    => 'forum_archive_title_top_border',
				'border_bottom' => 'forum_archive_title_bottom_border',
			],
			'live_method'  => [
				'forum_archive_title_top_border'    => [
					[
						'type'     => 'css_border',
						'selector' => '.forum-archive-hero-section .entry-hero-container-inner',
						'pattern'  => '$',
						'property' => 'border-top',
						'key'      => 'border',
					],
				],
				'forum_archive_title_bottom_border' => [ 
					[
						'type'     => 'css_border',
						'selector' => '.forum-archive-hero-section .entry-hero-container-inner',
						'property' => 'border-bottom',
						'pattern'  => '$',
						'key'      => 'border',
					],
				],
			],
		],
		'info_forum_archive_layout'               => [
			'control_type' => 'kadence_title_control',
			'section'      => 'forum_archive',
			'priority'     => 10,
			'label'        => esc_html__( 'Forum Archive Layout', 'kadence' ),
			'settings'     => false,
		],
		'info_forum_archive_layout_design'        => [
			'control_type' => 'kadence_title_control',
			'section'      => 'forum_archive_design',
			'priority'     => 10,
			'label'        => esc_html__( 'Forum Archive Layout', 'kadence' ),
			'settings'     => false,
		],
		'forum_archive_layout'                    => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'label'        => esc_html__( 'Archive Layout', 'kadence' ),
			'transport'    => 'refresh',
			'priority'     => 10,
			'default'      => kadence()->default( 'forum_archive_layout' ),
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
		'forum_archive_content_style'             => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'label'        => esc_html__( 'Content Style', 'kadence' ),
			'priority'     => 10,
			'default'      => kadence()->default( 'forum_archive_content_style' ),
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => 'body.post-type-archive-forum',
					'pattern'  => 'content-style-$',
					'key'      => '',
				],
				[
					'type'     => 'class',
					'selector' => 'body.forum-archive',
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
		'forum_archive_vertical_padding'          => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'forum_archive',
			'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
			'priority'     => 10,
			'default'      => kadence()->default( 'forum_archive_vertical_padding' ),
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => 'body.post-type-archive-forum, body.forum-archive',
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
		'forum_archive_background'                => [
			'control_type' => 'kadence_background_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Site Background', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_background' ),
			'live_method'  => [
				[
					'type'     => 'css_background',
					'selector' => 'body.post-type-archive-forum, body.forum-archive',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				],
			],
			'input_attrs'  => [
				'tooltip' => __( 'Course Archive Background', 'kadence' ),
			],
		],
		'forum_archive_content_background'        => [
			'control_type' => 'kadence_background_control',
			'section'      => 'forum_archive_design',
			'label'        => esc_html__( 'Content Background', 'kadence' ),
			'default'      => kadence()->default( 'forum_archive_content_background' ),
			'live_method'  => [
				[
					'type'     => 'css_background',
					'selector' => 'body.post-type-archive-forum .content-bg, body.forum-archive .content-bg, body.forum-archive.content-style-unboxed .site, body.post-type-archive-forum.content-style-unboxed .site',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				],
			],
			'input_attrs'  => [
				'tooltip' => __( 'Archive Content Background', 'kadence' ),
			],
		],
	]
);
