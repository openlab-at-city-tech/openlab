<?php
/**
 * Header Main Row Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'product_archive_tabs'                    => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'woocommerce_product_catalog',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'woocommerce_product_catalog',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'woocommerce_product_catalog_design',
			],
			'active'  => 'general',
		],
	],
	'product_archive_tabs_design'             => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'woocommerce_product_catalog_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'woocommerce_product_catalog',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'woocommerce_product_catalog_design',
			],
			'active'  => 'design',
		],
	],
	'info_product_archive_title'              => [
		'control_type' => 'kadence_title_control',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 2,
		'label'        => esc_html__( 'Archive Title', 'kadence' ),
		'settings'     => false,
	],
	'info_product_archive_title_design'       => [
		'control_type' => 'kadence_title_control',
		'section'      => 'woocommerce_product_catalog_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Archive Title', 'kadence' ),
		'settings'     => false,
	],
	'product_archive_title'                   => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 3,
		'default'      => kadence()->default( 'product_archive_title' ),
		'label'        => esc_html__( 'Show Archive Title?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'product_archive_title_layout'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'woocommerce_product_catalog',
		'label'        => esc_html__( 'Archive Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'product_archive_title_layout' ),
		'context'      => [
			[
				'setting'  => 'product_archive_title',
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
	'product_archive_title_inner_layout'      => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 4,
		'default'      => kadence()->default( 'product_archive_title_inner_layout' ),
		'label'        => esc_html__( 'Container Width', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'product_archive_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'product_archive_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.product-archive-hero-section',
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
	'product_archive_title_align'             => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'woocommerce_product_catalog',
		'label'        => esc_html__( 'Product Archive Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'product_archive_title_align' ),
		'context'      => [
			[
				'setting'  => 'product_archive_title',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.product-archive-title',
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
	'product_archive_title_height'            => [
		'control_type' => 'kadence_range_control',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 5,
		'label'        => esc_html__( 'Container Min Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'product_archive_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'product_archive_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#inner-wrap .product-archive-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'product_archive_title_height' ),
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
	'product_archive_title_elements'          => [
		'control_type' => 'kadence_sorter_control',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 6,
		'default'      => kadence()->default( 'product_archive_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => [
			'elements'    => 'product_archive_title_elements',
			'title'       => 'product_archive_title_element_title',
			'breadcrumb'  => 'product_archive_title_element_breadcrumb',
			'description' => 'product_archive_title_element_description',
		],
	],
	'product_archive_title_heading_font'      => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Title Font', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_title_heading_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.product-archive-title h1',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'product_archive_title_heading_font',
			'options' => 'no-color',
		],
	],
	'product_archive_title_color'             => [
		'control_type' => 'kadence_color_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Title Color', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_title_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.product-archive-title h1',
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
	'product_archive_title_description_color' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Description Colors', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_title_description_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.product-archive-title .archive-description',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.product-archive-title .archive-description a:hover',
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
	'product_archive_title_breadcrumb_color'  => [
		'control_type' => 'kadence_color_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_title_breadcrumb_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.product-archive-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.product-archive-title .kadence-breadcrumbs a:hover',
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
	'product_archive_title_background'        => [
		'control_type' => 'kadence_background_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Archive Title Background', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_title_background' ),
		'context'      => [
			[
				'setting'  => 'product_archive_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'product_archive_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#inner-wrap .product-archive-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Product Archive Title Background', 'kadence' ),
		],
	],
	'product_archive_title_overlay_color'     => [
		'control_type' => 'kadence_color_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_title_overlay_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.product-archive-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_archive_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'product_archive_title_layout',
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
	'product_archive_title_border'            => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_title_border' ),
		'context'      => [
			[
				'setting'  => 'product_archive_title',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'product_archive_title_layout',
				'operator' => '=',
				'value'    => 'above',
			],
		],
		'settings'     => [
			'border_top'    => 'product_archive_title_top_border',
			'border_bottom' => 'product_archive_title_bottom_border',
		],
		'live_method'  => [
			'product_archive_title_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => '.product-archive-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				],
			],
			'product_archive_title_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => '.product-archive-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'info_product_archive_layout'             => [
		'control_type' => 'kadence_title_control',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 7,
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'settings'     => false,
	],
	'info_product_archive_layout_design'      => [
		'control_type' => 'kadence_title_control',
		'section'      => 'woocommerce_product_catalog_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'settings'     => false,
	],
	'product_archive_layout'                  => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'woocommerce_product_catalog',
		'label'        => esc_html__( 'Archive Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 7,
		'default'      => kadence()->default( 'product_archive_layout' ),
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
	'product_archive_sidebar_id'              => [
		'control_type' => 'kadence_select_control',
		'section'      => 'woocommerce_product_catalog',
		'label'        => esc_html__( 'Product Archive Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 7,
		'default'      => kadence()->default( 'product_archive_sidebar_id' ),
		'input_attrs'  => [
			'options' => kadence()->sidebar_options(),
		],
	],
	'product_archive_content_style'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 7,
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_content_style' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.post-type-archive-product',
				'pattern'  => 'content-style-$',
				'key'      => '',
			],
			[
				'type'     => 'class',
				'selector' => 'body.tax-product_cat',
				'pattern'  => 'content-style-$',
				'key'      => '',
			],
			[
				'type'     => 'class',
				'selector' => 'body.tax-product_tag',
				'pattern'  => 'content-style-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'boxed'   => [
					'tooltip' => __( 'Boxed', 'kadence' ),
					'icon'    => 'gridBoxed',
				],
				'unboxed' => [
					'tooltip' => __( 'Unboxed', 'kadence' ),
					'icon'    => 'gridUnboxed',
				],
			],
			'responsive' => false,
			'class'      => 'kadence-two-col',
		],
	],
	'product_archive_show_results_count'      => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 7,
		'default'      => kadence()->default( 'product_archive_show_results_count' ),
		'label'        => esc_html__( 'Show Archive Results Count?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'product_archive_show_order'              => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 7,
		'default'      => kadence()->default( 'product_archive_show_order' ),
		'label'        => esc_html__( 'Show Archive Sorting Dropdown?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'product_archive_toggle'                  => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 7,
		'default'      => kadence()->default( 'product_archive_toggle' ),
		'label'        => esc_html__( 'Show Archive Grid/List Toggle?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'product_archive_default_view'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 7,
		'default'      => kadence()->default( 'product_archive_default_view' ),
		'label'        => esc_html__( 'Default Archive View', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'product_archive_toggle',
				'operator' => '=',
				'value'    => false,
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'grid' => [
					'tooltip' => __( 'Grid', 'kadence' ),
					'icon'    => 'grid',
				],
				'list' => [
					'tooltip'  => __( 'List', 'kadence' ),
					'dashicon' => 'list-view',
				],
			],
			'responsive' => false,
		],
	],
	'product_archive_image_hover_switch'      => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 7,
		'transport'    => 'refresh',
		'label'        => esc_html__( 'Product Image Hover Switch', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_image_hover_switch' ),
		'input_attrs'  => [
			'layout'     => [
				'none'  => [
					'tooltip' => __( 'None', 'kadence' ),
					'name'    => __( 'None', 'kadence' ),
				],
				'fade'  => [
					'tooltip' => __( 'Fade to second image', 'kadence' ),
					'name'    => __( 'Fade', 'kadence' ),
				],
				'slide' => [
					'tooltip' => __( 'Slide to second image', 'kadence' ),
					'name'    => __( 'Slide', 'kadence' ),
				],
				'zoom'  => [
					'tooltip' => __( 'Zoom into second image', 'kadence' ),
					'name'    => __( 'Zoom', 'kadence' ),
				],
				'flip'  => [
					'tooltip' => __( 'Flip to second image', 'kadence' ),
					'name'    => __( 'Flip', 'kadence' ),
				],
			],
			'responsive' => false,
			'class'      => is_shopkit_feature_enabled( 'feature_image_swap' )
				? 'kadence-three-col kadence-auto-height kadence-control-disabled'
				: 'kadence-three-col kadence-auto-height',
			'notice'     => is_shopkit_feature_enabled( 'feature_image_swap' )
				? wp_kses(
					sprintf(
						/* translators: %s: link to Shop Kit Image Swap settings */
						__( 'This setting is being overridden by Shop Kit\'s Image Swap Module. <a target="_blank" href="%s">Click here</a> to manage Image Swap settings.', 'kadence' ),
						esc_url( admin_url( 'admin.php?page=iconic-wis-settings' ) )
					),
					[ 
						'a' => [ 
							'href'   => [], 
							'target' => [],
						],
					]
				)
				: '',
		],
	],
	'product_archive_style'                   => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 7,
		'transport'    => 'refresh',
		'label'        => esc_html__( 'Button Action Style', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_style' ),
		'input_attrs'  => [
			'layout'     => [
				'action-on-hover' => [
					'tooltip' => __( 'Slide up on hover', 'kadence' ),
					'name'    => __( 'Bottom Slide Up', 'kadence' ),
				],
				'action-visible'  => [
					'tooltip' => __( 'On the Bottom Always Visible', 'kadence' ),
					'name'    => __( 'Always Visible', 'kadence' ),
				],
			],
			'responsive' => false,
			'class'      => 'kadence-tiny-text',
		],
	],
	'product_archive_button_style'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 7,
		'transport'    => 'refresh',
		'label'        => esc_html__( 'Button Style', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_button_style' ),
		'input_attrs'  => [
			'layout'     => [
				'text'   => [
					'tooltip' => __( 'Bold text with arrow icon.', 'kadence' ),
					'name'    => __( 'Text with Arrow', 'kadence' ),
				],
				'button' => [
					'tooltip' => __( 'Show as standard button', 'kadence' ),
					'name'    => __( 'Button', 'kadence' ),
				],
			],
			'responsive' => false,
			'class'      => 'kadence-tiny-text',
		],
	],
	'product_archive_button_align'            => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 7,
		'default'      => kadence()->default( 'product_archive_button_align' ),
		'label'        => esc_html__( 'Align Button at Bottom?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'product_archive_mobile_columns'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'woocommerce_product_catalog',
		'priority'     => 15,
		'transport'    => 'refresh',
		'label'        => esc_html__( 'Mobile Columns Layout', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_mobile_columns' ),
		'input_attrs'  => [
			'layout'     => [
				'default'   => [
					'tooltip' => '',
					'name'    => __( 'One Column', 'kadence' ),
				],
				'twocolumn' => [
					'tooltip' => '',
					'name'    => __( 'Two Columns', 'kadence' ),
				],
			],
			'responsive' => false,
			'class'      => 'kadence-tiny-text',
		],
	],
	'product_archive_title_font'              => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Product Archive Title Font', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.woocommerce ul.products li.product h3, .woocommerce ul.products li.product .product-details .woocommerce-loop-product__title, .woocommerce ul.products li.product .product-details .woocommerce-loop-category__title, .wc-block-grid__products .wc-block-grid__product .wc-block-grid__product-title',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id' => 'product_archive_title_font',
		],
	],
	'product_archive_price_font'              => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Product Archive Price Font', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_price_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.woocommerce ul.products li.product .product-details .price',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id' => 'product_archive_price_font',
		],
	],
	'product_archive_button_color'            => [
		'control_type' => 'kadence_color_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Product Archive Button Colors', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_button_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.woocommerce ul.products.woo-archive-btn-button .product-action-wrap .button:not(.kb-button), .woocommerce ul.products li.woo-archive-btn-button .button:not(.kb-button), .wc-block-grid__product.woo-archive-btn-button .product-details .wc-block-grid__product-add-to-cart .wp-block-button__link',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.woocommerce ul.products.woo-archive-btn-button .product-action-wrap .button:not(.kb-button):hover, .woocommerce ul.products li.woo-archive-btn-button .button:not(.kb-button):hover, .wc-block-grid__product.woo-archive-btn-button .product-details .wc-block-grid__product-add-to-cart .wp-block-button__link:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_archive_button_style',
				'operator' => '=',
				'value'    => 'button',
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
	'product_archive_button_background'       => [
		'control_type' => 'kadence_color_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Product Archive Button Background Colors', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_button_background' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.woocommerce ul.products.woo-archive-btn-button .product-action-wrap .button:not(.kb-button), .woocommerce ul.products li.woo-archive-btn-button .button:not(.kb-button), .wc-block-grid__product.woo-archive-btn-button .product-details .wc-block-grid__product-add-to-cart .wp-block-button__link',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.woocommerce ul.products.woo-archive-btn-button .product-action-wrap .button:not(.kb-button):hover, .woocommerce ul.products li.woo-archive-btn-button .button:not(.kb-button):hover, .wc-block-grid__product.woo-archive-btn-button .product-details .wc-block-grid__product-add-to-cart .wp-block-button__link:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_archive_button_style',
				'operator' => '=',
				'value'    => 'button',
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
	'product_archive_button_border_colors'    => [
		'control_type' => 'kadence_color_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Product Archive Button Border Colors', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_button_border' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.woocommerce ul.products.woo-archive-btn-button .product-action-wrap .button:not(.kb-button), .woocommerce ul.products li.woo-archive-btn-button .button:not(.kb-button), .wc-block-grid__product.woo-archive-btn-button .product-details .wc-block-grid__product-add-to-cart .wp-block-button__link',
				'property' => 'border-color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.woocommerce ul.products.woo-archive-btn-button .product-action-wrap .button:not(.kb-button):hover, .woocommerce ul.products li.woo-archive-btn-button .button:not(.kb-button):hover, .wc-block-grid__product.woo-archive-btn-button .product-details .wc-block-grid__product-add-to-cart .wp-block-button__link:hover',
				'property' => 'border-color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_archive_button_style',
				'operator' => '=',
				'value'    => 'button',
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
	'product_archive_button_border'           => [
		'control_type' => 'kadence_border_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Product Archive Button Border', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_button_border' ),
		'live_method'  => [
			[
				'type'     => 'css_border',
				'selector' => '.woocommerce ul.products.woo-archive-btn-button .product-action-wrap .button:not(.kb-button), .woocommerce ul.products li.woo-archive-btn-button .button:not(.kb-button), .wc-block-grid__product.woo-archive-btn-button .product-details .wc-block-grid__product-add-to-cart .wp-block-button__link',
				'property' => 'border',
				'pattern'  => '$',
				'key'      => 'border',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_archive_button_style',
				'operator' => '=',
				'value'    => 'button',
			],
		],
		'input_attrs'  => [
			'responsive' => false,
			'color'      => false,
		],
	],
	'product_archive_button_radius'           => [
		'control_type' => 'kadence_measure_control',
		'section'      => 'woocommerce_product_catalog_design',
		'priority'     => 10,
		'default'      => kadence()->default( 'product_archive_button_radius' ),
		'label'        => esc_html__( 'Product Archive Button Border Radius', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.woocommerce ul.products.woo-archive-btn-button .product-action-wrap .button:not(.kb-button), .woocommerce ul.products li.woo-archive-btn-button .button:not(.kb-button), .wc-block-grid__product.woo-archive-btn-button .product-details .wc-block-grid__product-add-to-cart .wp-block-button__link',
				'property' => 'border-radius',
				'pattern'  => '$',
				'key'      => 'measure',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_archive_button_style',
				'operator' => '=',
				'value'    => 'button',
			],
		],
		'input_attrs'  => [
			'responsive' => false,
		],
	],
	'product_archive_button_typography'       => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Product Archive Button Font', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_button_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.woocommerce ul.products.woo-archive-btn-button .product-action-wrap .button:not(.kb-button), .woocommerce ul.products li.woo-archive-btn-button .button:not(.kb-button), .wc-block-grid__product.woo-archive-btn-button .product-details .wc-block-grid__product-add-to-cart .wp-block-button__link',
				'pattern'  => [
					'desktop' => '$',
					'tablet'  => '$',
					'mobile'  => '$',
				],
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_archive_button_style',
				'operator' => '=',
				'value'    => 'button',
			],
		],
		'input_attrs'  => [
			'id'      => 'header_button_typography',
			'options' => 'no-color',
		],
	],
	'product_archive_button_shadow'           => [
		'control_type' => 'kadence_shadow_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Product Archive Button Shadow', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css_boxshadow',
				'selector' => '.woocommerce ul.products.woo-archive-btn-button .product-action-wrap .button:not(.kb-button), .woocommerce ul.products li.woo-archive-btn-button .button:not(.kb-button), .wc-block-grid__product.woo-archive-btn-button .product-details .wc-block-grid__product-add-to-cart .wp-block-button__link',
				'property' => 'box-shadow',
				'pattern'  => '$',
				'key'      => '',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_archive_button_style',
				'operator' => '=',
				'value'    => 'button',
			],
		],
		'default'      => kadence()->default( 'product_archive_button_shadow' ),
	],
	'product_archive_button_shadow_hover'     => [
		'control_type' => 'kadence_shadow_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Product Archive Button Hover State Shadow', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css_boxshadow',
				'selector' => '.woocommerce ul.products.woo-archive-btn-button .product-action-wrap .button:not(.kb-button):hover, .woocommerce ul.products li.woo-archive-btn-button .button:not(.kb-button):hover, .wc-block-grid__product.woo-archive-btn-button .product-details .wc-block-grid__product-add-to-cart .wp-block-button__link:hover',
				'property' => 'box-shadow',
				'pattern'  => '$',
				'key'      => '',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_archive_button_style',
				'operator' => '=',
				'value'    => 'button',
			],
		],
		'default'      => kadence()->default( 'product_archive_button_shadow_hover' ),
	],
	'product_archive_background'              => [
		'control_type' => 'kadence_background_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.post-type-archive-product, body.tax-product_cat, body.tax-product_tag',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Product Archive Background', 'kadence' ),
		],
	],
	'product_archive_content_background'      => [
		'control_type' => 'kadence_background_control',
		'section'      => 'woocommerce_product_catalog_design',
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'product_archive_content_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.post-type-archive-product .content-bg, body.tax-product_cat .content-bg, body.tax-product_tag .content-bg, body.tax-product_cat.content-style-unboxed .site, body.tax-product_tag.content-style-unboxed .site, body.post-type-archive-product.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Archive Content Background', 'kadence' ),
		],
	],
];

Theme_Customizer::add_settings( $settings );
