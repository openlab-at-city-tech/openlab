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
	'search_archive_tabs' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'search',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'search',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'search_design',
			),
			'active' => 'general',
		),
	),
	'search_archive_tabs_design' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'search_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'search',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'search_design',
			),
			'active' => 'design',
		),
	),
	'info_search_archive_title' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'search',
		'priority'     => 2,
		'label'        => esc_html__( 'Search Results Title', 'kadence' ),
		'settings'     => false,
	),
	'info_search_archive_title_design' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'search_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Search Results Title', 'kadence' ),
		'settings'     => false,
	),
	'search_archive_title' => array(
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'search',
		'priority'     => 3,
		'default'      => kadence()->default( 'search_archive_title' ),
		'label'        => esc_html__( 'Show Search Results Title?', 'kadence' ),
		'transport'    => 'refresh',
	),
	'search_archive_title_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'search',
		'label'        => esc_html__( 'Search Results Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'search_archive_title_layout' ),
		'context'      => array(
			array(
				'setting'    => 'search_archive_title',
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
	'search_archive_title_inner_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'search',
		'priority'     => 4,
		'default'      => kadence()->default( 'search_archive_title_inner_layout' ),
		'label'        => esc_html__( 'Container Width', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'search_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'search_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.search-archive-hero-section',
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
	'search_archive_title_align' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'search',
		'label'        => esc_html__( 'Search Results Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'search_archive_title_align' ),
		'context'      => array(
			array(
				'setting'    => 'search_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.search-archive-title',
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
	'search_archive_title_height' => array(
		'control_type' => 'kadence_range_control',
		'section'      => 'search',
		'priority'     => 5,
		'label'        => esc_html__( 'Container Min Height', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'search_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'search_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#inner-wrap .search-archive-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => kadence()->default( 'search_archive_title_height' ),
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
	'search_archive_title_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'search_design',
		'label'        => esc_html__( 'Title Color', 'kadence' ),
		'default'      => kadence()->default( 'search_archive_title_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.search-archive-title h1',
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
	'search_archive_title_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'search_design',
		'label'        => esc_html__( 'Archive Title Background', 'kadence' ),
		'default'      => kadence()->default( 'search_archive_title_background' ),
		'context'      => array(
			array(
				'setting'    => 'search_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'search_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '#inner-wrap .search-archive-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Post Title Background', 'kadence' ),
		),
	),
	'search_archive_title_overlay_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'search_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'search_archive_title_overlay_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.search-archive-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
		),
		'context'      => array(
			array(
				'setting'    => 'search_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'search_archive_title_layout',
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
	'search_archive_title_border' => array(
		'control_type' => 'kadence_borders_control',
		'section'      => 'search_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'search_archive_title_border' ),
		'context'      => array(
			array(
				'setting'    => 'search_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'search_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'settings'     => array(
			'border_top'    => 'search_archive_title_top_border',
			'border_bottom' => 'search_archive_title_bottom_border',
		),
		'live_method'     => array(
			'search_archive_title_top_border' => array(
				array(
					'type'     => 'css_border',
					'selector' => '.search-archive-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				),
			),
			'search_archive_title_bottom_border' => array( 
				array(
					'type'     => 'css_border',
					'selector' => '.search-archive-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
		),
	),
	'info_search_archive_layout' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'search',
		'priority'     => 10,
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'settings'     => false,
	),
	'info_search_archive_layout_design' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'search_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'settings'     => false,
	),
	'search_archive_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'search',
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'search_archive_layout' ),
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
	'search_archive_sidebar_id' => array(
		'control_type' => 'kadence_select_control',
		'section'      => 'search',
		'label'        => esc_html__( 'Post Archive Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'search_archive_sidebar_id' ),
		'input_attrs'  => array(
			'options' => kadence()->sidebar_options(),
		),
	),
	'search_archive_content_style' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'search',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'search_archive_content_style' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => 'body.search-results',
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
	'search_archive_columns' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'search',
		'priority'     => 10,
		'label'        => esc_html__( 'Search Result Columns', 'kadence' ),
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'search_archive_columns' ),
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
	'search_archive_item_image_placement' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'search',
		'label'        => esc_html__( 'Item Image Placement', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'search_archive_item_image_placement' ),
		'context'      => array(
			array(
				'setting' => 'search_archive_columns',
				'operator'   => '=',
				'value'   => '1',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.search-archive.grid-cols',
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
	'info_search_archive_item_layout' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'search',
		'priority'     => 12,
		'label'        => esc_html__( 'Search Item Layout', 'kadence' ),
		'settings'     => false,
	),
	'search_archive_elements' => array(
		'control_type' => 'kadence_sorter_control',
		'section'      => 'search',
		'priority'     => 12,
		'default'      => kadence()->default( 'search_archive_elements' ),
		'label'        => esc_html__( 'Search Item Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => array(
			'elements'   => 'search_archive_elements',
			'feature'    => 'search_archive_element_feature',
			'categories' => 'search_archive_element_categories',
			'title'      => 'search_archive_element_title',
			'meta'       => 'search_archive_element_meta',
			'excerpt'    => 'search_archive_element_excerpt',
			'readmore'   => 'search_archive_element_readmore',
		),
		'input_attrs'  => array(
			'groupe'   => 'search_archive_elements',
			'sortable' => false,
			'defaults' => array(
				'feature'    => kadence()->default( 'search_archive_element_feature' ),
				'categories' => kadence()->default( 'search_archive_element_categories' ),
				'title'      => kadence()->default( 'search_archive_element_title' ),
				'meta'       => kadence()->default( 'search_archive_element_meta' ),
				'excerpt'    => kadence()->default( 'search_archive_element_excerpt' ),
				'readmore'   => kadence()->default( 'search_archive_element_readmore' ),
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
	'search_archive_item_title_font' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'search_design',
		'label'        => esc_html__( 'Search Item Title Font', 'kadence' ),
		'default'      => kadence()->default( 'search_archive_item_title_font' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => 'body.search-results .loop-entry h2.entry-title',
				'property' => 'font',
				'key'      => 'typography',
			),
		),
		'input_attrs'  => array(
			'id'             => 'search_archive_item_title_font',
			'headingInherit' => true,
		),
	),
	'search_archive_item_category_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'search_design',
		'label'        => esc_html__( 'Item Category Colors', 'kadence' ),
		'default'      => kadence()->default( 'search_archive_item_category_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => 'body.search-results .loop-entry .entry-taxonomies, .loop-entry .entry-taxonomies a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => 'body.search-results .loop-entry .entry-taxonomies a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			),
			array(
				'type'     => 'css',
				'selector' => 'body.search-results .loop-entry .entry-taxonomies .category-style-pill a',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => 'body.search-results .loop-entry .entry-taxonomies .category-style-pill a:hover',
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
	'search_archive_item_category_font' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'search_design',
		'label'        => esc_html__( 'Item Category Font', 'kadence' ),
		'default'      => kadence()->default( 'search_archive_item_category_font' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => 'body.search-results .loop-entry .entry-taxonomies',
				'property' => 'font',
				'key'      => 'typography',
			),
		),
		'input_attrs'  => array(
			'id'      => 'search_archive_item_category_font',
			'options' => 'no-color',
		),
	),
	'search_archive_item_meta_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'search_design',
		'label'        => esc_html__( 'Item Meta Colors', 'kadence' ),
		'default'      => kadence()->default( 'search_archive_item_meta_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => 'body.search-results .loop-entry .entry-meta',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => 'body.search-results .loop-entry .entry-meta a:hover',
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
	'search_archive_item_meta_font' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'search_design',
		'label'        => esc_html__( 'Item Meta Font', 'kadence' ),
		'default'      => kadence()->default( 'search_archive_item_meta_font' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => 'body.search-results .loop-entry .entry-meta',
				'property' => 'font',
				'key'      => 'typography',
			),
		),
		'input_attrs'  => array(
			'id'      => 'search_archive_item_meta_font',
			'options' => 'no-color',
		),
	),
	'search_archive_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'search_design',
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'search_archive_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.search-results',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip' => __( 'Search Results Background', 'kadence' ),
		),
	),
	'search_archive_content_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'search_design',
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'search_archive_content_background' ),
		'live_method'  => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.search-results .content-bg, body.search-results.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip' => __( 'Search Results Content Background', 'kadence' ),
		),
	),
);

Theme_Customizer::add_settings( $settings );

