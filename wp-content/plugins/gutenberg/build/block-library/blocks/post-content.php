<?php
/**
 * Server-side rendering of the `core/post-content` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-content` block on the server.
 *
 * @return string Returns the filtered post content of the current post.
 */
function gutenberg_render_block_core_post_content() {
	$post = gutenberg_get_post_from_context();
	if ( ! $post ) {
		return '';
	}
	return (
		'<div class="entry-content">' .
			apply_filters( 'the_content', str_replace( ']]>', ']]&gt;', get_the_content( $post ) ) ) .
		'</div>'
	);
}

/**
 * Registers the `core/post-content` block on the server.
 */
function gutenberg_register_block_core_post_content() {
	register_block_type(
		'core/post-content',
		array(
			'render_callback' => 'gutenberg_render_block_core_post_content',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_post_content', 20 );
