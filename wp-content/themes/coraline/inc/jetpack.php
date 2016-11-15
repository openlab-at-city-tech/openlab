<?php
/**
 * Compatibility settings and functions for Jetpack.
 * See http://jetpack.me/support/infinite-scroll/
 */

/**
 * Add support for Infinite Scroll.
 */
function coraline_infinite_scroll_init() {
	add_theme_support( 'infinite-scroll', array(
		'container'      => 'content',
		'footer'         => 'container',
		'footer_widgets' => array( 'first-footer-widget-area', 'second-footer-widget-area', 'third-footer-widget-area', 'fourth-footer-widget-area' ),
		'render'         => 'coraline_infinite_scroll_render',
	) );
}
add_action( 'after_setup_theme', 'coraline_infinite_scroll_init' );

/**
 * Set the code to be rendered on for calling posts,
 * hooked to template parts when possible.
 *
 * Note: must define a loop.
 */
function coraline_infinite_scroll_render() {
	get_template_part( 'loop', 'index' );
}
