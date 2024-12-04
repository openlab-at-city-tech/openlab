<?php

//
// One click demo import
//
if ( ! function_exists( 'kenta_groovy_blog_demo_slug' ) ) {
	function kenta_groovy_blog_demo_slug() {
		return '';
	}
}
add_filter( 'kenta_welcome_demo_slug', 'kenta_groovy_blog_demo_slug' );

if ( ! function_exists( 'kenta_groovy_blog_demo_name' ) ) {
	function kenta_groovy_blog_demo_name() {
		return __( 'Kenta Groovy Blog', 'kenta-groovy-blog' );
	}
}
add_filter( 'kenta_welcome_demo_name', 'kenta_groovy_blog_demo_name' );

if ( ! function_exists( 'kenta_groovy_blog_demo_screenshot' ) ) {
	function kenta_groovy_blog_demo_screenshot() {
		return '';
	}
}
add_filter( 'kenta_welcome_demo_screenshot', 'kenta_groovy_blog_demo_screenshot' );

//
// Dynamic css cache
//
if ( ! function_exists( 'kenta_groovy_blog_cache_key' ) ) {
	function kenta_groovy_blog_cache_key() {
		return 'kenta_groovy_blog_dynamic_css';
	}
}
add_filter( 'kenta_filter_dynamic_css_cache_key', 'kenta_groovy_blog_cache_key' );

if ( ! function_exists( 'kenta_groovy_blog_cache_version' ) ) {
	function kenta_groovy_blog_cache_version() {
		return KENTA_GROOVY_BLOG_VERSION;
	}
}
add_filter( 'kenta_filter_cached_dynamic_css_version', 'kenta_groovy_blog_cache_version' );

if ( ! function_exists( 'kenta_groovy_blog_enqueue_styles' ) ) {
	function kenta_groovy_blog_enqueue_styles() {
		wp_enqueue_style(
			'kenta-groovy-blog-style',
			get_stylesheet_uri(),
			array(),
			KENTA_GROOVY_BLOG_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'kenta_groovy_blog_enqueue_styles', 9999 );

if ( ! function_exists( 'kenta_groovy_blog_setup' ) ) {
	/**
	 * Theme setup
	 */
	function kenta_groovy_blog_setup() {
		add_editor_style( 'style.css' );
	}
}
add_action( 'after_setup_theme', 'kenta_groovy_blog_setup' );

if ( ! function_exists( 'kenta_groovy_blog_setup' ) ) {
	function kenta_groovy_blog_setup() {
		remove_theme_support( 'block-templates' );
	}
}
add_action( 'after_setup_theme', 'kenta_groovy_blog_setup' );
