<?php
/**
 * Header Popup Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = array(
	'header_popup_tabs' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'header_popup',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'header_popup',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'header_popup_design',
			),
			'active' => 'general',
		),
	),
	'header_popup_tabs_design' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'header_popup_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'header_popup',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'header_popup_design',
			),
			'active' => 'design',
		),
	),
	'header_popup_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_popup',
		'priority'     => 4,
		'default'      => kadence()->default( 'header_popup_layout' ),
		'label'        => esc_html__( 'Layout', 'kadence' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '#mobile-drawer',
				'pattern'  => 'popup-drawer-layout-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'fullwidth' => array(
					'tooltip' => __( 'Reveal as Fullwidth', 'kadence' ),
					'name'    => __( 'Fullwidth', 'kadence' ),
					'icon'    => '',
				),
				'sidepanel' => array(
					'tooltip' => __( 'Reveal as Side Panel', 'kadence' ),
					'name'    => __( 'Side Panel', 'kadence' ),
					'icon'    => '',
				),
			),
			'responsive' => false,
		),
	),
	'header_popup_side' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_popup',
		'priority'     => 4,
		'default'      => kadence()->default( 'header_popup_side' ),
		'label'        => esc_html__( 'Slide-Out Side', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'header_popup_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'layout',
				'responsive' => false,
				'value'      => 'sidepanel',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '#mobile-drawer',
				'pattern'  => 'popup-drawer-side-$',
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
	'header_popup_width' => array(
		'control_type' => 'kadence_range_control',
		'section'      => 'header_popup',
		'priority'     => 5,
		'label'        => esc_html__( 'Panel Width', 'kadence' ),
		'description'  => esc_html__( 'Define the width for the off canvas panel', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'header_popup_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'layout',
				'responsive' => false,
				'value'      => 'sidepanel',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#mobile-drawer .drawer-inner',
				'property' => 'width',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => kadence()->default( 'header_popup_width' ),
		'input_attrs'  => array(
			'min'     => array(
				'px'  => 100,
				'em'  => 1,
				'rem' => 1,
				'vw'  => 0,
				'%'   => 0,
			),
			'max'     => array(
				'px'  => 2000,
				'em'  => 50,
				'rem' => 50,
				'vw'  => 100,
				'%'   => 100,
			),
			'step'    => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vw'  => 1,
				'%'   => 1,
			),
			'units'   => array( 'px', 'em', 'vw', '%' ),
		),
	),
	'header_popup_content_max_width' => array(
		'control_type' => 'kadence_range_control',
		'section'      => 'header_popup',
		'priority'     => 5,
		'label'        => esc_html__( 'Content Max Width', 'kadence' ),
		'description'  => esc_html__( "Define the max width for the off canvas panel's content", 'kadence' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#mobile-drawer .drawer-content',
				'property' => 'width',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => kadence()->default( 'header_popup_content_max_width' ),
		'input_attrs'  => array(
			'min'     => array(
				'px'  => 100,
				'em'  => 1,
				'rem' => 1,
				'vw'  => 0,
				'%'   => 0,
			),
			'max'     => array(
				'px'  => 2000,
				'em'  => 50,
				'rem' => 50,
				'vw'  => 100,
				'%'   => 100,
			),
			'step'    => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vw'  => 1,
				'%'   => 1,
			),
			'units'   => array( 'px', 'em', 'vw', '%' ),
		),
	),
	'enable_popup_body_animate' => array(
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'header_popup',
		'priority'     => 4,
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'enable_popup_body_animate' ),
		'label'        => esc_html__( 'Move Body with toggle?', 'kadence' ),
		'input_attrs'  => array(
			'help' => esc_html__( 'This can require a lot of memory to render the animation in mobile browsers, use with caution if you have graphically heavy pages.', 'kadence' ),
		),
		'context'      => array(
			array(
				'setting'    => 'header_popup_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'layout',
				'responsive' => false,
				'value'      => 'sidepanel',
			),
		),
	),
	'header_popup_animation' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_popup',
		'priority'     => 4,
		'default'      => kadence()->default( 'header_popup_animation' ),
		'label'        => esc_html__( 'Animation', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'header_popup_layout',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'layout',
				'responsive' => false,
				'value'      => 'fullwidth',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '#mobile-drawer',
				'pattern'  => 'popup-drawer-animation-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'fade' => array(
					'tooltip' => __( 'Fade In', 'kadence' ),
					'name'    => __( 'Fade', 'kadence' ),
					'icon'    => '',
				),
				'scale' => array(
					'tooltip' => __( 'Scale into view', 'kadence' ),
					'name'    => __( 'Scale', 'kadence' ),
					'icon'    => '',
				),
				'slice' => array(
					'tooltip' => __( 'Slice into view', 'kadence' ),
					'name'    => __( 'Slice', 'kadence' ),
					'icon'    => '',
				),
			),
			'responsive' => false,
		),
	),
	'header_popup_content_align' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_popup',
		'label'        => esc_html__( 'Content Align', 'kadence' ),
		'default'      => kadence()->default( 'header_popup_content_align' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.mobile-drawer-content',
				'pattern'  => 'content-align-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'left'   => array(
					'tooltip'  => __( 'Left Align', 'kadence' ),
					'dashicon' => 'editor-alignleft',
				),
				'center' => array(
					'tooltip'  => __( 'Center Align', 'kadence' ),
					'dashicon' => 'editor-aligncenter',
				),
				'right'  => array(
					'tooltip'  => __( 'Right Align', 'kadence' ),
					'dashicon' => 'editor-alignright',
				),
			),
			'responsive' => false,
		),
	),
	'header_popup_vertical_align' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_popup',
		'label'        => esc_html__( 'Content Vertical Align', 'kadence' ),
		'default'      => kadence()->default( 'header_popup_vertical_align' ),
		'live_method'  => array(
			array(
				'type'     => 'class',
				'selector' => '.mobile-drawer-content',
				'pattern'  => 'content-valign-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'top' => array(
					'tooltip' => __( 'Top Align', 'kadence' ),
					'icon'    => 'aligntop',
				),
				'middle' => array(
					'tooltip' => __( 'Middle Align', 'kadence' ),
					'icon'    => 'alignmiddle',
				),
				'bottom' => array(
					'tooltip' => __( 'Bottom Align', 'kadence' ),
					'icon'    => 'alignbottom',
				),
			),
			'responsive' => false,
		),
	),
	'header_popup_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'header_popup_design',
		'label'        => esc_html__( 'Popup Background', 'kadence' ),
		'default'      => kadence()->default( 'header_popup_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '#mobile-drawer .drawer-inner, #mobile-drawer.popup-drawer-layout-fullwidth.popup-drawer-animation-slice .pop-portion-bg',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Popup Background', 'kadence' ),
		),
	),
	'header_popup_close_icon_size' => array(
		'control_type' => 'kadence_range_control',
		'section'      => 'header_popup_design',
		'label'        => esc_html__( 'Close Icon Size', 'kadence' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#mobile-drawer .drawer-header .drawer-toggle',
				'property' => 'font-size',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => kadence()->default( 'header_popup_close_icon_size' ),
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
	'header_popup_close_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'header_popup_design',
		'label'        => esc_html__( 'Close Toggle Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_popup_close_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#mobile-drawer .drawer-header .drawer-toggle',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '#mobile-drawer .drawer-header .drawer-toggle:hover',
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
	'header_popup_close_background' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'header_popup_design',
		'label'        => esc_html__( 'Close Toggle Background Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_popup_close_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#mobile-drawer .drawer-header .drawer-toggle',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '#mobile-drawer .drawer-header .drawer-toggle:hover',
				'property' => 'background',
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
	'header_popup_close_padding' => array(
		'control_type' => 'kadence_measure_control',
		'section'      => 'header_popup_design',
		'default'      => kadence()->default( 'header_popup_close_padding' ),
		'label'        => esc_html__( 'Close Icon Padding', 'kadence' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#mobile-drawer .drawer-header .drawer-toggle',
				'property' => 'padding',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'input_attrs'  => array(
			'responsive' => false,
		),
	),
);

Theme_Customizer::add_settings( $settings );

