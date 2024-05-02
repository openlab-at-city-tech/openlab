<?php
/**
 * 404 Layout options.
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$layout_404_settings = array(
	'404_tabs' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'general_404',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'general_404',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'general_404_design',
			),
			'active' => 'general',
		),
	),
	'404_tabs_design' => array(
		'control_type' => 'kadence_tab_control',
		'section'      => 'general_404_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'kadence' ),
				'target' => 'general_404',
			),
			'design' => array(
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'general_404_design',
			),
			'active' => 'design',
		),
	),
	'info_404_layout' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'general_404',
		'priority'     => 10,
		'label'        => esc_html__( '404 Layout', 'kadence' ),
		'settings'     => false,
	),
	'info_404_layout_design' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'general_404_design',
		'priority'     => 10,
		'label'        => esc_html__( '404 Layout', 'kadence' ),
		'settings'     => false,
	),
	'404_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'general_404',
		'label'        => esc_html__( '404 Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( '404_layout' ),
		'input_attrs'  => array(
			'layout' => array(
				'normal' => array(
					'name' => __( 'Normal', 'kadence' ),
					'icon' => 'normal',
				),
				'narrow' => array(
					'name' => __( 'Narrow', 'kadence' ),
					'icon' => 'narrow',
				),
				'fullwidth' => array(
					'name' => __( 'Fullwidth', 'kadence' ),
					'icon' => 'fullwidth',
				),
				'left' => array(
					'name' => __( 'Left Sidebar', 'kadence' ),
					'icon' => 'leftsidebar',
				),
				'right' => array(
					'name' => __( 'Right Sidebar', 'kadence' ),
					'icon' => 'rightsidebar',
				),
			),
			'class'      => 'kadence-three-col',
			'responsive' => false,
		),
	),
	'404_sidebar_id' => array(
		'control_type' => 'kadence_select_control',
		'section'      => 'general_404',
		'label'        => esc_html__( '404 Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( '404_sidebar_id' ),
		'input_attrs'  => array(
			'options' => kadence()->sidebar_options(),
		),
	),
	'404_content_style' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'general_404',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( '404_content_style' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => 'body.error404',
				'pattern'  => 'content-style-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'boxed' => array(
					'name' => __( 'Boxed', 'kadence' ),
					'icon' => 'boxed',
				),
				'unboxed' => array(
					'name' => __( 'Unboxed', 'kadence' ),
					'icon' => 'narrow',
				),
			),
			'responsive' => false,
			'class'      => 'kadence-two-col',
		),
	),
	'404_vertical_padding' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'general_404',
		'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( '404_vertical_padding' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => 'body.error404',
				'pattern'  => 'content-vertical-padding-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'show' => array(
					'name' => __( 'Enable', 'kadence' ),
				),
				'hide' => array(
					'name' => __( 'Disable', 'kadence' ),
				),
				'top' => array(
					'name' => __( 'Top Only', 'kadence' ),
				),
				'bottom' => array(
					'name' => __( 'Bottom Only', 'kadence' ),
				),
			),
			'responsive' => false,
			'class'      => 'kadence-two-grid',
		),
	),
	'404_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'general_404_design',
		'label'        => esc_html__( 'Site Background', 'kadence' ),
		'default'      => kadence()->default( '404_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.error404',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip' => __( '404 Background', 'kadence' ),
		),
	),
	'404_content_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'general_404_design',
		'label'        => esc_html__( 'Content Background', 'kadence' ),
		'default'      => kadence()->default( '404_content_background' ),
		'live_method'  => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.error404 .content-bg, body.error404.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip' => __( '404 Content Background', 'kadence' ),
		),
	),
);
Theme_Customizer::add_settings( $layout_404_settings );
