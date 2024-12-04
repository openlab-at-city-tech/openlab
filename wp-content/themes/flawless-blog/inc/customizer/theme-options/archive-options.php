<?php
/**
 * Blog / Archive Options
 */

$wp_customize->add_section(
	'flawless_blog_archive_page_options',
	array(
		'title' => esc_html__( 'Blog / Archive Pages Options', 'flawless-blog' ),
		'panel' => 'flawless_blog_theme_options_panel',
	)
);

// Excerpt - Excerpt Length.
$wp_customize->add_setting(
	'flawless_blog_excerpt_length',
	array(
		'default'           => 30,
		'sanitize_callback' => 'flawless_blog_sanitize_number_range',
	)
);

$wp_customize->add_control(
	'flawless_blog_excerpt_length',
	array(
		'label'       => esc_html__( 'Excerpt Length (no. of words)', 'flawless-blog' ),
		'section'     => 'flawless_blog_archive_page_options',
		'settings'    => 'flawless_blog_excerpt_length',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 5,
			'max'  => 200,
			'step' => 1,
		),
	)
);

// Enable archive page category setting.
$wp_customize->add_setting(
	'flawless_blog_enable_archive_category',
	array(
		'default'           => true,
		'sanitize_callback' => 'flawless_blog_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new Flawless_Blog_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'flawless_blog_enable_archive_category',
		array(
			'label'    => esc_html__( 'Enable Category', 'flawless-blog' ),
			'settings' => 'flawless_blog_enable_archive_category',
			'section'  => 'flawless_blog_archive_page_options',
			'type'     => 'checkbox',
		)
	)
);

// Enable archive page author setting.
$wp_customize->add_setting(
	'flawless_blog_enable_archive_author',
	array(
		'default'           => true,
		'sanitize_callback' => 'flawless_blog_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new Flawless_Blog_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'flawless_blog_enable_archive_author',
		array(
			'label'    => esc_html__( 'Enable Author', 'flawless-blog' ),
			'settings' => 'flawless_blog_enable_archive_author',
			'section'  => 'flawless_blog_archive_page_options',
			'type'     => 'checkbox',
		)
	)
);

// Enable archive page date setting.
$wp_customize->add_setting(
	'flawless_blog_enable_archive_date',
	array(
		'default'           => true,
		'sanitize_callback' => 'flawless_blog_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new Flawless_Blog_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'flawless_blog_enable_archive_date',
		array(
			'label'    => esc_html__( 'Enable Date', 'flawless-blog' ),
			'settings' => 'flawless_blog_enable_archive_date',
			'section'  => 'flawless_blog_archive_page_options',
			'type'     => 'checkbox',
		)
	)
);
