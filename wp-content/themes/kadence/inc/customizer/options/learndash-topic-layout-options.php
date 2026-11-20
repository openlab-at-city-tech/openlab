<?php
/**
 * Topic Layout Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'sfwd-topic_layout_tabs'            => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'sfwd_topic_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'sfwd_topic_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'sfwd_topic_layout_design',
			],
			'active'  => 'general',
		],
	],
	'sfwd-topic_layout_tabs_design'     => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'sfwd_topic_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'sfwd_topic_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'sfwd_topic_layout_design',
			],
			'active'  => 'design',
		],
	],
	'info_sfwd-topic_title'             => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_topic_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Topic Title', 'kadence' ),
		'settings'     => false,
	],
	'info_sfwd-topic_title_design'      => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_topic_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Topic Title', 'kadence' ),
		'settings'     => false,
	],
	'sfwd-topic_title'                  => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_topic_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'sfwd-topic_title' ),
		'label'        => esc_html__( 'Show Topic Title?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'sfwd-topic_title_layout'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_topic_layout',
		'label'        => esc_html__( 'Topic Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-topic_title_layout' ),
		'context'      => [
			[
				'setting'  => 'sfwd-topic_title',
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
	'sfwd-topic_title_inner_layout'     => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_topic_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-topic_title_inner_layout' ),
		'label'        => esc_html__( 'Title Container Width', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'sfwd-topic_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-topic_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.sfwd-topic-hero-section',
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
	'sfwd-topic_title_align'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_topic_layout',
		'label'        => esc_html__( 'Topic Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'sfwd-topic_title_align' ),
		'context'      => [
			[
				'setting'  => 'sfwd-topic_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.sfwd-topic-title',
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
	'sfwd-topic_title_height'           => [
		'control_type' => 'kadence_range_control',
		'section'      => 'sfwd_topic_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Title Container Min Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'sfwd-topic_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-topic_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#inner-wrap .sfwd-topic-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'sfwd-topic_title_height' ),
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
	'sfwd-topic_title_elements'         => [
		'control_type' => 'kadence_sorter_control',
		'section'      => 'sfwd_topic_layout',
		'priority'     => 6,
		'default'      => kadence()->default( 'sfwd-topic_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => [
			'elements'   => 'sfwd-topic_title_elements',
			'title'      => 'sfwd-topic_title_element_title',
			'breadcrumb' => 'sfwd-topic_title_element_breadcrumb',
		],
		'context'      => [
			[
				'setting'  => 'sfwd-topic_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'group' => 'sfwd-topic_title_element',
		],
	],
	'sfwd-topic_title_font'             => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'sfwd_topic_layout_design',
		'label'        => esc_html__( 'Topic Title Font', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-topic_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.sfwd-topic-title h1',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting'  => 'sfwd-topic_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'id'             => 'sfwd-topic_title_font',
			'headingInherit' => true,
		],
	],
	'sfwd-topic_title_breadcrumb_color' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'sfwd_topic_layout_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-topic_title_breadcrumb_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.sfwd-topic-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.sfwd-topic-title .kadence-breadcrumbs a:hover',
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
	'sfwd-topic_title_breadcrumb_font'  => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'sfwd_topic_layout_design',
		'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-topic_title_breadcrumb_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.sfwd-topic-title .kadence-breadcrumbs',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'sfwd-topic_title_breadcrumb_font',
			'options' => 'no-color',
		],
	],
	'sfwd-topic_title_background'       => [
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_topic_layout_design',
		'label'        => esc_html__( 'Topic Above Area Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-topic_title_background' ),
		'context'      => [
			[
				'setting'  => 'sfwd-topic_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-topic_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#inner-wrap .sfwd-topic-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Topic Title Background', 'kadence' ),
		],
	],
	'sfwd-topic_title_featured_image'   => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_topic_layout_design',
		'default'      => kadence()->default( 'sfwd-topic_title_featured_image' ),
		'label'        => esc_html__( 'Use Featured Image for Background?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'sfwd-topic_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-topic_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
	],
	'sfwd-topic_title_overlay_color'    => [
		'control_type' => 'kadence_color_control',
		'section'      => 'sfwd_topic_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-topic_title_overlay_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.sfwd-topic-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting'  => 'sfwd-topic_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-topic_title_layout',
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
	'sfwd-topic_title_border'           => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'sfwd_topic_layout_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-topic_title_border' ),
		'context'      => [
			[
				'setting'  => 'sfwd-topic_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'sfwd-topic_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'settings'     => [
			'border_top'    => 'sfwd-topic_title_top_border',
			'border_bottom' => 'sfwd-topic_title_bottom_border',
		],
		'live_method'  => [
			'sfwd-topic_title_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => '.sfwd-topic-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				],
			],
			'sfwd-topic_title_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => '.sfwd-topic-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'info_sfwd-topic_layout'            => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_topic_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Topic Layout', 'kadence' ),
		'settings'     => false,
	],
	'info_sfwd-topic_layout_design'     => [
		'control_type' => 'kadence_title_control',
		'section'      => 'sfwd_topic_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Topic Layout', 'kadence' ),
		'settings'     => false,
	],
	'sfwd-topic_layout'                 => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_topic_layout',
		'label'        => esc_html__( 'Topic Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-topic_layout' ),
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
	'sfwd-topic_sidebar_id'             => [
		'control_type' => 'kadence_select_control',
		'section'      => 'sfwd_topic_layout',
		'label'        => esc_html__( 'Topic Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-topic_sidebar_id' ),
		'input_attrs'  => [
			'options' => kadence()->sidebar_options(),
		],
	],
	'sfwd-topic_content_style'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_topic_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-topic_content_style' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-sfwd-topic',
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
	'sfwd-topic_vertical_padding'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_topic_layout',
		'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'sfwd-topic_vertical_padding' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-sfwd-topic',
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
	'sfwd-topic_feature'                => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_topic_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'sfwd-topic_feature' ),
		'label'        => esc_html__( 'Show Featured Image?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'sfwd-topic_feature_position'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_topic_layout',
		'label'        => esc_html__( 'Featured Image Position', 'kadence' ),
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'sfwd-topic_feature_position' ),
		'context'      => [
			[
				'setting'  => 'sfwd-topic_feature',
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
	'sfwd-topic_feature_ratio'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_topic_layout',
		'label'        => esc_html__( 'Featured Image Ratio', 'kadence' ),
		'priority'     => 20,
		'default'      => kadence()->default( 'sfwd-topic_feature_ratio' ),
		'context'      => [
			[
				'setting'  => 'sfwd-topic_feature',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-sfwd-topic .article-post-thumbnail',
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
	'sfwd-topic_background'             => [
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_topic_layout_design',
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-topic_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-sfwd-topic',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Topic Background', 'kadence' ),
		],
	],
	'sfwd-topic_content_background'     => [
		'control_type' => 'kadence_background_control',
		'section'      => 'sfwd_topic_layout_design',
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-topic_content_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-sfwd-topic .content-bg, body.single-sfwd-topic.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Topic Content Background', 'kadence' ),
		],
	],
];

Theme_Customizer::add_settings( $settings );
