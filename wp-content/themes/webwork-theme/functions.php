<?php

function webwork_theme_assets() {
	wp_enqueue_style( 'hemingway-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'webwork-theme-style', get_stylesheet_directory_uri() . '/style.css', array( 'hemingway-style' ) );

	wp_enqueue_script( 'webwork-theme-js', get_stylesheet_directory_uri() . '/webwork.js', array( 'jquery', 'hemingway_global' ) );
}
add_action( 'wp_enqueue_scripts', 'webwork_theme_assets' );

// Add 'ol-webwork' class to body for style overrides.
add_filter( 'body_class', function( $class ) {
	$class[] = 'ol-webwork';
	return $class;
} );

/**
 * Don't allow the OpenLab to add additional links to nav menus.
 */
add_filter( 'wp_page_menu', function( $_ ) {
	remove_filter( 'wp_page_menu', 'my_page_menu_filter' );
	return $_;
}, 0 );

add_filter( 'wp_nav_menu_objects', function( $_ ) {
	remove_filter( 'wp_nav_menu_objects', 'cuny_add_group_menu_items', 10, 2 );
	return $_;
}, 0 );

/**
 * Don't let WordPress load emoji scripts on this theme.
 */
function webwork_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	//add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'webwork_disable_emojis' );
