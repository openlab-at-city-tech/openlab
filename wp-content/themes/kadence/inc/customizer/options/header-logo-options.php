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
	'logo_settings'                => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'title_tagline',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	],
	'logo_width'                   => [
		'control_type' => 'kadence_range_control',
		'section'      => 'title_tagline',
		'priority'     => 5,
		'label'        => esc_html__( 'Logo Max Width', 'kadence' ),
		'description'  => esc_html__( 'Define the maxium width for the logo', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'custom_logo',
				'operator' => '!empty',
				'value'    => '',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .custom-logo',
				'property' => 'max-width',
				'pattern'  => '$',
				'key'      => 'size',
			],
			[
				'type'     => 'css',
				'selector' => '#masthead .site-branding a.brand img.svg-logo-image',
				'property' => 'width',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'logo_width' ),
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
				'em'  => 50,
				'rem' => 50,
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
			'units' => [ 'px', 'em', 'rem', 'vw' ],
		],
	],
	'info_transparent_logo_in_use' => [
		'control_type' => 'kadence_title_control',
		'section'      => 'title_tagline',
		'priority'     => 5,
		'description'  => esc_html__( '*NOTICE Custom transparent logo will show on pages that have the transparent header enabled. Change that in your transparent header settings.', 'kadence' ),
		'settings'     => false,
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'    => 'logo_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'include',
				'responsive' => true,
				'value'      => 'logo',
			],
			[
				'setting'  => 'transparent_header_enable',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'transparent_header_custom_logo',
				'operator' => '=',
				'value'    => true,
			],
		],
	],
	'use_mobile_logo'              => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'title_tagline',
		'transport'    => 'refresh',
		'priority'     => 6,
		'default'      => kadence()->default( 'use_mobile_logo' ),
		'label'        => esc_html__( 'Different Logo for Mobile?', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => '__device',
				'operator' => 'in',
				'value'    => [ 'tablet', 'mobile' ],
			],
		],
	],
	'mobile_logo'                  => [
		'control_type' => 'media',
		'section'      => 'title_tagline',
		'transport'    => 'refresh',
		'priority'     => 6,
		'mime_type'    => 'image',
		'default'      => '',
		'label'        => esc_html__( 'Mobile Logo', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'use_mobile_logo',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => '__device',
				'operator' => 'in',
				'value'    => [ 'tablet', 'mobile' ],
			],
		],
	],
	'use_logo_icon'                => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'title_tagline',
		'transport'    => 'refresh',
		'priority'     => 6,
		'default'      => kadence()->default( 'use_logo_icon' ),
		'label'        => esc_html__( 'Use Logo Icon?', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'custom_logo',
				'operator' => 'empty',
				'value'    => '',
			],
		],
		'partial'      => [
			'selector'            => '#masthead',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\header_markup',
		],
	],
	'logo_icon'                    => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'title_tagline',
		'priority'     => 6,
		'default'      => kadence()->default( 'logo_icon' ),
		'label'        => esc_html__( 'Logo Icon', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'custom_logo',
				'operator' => 'empty',
				'value'    => '',
			],
			[
				'setting'  => 'use_logo_icon',
				'operator' => '=',
				'value'    => true,
			],
		],
		'partial'      => [
			'selector'            => '.logo-icon',
			'container_inclusive' => false,
			'render_callback'     => 'Kadence\logo_icon',
		],
		'input_attrs'  => [
			'layout'     => [
				'logoArrow'  => [
					'icon' => 'logoArrow',
				],
				'logoCircle' => [
					'icon' => 'logoCircle',
				],
				'logoLine'   => [
					'icon' => 'logoLine',
				],
				'custom'     => [
					'name' => __( 'Custom', 'kadence' ),
				],
			],
			'responsive' => false,
			'class'      => 'radio-icon-padding',
		],
	],
	'logo_icon_custom'             => [
		'control_type' => 'kadence_textarea_control',
		'section'      => 'title_tagline',
		'priority'     => 6,
		'default'      => kadence()->default( 'logo_icon_custom' ),
		'partial'      => [
			'selector'            => '.logo-icon',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\logo_icon',
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'custom_logo',
				'operator' => 'empty',
				'value'    => '',
			],
			[
				'setting'  => 'use_logo_icon',
				'operator' => '=',
				'value'    => true,
			],
			[
				'setting'  => 'logo_icon',
				'operator' => '=',
				'value'    => 'custom',
			],
		],
		'input_attrs'  => [
			'id' => 'logo_icon_custom',
		],
	],
	'logo_icon_width'              => [
		'control_type' => 'kadence_range_control',
		'section'      => 'title_tagline',
		'priority'     => 6,
		'label'        => esc_html__( 'Logo Icon Width', 'kadence' ),
		'description'  => esc_html__( 'Define the width for the logo icon', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'custom_logo',
				'operator' => 'empty',
				'value'    => '',
			],
			[
				'setting'  => 'use_logo_icon',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .logo-icon',
				'property' => 'max-width',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'logo_icon_width' ),
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
				'em'  => 50,
				'rem' => 50,
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
			'units' => [ 'px', 'em', 'rem', 'vw' ],
		],
	],
	'logo_icon_color'              => [
		'control_type' => 'kadence_color_control',
		'section'      => 'title_tagline',
		'label'        => esc_html__( 'Logo Icon Color', 'kadence' ),
		'default'      => kadence()->default( 'logo_icon_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .logo-icon',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'  => 'custom_logo',
				'operator' => 'empty',
				'value'    => '',
			],
			[
				'setting'  => 'use_logo_icon',
				'operator' => '=',
				'value'    => true,
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
	'logo_layout'                  => [
		'control_type' => 'kadence_multi_radio_icon_control',
		'section'      => 'title_tagline',
		'priority'     => 6,
		'default'      => kadence()->default( 'logo_layout' ),
		'label'        => esc_html__( 'Logo Layout', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'partial'      => [
			'selector'            => '#masthead',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\header_markup',
		],
	],
	'brand_typography'             => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'title_tagline',
		'label'        => esc_html__( 'Site Title Font', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'    => 'logo_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'include',
				'responsive' => true,
				'value'      => 'title',
			],
		],
		'default'      => kadence()->default( 'brand_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => [
					'desktop' => '#main-header .site-branding .site-title',
					'tablet'  => '#mobile-header .site-branding .site-title',
					'mobile'  => '#mobile-header .site-branding .site-title',
				],
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
			'id' => 'brand_typography',
		],
	],
	'brand_typography_color'       => [
		'control_type' => 'kadence_color_control',
		'section'      => 'title_tagline',
		'label'        => esc_html__( 'Site Title Hover and Active Colors', 'kadence' ),
		'default'      => kadence()->default( 'brand_typography_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .site-branding .site-title:hover',
				'pattern'  => '$',
				'property' => 'color',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => '#main-header .header-html a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
			[
				'type'     => 'css',
				'selector' => 'body.home #masthead .site-branding .site-title',
				'pattern'  => '$',
				'property' => 'color',
				'key'      => 'active',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'    => 'logo_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'include',
				'responsive' => true,
				'value'      => 'title',
			],
		],
		'input_attrs'  => [
			'colors' => [
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
	// Logo Tagline Typography.
	'brand_tag_typography'         => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'title_tagline',
		'label'        => esc_html__( 'Site Tagline Font', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'    => 'logo_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'include',
				'responsive' => true,
				'value'      => 'tagline',
			],
		],
		'default'      => kadence()->default( 'brand_tag_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => [
					'desktop' => '#masthead .site-branding .site-description',
					'tablet'  => '#masthead .site-branding .site-description',
					'mobile'  => '#masthead .site-branding .site-description',
				],
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
			'id' => 'brand_tag_typography',
		],
	],
	'header_logo_padding'          => [
		'control_type' => 'kadence_measure_control',
		'section'      => 'title_tagline',
		'priority'     => 15,
		'default'      => kadence()->default( 'header_logo_padding' ),
		'label'        => esc_html__( 'Padding', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.site-branding',
				'property' => 'padding',
				'pattern'  => '$',
				'key'      => 'measure',
			],
		],
		'input_attrs'  => [
			'min'        => [
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			],
			'max'        => [
				'px'  => 100,
				'em'  => 6,
				'rem' => 6,
			],
			'step'       => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			],
			'units'      => [ 'px', 'em', 'rem' ],
			'responsive' => true,
		],
	],
	'logo_link_to_site_icon'       => [
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'title_tagline',
		'settings'     => false,
		'priority'     => 25,
		'label'        => esc_html__( 'Site Icon', 'kadence' ),
		'input_attrs'  => [
			'section' => 'kadence_customizer_site_identity',
		],
	],
	'site_logo_link'               => [
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'site_identity',
		'settings'     => false,
		'priority'     => 5,
		'label'        => esc_html__( 'Site Title and Logo Control', 'kadence' ),
		'input_attrs'  => [
			'section' => 'title_tagline',
		],
	],
	'post_archive_home_title'      => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'static_front_page',
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'post_archive_home_title' ),
		'label'        => esc_html__( 'Show site name for page title', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'show_on_front',
				'operator' => '==',
				'value'    => 'posts',
			],
		],
	],
];

Theme_Customizer::add_settings( $settings );

