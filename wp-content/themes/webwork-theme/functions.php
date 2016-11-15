<?php

function webwork_theme_assets() {
	wp_enqueue_style( 'hemingway-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'webwork-theme-style', get_stylesheet_directory_uri() . '/style.css', array( 'hemingway-style' ) );

	wp_enqueue_script( 'webwork-theme-js', get_stylesheet_directory_uri() . '/webwork.js', array( 'jquery', 'hemingway_global' ) );
}
add_action( 'wp_enqueue_scripts', 'webwork_theme_assets' );

remove_filter( 'wp_list_pages', 'openlab_fix_fallback_menu_for_hemingway', 10, 3 );
/**
 * Don't allow the OpenLab to add additional links to nav menus.
 */
function webwork_remove_page_menu_items( $_ ) {
	remove_filter( 'wp_page_menu', 'my_page_menu_filter' );
	remove_filter( 'wp_nav_menu_objects', 'cuny_add_group_menu_items', 10, 2 );
	return $_;
//	var_dump( 'rchrch' ); die();
}
add_filter( 'template_include', 'webwork_remove_page_menu_items' );
