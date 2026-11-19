<?php
/**
 * Header Main Row Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

ob_start(); ?>
<div class="kadence-compontent-tabs nav-tab-wrapper wp-clearfix">
	<a href="#" class="nav-tab kadence-general-tab kadence-compontent-tabs-button nav-tab-active" data-tab="general">
		<span><?php esc_html_e( 'General', 'kadence' ); ?></span>
	</a>
	<a href="#" class="nav-tab kadence-design-tab kadence-compontent-tabs-button" data-tab="design">
		<span><?php esc_html_e( 'Design', 'kadence' ); ?></span>
	</a>
</div>
<?php
$compontent_tabs = ob_get_clean();
$settings        = [
	'page_layout_tabs'            => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'page_layout',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	],
	'info_page_title'             => [
		'control_type' => 'kadence_title_control',
		'section'      => 'page_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Page Title', 'kadence' ),
		'settings'     => false,
	],
	'page_title'                  => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'page_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'page_title' ),
		'label'        => esc_html__( 'Show Page Title?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
	],
	'page_title_layout'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Page Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'page_title_layout' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'page_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'normal' => [
					'name' => __( 'In Content', 'kadence' ),
					'icon' => 'incontent',
				],
				'above'  => [
					'name' => __( 'Above Content', 'kadence' ),
					'icon' => 'abovecontent',
				],
			],
			'responsive' => false,
			'class'      => 'kadence-two-col',
		],
	],
	'page_title_inner_layout'     => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'page_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'page_title_inner_layout' ),
		'label'        => esc_html__( 'Title Container Width', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'page_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'page_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.page-hero-section',
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
	'page_title_align'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Page Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'page_title_align' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'page_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.page-title',
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
	'page_title_height'           => [
		'control_type' => 'kadence_range_control',
		'section'      => 'page_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Title Container Min Height', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'page_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'page_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#inner-wrap .page-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'page_title_height' ),
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
	'page_title_elements'         => [
		'control_type' => 'kadence_sorter_control',
		'section'      => 'page_layout',
		'priority'     => 6,
		'default'      => kadence()->default( 'page_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => [
			'elements'   => 'page_title_elements',
			'title'      => 'page_title_element_title',
			'breadcrumb' => 'page_title_element_breadcrumb',
			'meta'       => 'page_title_element_meta',
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'page_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'defaults' => [
				'title'      => kadence()->default( 'page_title_element_title' ),
				'meta'       => kadence()->default( 'page_title_element_meta' ),
				'breadcrumb' => kadence()->default( 'page_title_element_breadcrumb' ),
			],
			'group'    => 'page_title_element',
		],
		// 'partial'      => array(
		// 'selector'            => '.page-title',
		// 'container_inclusive' => false,
		// 'render_callback'     => 'Kadence\kadence_entry_header',
		// ),
	],
	'page_title_font'             => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Page Title Font', 'kadence' ),
		'default'      => kadence()->default( 'page_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.wp-site-blocks .page-title h1',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'  => 'page_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'id'             => 'page_title_font',
			'headingInherit' => true,
		],
	],
	'page_title_breadcrumb_color' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'page_title_breadcrumb_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.page-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.page-title .kadence-breadcrumbs a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
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
	'page_title_breadcrumb_font'  => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
		'default'      => kadence()->default( 'page_title_breadcrumb_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.page-title .kadence-breadcrumbs',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'input_attrs'  => [
			'id'      => 'page_title_breadcrumb_font',
			'options' => 'no-color',
		],
	],
	'page_title_meta_color'       => [
		'control_type' => 'kadence_color_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Meta Colors', 'kadence' ),
		'default'      => kadence()->default( 'page_title_meta_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.page-title .entry-meta',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.page-title .entry-meta a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
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
	'page_title_meta_font'        => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Meta Font', 'kadence' ),
		'default'      => kadence()->default( 'page_title_meta_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.page-title .entry-meta',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'input_attrs'  => [
			'id'      => 'page_title_breadcrumb_font',
			'options' => 'no-color',
		],
	],
	'page_title_background'       => [
		'control_type' => 'kadence_background_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Page Title Background', 'kadence' ),
		'default'      => kadence()->default( 'page_title_background' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'  => 'page_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'page_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#inner-wrap .page-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Page Title Background', 'kadence' ),
		],
	],
	'page_title_featured_image'   => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'page_layout',
		'default'      => kadence()->default( 'page_title_featured_image' ),
		'label'        => esc_html__( 'Use Featured Image for Background?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'  => 'page_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'page_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
	],
	'page_title_overlay_color'    => [
		'control_type' => 'kadence_color_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'page_title_overlay_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.page-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'  => 'page_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'page_title_layout',
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
	'page_title_border'           => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'page_title_border' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'  => 'page_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'page_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'settings'     => [
			'border_top'    => 'page_title_top_border',
			'border_bottom' => 'page_title_bottom_border',
		],
		'live_method'  => [
			'page_title_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => '.page-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				],
			],
			'page_title_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => '.page-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'info_page_layout'            => [
		'control_type' => 'kadence_title_control',
		'section'      => 'page_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Default Page Layout', 'kadence' ),
		'settings'     => false,
	],
	'page_layout'                 => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Default Page Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'page_layout' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'normal'    => [
					'name' => __( 'Normal', 'kadence' ),
					'icon' => 'normal',
				],
				'narrow'    => [
					'name' => __( 'Narrow', 'kadence' ),
					'icon' => 'narrow',
				],
				'fullwidth' => [
					'name' => __( 'Fullwidth', 'kadence' ),
					'icon' => 'fullwidth',
				],
				'left'      => [
					'name' => __( 'Left Sidebar', 'kadence' ),
					'icon' => 'leftsidebar',
				],
				'right'     => [
					'name' => __( 'Right Sidebar', 'kadence' ),
					'icon' => 'rightsidebar',
				],
			],
			'responsive' => false,
			'class'      => 'kadence-three-col',
		],
	],
	'page_sidebar_id'             => [
		'control_type' => 'kadence_select_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Page Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'page_sidebar_id' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'input_attrs'  => [
			'options' => kadence()->sidebar_options(),
		],
	],
	'page_content_style'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'page_content_style' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.page',
				'pattern'  => 'content-style-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'boxed'   => [
					'name' => __( 'Boxed', 'kadence' ),
					'icon' => 'boxed',
				],
				'unboxed' => [
					'name' => __( 'Unboxed', 'kadence' ),
					'icon' => 'narrow',
				],
			],
			'responsive' => false,
			'class'      => 'kadence-two-col',
		],
	],
	'page_vertical_padding'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Content Vertical Spacing', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'page_vertical_padding' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.page',
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
	'page_feature'                => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'page_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'page_feature' ),
		'label'        => esc_html__( 'Show Featured Image?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
	],
	'page_feature_position'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Featured Image Position', 'kadence' ),
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'page_feature_position' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'page_feature',
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
	'page_feature_ratio'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Featured Image Ratio', 'kadence' ),
		'priority'     => 20,
		'default'      => kadence()->default( 'page_feature_ratio' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'page_feature',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.page .article-post-thumbnail',
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
	'page_comments'               => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'page_layout',
		'priority'     => 20,
		'default'      => kadence()->default( 'page_comments' ),
		'label'        => esc_html__( 'Show Comments?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
	],
	'page_background'             => [
		'control_type' => 'kadence_background_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'page_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.page',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Page Background', 'kadence' ),
		],
	],
	'page_content_background'     => [
		'control_type' => 'kadence_background_control',
		'section'      => 'page_layout',
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'page_content_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.page .content-bg, body.page.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Page Content Background', 'kadence' ),
		],
	],
];

Theme_Customizer::add_settings( $settings );

