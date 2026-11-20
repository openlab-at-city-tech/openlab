<?php
/**
 * Header Builder Options
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
	'primary_navigation_tabs'             => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'primary_navigation',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	],
	'primary_navigation_link'             => [
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'primary_navigation',
		'settings'     => false,
		'priority'     => 5,
		'label'        => esc_html__( 'Select Menu', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'input_attrs'  => [
			'section' => 'menu_locations',
		],
	],
	'primary_navigation_spacing'          => [
		'control_type' => 'kadence_range_control',
		'section'      => 'primary_navigation',
		'priority'     => 5,
		'label'        => esc_html__( 'Items Spacing', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.main-navigation .primary-menu-container > ul > li.menu-item > a',
				'property' => 'padding-left',
				'pattern'  => 'calc($ / 2)',
				'key'      => 'size',
			],
			[
				'type'     => 'css',
				'selector' => '.main-navigation .primary-menu-container > ul > li.menu-item > a',
				'property' => 'padding-right',
				'pattern'  => 'calc($ / 2)',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'primary_navigation_spacing' ),
		'input_attrs'  => [
			'min'        => [
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
				'vw'  => 0,
			],
			'max'        => [
				'px'  => 100,
				'em'  => 12,
				'rem' => 12,
				'vw'  => 12,
			],
			'step'       => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vw'  => 0.01,
			],
			'units'      => [ 'px', 'em', 'rem', 'vw' ],
			'responsive' => false,
		],
	],
	'primary_navigation_open_type'        => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'primary_navigation',
		'priority'     => 10,
		'default'      => kadence()->default( 'primary_navigation_open_type' ),
		'label'        => esc_html__( 'Open on: ', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'hover' => [
					'tooltip' => __( 'Hover', 'kadence' ),
					'name'    => __( 'Hover', 'kadence' ),
					'icon'    => '',
				],
				'click' => [
					'tooltip' => __( 'Click', 'kadence' ),
					'name'    => __( 'Click', 'kadence' ),
					'icon'    => '',
				],
			],
			'responsive' => false,
			'class'      => 'radio-btn-width-50',
		],
	],
	'primary_navigation_stretch'          => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'primary_navigation',
		'priority'     => 6,
		'default'      => kadence()->default( 'primary_navigation_stretch' ),
		'label'        => esc_html__( 'Stretch Menu?', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.site-header-item-main-navigation',
				'pattern'  => 'header-navigation-layout-stretch-$',
				'key'      => 'switch',
			],
		],
	],
	'primary_navigation_fill_stretch'     => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'primary_navigation',
		'priority'     => 6,
		'default'      => kadence()->default( 'primary_navigation_fill_stretch' ),
		'label'        => esc_html__( 'Fill and Center Menu Items?', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'primary_navigation_stretch',
				'operator' => '==',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.site-header-item-main-navigation',
				'pattern'  => 'header-navigation-layout-fill-stretch-$',
				'key'      => 'switch',
			],
		],
	],
	'primary_navigation_style'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'primary_navigation',
		'priority'     => 10,
		'default'      => kadence()->default( 'primary_navigation_style' ),
		'label'        => esc_html__( 'Navigation Style', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.main-navigation',
				'pattern'  => 'header-navigation-style-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'standard'             => [
					'tooltip' => __( 'Standard', 'kadence' ),
					'name'    => __( 'Standard', 'kadence' ),
					'icon'    => '',
				],
				'fullheight'           => [
					'tooltip' => __( 'Menu items are full height', 'kadence' ),
					'name'    => __( 'Full Height', 'kadence' ),
					'icon'    => '',
				],
				'underline'            => [
					'tooltip' => __( 'Underline Hover/Active', 'kadence' ),
					'name'    => __( 'Underline', 'kadence' ),
					'icon'    => '',
				],
				'underline-fullheight' => [
					'tooltip' => __( 'Full Height Underline Hover/Active', 'kadence' ),
					'name'    => __( 'Full Height Underline', 'kadence' ),
					'icon'    => '',
				],
			],
			'responsive' => false,
			'class'      => 'radio-btn-width-50',
		],
	],
	'primary_navigation_vertical_spacing' => [
		'control_type' => 'kadence_range_control',
		'section'      => 'primary_navigation',
		'label'        => esc_html__( 'Items Top and Bottom Padding', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'    => 'primary_navigation_style',
				'operator'   => 'sub_object_does_not_contain',
				'sub_key'    => 'layout',
				'responsive' => false,
				'value'      => 'fullheight',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.main-navigation .primary-menu-container > ul > li.menu-item > a',
				'property' => 'padding-top',
				'pattern'  => '$',
				'key'      => 'size',
			],
			[
				'type'     => 'css',
				'selector' => '.main-navigation .primary-menu-container > ul > li.menu-item > a',
				'property' => 'padding-bottom',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'primary_navigation_vertical_spacing' ),
		'input_attrs'  => [
			'min'        => [
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
				'vh'  => 0,
			],
			'max'        => [
				'px'  => 100,
				'em'  => 12,
				'rem' => 12,
				'vh'  => 12,
			],
			'step'       => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vh'  => 0.01,
			],
			'units'      => [ 'px', 'em', 'rem', 'vh' ],
			'responsive' => false,
		],
	],
	'primary_navigation_color'            => [
		'control_type' => 'kadence_color_control',
		'section'      => 'primary_navigation',
		'label'        => esc_html__( 'Navigation Colors', 'kadence' ),
		'default'      => kadence()->default( 'primary_navigation_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.main-navigation .primary-menu-container > ul > li.menu-item > a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.main-navigation .primary-menu-container > ul > li.menu-item > a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '.main-navigation .primary-menu-container > ul > li.menu-item.current-menu-item > a, .main-navigation .primary-menu-container > ul > li.current_page_item > a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'active',
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
				'color'  => [
					'tooltip' => __( 'Initial Color', 'kadence' ),
					'palette' => true,
				],
				'hover'  => [
					'tooltip' => __( 'Hover Color', 'kadence' ),
					'palette' => true,
				],
				'active' => [
					'tooltip' => __( 'Active Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'primary_navigation_background'       => [
		'control_type' => 'kadence_color_control',
		'section'      => 'primary_navigation',
		'label'        => esc_html__( 'Navigation Background', 'kadence' ),
		'default'      => kadence()->default( 'primary_navigation_background' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.main-navigation .primary-menu-container > ul > li.menu-item > a',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.main-navigation .primary-menu-container > ul > li.menu-item > a:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '.main-navigation .primary-menu-container > ul > li.menu-item.current-menu-item > a, .main-navigation .primary-menu-container > ul > li.current_page_item > a',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'active',
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
				'color'  => [
					'tooltip' => __( 'Initial Background', 'kadence' ),
					'palette' => true,
				],
				'hover'  => [
					'tooltip' => __( 'Hover Background', 'kadence' ),
					'palette' => true,
				],
				'active' => [
					'tooltip' => __( 'Active Background', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'primary_navigation_parent_active'    => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'primary_navigation',
		'default'      => kadence()->default( 'primary_navigation_parent_active' ),
		'label'        => esc_html__( 'Make Parent of Current Menu Item Active?', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
	],
	'primary_navigation_typography'       => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'primary_navigation',
		'label'        => esc_html__( 'Navigation Font', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'default'      => kadence()->default( 'primary_navigation_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.main-navigation .primary-menu-container > ul > li.menu-item a',
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
			'id'      => 'primary_navigation_typography',
			'options' => 'no-color',
		],
	],
	'info_primary_submenu'                => [
		'control_type' => 'kadence_title_control',
		'section'      => 'primary_navigation',
		'priority'     => 20,
		'label'        => esc_html__( 'Dropdown Options', 'kadence' ),
		'settings'     => false,
	],
	'primary_dropdown_link'               => [
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'primary_navigation',
		'settings'     => false,
		'priority'     => 20,
		'label'        => esc_html__( 'Dropdown Options', 'kadence' ),
		'input_attrs'  => [
			'section' => 'kadence_customizer_dropdown_navigation',
		],
	],
];

Theme_Customizer::add_settings( $settings );

