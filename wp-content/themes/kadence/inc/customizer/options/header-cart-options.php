<?php
/**
 * Header Builder Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

Theme_Customizer::add_settings(
	[
		'header_cart_tabs'             => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'cart',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'cart',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'cart_design',
				],
				'active'  => 'general',
			],
		],
		'header_cart_tabs_design'      => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'cart_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'cart',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'cart_design',
				],
				'active'  => 'design',
			],
		],
		'header_cart_label'            => [
			'control_type' => 'kadence_text_control',
			'section'      => 'cart',
			'sanitize'     => 'sanitize_text_field',
			'priority'     => 6,
			'default'      => kadence()->default( 'header_cart_label' ),
			'label'        => esc_html__( 'Cart Label', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'html',
					'selector' => '.header-mobile-cart-wrap .header-cart-label',
					'pattern'  => '$',
					'key'      => '',
				],
			],
		],
		'header_cart_icon'             => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'cart',
			'priority'     => 10,
			'default'      => kadence()->default( 'header_cart_icon' ),
			'label'        => esc_html__( 'Cart Icon', 'kadence' ),
			'partial'      => [
				'selector'            => '.header-cart-wrap',
				'container_inclusive' => true,
				'render_callback'     => 'Kadence\header_cart',
			],
			'input_attrs'  => [
				'layout'     => [
					'shopping-bag'  => [
						'icon' => 'shoppingBag',
					],
					'shopping-cart' => [
						'icon' => 'shoppingCart',
					],
				],
				'responsive' => false,
			],
		],
		'header_cart_style'            => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'cart',
			'priority'     => 10,
			'default'      => kadence()->default( 'header_cart_style' ),
			'label'        => esc_html__( 'Cart Click Action', 'kadence' ),
			'transport'    => 'refresh',
			'input_attrs'  => [
				'layout'     => [
					'link'     => [
						'name' => __( 'Link', 'kadence' ),
					],
					'slide'    => [
						'name' => __( 'Popout Cart', 'kadence' ),
					],
					'dropdown' => [
						'name' => __( 'Dropdown Cart', 'kadence' ),
					],
				],
				'responsive' => false,
			],
		],
		'header_cart_show_total'       => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'cart',
			'priority'     => 6,
			'partial'      => [
				'selector'            => '.header-cart-wrap',
				'container_inclusive' => true,
				'render_callback'     => 'Kadence\header_cart',
			],
			'default'      => kadence()->default( 'header_cart_show_total' ),
			'label'        => esc_html__( 'Show Item Total Indicator', 'kadence' ),
		],
		'header_cart_icon_size'        => [
			'control_type' => 'kadence_range_control',
			'section'      => 'cart_design',
			'label'        => esc_html__( 'Icon Size', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.header-cart-wrap .header-cart-button .kadence-svg-iconset',
					'property' => 'font-size',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'header_cart_icon_size' ),
			'input_attrs'  => [
				'min'        => [
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
				],
				'max'        => [
					'px'  => 100,
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
		'header_cart_color'            => [
			'control_type' => 'kadence_color_control',
			'section'      => 'cart_design',
			'label'        => esc_html__( 'Cart Colors', 'kadence' ),
			'default'      => kadence()->default( 'header_cart_color' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.site-header-item .header-cart-wrap .header-cart-inner-wrap .header-cart-button',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.site-header-item .header-cart-wrap .header-cart-inner-wrap .header-cart-button:hover',
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
		'header_cart_background'       => [
			'control_type' => 'kadence_color_control',
			'section'      => 'cart_design',
			'label'        => esc_html__( 'Cart Background', 'kadence' ),
			'default'      => kadence()->default( 'header_cart_background' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.site-header-item .header-cart-wrap .header-cart-inner-wrap .header-cart-button',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.site-header-item .header-cart-wrap .header-cart-inner-wrap .header-cart-button:hover',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'hover',
				],
			],
			'input_attrs'  => [
				'colors' => [
					'color' => [
						'tooltip' => __( 'Initial Background', 'kadence' ),
						'palette' => true,
					],
					'hover' => [
						'tooltip' => __( 'Hover Background', 'kadence' ),
						'palette' => true,
					],
				],
			],
		],
		'header_cart_total_color'      => [
			'control_type' => 'kadence_color_control',
			'section'      => 'cart_design',
			'label'        => esc_html__( 'Cart Total Colors', 'kadence' ),
			'default'      => kadence()->default( 'header_cart_total_color' ),
			'context'      => [
				[
					'setting'  => 'header_cart_show_total',
					'operator' => '=',
					'value'    => true,
				],
			],
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.header-cart-wrap .header-cart-button .header-cart-total',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.header-cart-wrap .header-cart-button:hover .header-cart-total',
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
		'header_cart_total_background' => [
			'control_type' => 'kadence_color_control',
			'section'      => 'cart_design',
			'label'        => esc_html__( 'Cart Total Background', 'kadence' ),
			'default'      => kadence()->default( 'header_cart_total_background' ),
			'context'      => [
				[
					'setting'  => 'header_cart_show_total',
					'operator' => '=',
					'value'    => true,
				],
			],
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.header-cart-wrap .header-cart-button .header-cart-total',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.header-cart-wrap .header-cart-button:hover .header-cart-total',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'hover',
				],
			],
			'input_attrs'  => [
				'colors' => [
					'color' => [
						'tooltip' => __( 'Initial Background', 'kadence' ),
						'palette' => true,
					],
					'hover' => [
						'tooltip' => __( 'Hover Background', 'kadence' ),
						'palette' => true,
					],
				],
			],
		],
		'header_cart_typography'       => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'cart_design',
			'label'        => esc_html__( 'Cart Label Font', 'kadence' ),
			'context'      => [
				[
					'setting'  => 'header_cart_label',
					'operator' => '!empty',
					'value'    => '',
				],
			],
			'default'      => kadence()->default( 'header_cart_typography' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.header-cart-wrap .header-cart-button .header-cart-label',
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
				'id'      => 'header_cart_typography',
				'options' => 'no-color',
			],
		],
		'header_cart_padding'          => [
			'control_type' => 'kadence_measure_control',
			'section'      => 'cart_design',
			'priority'     => 10,
			'default'      => kadence()->default( 'header_cart_padding' ),
			'label'        => esc_html__( 'Cart Padding', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.site-header-item .header-cart-wrap .header-cart-inner-wrap .header-cart-button',
					'property' => 'padding',
					'pattern'  => '$',
					'key'      => 'measure',
				],
			],
			'input_attrs'  => [
				'responsive' => false,
			],
		],
		'header_cart_popup_side'       => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'cart',
			'priority'     => 20,
			'default'      => kadence()->default( 'header_cart_popup_side' ),
			'label'        => esc_html__( 'Slide-Out Side', 'kadence' ),
			'context'      => [
				[
					'setting'  => 'header_cart_style',
					'operator' => '=',
					'value'    => 'slide',
				],
			],
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '#cart-drawer',
					'pattern'  => 'popup-drawer-side-$',
					'key'      => '',
				],
			],
			'input_attrs'  => [
				'layout'     => [
					'left'  => [
						'tooltip' => __( 'Reveal from Left', 'kadence' ),
						'name'    => __( 'Left', 'kadence' ),
						'icon'    => '',
					],
					'right' => [
						'tooltip' => __( 'Reveal from Right', 'kadence' ),
						'name'    => __( 'Right', 'kadence' ),
						'icon'    => '',
					],
				],
				'responsive' => false,
			],
		],
	]
);
