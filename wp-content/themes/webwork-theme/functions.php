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
