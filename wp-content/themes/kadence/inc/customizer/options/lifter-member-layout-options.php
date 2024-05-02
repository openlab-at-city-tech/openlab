<?php
/**
 * Product Layout Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = array(
	'info_llms_membership_title' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'llms_membership_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Membership Title', 'kadence' ),
		'settings'     => false,
	),
	'llms_membership_title' => array(
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'llms_membership_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'llms_membership_title' ),
		'label'        => esc_html__( 'Show Membership Title?', 'kadence' ),
		'transport'    => 'refresh',
	),
	'llms_membership_title_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'llms_membership_layout',
		'label'        => esc_html__( 'Membership Title Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => kadence()->default( 'llms_membership_title_layout' ),
		'context'      => array(
			array(
				'setting'    => 'llms_membership_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'normal' => array(
					'tooltip' => __( 'In Content', 'kadence' ),
					'icon'    => 'incontent',
				),
				'above' => array(
					'tooltip' => __( 'Above Content', 'kadence' ),
					'icon'    => 'abovecontent',
				),
			),
			'responsive' => false,
			'class'      => 'kadence-two-col',
		),
	),
	'llms_membership_title_inner_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'llms_membership_layout',
		'priority'     => 4,
		'default'      => kadence()->default( 'llms_membership_title_inner_layout' ),
		'label'        => esc_html__( 'Title Container Width', 'kadence' ),
		'context'      => array(
			array(
				'setting'    => 'llms_membership_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'llms_membership_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.llms_membership-hero-section',
				'pattern'  => 'entry-hero-layout-$',
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
			'responsive' => false,
		),
	),
	'llms_membership_title_align' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'llms_membership_layout',
		'label'        => esc_html__( 'Membership Title Align', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'llms_membership_title_align' ),
		'context'      => array(
			array(
				'setting'    => 'llms_membership_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.llms_membership-title',
				'pattern'  => array(
					'desktop' => 'title-align-$',
					'tablet'  => 'title-tablet-align-$',
					'mobile'  => 'title-mobile-align-$',
				),
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'left' => array(
					'tooltip'  => __( 'Left Align Title', 'kadence' ),
					'dashicon' => 'editor-alignleft',
				),
				'center' => array(
					'tooltip'  => __( 'Center Align Title', 'kadence' ),
					'dashicon' => 'editor-aligncenter',
				),
				'right' => array(
					'tooltip'  => __( 'Right Align Title', 'kadence' ),
					'dashicon' => 'editor-alignright',
				),
			),
			'responsive' => true,
		),
	),
	'info_llms_membership_layout' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'llms_membership_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Membership Layout', 'kadence' ),
		'settings'     => false,
	),
	'llms_membership_layout' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'llms_membership_layout',
		'label'        => esc_html__( 'Membership Layout', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'llms_membership_layout' ),
		'input_attrs'  => array(
			'layout' => array(
				'normal' => array(
					'tooltip' => __( 'Normal', 'kadence' ),
					'icon' => 'normal',
				),
				'narrow' => array(
					'tooltip' => __( 'Narrow', 'kadence' ),
					'icon' => 'narrow',
				),
				'fullwidth' => array(
					'tooltip' => __( 'Fullwidth', 'kadence' ),
					'icon' => 'fullwidth',
				),
				'left' => array(
					'tooltip' => __( 'Left Sidebar', 'kadence' ),
					'icon' => 'leftsidebar',
				),
				'right' => array(
					'tooltip' => __( 'Right Sidebar', 'kadence' ),
					'icon' => 'rightsidebar',
				),
			),
			'responsive' => false,
			'class'      => 'kadence-three-col',
		),
	),
	'llms_membership_sidebar_id' => array(
		'control_type' => 'kadence_select_control',
		'section'      => 'llms_membership_layout',
		'label'        => esc_html__( 'Membership Default Sidebar', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => kadence()->default( 'llms_membership_sidebar_id' ),
		'input_attrs'  => array(
			'options' => kadence()->sidebar_options(),
		),
	),
	'llms_membership_content_style' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'llms_membership_layout',
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'llms_membership_content_style' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => 'body.single-llms_membership',
				'pattern'  => 'content-style-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'boxed' => array(
					'tooltip' => __( 'Boxed', 'kadence' ),
					'icon' => 'boxed',
				),
				'unboxed' => array(
					'tooltip' => __( 'Unboxed', 'kadence' ),
					'icon' => 'narrow',
				),
			),
			'responsive' => false,
			'class'      => 'kadence-two-col',
		),
	),
	'llms_membership_vertical_padding' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'llms_membership_layout',
		'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'llms_membership_vertical_padding' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => 'body.single-llms_membership',
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
);

Theme_Customizer::add_settings( $settings );

