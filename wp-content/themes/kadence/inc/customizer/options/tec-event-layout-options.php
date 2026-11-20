<?php
/**
 * Event Layout Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'tribe_events_layout_tabs'            => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'tribe_events_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'tribe_events_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'tribe_events_layout_design',
			],
			'active'  => 'general',
		],
	],
	'tribe_events_layout_tabs_design'     => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'tribe_events_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'tribe_events_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'tribe_events_layout_design',
			],
			'active'  => 'design',
		],
	],
	'info_tribe_events_title'             => [
		'control_type' => 'kadence_title_control',
		'section'      => 'tribe_events_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Event Title', 'kadence' ),
		'settings'     => false,
	],
	'info_tribe_events_title_design'      => [
		'control_type' => 'kadence_title_control',
		'section'      => 'tribe_events_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Event Title', 'kadence' ),
		'settings'     => false,
	],
	'tribe_events_title'                  => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'tribe_events_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'tribe_events_title' ),
		'label'        => esc_html__( 'Show Event Title?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'tribe_events_title_layout'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'tribe_events_layout',
		'label'        => esc_html__( 'Event Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'tribe_events_title_layout' ),
		'context'      => [
			[
				'setting'  => 'tribe_events_title',
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
	'tribe_events_title_inner_layout'     => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'tribe_events_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'tribe_events_title_inner_layout' ),
		'label'        => esc_html__( 'Title Container Width', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'tribe_events_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'tribe_events_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.tribe_events-hero-section',
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
	'tribe_events_title_align'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'tribe_events_layout',
		'label'        => esc_html__( 'Event Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'tribe_events_title_align' ),
		'context'      => [
			[
				'setting'  => 'tribe_events_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.tribe_events-title',
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
	'tribe_events_title_height'           => [
		'control_type' => 'kadence_range_control',
		'section'      => 'tribe_events_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Title Container Min Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'tribe_events_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'tribe_events_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#inner-wrap .tribe_events-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'tribe_events_title_height' ),
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
	'tribe_events_title_elements'         => [
		'control_type' => 'kadence_sorter_control',
		'section'      => 'tribe_events_layout',
		'priority'     => 6,
		'default'      => kadence()->default( 'tribe_events_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => [
			'elements'   => 'tribe_events_title_elements',
			'title'      => 'tribe_events_title_element_title',
			'back_link'  => 'tribe_events_title_element_back_link',
			'breadcrumb' => 'tribe_events_title_element_breadcrumb',
		],
		'context'      => [
			[
				'setting'  => 'tribe_events_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'group' => 'tribe_events_title_element',
		],
	],
	'tribe_events_title_font'             => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'tribe_events_layout_design',
		'label'        => esc_html__( 'Event Title Font', 'kadence' ),
		'default'      => kadence()->default( 'tribe_events_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.tribe_events-title h1',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting'  => 'tribe_events_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'id'             => 'tribe_events_title_font',
			'headingInherit' => true,
		],
	],
	'tribe_events_title_breadcrumb_color' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'tribe_events_layout_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'tribe_events_title_breadcrumb_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.tribe_events-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.tribe_events-title .kadence-breadcrumbs a:hover',
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
	'tribe_events_title_breadcrumb_font'  => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'tribe_events_layout_design',
		'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
		'default'      => kadence()->default( 'tribe_events_title_breadcrumb_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.tribe_events-title .kadence-breadcrumbs',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'tribe_events_title_breadcrumb_font',
			'options' => 'no-color',
		],
	],
	'tribe_events_title_back_link_color'  => [
		'control_type' => 'kadence_color_control',
		'section'      => 'tribe_events_layout_design',
		'label'        => esc_html__( 'Back Link Colors', 'kadence' ),
		'default'      => kadence()->default( 'tribe_events_title_back_link_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.tribe_events-title .tribe-events-back a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.tribe_events-title .tribe-events-back a:hover',
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
					'tooltip' => __( 'Hover Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'tribe_events_title_back_link_font'   => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'tribe_events_layout_design',
		'label'        => esc_html__( 'Back Link Font', 'kadence' ),
		'default'      => kadence()->default( 'tribe_events_title_back_link_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.tribe_events-title .tribe-events-back a',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'tribe_events_title_back_link_font',
			'options' => 'no-color',
		],
	],
	'tribe_events_title_background'       => [
		'control_type' => 'kadence_background_control',
		'section'      => 'tribe_events_layout_design',
		'label'        => esc_html__( 'Event Above Area Background', 'kadence' ),
		'default'      => kadence()->default( 'tribe_events_title_background' ),
		'context'      => [
			[
				'setting'  => 'tribe_events_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'tribe_events_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#inner-wrap .tribe_events-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Event Title Background', 'kadence' ),
		],
	],
	'tribe_events_title_featured_image'   => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'tribe_events_layout_design',
		'default'      => kadence()->default( 'tribe_events_title_featured_image' ),
		'label'        => esc_html__( 'Use Featured Image for Background?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'tribe_events_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'tribe_events_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
	],
	'tribe_events_title_overlay_color'    => [
		'control_type' => 'kadence_color_control',
		'section'      => 'tribe_events_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'tribe_events_title_overlay_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.tribe_events-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting'  => 'tribe_events_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'tribe_events_title_layout',
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
	'tribe_events_title_border'           => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'tribe_events_layout_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'tribe_events_title_border' ),
		'context'      => [
			[
				'setting'  => 'tribe_events_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'tribe_events_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'settings'     => [
			'border_top'    => 'tribe_events_title_top_border',
			'border_bottom' => 'tribe_events_title_bottom_border',
		],
		'live_method'  => [
			'tribe_events_title_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => '.tribe_events-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				],
			],
			'tribe_events_title_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => '.tribe_events-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'info_tribe_events_layout'            => [
		'control_type' => 'kadence_title_control',
		'section'      => 'tribe_events_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Event Layout', 'kadence' ),
		'settings'     => false,
	],
	'info_tribe_events_layout_design'     => [
		'control_type' => 'kadence_title_control',
		'section'      => 'tribe_events_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Event Layout', 'kadence' ),
		'settings'     => false,
	],
	'tribe_events_layout'                 => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'tribe_events_layout',
		'label'        => esc_html__( 'Event Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'tribe_events_layout' ),
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
	'tribe_events_sidebar_id'             => [
		'control_type' => 'kadence_select_control',
		'section'      => 'tribe_events_layout',
		'label'        => esc_html__( 'Event Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'tribe_events_sidebar_id' ),
		'input_attrs'  => [
			'options' => kadence()->sidebar_options(),
		],
	],
	'tribe_events_content_style'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'tribe_events_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'tribe_events_content_style' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-tribe_events',
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
	'tribe_events_vertical_padding'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'tribe_events_layout',
		'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'tribe_events_vertical_padding' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-tribe_events',
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
	// 'tribe_events_feature' => array(
	// 'control_type' => 'kadence_switch_control',
	// 'sanitize'     => 'kadence_sanitize_toggle',
	// 'section'      => 'tribe_events_layout',
	// 'priority'     => 20,
	// 'default'      => kadence()->default( 'tribe_events_feature' ),
	// 'label'        => esc_html__( 'Show Featured Image?', 'kadence' ),
	// 'transport'    => 'refresh',
	// ),
	// 'tribe_events_feature_position' => array(
	// 'control_type' => 'kadence_radio_icon_control',
	// 'section'      => 'tribe_events_layout',
	// 'label'        => esc_html__( 'Featured Image Position', 'kadence' ),
	// 'priority'     => 20,
	// 'transport'    => 'refresh',
	// 'default'      => kadence()->default( 'tribe_events_feature_position' ),
	// 'context'      => array(
	// array(
	// 'setting'    => 'tribe_events_feature',
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
	// 'tribe_events_feature_ratio' => array(
	// 'control_type' => 'kadence_radio_icon_control',
	// 'section'      => 'tribe_events_layout',
	// 'label'        => esc_html__( 'Featured Image Ratio', 'kadence' ),
	// 'priority'     => 20,
	// 'default'      => kadence()->default( 'tribe_events_feature_ratio' ),
	// 'context'      => array(
	// array(
	// 'setting'    => 'tribe_events_feature',
	// 'operator'   => '=',
	// 'value'      => true,
	// ),
	// ),
	// 'live_method'     => array(
	// array(
	// 'type'     => 'class',
	// 'selector' => 'body.single-tribe_events .article-post-thumbnail',
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
	// 'name' => __( '4:3', 'kadence' ),
	// ),
	// '2-3' => array(
	// 'name' => __( '3:2', 'kadence' ),
	// ),
	// '9-16' => array(
	// 'name' => __( '16:9', 'kadence' ),
	// ),
	// '1-2' => array(
	// 'name' => __( '2:1', 'kadence' ),
	// ),
	// ),
	// 'responsive' => false,
	// 'class' => 'kadence-three-col-short',
	// ),
	// ),
	'tribe_events_background'             => [
		'control_type' => 'kadence_background_control',
		'section'      => 'tribe_events_layout_design',
		'priority'     => 20,
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'tribe_events_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-tribe_events',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Event Background', 'kadence' ),
		],
	],
	'tribe_events_content_background'     => [
		'control_type' => 'kadence_background_control',
		'section'      => 'tribe_events_layout_design',
		'priority'     => 20,
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'tribe_events_content_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-tribe_events .content-bg, body.single-tribe_events.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Event Content Background', 'kadence' ),
		],
	],
];

Theme_Customizer::add_settings( $settings );
