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
		'info_background' => array(
			'control_type' => 'kadence_title_control',
			'section'      => 'general_colors',
			'label'        => esc_html__( 'Backgrounds', 'kadence' ),
			'settings'     => false,
		),
		'site_background' => array(
			'control_type' => 'kadence_background_control',
			'section'      => 'general_colors',
			'label'        => esc_html__( 'Site Background', 'kadence' ),
			'default'      => kadence()->default( 'site_background' ),
			'live_method'     => array(
				array(
					'type'     => 'css_background',
					'selector' => 'body',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				),
			),
			'input_attrs'  => array(
				'tooltip'  => __( 'Site Background', 'kadence' ),
			),
		),
		'content_background' => array(
			'control_type' => 'kadence_background_control',
			'section'      => 'general_colors',
			'label'        => esc_html__( 'Content Background', 'kadence' ),
			'default'      => kadence()->default( 'content_background' ),
			'live_method'     => array(
				array(
					'type'     => 'css_background',
					'selector' => '.content-bg, body.content-style-unboxed .site',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				),
			),
			'input_attrs'  => array(
				'tooltip'  => __( 'Content Background', 'kadence' ),
			),
		),
		'above_title_background' => array(
			'control_type' => 'kadence_background_control',
			'section'      => 'general_colors',
			'label'        => esc_html__( 'Title Above Content Background', 'kadence' ),
			'default'      => kadence()->default( 'above_title_background' ),
			'live_method'     => array(
				array(
					'type'     => 'css_background',
					'selector' => '.wp-site-blocks .entry-hero-container-inner',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				),
			),
			'input_attrs'  => array(
				'tooltip'  => __( 'Title Above Content Background', 'kadence' ),
			),
		),
		'above_title_overlay_color' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'general_colors',
			'label'        => esc_html__( 'Title Above Content Overlay Color', 'kadence' ),
			'default'      => kadence()->default( 'above_title_overlay_color' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.entry-hero-container-inner .hero-section-overlay',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				),
			),
			'input_attrs'  => array(
				'colors' => array(
					'color' => array(
						'tooltip' => __( 'Overlay Color', 'kadence' ),
						'palette' => true,
					),
				),
				'allowGradient' => true,
			),
		),
		'info_links' => array(
			'control_type' => 'kadence_title_control',
			'section'      => 'general_colors',
			'label'        => esc_html__( 'Content Links', 'kadence' ),
			'settings'     => false,
		),
		'link_color' => array(
			'control_type' => 'kadence_color_link_control',
			'section'      => 'general_colors',
			'transport'    => 'refresh',
			'label'        => esc_html__( 'Links Color', 'kadence' ),
			'default'      => kadence()->default( 'link_color' ),
			'live_method'     => array(
				array(
					'type'     => 'css_link',
					'selector' => 'a',
					'property' => 'color',
					'pattern'  => 'link-style-$',
					'key'      => 'base',
				),
			),
		),
	)
);

