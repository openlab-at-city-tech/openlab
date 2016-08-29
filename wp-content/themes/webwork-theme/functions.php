<?php

function webwork_theme_assets() {
	wp_enqueue_style( 'hemingway-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'webwork-theme-style', get_stylesheet_directory_uri() . '/style.css', array( 'hemingway-style' ) );

	wp_enqueue_script( 'webwork-theme-js', get_stylesheet_directory_uri() . '/webwork.js', array( 'jquery', 'hemingway_global' ) );
}
add_action( 'wp_enqueue_scripts', 'webwork_theme_assets' );

