<?php

function themerain_customize_register( $wp_customize ) {
	/**
	 * Settings
	 */
	$wp_customize->add_panel( 'rain_settings', array(
		'title' => 'Settings',
		'priority' => 1
	) );

	// Colors
	$wp_customize->add_section( 'rain_settings_colors', array(
		'title' => 'Colors',
		'panel' => 'rain_settings'
	) );

	$wp_customize->add_setting( 'rain_accent_color', array( 'default' => '#b09a68' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rain_accent_color', array(
		'label' => 'Accent Color',
		'description' => '',
		'section' => 'rain_settings_colors',
		'settings' => 'rain_accent_color'
	) ) );

	// Blog
	$wp_customize->add_section( 'rain_settings_blog', array(
		'title' => 'Blog',
		'panel' => 'rain_settings'
	) );

	$wp_customize->add_setting( 'rain_default_blog_title', array( 'default' => 'Blog' ) );
	$wp_customize->add_control( 'rain_default_blog_title', array(
		'type' => 'text',
		'label' => 'Default Blog Title',
		'description' => '',
		'section' => 'rain_settings_blog'
	) );

	$wp_customize->add_setting( 'rain_default_blog_subtitle', array( 'default' => 'Blog subtitle' ) );
	$wp_customize->add_control( 'rain_default_blog_subtitle', array(
		'type' => 'text',
		'label' => 'Default Blog Subtitle',
		'description' => '',
		'section' => 'rain_settings_blog'
	) );

	$wp_customize->add_setting( 'rain_default_blog_image' );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'rain_default_blog_image', array(
		'label' => 'Default Blog Image',
		'description' => '',
		'section' => 'rain_settings_blog',
		'settings' => 'rain_default_blog_image'
	) ) );

	// Portfolio
	$wp_customize->add_section( 'rain_settings_portfolio', array(
		'title' => 'Portfolio',
		'panel' => 'rain_settings'
	) );

	$wp_customize->add_setting( 'rain_projects_per_page', array( 'default' => '9' ) );
	$wp_customize->add_control( 'rain_projects_per_page', array(
		'type' => 'number',
		'label' => 'Portfolio Count',
		'description' => 'Set the number of projects to display on the portfolio page. Use "-1" to load them all.',
		'section' => 'rain_settings_portfolio'
	) );

	$wp_customize->add_setting( 'rain_default_portfolio_page' );
	$wp_customize->add_control( 'rain_default_portfolio_page', array(
		'type' => 'dropdown-pages',
		'label' => 'Default Portfolio Page',
		'description' => '',
		'section' => 'rain_settings_portfolio'
	) );

	// Footer
	$wp_customize->add_section( 'rain_settings_footer', array(
		'title' => 'Footer',
		'panel' => 'rain_settings'
	) );

	$wp_customize->add_setting( 'rain_copyright', array( 'default' => '&copy; ' . date( 'Y ' ) . get_bloginfo( 'name' ) ) );
	$wp_customize->add_control( 'rain_copyright', array(
		'type' => 'textarea',
		'label' => 'Footer Copyright',
		'description' => '',
		'section' => 'rain_settings_footer'
	) );

	/**
	 * Site Identity
	 */
	$wp_customize->add_setting( 'rain_logo' );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'rain_logo', array(
		'label' => 'Logo',
		'description' => '',
		'section' => 'title_tagline',
		'settings' => 'rain_logo'
	) ) );

	$wp_customize->add_setting( 'rain_logo_width' );
	$wp_customize->add_control( 'rain_logo_width', array(
		'type' => 'text',
		'label' => 'Logo Width',
		'description' => '',
		'section' => 'title_tagline'
	) );

	/**
	 * Custom CSS
	 */
	$wp_customize->add_section( 'rain_custom_css_section', array(
		'title' => 'Custom CSS',
		'priority' => 30
	) );

	$wp_customize->add_setting( 'rain_custom_css' );
	$wp_customize->add_control( 'rain_custom_css', array(
		'type' => 'textarea',
		'label' => 'Custom CSS',
		'description' => '',
		'section' => 'rain_custom_css_section'
	) );
}
add_action( 'customize_register', 'themerain_customize_register', 11 );

// Enqueues dynamic CSS
function themerain_dynamic_css() {
	$accent_color = get_theme_mod( 'rain_accent_color', '#b09a68' );
	$custom_css = get_theme_mod( 'rain_custom_css' );

	$css = '
		.page-header-content a,
		.post-category a,
		.post-content a,
		.widget_text a {
			color: ' . $accent_color . ';
		}

		.page-header-content a:after,
		.post-category a:after {
			background-color: ' . $accent_color . ';
		}

		' . $custom_css . '
	';

	wp_add_inline_style( 'style', $css );
}
add_action( 'wp_enqueue_scripts', 'themerain_dynamic_css', 11 );