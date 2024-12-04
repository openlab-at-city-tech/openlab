<?php
/**
 * Single Post Options
 */

$wp_customize->add_section(
	'flawless_blog_single_page_options',
	array(
		'title' => esc_html__( 'Single Post Options', 'flawless-blog' ),
		'panel' => 'flawless_blog_theme_options_panel',
	)
);

// Enable single post category setting.
$wp_customize->add_setting(
	'flawless_blog_enable_single_category',
	array(
		'default'           => true,
		'sanitize_callback' => 'flawless_blog_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new Flawless_Blog_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'flawless_blog_enable_single_category',
		array(
			'label'    => esc_html__( 'Enable Category', 'flawless-blog' ),
			'settings' => 'flawless_blog_enable_single_category',
			'section'  => 'flawless_blog_single_page_options',
			'type'     => 'checkbox',
		)
	)
);

// Enable single post author setting.
$wp_customize->add_setting(
	'flawless_blog_enable_single_author',
	array(
		'default'           => true,
		'sanitize_callback' => 'flawless_blog_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new Flawless_Blog_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'flawless_blog_enable_single_author',
		array(
			'label'    => esc_html__( 'Enable Author', 'flawless-blog' ),
			'settings' => 'flawless_blog_enable_single_author',
			'section'  => 'flawless_blog_single_page_options',
			'type'     => 'checkbox',
		)
	)
);

// Enable single post date setting.
$wp_customize->add_setting(
	'flawless_blog_enable_single_date',
	array(
		'default'           => true,
		'sanitize_callback' => 'flawless_blog_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new Flawless_Blog_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'flawless_blog_enable_single_date',
		array(
			'label'    => esc_html__( 'Enable Date', 'flawless-blog' ),
			'settings' => 'flawless_blog_enable_single_date',
			'section'  => 'flawless_blog_single_page_options',
			'type'     => 'checkbox',
		)
	)
);

// Enable single post tag setting.
$wp_customize->add_setting(
	'flawless_blog_enable_single_tag',
	array(
		'default'           => true,
		'sanitize_callback' => 'flawless_blog_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new Flawless_Blog_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'flawless_blog_enable_single_tag',
		array(
			'label'    => esc_html__( 'Enable Post Tag', 'flawless-blog' ),
			'settings' => 'flawless_blog_enable_single_tag',
			'section'  => 'flawless_blog_single_page_options',
			'type'     => 'checkbox',
		)
	)
);

// Single post related Posts title label.
$wp_customize->add_setting(
	'flawless_blog_related_posts_title',
	array(
		'default'           => __( 'Related Posts', 'flawless-blog' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'flawless_blog_related_posts_title',
	array(
		'label'    => esc_html__( 'Related Posts Title', 'flawless-blog' ),
		'section'  => 'flawless_blog_single_page_options',
		'settings' => 'flawless_blog_related_posts_title',
	)
);
