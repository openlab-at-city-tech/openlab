<?php

/**
 * Color Options
 */

// Site tagline color setting.
$wp_customize->add_setting(
	'flawless_blog_header_tagline',
	array(
		'default'           => '#222222',
		'sanitize_callback' => 'flawless_blog_sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new WP_Customize_Color_Control(
		$wp_customize,
		'flawless_blog_header_tagline',
		array(
			'label'   => esc_html__( 'Site tagline Color', 'flawless-blog' ),
			'section' => 'colors',
		)
	)
);
