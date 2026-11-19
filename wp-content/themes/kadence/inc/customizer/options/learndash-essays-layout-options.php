<?php
/**
 * Essay Layout Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'sfwd_essays_layout_tabs'            => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'sfwd_essays_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'sfwd_essays_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'sfwd_essays_layout_design',
			],
			'active'  => 'general',
		],
	],
	'sfwd_essays_layout_tabs_design'     => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'sfwd_essays_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'sfwd_essays_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'sfwd_essays_layout_design',
			],
			'active'  => 'design',
		],
	],
	'info_essays_title'                  => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_essays_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Essay Title', 'kadence' ),
		'settings'     => false,
	],
	'info_essays_title_design'           => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_essays_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Essay Title', 'kadence' ),
		'settings'     => false,
	],
	'sfwd-essays_title'                  => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_essays_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'sfwd-essays_title' ),
		'label'        => esc_html__( 'Show Essay Title?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'sfwd-essays_title_layout'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_essays_layout',
		'label'        => esc_html__( 'Essay Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-essays_title_layout' ),
		'context'      => [
			[
				'setting'  => 'sfwd-essays_title',
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
	'sfwd-essays_title_inner_layout'     => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_essays_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-essays_title_inner_layout' ),
		'label'        => esc_html__( 'Title Container Width', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'sfwd-essays_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-essays_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.sfwd-essays-hero-section',
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
	'sfwd-essays_title_align'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_essays_layout',
		'label'        => esc_html__( 'Essay Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-essays_title_align' ),
		'context'      => [
			[
				'setting'  => 'sfwd-essays_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.sfwd-essays-title',
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
	'sfwd-essays_title_height'           => [
		'control_type' => 'kadence_range_control',
		'section'      => 'sfwd_essays_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Title Container Min Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'sfwd-essays_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-essays_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#inner-wrap .sfwd-essays-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'sfwd-essays_title_height' ),
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
	'sfwd-essays_title_elements'         => [
		'control_type' => 'kadence_sorter_control',
		'section'      => 'sfwd_essays_layout',
		'priority'     => 6,
		'default'      => kadence()->default( 'sfwd-essays_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => [
			'elements'   => 'sfwd-essays_title_elements',
			'title'      => 'sfwd-essays_title_element_title',
			'breadcrumb' => 'sfwd-essays_title_element_breadcrumb',
		],
		'context'      => [
			[
				'setting'  => 'sfwd-essays_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'group' => 'sfwd-essays_title_element',
		],
	],
	'sfwd-essays_title_font'             => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'sfwd_essays_layout_design',
		'label'        => esc_html__( 'Essay Title Font', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-essays_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.sfwd-essays-title h1',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting'  => 'sfwd-essays_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'id'             => 'sfwd-essays_title_font',
			'headingInherit' => true,
		],
	],
	'sfwd-essays_title_breadcrumb_color' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'sfwd_essays_layout_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-essays_title_breadcrumb_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.sfwd-essays-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.sfwd-essays-title .kadence-breadcrumbs a:hover',
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
	'sfwd-essays_title_breadcrumb_font'  => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'sfwd_essays_layout_design',
		'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-essays_title_breadcrumb_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.sfwd-essays-title .kadence-breadcrumbs',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'sfwd-essays_title_breadcrumb_font',
			'options' => 'no-color',
		],
	],
	'sfwd-essays_title_background'       => [
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_essays_layout_design',
		'label'        => esc_html__( 'Essay Above Area Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-essays_title_background' ),
		'context'      => [
			[
				'setting'  => 'sfwd-essays_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-essays_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#inner-wrap .sfwd-essays-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Essay Title Background', 'kadence' ),
		],
	],
	'sfwd-essays_title_featured_image'   => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_essays_layout_design',
		'default'      => kadence()->default( 'sfwd-essays_title_featured_image' ),
		'label'        => esc_html__( 'Use Featured Image for Background?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'sfwd-essays_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-essays_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
	],
	'sfwd-essays_title_overlay_color'    => [
		'control_type' => 'kadence_color_control',
		'section'      => 'sfwd_essays_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-essays_title_overlay_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.sfwd-essays-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting'  => 'sfwd-essays_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-essays_title_layout',
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
	'sfwd-essays_title_border'           => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'sfwd_essays_layout_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-essays_title_border' ),
		'context'      => [
			[
				'setting'  => 'sfwd-essays_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-essays_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'settings'     => [
			'border_top'    => 'sfwd-essays_title_top_border',
			'border_bottom' => 'sfwd-essays_title_bottom_border',
		],
		'live_method'  => [
			'sfwd-essays_title_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => '.sfwd-essays-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				],
			],
			'sfwd-essays_title_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => '.sfwd-essays-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'info_sfwd_essays_layout'            => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_essays_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Essay Layout', 'kadence' ),
		'settings'     => false,
	],
	'info_sfwd_essays_layout_design'     => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_essays_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Essay Layout', 'kadence' ),
		'settings'     => false,
	],
	'sfwd-essays_layout'                 => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_essays_layout',
		'label'        => esc_html__( 'Essay Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-essays_layout' ),
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
	'sfwd-essays_sidebar_id'             => [
		'control_type' => 'kadence_select_control',
		'section'      => 'sfwd_essays_layout',
		'label'        => esc_html__( 'Essay Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-essays_sidebar_id' ),
		'input_attrs'  => [
			'options' => kadence()->sidebar_options(),
		],
	],
	'sfwd-essays_content_style'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_essays_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-essays_content_style' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-sfwd-essays',
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
	'sfwd-essays_vertical_padding'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_essays_layout',
		'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-essays_vertical_padding' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-sfwd-essays',
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
	'sfwd-essays_feature'                => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_essays_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'sfwd-essays_feature' ),
		'label'        => esc_html__( 'Show Featured Image?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'sfwd-essays_feature_position'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_essays_layout',
		'label'        => esc_html__( 'Featured Image Position', 'kadence' ),
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'sfwd-essays_feature_position' ),
		'context'      => [
			[
				'setting'  => 'sfwd-essays_feature',
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
	'sfwd-essays_feature_ratio'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_essays_layout',
		'label'        => esc_html__( 'Featured Image Ratio', 'kadence' ),
		'priority'     => 20,
		'default'      => kadence()->default( 'sfwd-essays_feature_ratio' ),
		'context'      => [
			[
				'setting'  => 'sfwd-essays_feature',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-sfwd-essays .article-post-thumbnail',
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
	'sfwd-essays_comments'               => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_essays_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'sfwd-essays_comments' ),
		'label'        => esc_html__( 'Show Comments?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'sfwd-essays_background'             => [
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_essays_layout_design',
		'priority'     => 20,
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-essays_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-sfwd-essays',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Essay Background', 'kadence' ),
		],
	],
	'sfwd-essays_content_background'     => [
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_essays_layout_design',
		'priority'     => 20,
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-essays_content_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-sfwd-essays .content-bg, body.single-sfwd-essays.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Essay Content Background', 'kadence' ),
		],
	],
];

Theme_Customizer::add_settings( $settings );
