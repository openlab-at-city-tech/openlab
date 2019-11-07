<?php
/**
 * Server-side rendering of the `core/site-title` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/site-title` block on the server.
 *
 * @return string The render.
 */
function gutenberg_render_block_core_site_title() {
	return sprintf( '<h1>%s</h1>', get_bloginfo( 'name' ) );
}

/**
 * Registers the `core/site-title` block on the server.
 */
function gutenberg_register_block_core_site_title() {
	register_block_type(
		'core/site-title',
		array(
			'render_callback' => 'gutenberg_render_block_core_site_title',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_site_title', 20 );
