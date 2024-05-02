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
$settings = array(
	'header_bottom_tabs' => array(
		'control_type' => 'kadence_blank_control',
		'section'      => 'header_bottom',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	),
	'header_bottom_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_bottom',
		'priority'     => 4,
		'default'      => kadence()->default( 'header_bottom_layout' ),
		'label'        => esc_html__( 'Layout', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => array(
					'desktop' => '.site-bottom-header-wrap',
					'tablet'  => '#mobile-header .site-bottom-header-wrap',
					'mobile'  => '#mobile-header .site-bottom-header-wrap',
				),
				'pattern'  => array(
					'desktop' => 'site-header-row-layout-$',
					'tablet'  => 'site-header-row-tablet-layout-$',
					'mobile'  => 'site-header-row-mobile-layout-$',
				),
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'standard' => array(
					'tooltip' => __( 'Background Fullwidth, Content Contained', 'kadence' ),
					'name'    => __( 'Standard', 'kadence' ),
					'icon'    => '',
				),
				'fullwidth' => array(
					'tooltip' => __( 'Background & Content Fullwidth', 'kadence' ),
					'name'    => __( 'Fullwidth', 'kadence' ),
					'icon'    => '',
				),
				'contained' => array(
					'tooltip' => __( 'Background & Content Contained', 'kadence' ),
					'name'    => __( 'Contained', 'kadence' ),
					'icon'    => '',
				),
			),
		),
	),
	'header_bottom_height' => array(
		'control_type' => 'kadence_range_control',
		'section'      => 'header_bottom',
		'priority'     => 5,
		'label'        => esc_html__( 'Min Height', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#masthead .site-bottom-header-inner-wrap',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => kadence()->default( 'header_bottom_height' ),
		'input_attrs'  => array(
			'min'     => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
				'vh'  => 0,
			),
			'max'     => array(
				'px'  => 400,
				'em'  => 12,
				'rem' => 12,
				'vh'  => 40,
			),
			'step'    => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vh'  => 1,
			),
			'units'   => array( 'px', 'em', 'rem', 'vh' ),
		),
	),
	'header_bottom_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'header_bottom',
		'label'        => esc_html__( 'Bottom Row Background', 'kadence' ),
		'default'      => kadence()->default( 'header_bottom_background' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '.site-bottom-header-wrap .site-header-row-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Main Row Background', 'kadence' ),
		),
	),
	'header_bottom_border' => array(
		'control_type' => 'kadence_borders_control',
		'section'      => 'header_bottom',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'header_bottom_border' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'settings'     => array(
			'border_top'    => 'header_bottom_top_border',
			'border_bottom' => 'header_bottom_bottom_border',
		),
		'live_method'     => array(
			'header_bottom_top_border' => array(
				array(
					'type'     => 'css_border',
					'selector' => array(
						'desktop' => '.site-bottom-header-wrap .site-header-row-container-inner',
						'tablet'  => '#mobile-header .site-bottom-header-wrap .site-header-row-container-inner',
						'mobile'  => '#mobile-header .site-bottom-header-wrap .site-header-row-container-inner',
					),
					'pattern'  => array(
						'desktop' => '$',
						'tablet'  => '$',
						'mobile'  => '$',
					),
					'property' => 'border-top',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
			'header_bottom_bottom_border' => array( 
				array(
					'type'     => 'css_border',
					'selector' => array(
						'desktop' => '.site-bottom-header-wrap .site-header-row-container-inner',
						'tablet'  => '#mobile-header .site-bottom-header-wrap .site-header-row-container-inner',
						'mobile'  => '#mobile-header .site-bottom-header-wrap .site-header-row-container-inner',
					),
					'pattern'  => array(
						'desktop' => '$',
						'tablet'  => '$',
						'mobile'  => '$',
					),
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
		),
	),
	'header_bottom_trans_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'header_bottom',
		'label'        => esc_html__( '(When Transparent Header) Bottom Row Background', 'kadence' ),
		'default'      => kadence()->default( 'header_bottom_trans_background' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
			array(
				'setting'  => 'transparent_header_enable',
				'operator' => '=',
				'value'    => true,
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '.transparent-header #masthead .site-bottom-header-wrap .site-header-row-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Transparent Header Bottom Row Background', 'kadence' ),
		),
	),
	'header_bottom_padding' => array(
		'control_type' => 'kadence_measure_control',
		'section'      => 'header_bottom',
		'default'      => kadence()->default( 'header_bottom_padding' ),
		'label'        => esc_html__( 'Padding', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.site-bottom-header-wrap .site-header-row-container-inner>.site-container',
				'property' => 'padding',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			),
			'max'        => array(
				'px'  => 100,
				'em'  => 6,
				'rem' => 6,
			),
			'step'       => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			),
			'units'      => array( 'px', 'em', 'rem' ),
			'responsive' => true,
		),
	),
);

Theme_Customizer::add_settings( $settings );

