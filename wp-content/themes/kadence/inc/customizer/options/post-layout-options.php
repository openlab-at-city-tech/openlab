<?php
/**
 * Post Layout Options.
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$kadence_post_settings = [
	'post_layout_tabs'            => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'post_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'post_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'post_layout_design',
			],
			'active'  => 'general',
		],
	],
	'post_layout_tabs_design'     => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'post_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'post_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'post_layout_design',
			],
			'active'  => 'design',
		],
	],
	'info_post_title'             => [
		'control_type' => 'kadence_title_control',
		'section'      => 'post_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Post Title', 'kadence' ),
		'settings'     => false,
	],
	'info_post_title_design'      => [
		'control_type' => 'kadence_title_control',
		'section'      => 'post_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Post Title', 'kadence' ),
		'settings'     => false,
	],
	'post_title'                  => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'post_title' ),
		'label'        => esc_html__( 'Show Post Title?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'post_title_layout'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Post Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'post_title_layout' ),
		'context'      => [
			[
				'setting'  => 'post_title',
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
	'post_title_inner_layout'     => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'post_title_inner_layout' ),
		'label'        => esc_html__( 'Container Width', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'post_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'post_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.post-hero-section',
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
	'post_title_align'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Post Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'post_title_align' ),
		'context'      => [
			[
				'setting'  => 'post_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.post-title',
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
	'post_title_height'           => [
		'control_type' => 'kadence_range_control',
		'section'      => 'post_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Container Min Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'post_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'post_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#inner-wrap .post-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'post_title_height' ),
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
	'post_title_elements'         => [
		'control_type' => 'kadence_sorter_control',
		'section'      => 'post_layout',
		'priority'     => 6,
		'default'      => kadence()->default( 'post_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => [
			'elements'   => 'post_title_elements',
			'title'      => 'post_title_element_title',
			'breadcrumb' => 'post_title_element_breadcrumb',
			'meta'       => 'post_title_element_meta',
			'categories' => 'post_title_element_categories',
			'excerpt'    => 'post_title_element_excerpt',
		],
		'input_attrs'  => [
			'defaults' => [
				'categories' => kadence()->default( 'post_title_element_categories' ),
				'title'      => kadence()->default( 'post_title_element_title' ),
				'meta'       => kadence()->default( 'post_title_element_meta' ),
				'excerpt'    => kadence()->default( 'post_title_element_excerpt' ),
				'breadcrumb' => kadence()->default( 'post_title_element_breadcrumb' ),
			],
			'dividers' => [
				'dot'        => [
					'icon' => 'dot',
				],
				'slash'      => [
					'icon' => 'slash',
				],
				'dash'       => [
					'icon' => 'dash',
				],
				'vline'      => [
					'icon' => 'vline',
				],
				'customicon' => [
					'icon' => 'hours',
				],
			],
		],
	],
	'post_title_font'             => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Post Title Font', 'kadence' ),
		'default'      => kadence()->default( 'post_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.wp-site-blocks .post-title h1',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'             => 'post_title_font',
			'headingInherit' => true,
		],
	],
	'post_title_category_color'   => [
		'control_type' => 'kadence_color_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Category Colors', 'kadence' ),
		'default'      => kadence()->default( 'post_title_category_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.post-title .entry-taxonomies, .post-title .entry-taxonomies a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.post-title .entry-taxonomies a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '.post-title .entry-taxonomies .category-style-pill a',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.post-title .entry-taxonomies .category-style-pill a:hover',
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
					'tooltip' => __( 'Link Hover Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'post_title_category_font'    => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Category Font', 'kadence' ),
		'default'      => kadence()->default( 'post_title_category_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.post-title .entry-taxonomies, .post-title .entry-taxonomies a',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'post_title_category_font',
			'options' => 'no-color',
		],
	],
	'post_title_breadcrumb_color' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'post_title_breadcrumb_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.post-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.post-title .kadence-breadcrumbs a:hover',
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
	'post_title_breadcrumb_font'  => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
		'default'      => kadence()->default( 'post_title_breadcrumb_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.post-title .kadence-breadcrumbs',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'post_title_breadcrumb_font',
			'options' => 'no-color',
		],
	],
	'post_title_meta_color'       => [
		'control_type' => 'kadence_color_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Meta Colors', 'kadence' ),
		'default'      => kadence()->default( 'post_title_meta_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.post-title .entry-meta',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.post-title .entry-meta a:hover',
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
	'post_title_meta_font'        => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Meta Font', 'kadence' ),
		'default'      => kadence()->default( 'post_title_meta_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.post-title .entry-meta',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'post_title_meta_font',
			'options' => 'no-color',
		],
	],
	'post_title_excerpt_color'    => [
		'control_type' => 'kadence_color_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Excerpt Colors', 'kadence' ),
		'default'      => kadence()->default( 'post_title_meta_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.post-title .title-entry-excerpt',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.post-title .title-entry-excerpt a:hover',
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
	'post_title_excerpt_font'     => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Excerpt Font', 'kadence' ),
		'default'      => kadence()->default( 'post_title_excerpt_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.post-title .title-entry-excerpt',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'post_title_excerpt_font',
			'options' => 'no-color',
		],
	],
	'post_title_background'       => [
		'control_type' => 'kadence_background_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Post Title Background', 'kadence' ),
		'default'      => kadence()->default( 'post_title_background' ),
		'context'      => [
			[
				'setting'  => 'post_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'post_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#inner-wrap .post-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Post Title Background', 'kadence' ),
		],
	],
	'post_title_featured_image'   => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout_design',
		'default'      => kadence()->default( 'post_title_featured_image' ),
		'label'        => esc_html__( 'Use Featured Image for Background?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'post_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'post_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
	],
	'post_title_overlay_color'    => [
		'control_type' => 'kadence_color_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'post_title_overlay_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.post-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting'  => 'post_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'post_title_layout',
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
	'post_title_border'           => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'post_title_border' ),
		'context'      => [
			[
				'setting'  => 'post_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'post_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'settings'     => [
			'border_top'    => 'post_title_top_border',
			'border_bottom' => 'post_title_bottom_border',
		],
		'live_method'  => [
			'post_title_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => '.post-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				],
			],
			'post_title_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => '.post-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'info_post_layout'            => [
		'control_type' => 'kadence_title_control',
		'section'      => 'post_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Default Post Layout', 'kadence' ),
		'settings'     => false,
	],
	'info_post_layout_design'     => [
		'control_type' => 'kadence_title_control',
		'section'      => 'post_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Default Post Layout', 'kadence' ),
		'settings'     => false,
	],
	'post_layout'                 => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Default Post Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'post_layout' ),
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
	'post_sidebar_id'             => [
		'control_type' => 'kadence_select_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Post Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'post_sidebar_id' ),
		'input_attrs'  => [
			'options' => kadence()->sidebar_options(),
		],
	],
	'post_content_style'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'post_content_style' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single',
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
	'post_vertical_padding'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Content Vertical Spacing', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'post_vertical_padding' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single',
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
	'post_feature'                => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'post_feature' ),
		'label'        => esc_html__( 'Show Featured Image?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'post_feature_position'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Featured Image Position', 'kadence' ),
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'post_feature_position' ),
		'context'      => [
			[
				'setting'  => 'post_feature',
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
	'post_feature_width'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Featured Image Width', 'kadence' ),
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'post_feature_width' ),
		'context'      => [
			[
				'setting'  => 'post_feature',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'post_feature_position',
				'operator' => '=',
				'value'    => 'behind',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'wide' => [
					'name' => __( 'Wide', 'kadence' ),
				],
				'full' => [
					'name' => __( 'Stretch Full', 'kadence' ),
				],
			],
			'responsive' => false,
		],
	],
	'post_feature_caption'        => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'post_feature_caption' ),
		'label'        => esc_html__( 'Show Featured Image Caption?', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'post_feature',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'post_feature_position',
				'operator' => '!=',
				'value'    => 'behind',
			],
		],
		'transport'    => 'refresh',
	],
	'post_feature_ratio'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Featured Image Ratio', 'kadence' ),
		'priority'     => 20,
		'default'      => kadence()->default( 'post_feature_ratio' ),
		'context'      => [
			[
				'setting'  => 'post_feature',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single .article-post-thumbnail',
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
	'post_tags'                   => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'post_tags' ),
		'label'        => esc_html__( 'Show Post Tags?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'post_footer_area_boxed'      => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'post_footer_area_boxed' ),
		'label'        => esc_html__( 'Show Footer Area in Boxed Mode?', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'post_content_style',
				'operator' => '=',
				'value'    => 'boxed',
			],
		],
	],
	'post_author_box'             => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'post_author_box' ),
		'label'        => esc_html__( 'Show Post Author Box?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'post_author_box_style'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Author Box Style', 'kadence' ),
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'post_author_box_style' ),
		'context'      => [
			[
				'setting'  => 'post_author_box',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'normal' => [
					'name' => __( 'Normal', 'kadence' ),
				],
				'center' => [
					'name' => __( 'Center', 'kadence' ),
				],
			],
			'responsive' => false,
		],
	],
	'post_author_box_link'        => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'post_author_box_link' ),
		'label'        => esc_html__( 'Use Author Archive Link?', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'post_author_box',
				'operator' => '=',
				'value'    => true,
			],
		],
	],
	'post_navigation'             => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'post_navigation' ),
		'label'        => esc_html__( 'Show Post Navigation?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'post_related'                => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'post_related' ),
		'label'        => esc_html__( 'Show Related Posts?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'post_related_carousel_dots'  => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'post_related_carousel_dots' ),
		'label'        => esc_html__( 'Show Related Posts Pagination Dots?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'post_related',
				'operator' => '=',
				'value'    => true,
			],
		],
	],
	'post_related_columns'        => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_layout',
		'priority'     => 20,
		'label'        => esc_html__( 'Related Posts Columns', 'kadence' ),
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'post_related_columns' ),
		'context'      => [
			[
				'setting'  => 'post_related',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'2' => [
					'name' => __( '2', 'kadence' ),
				],
				'3' => [
					'name' => __( '3', 'kadence' ),
				],
				'4' => [
					'name' => __( '4', 'kadence' ),
				],
			],
			'responsive' => false,
		],
	],
	'post_related_title'          => [
		'control_type' => 'kadence_text_control',
		'section'      => 'post_layout',
		'priority'     => 20,
		'sanitize'     => 'sanitize_text_field',
		'label'        => esc_html__( 'Related Posts Title', 'kadence' ),
		'default'      => kadence()->default( 'post_related_title' ),
		'partial'      => [
			'selector'            => '.entry-related-title',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\related_posts_title',
		],
		'context'      => [
			[
				'setting'  => 'post_related',
				'operator' => '=',
				'value'    => true,
			],
		],
	],
	'post_related_orderby'        => [
		'control_type' => 'kadence_select_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Related Posts Order By', 'kadence' ),
		'priority'     => 20,
		'default'      => kadence()->default( 'post_related_orderby' ),
		'input_attrs'  => [
			'options' => [
				''              => [
					'name' => __( 'Random (default)', 'kadence' ),
				],
				'ID'            => [
					'name' => __( 'Post ID', 'kadence' ),
				],
				'author'        => [
					'name' => __( 'Author', 'kadence' ),
				],
				'title'         => [
					'name' => __( 'Post Title', 'kadence' ),
				],
				'name'          => [
					'name' => __( 'Post Slug', 'kadence' ),
				],
				'date'          => [
					'name' => __( 'Post Date', 'kadence' ),
				],
				'modified'      => [
					'name' => __( 'Date Modified', 'kadence' ),
				],
				'parent'        => [
					'name' => __( 'Parent Post ID', 'kadence' ),
				],
				'comment_count' => [
					'name' => __( 'Comment Count', 'kadence' ),
				],
				'menu_order'    => [
					'name' => __( 'Menu Order', 'kadence' ),
				],
			],
		],
	],
	'post_related_order'          => [
		'control_type' => 'kadence_select_control',
		'section'      => 'post_layout',
		'label'        => esc_html__( 'Related Posts Order', 'kadence' ),
		'priority'     => 20,
		'default'      => kadence()->default( 'post_related_order' ),
		'input_attrs'  => [
			'options' => [
				''    => [
					'name' => __( 'Descending (default)', 'kadence' ),
				],
				'ASC' => [
					'name' => __( 'Ascending', 'kadence' ),
				],
			],
		],
	],
	'post_related_carousel_loop'  => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'post_related_carousel_loop' ),
		'label'        => esc_html__( 'Endlessly Loop Related Carousel?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'post_related',
				'operator' => '=',
				'value'    => true,
			],
		],
	],
	'post_comments'               => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'post_comments' ),
		'label'        => esc_html__( 'Show Comments?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'post_comments_date'          => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_layout',
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'post_comments_date' ),
		'label'        => esc_html__( 'Show Comment Date?', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'post_comments',
				'operator' => '=',
				'value'    => true,
			],
		],
	],
	'post_background'             => [
		'control_type' => 'kadence_background_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'post_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Post Background', 'kadence' ),
		],
	],
	'post_content_background'     => [
		'control_type' => 'kadence_background_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'post_content_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single .content-bg, body.single.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Post Content Background', 'kadence' ),
		],
	],
	'info_post_related_design'    => [
		'control_type' => 'kadence_title_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Related Posts Section', 'kadence' ),
		'settings'     => false,
		'context'      => [
			[
				'setting'  => 'post_related',
				'operator' => '=',
				'value'    => true,
			],
		],
	],
	'post_related_title_font'     => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Related Posts Section Title Font', 'kadence' ),
		'default'      => kadence()->default( 'post_related_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.wp-site-blocks .entry-related h2.entry-related-title',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'             => 'post_related_title_font',
			'headingInherit' => true,
		],
		'context'      => [
			[
				'setting'  => 'post_related',
				'operator' => '=',
				'value'    => true,
			],
		],
	],
	'post_related_background'     => [
		'control_type' => 'kadence_background_control',
		'section'      => 'post_layout_design',
		'label'        => esc_html__( 'Related Posts Section Background', 'kadence' ),
		'default'      => kadence()->default( 'post_related_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single .entry-related',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'context'      => [
			[
				'setting'  => 'post_related',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Related Posts Background', 'kadence' ),
		],
	],
];

Theme_Customizer::add_settings( $kadence_post_settings );
