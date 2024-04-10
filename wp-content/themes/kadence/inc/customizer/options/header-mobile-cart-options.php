<?php
/**
 * Header Builder Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = array(
	'header_mobile_cart_tabs' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'mobile_cart',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'mobile_cart',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'mobile_cart_design',
			),
			'active' => 'general',
		),
	),
	'header_mobile_cart_tabs_design' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'mobile_cart_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'mobile_cart',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'mobile_cart_design',
			),
			'active' => 'design',
		),
	),
	'header_mobile_cart_label' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'mobile_cart',
		'sanitize'     => 'sanitize_text_field',
		'priority'     => 6,
		'default'      => kadence()->default( 'header_mobile_cart_label' ),
		'label'        => esc_html__( 'Cart Label', 'kadence' ),
		'live_method'     => array(
			array(
				'type'     => 'html',
				'selector' => '.header-mobile-cart-wrap .header-cart-label',
				'pattern'  => '$',
				'key'      => '',
			),
		),
	),
	'header_mobile_cart_icon' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'mobile_cart',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_mobile_cart_icon' ),
		'label'        => esc_html__( 'Cart Icon', 'kadence' ),
		'partial'      => array(
			'selector'            => '.header-mobile-cart-wrap',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\mobile_cart',
		),
		'input_attrs'  => array(
			'layout' => array(
				'shopping-bag' => array(
					'icon' => 'shoppingBag',
				),
				'shopping-cart' => array(
					'icon' => 'shoppingCart',
				),
			),
			'responsive' => false,
		),
	),
	'header_mobile_cart_style' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'mobile_cart',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_mobile_cart_style' ),
		'label'        => esc_html__( 'Cart Click Action', 'kadence' ),
		'transport'    => 'refresh',
		'input_attrs'  => array(
			'layout' => array(
				'link' => array(
					'name' => __( 'Link', 'kadence' ),
				),
				'slide' => array(
					'name' => __( 'Popout Cart', 'kadence' ),
				),
			),
			'responsive' => false,
		),
	),
	'header_mobile_cart_show_total' => array(
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'mobile_cart',
		'priority'     => 6,
		'partial'      => array(
			'selector'            => '.header-mobile-cart-wrap',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\mobile_cart',
		),
		'default'      => kadence()->default( 'header_mobile_cart_show_total' ),
		'label'        => esc_html__( 'Show Item Total Indicator', 'kadence' ),
	),
	'header_mobile_cart_icon_size' => array(
		'control_type' => 'kadence_range_control',
		'section'      => 'mobile_cart_design',
		'label'        => esc_html__( 'Icon Size', 'kadence' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.header-mobile-cart-wrap .header-cart-button .kadence-svg-iconset',
				'property' => 'font-size',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => kadence()->default( 'header_mobile_cart_icon_size' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			),
			'max'        => array(
				'px'  => 100,
				'em'  => 12,
				'rem' => 12,
			),
			'step'       => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			),
			'units'      => array( 'px', 'em', 'rem' ),
			'responsive' => false,
		),
	),
	'header_mobile_cart_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_cart_design',
		'label'        => esc_html__( 'Cart Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_mobile_cart_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.header-mobile-cart-wrap .header-cart-inner-wrap .header-cart-button',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.header-mobile-cart-wrap .header-cart-inner-wrap .header-cart-button:hover',
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
					'tooltip' => __( 'Hover Color', 'kadence' ),
					'palette' => true,
				),
			),
		),
	),
	'header_mobile_cart_background' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_cart_design',
		'label'        => esc_html__( 'Cart Background', 'kadence' ),
		'default'      => kadence()->default( 'header_mobile_cart_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.header-mobile-cart-wrap .header-cart-inner-wrap .header-cart-button',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.header-mobile-cart-wrap .header-cart-inner-wrap .header-cart-button:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			),
		),
		'input_attrs'  => array(
			'colors' => array(
				'color' => array(
					'tooltip' => __( 'Initial Background', 'kadence' ),
					'palette' => true,
				),
				'hover' => array(
					'tooltip' => __( 'Hover Background', 'kadence' ),
					'palette' => true,
				),
			),
		),
	),
	'header_mobile_cart_total_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_cart_design',
		'label'        => esc_html__( 'Cart Total Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_mobile_cart_total_color' ),
		'context'      => array(
			array(
				'setting'  => 'header_mobile_cart_show_total',
				'operator' => '=',
				'value'    => true,
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.header-mobile-cart-wrap .header-cart-button .header-cart-total',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.header-mobile-cart-wrap .header-cart-button:hover .header-cart-total',
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
					'tooltip' => __( 'Hover Color', 'kadence' ),
					'palette' => true,
				),
			),
		),
	),
	'header_mobile_cart_total_background' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_cart_design',
		'label'        => esc_html__( 'Cart Total Background', 'kadence' ),
		'default'      => kadence()->default( 'header_mobile_cart_total_background' ),
		'context'      => array(
			array(
				'setting'  => 'header_mobile_cart_show_total',
				'operator' => '=',
				'value'    => true,
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.header-mobile-cart-wrap .header-cart-button .header-cart-total',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.header-mobile-cart-wrap .header-cart-button:hover .header-cart-total',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			),
		),
		'input_attrs'  => array(
			'colors' => array(
				'color' => array(
					'tooltip' => __( 'Initial Background', 'kadence' ),
					'palette' => true,
				),
				'hover' => array(
					'tooltip' => __( 'Hover Background', 'kadence' ),
					'palette' => true,
				),
			),
		),
	),
	'header_mobile_cart_typography' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'mobile_cart_design',
		'label'        => esc_html__( 'Cart Label Font', 'kadence' ),
		'context'      => array(
			array(
				'setting'  => 'header_mobile_cart_label',
				'operator' => '!empty',
				'value'    => '',
			),
		),
		'default'      => kadence()->default( 'header_mobile_cart_typography' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.header-mobile-cart-wrap .header-cart-button .header-cart-label',
				'pattern'  => array(
					'desktop' => '$',
					'tablet'  => '$',
					'mobile'  => '$',
				),
				'property' => 'font',
				'key'      => 'typography',
			),
		),
		'input_attrs'  => array(
			'id'      => 'header_mobile_cart_typography',
			'options' => 'no-color',
		),
	),
	'header_mobile_cart_padding' => array(
		'control_type' => 'kadence_measure_control',
		'section'      => 'mobile_cart_design',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_mobile_cart_padding' ),
		'label'        => esc_html__( 'Cart Padding', 'kadence' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.header-mobile-cart-wrap .header-cart-inner-wrap .header-cart-button',
				'property' => 'padding',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'input_attrs'  => array(
			'responsive' => false,
		),
	),
	'header_mobile_cart_popup_side' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'mobile_cart',
		'priority'     => 20,
		'default'      => kadence()->default( 'header_mobile_cart_popup_side' ),
		'label'        => esc_html__( 'Slide-Out Side', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'header_mobile_cart_style',
				'operator'   => '=',
				'value'      => 'slide',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '#cart-drawer',
				'pattern'  => 'popup-mobile-drawer-side-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'left' => array(
					'tooltip' => __( 'Reveal from Left', 'kadence' ),
					'name'    => __( 'Left', 'kadence' ),
					'icon'    => '',
				),
				'right' => array(
					'tooltip' => __( 'Reveal from Right', 'kadence' ),
					'name'    => __( 'Right', 'kadence' ),
					'icon'    => '',
				),
			),
			'responsive' => false,
		),
	),
);

Theme_Customizer::add_settings( $settings );

