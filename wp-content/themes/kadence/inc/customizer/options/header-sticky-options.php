<?php
/**
 * Header Sticky Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'header_sticky_tabs'                  => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'header_sticky',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'header_sticky',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'header_sticky_design',
			],
			'active'  => 'general',
		],
	],
	'header_sticky_tabs_design'           => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'header_sticky_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'header_sticky',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'header_sticky_design',
			],
			'active'  => 'design',
		],
	],
	'header_sticky'                       => [
		'control_type' => 'kadence_select_control',
		'section'      => 'header_sticky',
		'priority'     => 10,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'header_sticky' ),
		'label'        => esc_html__( 'Enable Sticky Header?', 'kadence' ),
		'input_attrs'  => [
			'options' => [
				'no'              => [
					'name' => __( 'No', 'kadence' ),
				],
				'main'            => [
					'name' => __( 'Yes - Only Main Row', 'kadence' ),
				],
				'top_main'        => [
					'name' => __( 'Yes - Top Row & Main Row', 'kadence' ),
				],
				'top_main_bottom' => [
					'name' => __( 'Yes - Whole Header', 'kadence' ),
				],
				'top'             => [
					'name' => __( 'Yes - Only Top Row', 'kadence' ),
				],
				'bottom'          => [
					'name' => __( 'Yes - Only Bottom Row', 'kadence' ),
				],
			],
		],
	],
	'header_reveal_scroll_up'             => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'header_sticky',
		'default'      => kadence()->default( 'header_reveal_scroll_up' ),
		'label'        => esc_html__( 'Enable Reveal Sticky on Scroll up', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'header_sticky',
				'operator' => '!=',
				'value'    => 'no',
			],
		],
	],
	'header_sticky_shrink'                => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'header_sticky',
		'default'      => kadence()->default( 'header_sticky_shrink' ),
		'label'        => esc_html__( 'Enable Main Row Shrinking', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'header_sticky',
				'operator' => '!=',
				'value'    => 'no',
			],
		],
	],
	'header_sticky_main_shrink'           => [
		'control_type' => 'kadence_range_control',
		'section'      => 'header_sticky',
		'label'        => esc_html__( 'Main Row Shrink Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'header_sticky_shrink',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'header_sticky',
				'operator' => 'contain',
				'value'    => 'main',
			],
		],
		'default'      => kadence()->default( 'header_sticky_main_shrink' ),
		'input_attrs'  => [
			'min'        => [
				'px'  => 5,
				'em'  => 0,
				'rem' => 0,
			],
			'max'        => [
				'px'  => 400,
				'em'  => 12,
				'rem' => 12,
			],
			'step'       => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			],
			'units'      => [ 'px' ],
			'responsive' => false,
		],
	],
	'header_sticky_custom_logo'           => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'header_sticky',
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'header_sticky_custom_logo' ),
		'label'        => esc_html__( 'Different Logo for Stuck Header?', 'kadence' ),
		'context'      => [
			[
				'setting'    => 'logo_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'include',
				'responsive' => true,
				'value'      => 'logo',
			],
			[
				'setting'  => 'header_sticky',
				'operator' => '!=',
				'value'    => 'no',
			],
		],
	],
	'header_sticky_logo'                  => [
		'control_type' => 'media',
		'section'      => 'header_sticky',
		'transport'    => 'refresh',
		'mime_type'    => 'image',
		'default'      => '',
		'label'        => esc_html__( 'Stuck Header Logo', 'kadence' ),
		'context'      => [
			[
				'setting'    => 'logo_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'include',
				'responsive' => true,
				'value'      => 'logo',
			],
			[
				'setting'  => 'header_sticky',
				'operator' => '!=',
				'value'    => 'no',
			],
			[
				'setting'  => 'header_sticky_custom_logo',
				'operator' => '=',
				'value'    => true,
			],
		],
	],
	'header_sticky_logo_width'            => [
		'control_type' => 'kadence_range_control',
		'section'      => 'header_sticky',
		'label'        => esc_html__( 'Logo Max Width', 'kadence' ),
		'description'  => esc_html__( 'Define the maxium width for the logo', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'header_sticky',
				'operator' => '!=',
				'value'    => 'no',
			],
			[
				'setting'    => 'logo_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'include',
				'responsive' => true,
				'value'      => 'logo',
			],
			[
				'setting'  => 'header_sticky_custom_logo',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'header_sticky_logo',
				'operator' => '!empty',
				'value'    => '',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .site-branding img',
				'property' => 'max-width',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'header_sticky_logo_width' ),
		'input_attrs'  => [
			'min'   => [
				'px'  => 10,
				'em'  => 1,
				'rem' => 1,
				'vw'  => 2,
				'%'   => 2,
			],
			'max'   => [
				'px'  => 800,
				'em'  => 12,
				'rem' => 12,
				'vw'  => 80,
				'%'   => 80,
			],
			'step'  => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vw'  => 1,
				'%'   => 1,
			],
			'units' => [ 'px', 'em', 'rem', 'vw', '%' ],
		],
	],
	'info_mobile_header_sticky'           => [
		'control_type' => 'kadence_title_control',
		'section'      => 'header_sticky',
		'priority'     => 20,
		'label'        => esc_html__( 'Mobile Sticky', 'kadence' ),
		'settings'     => false,
	],
	'mobile_header_sticky'                => [
		'control_type' => 'kadence_select_control',
		'section'      => 'header_sticky',
		'priority'     => 20,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'mobile_header_sticky' ),
		'label'        => esc_html__( 'Enable Sticky for Mobile?', 'kadence' ),
		'input_attrs'  => [
			'options' => [
				'no'              => [
					'name' => __( 'No', 'kadence' ),
				],
				'main'            => [
					'name' => __( 'Yes - Only Main Row', 'kadence' ),
				],
				'top_main'        => [
					'name' => __( 'Yes - Top Row & Main Row', 'kadence' ),
				],
				'top_main_bottom' => [
					'name' => __( 'Yes - Whole Header', 'kadence' ),
				],
				'top'             => [
					'name' => __( 'Yes - Only Top Row', 'kadence' ),
				],
				'bottom'          => [
					'name' => __( 'Yes - Only Bottom Row', 'kadence' ),
				],
			],
		],
	],
	'mobile_header_reveal_scroll_up'      => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'header_sticky',
		'priority'     => 20,
		'default'      => kadence()->default( 'header_reveal_scroll_up' ),
		'label'        => esc_html__( 'Enable Reveal Sticky on Scroll up', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'mobile_header_sticky',
				'operator' => '!=',
				'value'    => 'no',
			],
		],
	],
	'mobile_header_sticky_shrink'         => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'header_sticky',
		'priority'     => 20,
		'default'      => kadence()->default( 'mobile_header_sticky_shrink' ),
		'label'        => esc_html__( 'Enabled Main Row Shrinking', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'mobile_header_sticky',
				'operator' => '!=',
				'value'    => 'no',
			],
		],
	],
	'mobile_header_sticky_main_shrink'    => [
		'control_type' => 'kadence_range_control',
		'section'      => 'header_sticky',
		'priority'     => 20,
		'label'        => esc_html__( 'Main Row Shrink Height', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'mobile_header_sticky_shrink',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'mobile_header_sticky',
				'operator' => 'contain',
				'value'    => 'main',
			],
		],
		'default'      => kadence()->default( 'mobile_header_sticky_main_shrink' ),
		'input_attrs'  => [
			'min'        => [
				'px'  => 5,
				'em'  => 0,
				'rem' => 0,
			],
			'max'        => [
				'px'  => 400,
				'em'  => 12,
				'rem' => 12,
			],
			'step'       => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			],
			'units'      => [ 'px' ],
			'responsive' => false,
		],
	],
	'header_sticky_custom_mobile_logo'    => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'header_sticky',
		'transport'    => 'refresh',
		'priority'     => 20,
		'default'      => kadence()->default( 'use_mobile_logo' ),
		'label'        => esc_html__( 'Different Logo for Mobile?', 'kadence' ),
		'context'      => [
			[
				'setting'    => 'logo_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'include',
				'responsive' => true,
				'value'      => 'logo',
			],
			[
				'setting'  => 'mobile_header_sticky',
				'operator' => '!=',
				'value'    => 'no',
			],
		],
	],
	'header_sticky_mobile_logo'           => [
		'control_type' => 'media',
		'section'      => 'header_sticky',
		'transport'    => 'refresh',
		'priority'     => 20,
		'mime_type'    => 'image',
		'default'      => '',
		'label'        => esc_html__( 'Mobile Logo', 'kadence' ),
		'context'      => [
			[
				'setting'    => 'logo_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'include',
				'responsive' => true,
				'value'      => 'logo',
			],
			[
				'setting'  => 'mobile_header_sticky',
				'operator' => '!=',
				'value'    => 'no',
			],
			[
				'setting'  => 'header_sticky_custom_mobile_logo',
				'operator' => '=',
				'value'    => true,
			],
		],
	],
	'header_sticky_site_title_color'      => [
		'control_type' => 'kadence_color_control',
		'section'      => 'header_sticky_design',
		'label'        => esc_html__( 'Site Title Color', 'kadence' ),
		'default'      => kadence()->default( 'header_sticky_site_title_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .site-branding .site-title, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .site-branding .site-description',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color' => [
					'tooltip' => __( 'Initial Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'header_sticky_logo_icon_color'       => [
		'control_type' => 'kadence_color_control',
		'section'      => 'header_sticky_design',
		'label'        => esc_html__( 'Logo Icon Color', 'kadence' ),
		'default'      => kadence()->default( 'header_sticky_logo_icon_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .site-branding .logo-icon',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color' => [
					'tooltip' => __( 'Initial Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'header_sticky_navigation_color'      => [
		'control_type' => 'kadence_color_control',
		'section'      => 'header_sticky_design',
		'label'        => esc_html__( 'Navigation Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_sticky_navigation_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li > a, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-toggle-open-container .menu-toggle-open, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .search-toggle-open-container .search-toggle-open',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li > a:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-toggle-open-container .menu-toggle-open:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .search-toggle-open-container .search-toggle-open:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li.current-menu-item > a, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li.current_page_item > a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'active',
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
	'header_sticky_navigation_background' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'header_sticky_design',
		'label'        => esc_html__( 'Navigation Items Background', 'kadence' ),
		'default'      => kadence()->default( 'header_sticky_navigation_background' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li > a',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li > a:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li.current-menu-item > a, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li.current_page_item > a',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'active',
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
	'header_sticky_button_color'          => [
		'control_type' => 'kadence_color_control',
		'section'      => 'header_sticky_design',
		'label'        => esc_html__( 'Button Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_sticky_button_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-button, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-header-button-wrap .mobile-header-button',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-button:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-header-button-wrap .mobile-header-button:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-button, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-header-button-wrap .mobile-header-button',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'background',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-button:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-header-button-wrap .mobile-header-button:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'backgroundHover',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-button, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-header-button-wrap .mobile-header-button',
				'property' => 'border-color',
				'pattern'  => '$',
				'key'      => 'border',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-button:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-header-button-wrap .mobile-header-button:hover',
				'property' => 'border-color',
				'pattern'  => '$',
				'key'      => 'borderHover',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color'           => [
					'tooltip' => __( 'Color', 'kadence' ),
					'palette' => true,
				],
				'hover'           => [
					'tooltip' => __( 'Hover Color', 'kadence' ),
					'palette' => true,
				],
				'background'      => [
					'tooltip' => __( 'Background', 'kadence' ),
					'palette' => true,
				],
				'backgroundHover' => [
					'tooltip' => __( 'Background Hover', 'kadence' ),
					'palette' => true,
				],
				'border'          => [
					'tooltip' => __( 'Border', 'kadence' ),
					'palette' => true,
				],
				'borderHover'     => [
					'tooltip' => __( 'Border Hover', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'header_sticky_social_color'          => [
		'control_type' => 'kadence_color_control',
		'section'      => 'header_sticky_design',
		'label'        => esc_html__( 'Social Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_sticky_social_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-social-wrap a.social-button, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-social-wrap a.social-button',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-social-wrap a.social-button:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-social-wrap a.social-button:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-social-wrap a.social-button, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-social-wrap a.social-button',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'background',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-social-wrap a.social-button:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-social-wrap a.social-button:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'backgroundHover',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-social-wrap a.social-button, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-social-wrap a.social-button',
				'property' => 'border-color',
				'pattern'  => '$',
				'key'      => 'border',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-social-wrap a.social-button:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-social-wrap a.social-button:hover',
				'property' => 'border-color',
				'pattern'  => '$',
				'key'      => 'borderHover',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color'           => [
					'tooltip' => __( 'Color', 'kadence' ),
					'palette' => true,
				],
				'hover'           => [
					'tooltip' => __( 'Hover Color', 'kadence' ),
					'palette' => true,
				],
				'background'      => [
					'tooltip' => __( 'Background', 'kadence' ),
					'palette' => true,
				],
				'backgroundHover' => [
					'tooltip' => __( 'Background Hover', 'kadence' ),
					'palette' => true,
				],
				'border'          => [
					'tooltip' => __( 'Border', 'kadence' ),
					'palette' => true,
				],
				'borderHover'     => [
					'tooltip' => __( 'Border Hover', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'header_sticky_html_color'            => [
		'control_type' => 'kadence_color_control',
		'section'      => 'header_sticky_design',
		'label'        => esc_html__( 'HTML Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_sticky_html_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-html,#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-html',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-html a, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-html a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'link',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-html a:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-html a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'context'      => [
			[
				'setting'  => '__device',
				'operator' => '==',
				'value'    => 'desktop',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color' => [
					'tooltip' => __( 'Color', 'kadence' ),
					'palette' => true,
				],
				'link'  => [
					'tooltip' => __( 'Link Color', 'kadence' ),
					'palette' => true,
				],
				'hover' => [
					'tooltip' => __( 'Link Hover', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'header_sticky_background'            => [
		'control_type' => 'kadence_background_control',
		'section'      => 'header_sticky_design',
		'label'        => esc_html__( 'Sticky Header Background', 'kadence' ),
		'default'      => kadence()->default( 'header_sticky_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '.wp-site-blocks #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start):not(.site-header-row-container), .wp-site-blocks #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) > .site-header-row-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Sticky Header Background', 'kadence' ),
		],
	],
	'header_sticky_bottom_border'         => [
		'control_type' => 'kadence_border_control',
		'section'      => 'header_sticky_design',
		'label'        => esc_html__( 'Sticky Bottom Border', 'kadence' ),
		'default'      => kadence()->default( 'header_sticky_bottom_border' ),
		'live_method'  => [
			[
				'type'     => 'css_border',
				'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start)',
				'property' => 'border-bottom',
				'pattern'  => '$',
				'key'      => 'border',
			],
		],
	],
	'header_sticky_box_shadow'            => [
		'control_type' => 'kadence_shadow_control',
		'section'      => 'header_sticky_design',
		'label'        => esc_html__( 'Sticky Header Box Shadow', 'kadence' ),
		'default'      => kadence()->default( 'header_sticky_box_shadow' ),
		'live_method'  => [
			[
				'type'     => 'css_boxshadow',
				'selector' => '.site-main-header-wrap.site-header-row-container.site-header-focus-item.site-header-row-layout-standard.kadence-sticky-header.item-is-fixed.item-is-stuck, .site-header-upper-inner-wrap.kadence-sticky-header.item-is-fixed.item-is-stuck, .site-header-inner-wrap.kadence-sticky-header.item-is-fixed.item-is-stuck, .site-top-header-wrap.site-header-row-container.site-header-focus-item.site-header-row-layout-standard.kadence-sticky-header.item-is-fixed.item-is-stuck, .site-bottom-header-wrap.site-header-row-container.site-header-focus-item.site-header-row-layout-standard.kadence-sticky-header.item-is-fixed.item-is-stuck',
				'property' => 'box-shadow',
				'pattern'  => '$',
				'key'      => 'box-shadow',
			],
		],
	],
];
if ( class_exists( 'woocommerce' ) ) {
	$settings = array_merge(
		$settings,
		[
			'header_sticky_cart_color'       => [
				'control_type' => 'kadence_color_control',
				'section'      => 'header_sticky_design',
				'label'        => esc_html__( 'Cart Colors', 'kadence' ),
				'default'      => kadence()->default( 'header_sticky_cart_color' ),
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					],
					[
						'type'     => 'css',
						'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button:hover, , #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button:hover',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'hover',
					],
					[
						'type'     => 'css',
						'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'background',
					],
					[
						'type'     => 'css',
						'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button:hover',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'backgroundHover',
					],
				],
				'input_attrs'  => [
					'colors' => [
						'color'           => [
							'tooltip' => __( 'Color', 'kadence' ),
							'palette' => true,
						],
						'hover'           => [
							'tooltip' => __( 'Hover Color', 'kadence' ),
							'palette' => true,
						],
						'background'      => [
							'tooltip' => __( 'Background', 'kadence' ),
							'palette' => true,
						],
						'backgroundHover' => [
							'tooltip' => __( 'Background Hover', 'kadence' ),
							'palette' => true,
						],
					],
				],
			],
			'header_sticky_cart_total_color' => [
				'control_type' => 'kadence_color_control',
				'section'      => 'header_sticky_design',
				'label'        => esc_html__( 'Cart Total Colors', 'kadence' ),
				'default'      => kadence()->default( 'header_sticky_cart_total_color' ),
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button .header-cart-total, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button .header-cart-total',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					],
					[
						'type'     => 'css',
						'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button:hover .header-cart-total, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button:hover .header-cart-total',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'hover',
					],
					[
						'type'     => 'css',
						'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button .header-cart-total, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button .header-cart-total',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'background',
					],
					[
						'type'     => 'css',
						'selector' => '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button:hover .header-cart-total, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button:hover .header-cart-total',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'backgroundHover',
					],
				],
				'input_attrs'  => [
					'colors' => [
						'color'           => [
							'tooltip' => __( 'Color', 'kadence' ),
							'palette' => true,
						],
						'hover'           => [
							'tooltip' => __( 'Hover Color', 'kadence' ),
							'palette' => true,
						],
						'background'      => [
							'tooltip' => __( 'Background', 'kadence' ),
							'palette' => true,
						],
						'backgroundHover' => [
							'tooltip' => __( 'Background Hover', 'kadence' ),
							'palette' => true,
						],
					],
				],
			],
		]
	);
}

Theme_Customizer::add_settings( $settings );
