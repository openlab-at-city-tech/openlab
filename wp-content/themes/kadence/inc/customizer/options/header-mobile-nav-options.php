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
	'mobile_navigation_tabs'             => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'mobile_navigation',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	],
	'mobile_navigation_link'             => [
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'mobile_navigation',
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
	'mobile_navigation_color'            => [
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_navigation',
		'label'        => esc_html__( 'Colors', 'kadence' ),
		'default'      => kadence()->default( 'mobile_navigation_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.mobile-navigation ul li:not(.menu-item-has-children) > a, .mobile-navigation ul li.menu-item-has-children > .drawer-nav-drop-wrap',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.mobile-navigation ul li:not(.menu-item-has-children) > a:hover, .mobile-navigation ul li.menu-item-has-children > .drawer-nav-drop-wrap:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '.mobile-navigation ul li.current-menu-item:not(.menu-item-has-children) > a, .mobile-navigation ul li.current-menu-item.menu-item-has-children > .drawer-nav-drop-wrap',
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
	'mobile_navigation_background'       => [
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_navigation',
		'priority'     => 20,
		'label'        => esc_html__( 'Background', 'kadence' ),
		'default'      => kadence()->default( 'mobile_navigation_background' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.mobile-navigation ul li:not(.menu-item-has-children) > a, .mobile-navigation ul li.menu-item-has-children > .drawer-nav-drop-wrap',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.mobile-navigation ul li:not(.menu-item-has-children) > a:hover, .mobile-navigation ul li.menu-item-has-children > .drawer-nav-drop-wrap:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '.mobile-navigation ul li.current-menu-item:not(.menu-item-has-children) > a, .mobile-navigation ul li.current-menu-item.menu-item-has-children > .drawer-nav-drop-wrap',
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
	'mobile_navigation_divider'          => [
		'control_type' => 'kadence_border_control',
		'section'      => 'mobile_navigation',
		'priority'     => 20,
		'label'        => esc_html__( 'Item Divider', 'kadence' ),
		'default'      => kadence()->default( 'mobile_navigation_divider' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_border',
				'selector' => '.mobile-navigation ul li.menu-item-has-children .drawer-nav-drop-wrap, .mobile-navigation ul li:not(.menu-item-has-children) a ',
				'pattern'  => '$',
				'property' => 'border-bottom',
				'pattern'  => '$',
				'key'      => 'border',
			],
			[
				'type'     => 'css_border',
				'selector' => '.mobile-navigation ul li.menu-item-has-children .drawer-nav-drop-wrap button',
				'pattern'  => '$',
				'property' => 'border-left',
				'pattern'  => '$',
				'key'      => 'border',
			],
		],
		'input_attrs'  => [
			'responsive' => false,
		],
	],
	'mobile_navigation_typography'       => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'mobile_navigation',
		'priority'     => 20,
		'label'        => esc_html__( 'Font', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'default'      => kadence()->default( 'mobile_navigation_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.mobile-navigation ul li',
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
			'id'      => 'mobile_navigation_typography',
			'options' => 'no-color',
		],
	],
	'mobile_navigation_vertical_spacing' => [
		'control_type' => 'kadence_range_control',
		'section'      => 'mobile_navigation',
		'priority'     => 20,
		'label'        => esc_html__( 'Items Vertical Spacing', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.mobile-navigation ul li a',
				'property' => 'padding-top',
				'pattern'  => '$',
				'key'      => 'size',
			],
			[
				'type'     => 'css',
				'selector' => '.mobile-navigation ul li a',
				'property' => 'padding-bottom',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'mobile_navigation_vertical_spacing' ),
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
	'mobile_navigation_collapse'         => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'mobile_navigation',
		'priority'     => 20,
		'default'      => kadence()->default( 'mobile_navigation_collapse' ),
		'label'        => esc_html__( 'Collapse sub menu items?', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'partial'      => [
			'selector'            => '#mobile-site-navigation',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\mobile_navigation',
		],
	],
	'mobile_navigation_parent_toggle'    => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'mobile_navigation',
		'priority'     => 20,
		'default'      => kadence()->default( 'mobile_navigation_parent_toggle' ),
		'label'        => esc_html__( 'Entire parent menu item expands sub menu', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting' => 'mobile_navigation_collapse',
				'value'   => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.mobile-navigation',
				'pattern'  => 'drawer-navigation-parent-toggle-$',
				'key'      => '',
			],
		],
	],
];

Theme_Customizer::add_settings( $settings );

