<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Miniva
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function miniva_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	$miniva_sidebar = apply_filters( 'miniva_sidebar', 'sidebar-1' );
	if ( empty( $miniva_sidebar ) ) {
		$miniva_sidebar = 'sidebar-1';
	}
	if ( ! is_active_sidebar( $miniva_sidebar ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'miniva_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function miniva_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'miniva_pingback_header' );
