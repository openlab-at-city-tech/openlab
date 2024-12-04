<?php
/**
 * Adore Themes Customizer
 *
 * @package Flawless Blog
 *
 * Flash Articles Section
 */

$wp_customize->add_section(
	'flawless_blog_flash_articles_section',
	array(
		'title' => esc_html__( 'Flash Articles Section', 'flawless-blog' ),
		'panel' => 'flawless_blog_frontpage_panel',
	)
);

// Flash Articles section enable settings.
$wp_customize->add_setting(
	'flawless_blog_flash_articles_section_enable',
	array(
		'default'           => false,
		'sanitize_callback' => 'flawless_blog_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new Flawless_Blog_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'flawless_blog_flash_articles_section_enable',
		array(
			'label'    => esc_html__( 'Enable Flash Articles Section', 'flawless-blog' ),
			'type'     => 'checkbox',
			'settings' => 'flawless_blog_flash_articles_section_enable',
			'section'  => 'flawless_blog_flash_articles_section',
		)
	)
);

// Flash Articles title settings.
$wp_customize->add_setting(
	'flawless_blog_flash_articles_title',
	array(
		'default'           => __( 'New', 'flawless-blog' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'flawless_blog_flash_articles_title',
	array(
		'label'           => esc_html__( 'Title', 'flawless-blog' ),
		'section'         => 'flawless_blog_flash_articles_section',
		'active_callback' => 'flawless_blog_if_flash_articles_enabled',
	)
);

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'flawless_blog_flash_articles_title',
		array(
			'selector'            => '.articles-ticker-section .theme-wrapper',
			'settings'            => 'flawless_blog_flash_articles_title',
			'container_inclusive' => false,
			'fallback_refresh'    => true,
			'render_callback'     => 'flawless_blog_flash_articles_title_text_partial',
		)
	);
}

// flash_articles content type settings.
$wp_customize->add_setting(
	'flawless_blog_flash_articles_content_type',
	array(
		'default'           => 'post',
		'sanitize_callback' => 'flawless_blog_sanitize_select',
	)
);

$wp_customize->add_control(
	'flawless_blog_flash_articles_content_type',
	array(
		'label'           => esc_html__( 'Content type:', 'flawless-blog' ),
		'description'     => esc_html__( 'Choose where you want to render the content from.', 'flawless-blog' ),
		'section'         => 'flawless_blog_flash_articles_section',
		'type'            => 'select',
		'active_callback' => 'flawless_blog_if_flash_articles_enabled',
		'choices'         => array(
			'post'     => esc_html__( 'Post', 'flawless-blog' ),
			'category' => esc_html__( 'Category', 'flawless-blog' ),
		),
	)
);

for ( $i = 1; $i <= 5; $i++ ) {
	// flash_articles post setting.
	$wp_customize->add_setting(
		'flawless_blog_flash_articles_post_' . $i,
		array(
			'sanitize_callback' => 'flawless_blog_sanitize_dropdown_pages',
		)
	);

	$wp_customize->add_control(
		'flawless_blog_flash_articles_post_' . $i,
		array(
			'label'           => sprintf( esc_html__( 'Post %d', 'flawless-blog' ), $i ),
			'section'         => 'flawless_blog_flash_articles_section',
			'type'            => 'select',
			'choices'         => flawless_blog_get_post_choices(),
			'active_callback' => 'flawless_blog_flash_articles_section_content_type_post_enabled',
		)
	);
}

// flash_articles category setting.
$wp_customize->add_setting(
	'flawless_blog_flash_articles_category',
	array(
		'sanitize_callback' => 'flawless_blog_sanitize_select',
	)
);

$wp_customize->add_control(
	'flawless_blog_flash_articles_category',
	array(
		'label'           => esc_html__( 'Category', 'flawless-blog' ),
		'section'         => 'flawless_blog_flash_articles_section',
		'type'            => 'select',
		'choices'         => flawless_blog_get_post_cat_choices(),
		'active_callback' => 'flawless_blog_flash_articles_section_content_type_category_enabled',
	)
);

/*========================Active Callback==============================*/
function flawless_blog_if_flash_articles_enabled( $control ) {
	return $control->manager->get_setting( 'flawless_blog_flash_articles_section_enable' )->value();
}
function flawless_blog_flash_articles_section_content_type_post_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'flawless_blog_flash_articles_content_type' )->value();
	return flawless_blog_if_flash_articles_enabled( $control ) && ( 'post' === $content_type );
}
function flawless_blog_flash_articles_section_content_type_category_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'flawless_blog_flash_articles_content_type' )->value();
	return flawless_blog_if_flash_articles_enabled( $control ) && ( 'category' === $content_type );
}

/*========================Partial Refresh==============================*/
if ( ! function_exists( 'flawless_blog_flash_articles_title_text_partial' ) ) :
	// Title.
	function flawless_blog_flash_articles_title_text_partial() {
		return esc_html( get_theme_mod( 'flawless_blog_flash_articles_title' ) );
	}
endif;
