<?php
/**
 * Breadcrumb settings
 */

$wp_customize->add_section(
	'flawless_blog_breadcrumb_section',
	array(
		'title' => esc_html__( 'Breadcrumb Options', 'flawless-blog' ),
		'panel' => 'flawless_blog_theme_options_panel',
	)
);

// Breadcrumb enable setting.
$wp_customize->add_setting(
	'flawless_blog_breadcrumb_enable',
	array(
		'default'           => true,
		'sanitize_callback' => 'flawless_blog_sanitize_checkbox',
	)
);
$wp_customize->add_control(
	new Flawless_Blog_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'flawless_blog_breadcrumb_enable',
		array(
			'label'    => esc_html__( 'Enable breadcrumb.', 'flawless-blog' ),
			'type'     => 'checkbox',
			'settings' => 'flawless_blog_breadcrumb_enable',
			'section'  => 'flawless_blog_breadcrumb_section',
		)
	)
);

// Breadcrumb - Separator.
$wp_customize->add_setting(
	'flawless_blog_breadcrumb_separator',
	array(
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => '/',
	)
);

$wp_customize->add_control(
	'flawless_blog_breadcrumb_separator',
	array(
		'label'           => esc_html__( 'Separator', 'flawless-blog' ),
		'section'         => 'flawless_blog_breadcrumb_section',
		'active_callback' => function( $control ) {
			return ( $control->manager->get_setting( 'flawless_blog_breadcrumb_enable' )->value() );
		},
	)
);
