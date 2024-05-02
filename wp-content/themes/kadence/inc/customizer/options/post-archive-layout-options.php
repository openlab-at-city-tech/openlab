<?php
/**
 * Header Main Row Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = array(
	'post_archive_tabs' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'post_archive',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'post_archive',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'post_archive_design',
			),
			'active' => 'general',
		),
	),
	'post_archive_tabs_design' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'post_archive_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'post_archive',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'post_archive_design',
			),
			'active' => 'design',
		),
	),
	'info_post_archive_title' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'post_archive',
		'priority'     => 2,
		'label'        => esc_html__( 'Archive Title', 'kadence' ),
		'settings'     => false,
	),
	'info_post_archive_title_design' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'post_archive_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Archive Title', 'kadence' ),
		'settings'     => false,
	),
	'post_archive_title' => array(
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'post_archive',
		'priority'     => 3,
		'default'      => kadence()->default( 'post_archive_title' ),
		'label'        => esc_html__( 'Show Archive Title?', 'kadence' ),
		'transport'    => 'refresh',
	),
	'post_archive_title_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_archive',
		'label'        => esc_html__( 'Archive Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'post_archive_title_layout' ),
		'context'      => array(
			array(
				'setting'    => 'post_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'normal' => array(
					'name' => __( 'In Content', 'kadence' ),
					'icon'    => 'incontent',
				),
				'above' => array(
					'name' => __( 'Above Content', 'kadence' ),
					'icon'    => 'abovecontent',
				),
			),
			'responsive' => false,
			'class'      => 'kadence-two-col',
		),
	),
	'post_archive_title_inner_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_archive',
		'priority'     => 4,
		'default'      => kadence()->default( 'post_archive_title_inner_layout' ),
		'label'        => esc_html__( 'Container Width', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'post_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'post_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.post-archive-hero-section',
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
	'post_archive_title_align' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_archive',
		'label'        => esc_html__( 'Post Archive Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'post_archive_title_align' ),
		'context'      => array(
			array(
				'setting'    => 'post_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.post-archive-title',
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
	'post_archive_title_height' => array(
		'control_type' => 'kadence_range_control',
		'section'      => 'post_archive',
		'priority'     => 5,
		'label'        => esc_html__( 'Container Min Height', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'post_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'post_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#inner-wrap .post-archive-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => kadence()->default( 'post_archive_title_height' ),
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
	'post_archive_title_elements' => array(
		'control_type' => 'kadence_sorter_control',
		'section'      => 'post_archive',
		'priority'     => 6,
		'default'      => kadence()->default( 'post_archive_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => array(
			'elements'    => 'post_archive_title_elements',
			'title'       => 'post_archive_title_element_title',
			'breadcrumb'  => 'post_archive_title_element_breadcrumb',
			'description' => 'post_archive_title_element_description',
		),
	),
	'post_archive_title_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Title Color', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_title_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.wp-site-blocks .post-archive-title h1',
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
	'post_archive_title_description_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Description Colors', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_title_description_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.post-archive-title .archive-description',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.post-archive-title .archive-description a:hover',
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
	'post_archive_title_breadcrumb_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_title_breadcrumb_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.post-archive-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.post-archive-title .kadence-breadcrumbs a:hover',
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
	'post_archive_title_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Archive Title Background', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_title_background' ),
		'context'      => array(
			array(
				'setting'    => 'post_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'post_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '#inner-wrap .post-archive-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Post Title Background', 'kadence' ),
		),
	),
	'post_archive_title_overlay_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_title_overlay_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.post-archive-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
		),
		'context'      => array(
			array(
				'setting'    => 'post_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'post_archive_title_layout',
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
	'post_archive_title_border' => array(
		'control_type' => 'kadence_borders_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_title_border' ),
		'context'      => array(
			array(
				'setting'    => 'post_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'post_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'settings'     => array(
			'border_top'    => 'post_archive_title_top_border',
			'border_bottom' => 'post_archive_title_bottom_border',
		),
		'live_method'     => array(
			'post_archive_title_top_border' => array(
				array(
					'type'     => 'css_border',
					'selector' => '.post-archive-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				),
			),
			'post_archive_title_bottom_border' => array( 
				array(
					'type'     => 'css_border',
					'selector' => '.post-archive-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
		),
	),
	'info_post_archive_layout' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'post_archive',
		'priority'     => 10,
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'settings'     => false,
	),
	'info_post_archive_layout_design' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'post_archive_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'settings'     => false,
	),
	'post_archive_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_archive',
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'post_archive_layout' ),
		'input_attrs'  => array(
			'layout' => array(
				'normal' => array(
					'name' => __( 'Normal', 'kadence' ),
					'icon' => 'normal',
				),
				'narrow' => array(
					'name' => __( 'Narrow', 'kadence' ),
					'icon' => 'narrow',
				),
				'fullwidth' => array(
					'name' => __( 'Fullwidth', 'kadence' ),
					'icon' => 'fullwidth',
				),
				'left' => array(
					'name' => __( 'Left Sidebar', 'kadence' ),
					'icon' => 'leftsidebar',
				),
				'right' => array(
					'name' => __( 'Right Sidebar', 'kadence' ),
					'icon' => 'rightsidebar',
				),
			),
			'class'      => 'kadence-three-col',
			'responsive' => false,
		),
	),
	'post_archive_sidebar_id' => array(
		'control_type' => 'kadence_select_control',
		'section'      => 'post_archive',
		'label'        => esc_html__( 'Post Archive Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'post_archive_sidebar_id' ),
		'input_attrs'  => array(
			'options' => kadence()->sidebar_options(),
		),
	),
	'post_archive_content_style' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_archive',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'post_archive_content_style' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => 'body.archive',
				'pattern'  => 'content-style-$',
				'key'      => '',
			),
			array(
				'type'     => 'class',
				'selector' => 'body.blog',
				'pattern'  => 'content-style-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'boxed' => array(
					'tooltip' => __( 'Boxed', 'kadence' ),
					'icon' => 'gridBoxed',
					'name' => __( 'Boxed', 'kadence' ),
				),
				'unboxed' => array(
					'tooltip' => __( 'Unboxed', 'kadence' ),
					'icon' => 'gridUnboxed',
					'name' => __( 'Unboxed', 'kadence' ),
				),
			),
			'responsive' => false,
			'class'      => 'kadence-two-col',
		),
	),
	'post_archive_columns' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_archive',
		'priority'     => 10,
		'label'        => esc_html__( 'Post Archive Columns', 'kadence' ),
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'post_archive_columns' ),
		'input_attrs'  => array(
			'layout' => array(
				'1' => array(
					'name' => __( '1', 'kadence' ),
				),
				'2' => array(
					'name' => __( '2', 'kadence' ),
				),
				'3' => array(
					'name' => __( '3', 'kadence' ),
				),
				'4' => array(
					'name' => __( '4', 'kadence' ),
				),
			),
			'responsive' => false,
		),
	),
	'post_archive_item_image_placement' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_archive',
		'label'        => esc_html__( 'Item Image Placement', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'post_archive_item_image_placement' ),
		'context'      => array(
			array(
				'setting' => 'post_archive_columns',
				'operator'   => '=',
				'value'   => '1',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.post-archive.grid-cols',
				'pattern'  => 'item-image-style-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'beside' => array(
					'name' => __( 'Beside', 'kadence' ),
				),
				'above' => array(
					'name' => __( 'Above', 'kadence' ),
				),
			),
			'responsive' => false,
		),
	),
	'post_archive_item_vertical_alignment' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'post_archive',
		'label'        => esc_html__( 'Content Vertical Alignment', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'post_archive_item_vertical_alignment' ),
		'context'      => array(
			array(
				'setting' => 'post_archive_columns',
				'operator'   => '=',
				'value'   => '1',
			),
			array(
				'setting' => 'post_archive_item_image_placement',
				'operator'   => '=',
				'value'   => 'beside',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.post-archive.grid-cols',
				'pattern'  => 'item-content-vertical-align-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'top' => array(
					'name' => __( 'Top', 'kadence' ),
				),
				'center' => array(
					'name' => __( 'Center', 'kadence' ),
				),
			),
			'responsive' => false,
		),
	),
	'info_post_archive_item_layout' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'post_archive',
		'priority'     => 12,
		'label'        => esc_html__( 'Post Item Layout', 'kadence' ),
		'settings'     => false,
	),
	'post_archive_elements' => array(
		'control_type' => 'kadence_sorter_control',
		'section'      => 'post_archive',
		'priority'     => 12,
		'default'      => kadence()->default( 'post_archive_elements' ),
		'label'        => esc_html__( 'Post Item Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => array(
			'elements'   => 'post_archive_elements',
			'feature'    => 'post_archive_element_feature',
			'categories' => 'post_archive_element_categories',
			'title'      => 'post_archive_element_title',
			'meta'       => 'post_archive_element_meta',
			'excerpt'    => 'post_archive_element_excerpt',
			'readmore'   => 'post_archive_element_readmore',
		),
		'input_attrs'  => array(
			'groupe'   => 'post_archive_elements',
			'sortable' => false,
			'defaults' => array(
				'feature'    => kadence()->default( 'post_archive_element_feature' ),
				'categories' => kadence()->default( 'post_archive_element_categories' ),
				'title'      => kadence()->default( 'post_archive_element_title' ),
				'meta'       => kadence()->default( 'post_archive_element_meta' ),
				'excerpt'    => kadence()->default( 'post_archive_element_excerpt' ),
				'readmore'   => kadence()->default( 'post_archive_element_readmore' ),
			),
			'dividers' => array(
				'dot' => array(
					'icon' => 'dot',
				),
				'slash' => array(
					'icon' => 'slash',
				),
				'dash' => array(
					'icon' => 'dash',
				),
				'vline' => array(
					'icon' => 'vline',
				),
				'customicon' => array(
					'icon' => 'hours',
				),
			),
		),
	),
	'post_archive_item_title_font' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Post Item Title Font', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_item_title_font' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.loop-entry.type-post h2.entry-title',
				'property' => 'font',
				'key'      => 'typography',
			),
		),
		'input_attrs'  => array(
			'id'             => 'post_archive_item_title_font',
			'headingInherit' => true,
		),
	),
	'post_archive_item_category_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Item Category Colors', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_item_category_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.loop-entry.type-post .entry-taxonomies, .loop-entry.type-post .entry-taxonomies a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.loop-entry.type-post .entry-taxonomies a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			),
			array(
				'type'     => 'css',
				'selector' => '.loop-entry.type-post .entry-taxonomies .category-style-pill a',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.loop-entry.type-post .entry-taxonomies .category-style-pill a:hover',
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
					'tooltip' => __( 'Link Hover Color', 'kadence' ),
					'palette' => true,
				),
			),
		),
	),
	'post_archive_item_category_font' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Item Category Font', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_item_category_font' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.loop-entry.type-post .entry-taxonomies',
				'property' => 'font',
				'key'      => 'typography',
			),
		),
		'input_attrs'  => array(
			'id'      => 'post_archive_item_category_font',
			'options' => 'no-color',
		),
	),
	'post_archive_item_meta_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Item Meta Colors', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_item_meta_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.loop-entry.type-post .entry-meta',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.loop-entry.type-post .entry-meta a:hover',
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
	'post_archive_item_meta_font' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Item Meta Font', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_item_meta_font' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.loop-entry.type-post .entry-meta',
				'property' => 'font',
				'key'      => 'typography',
			),
		),
		'input_attrs'  => array(
			'id'      => 'post_archive_item_meta_font',
			'options' => 'no-color',
		),
	),
	'post_archive_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.archive, body.blog',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip' => __( 'Post Background', 'kadence' ),
		),
	),
	'post_archive_content_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'post_archive_design',
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'post_archive_content_background' ),
		'live_method'  => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.blog .content-bg, body.archive .content-bg, body.archive.content-style-unboxed .site, body.blog.content-style-unboxed .site',
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

