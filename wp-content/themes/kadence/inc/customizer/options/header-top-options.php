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
	'header_top_tabs'             => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'header_top',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	],
	'header_top_layout'           => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_top',
		'priority'     => 4,
		'default'      => kadence()->default( 'header_top_layout' ),
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
					'desktop' => '.site-top-header-wrap',
					'tablet'  => '#mobile-header .site-top-header-wrap',
					'mobile'  => '#mobile-header .site-top-header-wrap',
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
	'header_top_height'           => [
		'control_type' => 'kadence_range_control',
		'section'      => 'header_top',
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
				'selector' => '#masthead .site-top-header-inner-wrap',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'header_top_height' ),
		'input_attrs'  => [
			'min'   => [
				'px'  => 10,
				'em'  => 1,
				'rem' => 1,
				'vh'  => 2,
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
	'header_top_background'       => [
		'control_type' => 'kadence_background_control',
		'section'      => 'header_top',
		'label'        => esc_html__( 'Top Row Background', 'kadence' ),
		'default'      => kadence()->default( 'header_top_background' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '.site-top-header-wrap .site-header-row-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Top Row Background', 'kadence' ),
		],
	],
	'header_top_border'           => [
		'control_type' => 'kadence_borders_control',
		'section'      => 'header_top',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'header_top_border' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'settings'     => [
			'border_top'    => 'header_top_top_border',
			'border_bottom' => 'header_top_bottom_border',
		],
		'live_method'  => [
			'header_top_top_border'    => [
				[
					'type'     => 'css_border',
					'selector' => [
						'desktop' => '.site-top-header-wrap .site-header-row-container-inner',
						'tablet'  => '#mobile-header .site-top-header-wrap .site-header-row-container-inner',
						'mobile'  => '#mobile-header .site-top-header-wrap .site-header-row-container-inner',
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
			'header_top_bottom_border' => [ 
				[
					'type'     => 'css_border',
					'selector' => [
						'desktop' => '.site-top-header-wrap .site-header-row-container-inner',
						'tablet'  => '#mobile-header .site-top-header-wrap .site-header-row-container-inner',
						'mobile'  => '#mobile-header .site-top-header-wrap .site-header-row-container-inner',
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
	'header_top_trans_background' => [
		'control_type' => 'kadence_background_control',
		'section'      => 'header_top',
		'label'        => esc_html__( '(When Transparent Header) Top Row Background', 'kadence' ),
		'default'      => kadence()->default( 'header_top_trans_background' ),
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
				'selector' => '.transparent-header #masthead .site-top-header-wrap .site-header-row-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Transparent Header Top Row Background', 'kadence' ),
		],
	],
	'header_top_padding'          => [
		'control_type' => 'kadence_measure_control',
		'section'      => 'header_top',
		'default'      => kadence()->default( 'header_top_padding' ),
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
				'selector' => '.site-top-header-wrap .site-header-row-container-inner>.site-container',
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

