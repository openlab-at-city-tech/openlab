<?php
/**
 * Header Builder Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

Theme_Customizer::add_settings(
	array(
		'comment_form_before_list' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_comments',
			'default'      => kadence()->default( 'comment_form_before_list' ),
			'label'        => esc_html__( 'Move Comments input above comment list.', 'kadence' ),
			'transport'    => 'refresh',
		),
		'comment_form_remove_web' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_comments',
			'default'      => kadence()->default( 'comment_form_remove_web' ),
			'label'        => esc_html__( 'Remove Comments Website field.', 'kadence' ),
			'transport'    => 'refresh',
		),
	)
);
