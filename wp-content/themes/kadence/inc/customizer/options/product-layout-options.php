<?php
/**
 * Product Layout Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'product_layout_tabs'                  => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'product_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'product_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'product_layout_design',
			],
			'active'  => 'general',
		],
	],
	'product_layout_tabs_design'           => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'product_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'product_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'product_layout_design',
			],
			'active'  => 'design',
		],
	],
	'info_product_title'                   => [
		'control_type' => 'kadence_title_control',
		'section'      => 'product_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Product Above Content', 'kadence' ),
		'settings'     => false,
	],
	'info_product_title_design'            => [
		'control_type' => 'kadence_title_control',
		'section'      => 'product_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Product Above Content', 'kadence' ),
		'settings'     => false,
	],
	'product_above_layout'                 => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'product_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'product_above_layout' ),
		'label'        => esc_html__( 'Above Content Layout', 'kadence' ),
		'transport'    => 'refresh',
		'input_attrs'  => [
			'layout'     => [
				'title'       => [
					'tooltip' => __( 'Enables an Extra above content title area', 'kadence' ),
					'name'    => __( 'Extra Title Area', 'kadence' ),
					'icon'    => '',
				],
				'breadcrumbs' => [
					'tooltip' => __( 'Enables Breadcrumbs', 'kadence' ),
					'name'    => __( 'Breadcrumbs', 'kadence' ),
					'icon'    => '',
				],
				'none'        => [
					'tooltip' => __( 'Hides this area', 'kadence' ),
					'name'    => __( 'Nothing', 'kadence' ),
					'icon'    => '',
				],
			],
			'responsive' => false,
			'class'      => 'kadence-tiny-text',
		],
	],
	'product_title_inner_layout'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'product_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'product_title_inner_layout' ),
		'label'        => esc_html__( 'Title Container Width', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '=',
				'value'    => 'title',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.product-hero-section',
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
	'product_title_align'                  => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'product_layout',
		'label'        => esc_html__( 'Product Above Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'product_title_align' ),
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '=',
				'value'    => 'title',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.product-title',
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
	'product_title_height'                 => [
		'control_type' => 'kadence_range_control',
		'section'      => 'product_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Above Container Min Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '=',
				'value'    => 'title',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#inner-wrap .product-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'product_title_height' ),
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
	'product_title_elements'               => [
		'control_type' => 'kadence_sorter_control',
		'section'      => 'product_layout',
		'priority'     => 6,
		'default'      => kadence()->default( 'product_title_elements' ),
		'label'        => esc_html__( 'Above Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => [
			'elements'    => 'product_title_elements',
			'above_title' => 'product_title_element_above_title',
			'breadcrumb'  => 'product_title_element_breadcrumb',
			'category'    => 'product_title_element_category',
		],
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '=',
				'value'    => 'title',
			],
		],
		'input_attrs'  => [
			'group' => 'product_title_element',
		],
	],
	'product_above_title_font'             => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'product_layout_design',
		'label'        => esc_html__( 'Product Above Title Font', 'kadence' ),
		'default'      => kadence()->default( 'product_above_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.product-hero-section .extra-title',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '=',
				'value'    => 'title',
			],
		],
		'input_attrs'  => [
			'id'             => 'product_above_title_font',
			'headingInherit' => true,
		],
	],
	'product_above_category_font'          => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'product_layout_design',
		'label'        => esc_html__( 'Product Above Category Font', 'kadence' ),
		'default'      => kadence()->default( 'product_above_category_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.product-hero-section .single-category',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '=',
				'value'    => 'title',
			],
		],
		'input_attrs'  => [
			'id'             => 'product_above_category_font',
			'headingInherit' => true,
		],
	],
	'product_title_breadcrumb_color'       => [
		'control_type' => 'kadence_color_control',
		'section'      => 'product_layout_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
		'default'      => kadence()->default( 'product_title_breadcrumb_color' ),
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '!=',
				'value'    => 'none',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.product-title .kadence-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.product-title .kadence-breadcrumbs a:hover',
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
	'product_title_breadcrumb_font'        => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'product_layout_design',
		'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
		'default'      => kadence()->default( 'product_title_breadcrumb_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.product-title .kadence-breadcrumbs',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '!=',
				'value'    => 'none',
			],
		],
		'input_attrs'  => [
			'id'      => 'product_title_breadcrumb_font',
			'options' => 'no-color',
		],
	],
	'product_title_background'             => [
		'control_type' => 'kadence_background_control',
		'section'      => 'product_layout_design',
		'label'        => esc_html__( 'Product Above Area Background', 'kadence' ),
		'default'      => kadence()->default( 'product_title_background' ),
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '=',
				'value'    => 'title',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#inner-wrap .product-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Product Above Title Background', 'kadence' ),
		],
	],
	'product_title_featured_image'         => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'product_layout_design',
		'default'      => kadence()->default( 'product_title_featured_image' ),
		'label'        => esc_html__( 'Use Featured Image for Background?', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '=',
				'value'    => 'title',
			],
		],
	],
	'product_title_overlay_color'          => [
		'control_type' => 'kadence_color_control',
		'section'      => 'product_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
		'default'      => kadence()->default( 'product_title_overlay_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.product-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '=',
				'value'    => 'title',
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
	'product_title_border'                 => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'product_layout_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'product_title_border' ),
		'context'      => [
			[
				'setting'  => 'product_above_layout',
				'operator' => '=',
				'value'    => 'title',
			],
		],
		'settings'     => [
			'border_top'    => 'product_title_top_border',
			'border_bottom' => 'product_title_bottom_border',
		],
		'live_method'  => [
			'product_title_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => '.product-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				],
			],
			'product_title_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => '.product-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'info_product_layout'                  => [
		'control_type' => 'kadence_title_control',
		'section'      => 'product_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Product Layout', 'kadence' ),
		'settings'     => false,
	],
	'info_product_layout_design'           => [
		'control_type' => 'kadence_title_control',
		'section'      => 'product_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Product Layout', 'kadence' ),
		'settings'     => false,
	],
	'product_single_category_font'         => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'product_layout_design',
		'label'        => esc_html__( 'Product In Content Category Font', 'kadence' ),
		'default'      => kadence()->default( 'product_single_category_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.woocommerce div.product .product-single-category',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id' => 'product_single_category_font',
		],
	],
	'product_title_font'                   => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'product_layout_design',
		'label'        => esc_html__( 'Product Title Font', 'kadence' ),
		'default'      => kadence()->default( 'product_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.woocommerce div.product .product_title',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'             => 'product_title_font',
			'headingInherit' => true,
		],
	],
	'product_layout'                       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'product_layout',
		'label'        => esc_html__( 'Product Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'product_layout' ),
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
	'product_sidebar_id'                   => [
		'control_type' => 'kadence_select_control',
		'section'      => 'product_layout',
		'label'        => esc_html__( 'Product Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'product_sidebar_id' ),
		'input_attrs'  => [
			'options' => kadence()->sidebar_options(),
		],
	],
	'product_content_style'                => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'product_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'product_content_style' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-product',
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
	'product_vertical_padding'             => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'product_layout',
		'label'        => esc_html__( 'Content Vertical Spacing', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'product_vertical_padding' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-product',
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
	'product_content_elements'             => [
		'control_type' => 'kadence_sorter_control',
		'section'      => 'product_layout',
		'priority'     => 10,
		'default'      => kadence()->default( 'product_content_elements' ),
		'label'        => esc_html__( 'Product Elements', 'kadence' ),
		'transport'    => 'refresh',
		'settings'     => [
			'elements'     => 'product_content_elements',
			'category'     => 'product_content_element_category',
			'title'        => 'product_content_element_title',
			'rating'       => 'product_content_element_rating',
			'price'        => 'product_content_element_price',
			'excerpt'      => 'product_content_element_excerpt',
			'add_to_cart'  => 'product_content_element_add_to_cart',
			'extras'       => 'product_content_element_extras',
			'payments'     => 'product_content_element_payments',
			'product_meta' => 'product_content_element_product_meta',
			'share'        => 'product_content_element_share',
		],
		'input_attrs'  => [
			'group'    => 'product_content_element',
			'sortable' => false,
		],
	],
	'custom_quantity'                      => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'product_layout',
		'priority'     => 10,
		'default'      => kadence()->default( 'custom_quantity' ),
		'label'        => esc_html__( 'Use Custom Quantity Plus and Minus', 'kadence' ),
		'transport'    => 'refresh',
	],
	'variation_direction'                  => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'product_layout',
		'priority'     => 10,
		'default'      => kadence()->default( 'variation_direction' ),
		'label'        => esc_html__( 'Product Variation Display', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-product',
				'pattern'  => 'product-variation-style-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'horizontal' => [
					'name' => __( 'Horizontal', 'kadence' ),
				],
				'vertical'   => [
					'name' => __( 'Vertical', 'kadence' ),
				],
			],
			'responsive' => false,
		],
	],
	'product_tab_style'                    => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'product_layout',
		'label'        => esc_html__( 'Tab Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'product_tab_style' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => 'body.single-product',
				'pattern'  => 'product-tab-style-$',
				'key'      => '',
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
	'product_tab_title'                    => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'product_layout',
		'priority'     => 10,
		'default'      => kadence()->default( 'product_tab_title' ),
		'label'        => esc_html__( 'Show default headings in tab content', 'kadence' ),
		'transport'    => 'refresh',
	],
	'product_additional_weight_dimensions' => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'product_layout',
		'priority'     => 10,
		'default'      => kadence()->default( 'product_additional_weight_dimensions' ),
		'label'        => esc_html__( 'Show Weight and Dimensions in Additional Information tab?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'product_related'                      => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'product_layout',
		'priority'     => 10,
		'default'      => kadence()->default( 'product_related' ),
		'label'        => esc_html__( 'Show Related Products?', 'kadence' ),
		'transport'    => 'refresh',
	],
	'product_related_columns'              => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'product_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Related Products Columns', 'kadence' ),
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'product_related_columns' ),
		'context'      => [
			[
				'setting'  => 'product_related',
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
	'product_background'                   => [
		'control_type' => 'kadence_background_control',
		'section'      => 'product_layout_design',
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( 'product_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-product',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Product Background', 'kadence' ),
		],
	],
	'product_content_background'           => [
		'control_type' => 'kadence_background_control',
		'section'      => 'product_layout_design',
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( 'product_content_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => 'body.single-product .content-bg, body.single-product.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Product Content Background', 'kadence' ),
		],
	],
];

Theme_Customizer::add_settings( $settings );
