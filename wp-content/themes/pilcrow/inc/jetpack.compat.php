<?php
/**
 * Compatibility settings and functions for Jetpack.
 * See http://jetpack.me/support/infinite-scroll/
 *
 * @package Pilcrow
 */

/**
 * Add support for Infinite Scroll.
 */
function pilcrow_infinite_scroll_init() {
	add_theme_support( 'infinite-scroll', array(
		'container'      => 'content',
		'footer'         => 'page',
		'footer_widgets' => array( 'sidebar-4', 'sidebar-5' ),
		'render'         => 'pilcrow_infinite_scroll_render',
	) );
}
add_action( 'after_setup_theme', 'pilcrow_infinite_scroll_init' );

/**
 * Set the code to be rendered on for calling posts,
 * hooked to template parts when possible.
 *
 * Note: must define a loop.
 */
function pilcrow_infinite_scroll_render() {
	get_template_part( 'loop', 'index' );
}
