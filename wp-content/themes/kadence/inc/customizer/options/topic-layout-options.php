<?php
/**
 * Product Layout Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

Theme_Customizer::add_settings(
	[
		'topic_layout_tabs'               => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'topic_layout',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'topic_layout',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'topic_layout_design',
				],
				'active'  => 'general',
			],
		],
		'topic_layout_tabs_design'        => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'topic_layout_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'topic_layout',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'topic_layout_design',
				],
				'active'  => 'design',
			],
		],
		'info_topic_title'                => [
			'control_type' => 'kadence_title_control',
			'section'      => 'topic_layout',
			'priority'     => 2,
			'label'        => esc_html__( 'Topic Title', 'kadence' ),
			'settings'     => false,
		],
		'info_topic_title_design'         => [
			'control_type' => 'kadence_title_control',
			'section'      => 'topic_layout_design',
			'priority'     => 2,
			'label'        => esc_html__( 'Topic Title', 'kadence' ),
			'settings'     => false,
		],
		'topic_title'                     => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'topic_layout',
			'priority'     => 3,
			'default'      => kadence()->default( 'topic_title' ),
			'label'        => esc_html__( 'Show Topic Title?', 'kadence' ),
			'transport'    => 'refresh',
		],
		'topic_title_layout'              => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'topic_layout',
			'label'        => esc_html__( 'Topic Title Layout', 'kadence' ),
			'transport'    => 'refresh',
			'priority'     => 4,
			'default'      => kadence()->default( 'topic_title_layout' ),
			'context'      => [
				[
					'setting'  => 'topic_title',
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
		'topic_title_inner_layout'        => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'topic_layout',
			'priority'     => 4,
			'default'      => kadence()->default( 'topic_title_inner_layout' ),
			'label'        => esc_html__( 'Title Container Width', 'kadence' ),
			'context'      => [
				[
					'setting'  => 'topic_title',
					'operator' => '=',
					'value'    => true,
				],
				[
					'setting'  => 'topic_title_layout',
					'operator' => '=',
					'value'    => 'above',
				],
			],
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '.topic-hero-section',
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
		'topic_title_align'               => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'topic_layout',
			'label'        => esc_html__( 'Topic Title Align', 'kadence' ),
			'priority'     => 4,
			'default'      => kadence()->default( 'topic_title_align' ),
			'context'      => [
				[
					'setting'  => 'topic_title',
					'operator' => '=',
					'value'    => true,
				],
			],
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '.topic-title',
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
		'topic_title_height'              => [
			'control_type' => 'kadence_range_control',
			'section'      => 'topic_layout',
			'priority'     => 5,
			'label'        => esc_html__( 'Title Container Min Height', 'kadence' ),
			'context'      => [
				[
					'setting'  => 'topic_title',
					'operator' => '=',
					'value'    => true,
				],
				[
					'setting'  => 'topic_title_layout',
					'operator' => '=',
					'value'    => 'above',
				],
			],
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#inner-wrap .topic-hero-section .entry-header',
					'property' => 'min-height',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'topic_title_height' ),
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
		'topic_title_elements'            => [
			'control_type' => 'kadence_sorter_control',
			'section'      => 'topic_layout',
			'priority'     => 6,
			'default'      => kadence()->default( 'topic_title_elements' ),
			'label'        => esc_html__( 'Title Elements', 'kadence' ),
			'transport'    => 'refresh',
			'settings'     => [
				'elements'   => 'topic_title_elements',
				'title'      => 'topic_title_element_title',
				'breadcrumb' => 'topic_title_element_breadcrumb',
				'search'     => 'topic_title_element_search',
				'info'       => 'topic_title_element_info',
			],
			'context'      => [
				[
					'setting'  => 'topic_title',
					'operator' => '=',
					'value'    => true,
				],
			],
			'input_attrs'  => [
				'group' => 'topic_title_element',
			],
		],
		'topic_title_font'                => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Topic Title Font', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.topic-title h1',
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'context'      => [
				[
					'setting'  => 'topic_title',
					'operator' => '=',
					'value'    => true,
				],
			],
			'input_attrs'  => [
				'id'             => 'topic_title_font',
				'headingInherit' => true,
			],
		],
		'topic_title_search_width'        => [
			'control_type' => 'kadence_range_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Search Bar Width', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.topic-title .bbp-search-form',
					'property' => 'width',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'topic_title_search_width' ),
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
		'topic_title_search_color'        => [
			'control_type' => 'kadence_color_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Input Text Colors', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_search_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.topic-title .bbp-search-form input.search-field, .topic-title .bbp-search-form .kadence-search-icon-wrap',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.topic-title .bbp-search-form input.search-field:focus, .topic-title .bbp-search-form input.search-submit:hover ~ .kadence-search-icon-wrap',
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
		'topic_title_search_background'   => [
			'control_type' => 'kadence_color_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Input Background', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_search_background' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.topic-title .bbp-search-form input.search-field',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.topic-title .bbp-search-form input.search-field:focus',
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
		'topic_title_search_border'       => [
			'control_type' => 'kadence_border_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_search_border' ),
			'live_method'  => [
				[
					'type'     => 'css_border',
					'selector' => '.topic-title .bbp-search-form input.search-field',
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
		'topic_title_search_border_color' => [
			'control_type' => 'kadence_color_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Input Border Color', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_search_border_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.topic-title .bbp-search-form input.search-field',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.topic-title .bbp-search-form input.search-field:focus',
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
		'topic_title_search_typography'   => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Font', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_search_typography' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.topic-title .bbp-search-form input.search-field',
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
				'id'      => 'topic_title_search_typography',
				'options' => 'no-color',
			],
		],
		'topic_title_search_margin'       => [
			'control_type' => 'kadence_measure_control',
			'section'      => 'topic_layout_design',
			'default'      => kadence()->default( 'topic_title_search_margin' ),
			'label'        => esc_html__( 'Margin', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.topic-title .bbp-search-form form',
					'property' => 'margin',
					'pattern'  => '$',
					'key'      => 'measure',
				],
			],
			'input_attrs'  => [
				'responsive' => false,
			],
		],
		'topic_title_breadcrumb_color'    => [
			'control_type' => 'kadence_color_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_breadcrumb_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.topic-title .kadence-breadcrumbs',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.topic-title .kadence-breadcrumbs a:hover',
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
		'topic_title_breadcrumb_font'     => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_breadcrumb_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.topic-title .kadence-breadcrumbs',
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'input_attrs'  => [
				'id'      => 'topic_title_breadcrumb_font',
				'options' => 'no-color',
			],
		],
		'topic_title_info_color'          => [
			'control_type' => 'kadence_color_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Info Colors', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_info_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.wp-site-blocks .topic-title .bbpress-topic-meta',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.wp-site-blocks .topic-title .bbpress-topic-meta a:hover',
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
		'topic_title_info_font'           => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Meta Font', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_info_font' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.topic-title .bbpress-topic-meta',
					'property' => 'font',
					'key'      => 'typography',
				],
			],
			'input_attrs'  => [
				'id'      => 'topic_title_info_font',
				'options' => 'no-color',
			],
		],
		'topic_title_background'          => [
			'control_type' => 'kadence_background_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Forum Above Area Background', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_background' ),
			'context'      => [
				[
					'setting'  => 'topic_title',
					'operator' => '=',
					'value'    => true,
				],
				[
					'setting'  => 'topic_title_layout',
					'operator' => '=',
					'value'    => 'above',
				],
			],
			'live_method'  => [
				[
					'type'     => 'css_background',
					'selector' => '#inner-wrap .topic-hero-section .entry-hero-container-inner',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				],
			],
			'input_attrs'  => [
				'tooltip' => __( 'Topic Title Background', 'kadence' ),
			],
		],
		'topic_title_overlay_color'       => [
			'control_type' => 'kadence_color_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_overlay_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.topic-hero-section .hero-section-overlay',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				],
			],
			'context'      => [
				[
					'setting'  => 'topic_title',
					'operator' => '=',
					'value'    => true,
				],
				[
					'setting'  => 'topic_title_layout',
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
		'topic_title_border'              => [
			'control_type' => 'kadence_borders_control',
			'section'      => 'topic_layout_design',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'topic_title_border' ),
			'context'      => [
				[
					'setting'  => 'topic_title',
					'operator' => '=',
					'value'    => true,
				],
				[
					'setting'  => 'topic_title_layout',
					'operator' => '=',
					'value'    => 'above',
				],
			],
			'settings'     => [
				'border_top'    => 'topic_title_top_border',
				'border_bottom' => 'topic_title_bottom_border',
			],
			'live_method'  => [
				'topic_title_top_border'    => [
					[
						'type'     => 'css_border',
						'selector' => '.topic-hero-section .entry-hero-container-inner',
						'pattern'  => '$',
						'property' => 'border-top',
						'key'      => 'border',
					],
				],
				'topic_title_bottom_border' => [ 
					[
						'type'     => 'css_border',
						'selector' => '.topic-hero-section .entry-hero-container-inner',
						'property' => 'border-bottom',
						'pattern'  => '$',
						'key'      => 'border',
					],
				],
			],
		],
		'info_topic_layout'               => [
			'control_type' => 'kadence_title_control',
			'section'      => 'topic_layout',
			'priority'     => 10,
			'label'        => esc_html__( 'Topic Layout', 'kadence' ),
			'settings'     => false,
		],
		'info_topic_layout_design'        => [
			'control_type' => 'kadence_title_control',
			'section'      => 'topic_layout_design',
			'priority'     => 10,
			'label'        => esc_html__( 'Topic Layout', 'kadence' ),
			'settings'     => false,
		],
		'topic_layout'                    => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'topic_layout',
			'label'        => esc_html__( 'Topic Layout', 'kadence' ),
			'transport'    => 'refresh',
			'priority'     => 10,
			'default'      => kadence()->default( 'topic_layout' ),
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
		'topic_sidebar_id'                => [
			'control_type' => 'kadence_select_control',
			'section'      => 'topic_layout',
			'label'        => esc_html__( 'Topic Default Sidebar', 'kadence' ),
			'transport'    => 'refresh',
			'priority'     => 10,
			'default'      => kadence()->default( 'topic_sidebar_id' ),
			'input_attrs'  => [
				'options' => kadence()->sidebar_options(),
			],
		],
		'topic_content_style'             => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'topic_layout',
			'label'        => esc_html__( 'Content Style', 'kadence' ),
			'priority'     => 10,
			'default'      => kadence()->default( 'topic_content_style' ),
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => 'body.single-topic',
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
		'topic_vertical_padding'          => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'topic_layout',
			'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
			'priority'     => 10,
			'default'      => kadence()->default( 'topic_vertical_padding' ),
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => 'body.single-forum',
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
		'topic_background'                => [
			'control_type' => 'kadence_background_control',
			'section'      => 'topic_layout_design',
			'priority'     => 20,
			'label'        => esc_html__( 'Site Background', 'kadence' ),
			'default'      => kadence()->default( 'topic_background' ),
			'live_method'  => [
				[
					'type'     => 'css_background',
					'selector' => 'body.single-topic',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				],
			],
			'input_attrs'  => [
				'tooltip' => __( 'Topic Background', 'kadence' ),
			],
		],
		'topic_content_background'        => [
			'control_type' => 'kadence_background_control',
			'section'      => 'topic_layout_design',
			'priority'     => 20,
			'label'        => esc_html__( 'Content Background', 'kadence' ),
			'default'      => kadence()->default( 'topic_content_background' ),
			'live_method'  => [
				[
					'type'     => 'css_background',
					'selector' => 'body.single-topic .content-bg, body.single-topic.content-style-unboxed .site',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				],
			],
			'input_attrs'  => [
				'tooltip' => __( 'Topic Content Background', 'kadence' ),
			],
		],
	]
);
