<?php
/**
 * Header Top Row Options
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
	'header_bottom_tabs'             => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'header_bottom',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	],
	'header_bottom_layout'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_bottom',
		'priority'     => 4,
		'default'      => kadence()->default( 'header_bottom_layout' ),
		'label'        => esc_html__( 'Layout', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => [
					'desktop' => '.site-bottom-header-wrap',
					'tablet'  => '#mobile-header .site-bottom-header-wrap',
					'mobile'  => '#mobile-header .site-bottom-header-wrap',
				],
				'pattern'  => [
					'desktop' => 'site-header-row-layout-$',
					'tablet'  => 'site-header-row-tablet-layout-$',
					'mobile'  => 'site-header-row-mobile-layout-$',
				],
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout' => [
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
		],
	],
	'header_bottom_height'           => [
		'control_type' => 'kadence_range_control',
		'section'      => 'header_bottom',
		'priority'     => 5,
		'label'        => esc_html__( 'Min Height', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#masthead .site-bottom-header-inner-wrap',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'header_bottom_height' ),
		'input_attrs'  => [
			'min'   => [
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
				'vh'  => 0,
			],
			'max'   => [
				'px'  => 400,
				'em'  => 12,
				'rem' => 12,
				'vh'  => 40,
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
	'header_bottom_background'       => [
		'control_type' => 'kadence_background_control',
		'section'      => 'header_bottom',
		'label'        => esc_html__( 'Bottom Row Background', 'kadence' ),
		'default'      => kadence()->default( 'header_bottom_background' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '.site-bottom-header-wrap .site-header-row-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Main Row Background', 'kadence' ),
		],
	],
	'header_bottom_border'           => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'header_bottom',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'header_bottom_border' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'settings'     => [
			'border_top'    => 'header_bottom_top_border',
			'border_bottom' => 'header_bottom_bottom_border',
		],
		'live_method'  => [
			'header_bottom_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => [
						'desktop' => '.site-bottom-header-wrap .site-header-row-container-inner',
						'tablet'  => '#mobile-header .site-bottom-header-wrap .site-header-row-container-inner',
						'mobile'  => '#mobile-header .site-bottom-header-wrap .site-header-row-container-inner',
					],
					'pattern'  => [
						'desktop' => '$',
						'tablet'  => '$',
						'mobile'  => '$',
					],
					'property' => 'border-top',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
			'header_bottom_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => [
						'desktop' => '.site-bottom-header-wrap .site-header-row-container-inner',
						'tablet'  => '#mobile-header .site-bottom-header-wrap .site-header-row-container-inner',
						'mobile'  => '#mobile-header .site-bottom-header-wrap .site-header-row-container-inner',
					],
					'pattern'  => [
						'desktop' => '$',
						'tablet'  => '$',
						'mobile'  => '$',
					],
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
	],
	'header_bottom_trans_background' => [
		'control_type' => 'kadence_background_control',
		'section'      => 'header_bottom',
		'label'        => esc_html__( '(When Transparent Header) Bottom Row Background', 'kadence' ),
		'default'      => kadence()->default( 'header_bottom_trans_background' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'  => 'transparent_header_enable',
				'operator' => '=',
				'value'    => true,
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '.transparent-header #masthead .site-bottom-header-wrap .site-header-row-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Transparent Header Bottom Row Background', 'kadence' ),
		],
	],
	'header_bottom_padding'          => [
		'control_type' => 'kadence_measure_control',
		'section'      => 'header_bottom',
		'default'      => kadence()->default( 'header_bottom_padding' ),
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
				'selector' => '.site-bottom-header-wrap .site-header-row-container-inner>.site-container',
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
];

Theme_Customizer::add_settings( $settings );

