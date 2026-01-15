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
	array(
		'dropdown_navigation_tabs' => array(
			'control_type' => 'kadence_tab_control',
			'section'      => 'dropdown_navigation',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'kadence' ),
					'target' => 'dropdown_navigation',
				),
				'design' => array(
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'dropdown_navigation_design',
				),
				'active' => 'general',
			),
		),
		'dropdown_navigation_tabs_design' => array(
			'control_type' => 'kadence_tab_control',
			'section'      => 'dropdown_navigation_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'kadence' ),
					'target' => 'dropdown_navigation',
				),
				'design' => array(
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'dropdown_navigation_design',
				),
				'active' => 'design',
			),
		),
		'dropdown_navigation_color' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'dropdown_navigation_design',
			'label'        => esc_html__( 'Dropdown Colors', 'kadence' ),
			'default'      => kadence()->default( 'dropdown_navigation_color' ),
			'live_method'  => array(
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a:hover',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'hover',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul > li.menu-item.current-menu-item > a, .header-navigation .header-menu-container ul ul > li.menu-item.current_page_item > a',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'active',
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
					'active' => array(
						'tooltip' => __( 'Active Color', 'kadence' ),
						'palette' => true,
					),
				),
			),
		),
		'dropdown_navigation_background' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'dropdown_navigation_design',
			'priority'     => 20,
			'label'        => esc_html__( 'Dropdown Background', 'kadence' ),
			'default'      => kadence()->default( 'dropdown_navigation_background' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a:hover',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'hover',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item.current-menu-item > a, .header-navigation .header-menu-container ul ul li.menu-item.current_page_item > a',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'active',
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
					'active' => array(
						'tooltip' => __( 'Active Background', 'kadence' ),
						'palette' => true,
					),
				),
			),
		),
		'dropdown_navigation_divider' => array(
			'control_type' => 'kadence_border_control',
			'section'      => 'dropdown_navigation_design',
			'priority'     => 20,
			'label'        => esc_html__( 'Item Divider', 'kadence' ),
			'default'      => kadence()->default( 'dropdown_navigation_divider' ),
			'live_method'     => array(
				array(
					'type'     => 'css_border',
					'selector' => '.header-navigation ul ul li.menu-item',
					'pattern'  => '$',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
			'input_attrs'  => array(
				'responsive' => false,
			),
		),
		'dropdown_navigation_border_radius' => array(
			'control_type' => 'kadence_measure_control',
			'section'      => 'dropdown_navigation_design',
			'priority'     => 20,
			'default'      => kadence()->default( 'dropdown_navigation_border_radius' ),
			'label'        => esc_html__( 'Border Radius', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item, .header-menu-container ul.menu > li.kadence-menu-mega-enabled > ul > li.menu-item > a',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'measure',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a:hover',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'measure',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item.current-menu-item > a',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'measure',
				),
			),
			'input_attrs'  => array(
				'responsive' => false,
			),
		),
		'dropdown_navigation_typography' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'dropdown_navigation_design',
			'priority'     => 20,
			'label'        => esc_html__( 'Dropdown Font', 'kadence' ),
			'default'      => kadence()->default( 'dropdown_navigation_typography' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a',
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
				'id'      => 'dropdown_navigation_typography',
				'options' => 'no-color',
			),
		),
		'dropdown_navigation_shadow' => array(
			'control_type' => 'kadence_shadow_control',
			'section'      => 'dropdown_navigation_design',
			'priority'     => 20,
			'label'        => esc_html__( 'Dropdown Shadow', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css_boxshadow',
					'selector' => '.header-navigation .header-menu-container ul ul.submenu',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				),
			),
			'default'      => kadence()->default( 'dropdown_navigation_shadow' ),
		),
		'dropdown_navigation_reveal' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'dropdown_navigation',
			'priority'     => 20,
			'default'      => kadence()->default( 'dropdown_navigation_reveal' ),
			'label'        => esc_html__( 'Dropdown Reveal', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '.header-navigation',
					'pattern'  => 'header-navigation-dropdown-animation-$',
					'key'      => '',
				),
			),
			'input_attrs'  => array(
				'layout' => array(
					'none' => array(
						'name'    => __( 'None', 'kadence' ),
					),
					'fade' => array(
						'name'    => __( 'Fade', 'kadence' ),
					),
					'fade-up' => array(
						'name'    => __( 'Fade Up', 'kadence' ),
					),
					'fade-down' => array(
						'name'    => __( 'Fade Down', 'kadence' ),
					),
				),
				'responsive' => false,
			),
		),
		'dropdown_navigation_width' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'dropdown_navigation',
			'priority'     => 20,
			'label'        => esc_html__( 'Dropdown Width', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.wp-site-blocks .header-navigation .header-menu-container ul ul li.menu-item > a',
					'property' => 'width',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'dropdown_navigation_width' ),
			'input_attrs'  => array(
				'min'        => array(
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
					'vw'  => 0,
				),
				'max'        => array(
					'px'  => 600,
					'em'  => 50,
					'rem' => 50,
					'vw'  => 50,
				),
				'step'       => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'vw'  => 1,
				),
				'units'      => array( 'px', 'em', 'rem', 'vw' ),
				'responsive' => false,
			),
		),
		'dropdown_navigation_vertical_spacing' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'dropdown_navigation',
			'priority'     => 20,
			'label'        => esc_html__( 'Dropdown Items Vertical Spacing', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a',
					'property' => 'padding-top',
					'pattern'  => '$',
					'key'      => 'size',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a',
					'property' => 'padding-bottom',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'dropdown_navigation_vertical_spacing' ),
			'input_attrs'  => array(
				'min'        => array(
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
					'vh'  => 0,
				),
				'max'        => array(
					'px'  => 100,
					'em'  => 12,
					'rem' => 12,
					'vh'  => 12,
				),
				'step'       => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'vh'  => 0.01,
				),
				'units'      => array( 'px', 'em', 'rem', 'vh' ),
				'responsive' => false,
			),
		),
	)
);
