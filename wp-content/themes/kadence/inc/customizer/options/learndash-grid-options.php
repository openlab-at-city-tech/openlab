<?php
/**
 * Grid Layout Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'sfwd-grid_layout_tabs'        => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'sfwd_grid_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'sfwd_grid_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'sfwd_grid_layout_design',
			],
			'active'  => 'general',
		],
	],
	'sfwd-grid_layout_tabs_design' => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'sfwd_grid_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'sfwd_grid_layout',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'sfwd_grid_layout_design',
			],
			'active'  => 'design',
		],
	],
	'learndash_course_grid'        => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'sfwd_grid_layout',
		'priority'     => 3,
		'default'      => kadence()->default( 'learndash_course_grid' ),
		'label'        => esc_html__( 'Override Course Grid Styles', 'kadence' ),
		'transport'    => 'refresh',
	],
	'learndash_course_grid_style'  => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sfwd_grid_layout',
		'priority'     => 7,
		'label'        => esc_html__( 'Content Style', 'kadence' ),
		'default'      => kadence()->default( 'learndash_course_grid_style' ),
		'transport'    => 'refresh',
		'context'      => [
			[
				'setting'  => 'learndash_course_grid',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'boxed'   => [
					'tooltip' => __( 'Boxed', 'kadence' ),
					'icon'    => 'gridBoxed',
				],
				'unboxed' => [
					'tooltip' => __( 'Unboxed', 'kadence' ),
					'icon'    => 'gridUnboxed',
				],
			],
			'responsive' => false,
			'class'      => 'kadence-two-col',
		],
	],
	'sfwd-grid_title_font'         => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'sfwd_grid_layout_design',
		'label'        => esc_html__( 'Course Grid Entry Title Font', 'kadence' ),
		'default'      => kadence()->default( 'sfwd-grid_title_font' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.ld-course-list-items .ld_course_grid.entry .entry-title',
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'context'      => [
			[
				'setting'  => 'learndash_course_grid',
				'operator' => '=',
				'value'    => true,
			],
		],
		'input_attrs'  => [
			'id'             => 'sfwd-grid_title_font',
			'headingInherit' => true,
		],
	],
];

Theme_Customizer::add_settings( $settings );
