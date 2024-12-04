<?php
/**
 * Adore Themes Customizer
 *
 * @package Flawless Blog
 *
 * Banner Section
 */

$wp_customize->add_section(
	'flawless_blog_banner_section',
	array(
		'title' => esc_html__( 'Banner Section', 'flawless-blog' ),
		'panel' => 'flawless_blog_frontpage_panel',
	)
);

// Banner enable setting.
$wp_customize->add_setting(
	'flawless_blog_banner_section_enable',
	array(
		'default'           => false,
		'sanitize_callback' => 'flawless_blog_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new Flawless_Blog_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'flawless_blog_banner_section_enable',
		array(
			'label'    => esc_html__( 'Enable Banner Section', 'flawless-blog' ),
			'type'     => 'checkbox',
			'settings' => 'flawless_blog_banner_section_enable',
			'section'  => 'flawless_blog_banner_section',
		)
	)
);

// Banner bg image.
$wp_customize->add_setting(
	'flawless_blog_banner_image',
	array(
		'default'           => '',
		'sanitize_callback' => 'flawless_blog_sanitize_image',
	)
);

$wp_customize->add_control(
	new WP_Customize_Image_Control(
		$wp_customize,
		'flawless_blog_banner_image',
		array(
			'label'           => esc_html__( 'Banner Image', 'flawless-blog' ),
			'section'         => 'flawless_blog_banner_section',
			'settings'        => 'flawless_blog_banner_image',
			'active_callback' => 'flawless_blog_if_banner_enabled',
		)
	)
);

// Banner title settings.
$wp_customize->add_setting(
	'flawless_blog_banner_title',
	array(
		'default'           => __( 'Welcome to my blog', 'flawless-blog' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'flawless_blog_banner_title',
	array(
		'label'           => esc_html__( 'Title', 'flawless-blog' ),
		'section'         => 'flawless_blog_banner_section',
		'active_callback' => 'flawless_blog_if_banner_enabled',
		'settings'        => 'flawless_blog_banner_title',
		'active_callback' => 'flawless_blog_if_banner_enabled',
	)
);

$wp_customize->selective_refresh->add_partial(
	'flawless_blog_banner_title',
	array(
		'selector'            => '.banner-intro-head h2',
		'settings'            => 'flawless_blog_banner_title',
		'container_inclusive' => false,
		'fallback_refresh'    => true,
		'render_callback'     => 'flawless_blog_banner_partial_title',
	)
);

// Banner description settings.
$wp_customize->add_setting(
	'flawless_blog_banner_description',
	array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'flawless_blog_banner_description',
	array(
		'label'           => esc_html__( 'Description', 'flawless-blog' ),
		'section'         => 'flawless_blog_banner_section',
		'active_callback' => 'flawless_blog_if_banner_enabled',
		'settings'        => 'flawless_blog_banner_description',
		'type'            => 'textarea',
		'active_callback' => 'flawless_blog_if_banner_enabled',
	)
);

$wp_customize->selective_refresh->add_partial(
	'flawless_blog_banner_description',
	array(
		'selector'            => '.banner-intro-txt p',
		'settings'            => 'flawless_blog_banner_description',
		'container_inclusive' => false,
		'fallback_refresh'    => true,
		'render_callback'     => 'flawless_blog_banner_partial_subtitle',
	)
);

// Read More button label setting.
$wp_customize->add_setting(
	'flawless_blog_banner_read_more_button_label',
	array(
		'default'           => __( 'Read More', 'flawless-blog' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'flawless_blog_banner_read_more_button_label',
	array(
		'label'           => esc_html__( 'Read More Button Label', 'flawless-blog' ),
		'section'         => 'flawless_blog_banner_section',
		'settings'        => 'flawless_blog_banner_read_more_button_label',
		'type'            => 'text',
		'active_callback' => 'flawless_blog_if_banner_enabled',
	)
);

// Read More button URL setting.
$wp_customize->add_setting(
	'flawless_blog_banner_read_more_button_url',
	array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	)
);

$wp_customize->add_control(
	'flawless_blog_banner_read_more_button_url',
	array(
		'label'           => esc_html__( 'Read More Button Link', 'flawless-blog' ),
		'section'         => 'flawless_blog_banner_section',
		'settings'        => 'flawless_blog_banner_read_more_button_url',
		'type'            => 'url',
		'active_callback' => 'flawless_blog_if_banner_enabled',
	)
);

/*========================Active Callback==============================*/
function flawless_blog_if_banner_enabled( $control ) {
	return $control->manager->get_setting( 'flawless_blog_banner_section_enable' )->value();
}
