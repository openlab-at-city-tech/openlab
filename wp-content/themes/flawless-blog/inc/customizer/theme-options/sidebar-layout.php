<?php
/**
 * Sidebar settings
 */

$wp_customize->add_section(
	'flawless_blog_sidebar_option',
	array(
		'title' => esc_html__( 'Sidebar Options', 'flawless-blog' ),
		'panel' => 'flawless_blog_theme_options_panel',
	)
);

// Sidebar Option - Global Sidebar Position.
$wp_customize->add_setting(
	'flawless_blog_sidebar_position',
	array(
		'sanitize_callback' => 'flawless_blog_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'flawless_blog_sidebar_position',
	array(
		'label'   => esc_html__( 'Global Sidebar Position', 'flawless-blog' ),
		'section' => 'flawless_blog_sidebar_option',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'flawless-blog' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'flawless-blog' ),
		),
	)
);

// Sidebar Option - Post Sidebar Position.
$wp_customize->add_setting(
	'flawless_blog_post_sidebar_position',
	array(
		'sanitize_callback' => 'flawless_blog_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'flawless_blog_post_sidebar_position',
	array(
		'label'   => esc_html__( 'Post Sidebar Position', 'flawless-blog' ),
		'section' => 'flawless_blog_sidebar_option',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'flawless-blog' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'flawless-blog' ),
		),
	)
);

// Sidebar Option - Page Sidebar Position.
$wp_customize->add_setting(
	'flawless_blog_page_sidebar_position',
	array(
		'sanitize_callback' => 'flawless_blog_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'flawless_blog_page_sidebar_position',
	array(
		'label'   => esc_html__( 'Page Sidebar Position', 'flawless-blog' ),
		'section' => 'flawless_blog_sidebar_option',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'flawless-blog' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'flawless-blog' ),
		),
	)
);
