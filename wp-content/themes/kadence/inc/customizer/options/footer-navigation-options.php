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
	'footer_navigation_tabs'             => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'footer_navigation',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	],
	'footer_navigation_link'             => [
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'footer_navigation',
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
	'footer_navigation_spacing'          => [
		'control_type' => 'kadence_range_control',
		'section'      => 'footer_navigation',
		'priority'     => 5,
		'label'        => esc_html__( 'Items Spacing', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul > li > a',
				'property' => 'padding-left',
				'pattern'  => 'calc($ / 2)',
				'key'      => 'size',
			],
			[
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul > li > a',
				'property' => 'padding-right',
				'pattern'  => 'calc($ / 2)',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'footer_navigation_spacing' ),
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
	'footer_navigation_stretch'          => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'footer_navigation',
		'priority'     => 5,
		'default'      => kadence()->default( 'footer_navigation_stretch' ),
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
				'selector' => '.footer-navigation-wrap',
				'pattern'  => 'footer-navigation-layout-stretch-$',
				'key'      => 'switch',
			],
		],
	],
	'footer_navigation_vertical_spacing' => [
		'control_type' => 'kadence_range_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Items Top and Bottom Padding', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul > li > a',
				'property' => 'padding-top',
				'pattern'  => '$',
				'key'      => 'size',
			],
			[
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul > li > a',
				'property' => 'padding-bottom',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'footer_navigation_vertical_spacing' ),
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
	'footer_navigation_align'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Content Align', 'kadence' ),
		'priority'     => 5,
		'default'      => kadence()->default( 'footer_navigation_align' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.footer-navigation-wrap',
				'pattern'  => [
					'desktop' => 'content-align-$',
					'tablet'  => 'content-tablet-align-$',
					'mobile'  => 'content-mobile-align-$',
				],
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'left'   => [
					'tooltip'  => __( 'Left Align', 'kadence' ),
					'dashicon' => 'editor-alignleft',
				],
				'center' => [
					'tooltip'  => __( 'Center Align', 'kadence' ),
					'dashicon' => 'editor-aligncenter',
				],
				'right'  => [
					'tooltip'  => __( 'Right Align', 'kadence' ),
					'dashicon' => 'editor-alignright',
				],
			],
			'responsive' => true,
		],
	],
	'footer_navigation_vertical_align'   => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Content Vertical Align', 'kadence' ),
		'priority'     => 5,
		'default'      => kadence()->default( 'footer_navigation_vertical_align' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.footer-navigation-wrap',
				'pattern'  => [
					'desktop' => 'content-valign-$',
					'tablet'  => 'content-tablet-valign-$',
					'mobile'  => 'content-mobile-valign-$',
				],
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'top'    => [
					'tooltip' => __( 'Top Align', 'kadence' ),
					'icon'    => 'aligntop',
				],
				'middle' => [
					'tooltip' => __( 'Middle Align', 'kadence' ),
					'icon'    => 'alignmiddle',
				],
				'bottom' => [
					'tooltip' => __( 'Bottom Align', 'kadence' ),
					'icon'    => 'alignbottom',
				],
			],
			'responsive' => true,
		],
	],
	'footer_navigation_color'            => [
		'control_type' => 'kadence_color_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Navigation Colors', 'kadence' ),
		'default'      => kadence()->default( 'footer_navigation_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li.current-menu-item > a, #colophon .footer-navigation .footer-menu-container > ul li.current_page_item > a',
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
	'footer_navigation_background'       => [
		'control_type' => 'kadence_color_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Navigation Background', 'kadence' ),
		'default'      => kadence()->default( 'footer_navigation_background' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li a',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li a:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li.current-menu-item > a, #colophon .footer-navigation .footer-menu-container > ul > li.current_page_item > a',
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
	'footer_navigation_typography'       => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Navigation Font', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'default'      => kadence()->default( 'footer_navigation_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li a',
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
			'id'      => 'footer_navigation_typography',
			'options' => 'no-color',
		],
	],
];

Theme_Customizer::add_settings( $settings );

