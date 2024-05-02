<?php
/**
 * Breadcrumb Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

Theme_Customizer::add_settings(
	array(
		'breadcrumb_engine' => array(
			'control_type' => 'kadence_select_control',
			'section'      => 'breadcrumbs',
			'transport'    => 'refresh',
			'default'      => kadence()->default( 'breadcrumb_engine' ),
			'label'        => esc_html__( 'Breadcrumb Engine', 'kadence' ),
			'input_attrs'  => array(
				'options' => array(
					'' => array(
						'name' => __( 'Default', 'kadence' ),
					),
					'rankmath' => array(
						'name' => __( 'RankMath (must have activated in plugin)', 'kadence' ),
					),
					'yoast' => array(
						'name' => __( 'Yoast (must have activated in plugin)', 'kadence' ),
					),
					'seopress' => array(
						'name' => __( 'SEOPress (must have activated in plugin)', 'kadence' ),
					),
				),
			),
		),
		'breadcrumb_home_icon' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'breadcrumbs',
			'default'      => kadence()->default( 'breadcrumb_home_icon' ),
			'label'        => esc_html__( 'Use icon for home?', 'kadence' ),
			'transport'    => 'refresh',
			'context'      => array(
				array(
					'setting'    => 'breadcrumb_engine',
					'operator'   => '=',
					'value'      => '',
				),
			),
		),
	)
);
