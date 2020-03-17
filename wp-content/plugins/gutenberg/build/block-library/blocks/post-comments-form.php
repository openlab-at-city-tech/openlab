<?php
/**
 * Server-side rendering of the `core/post-comments-form` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-comments-form` block on the server.
 *
 * @return string Returns the filtered post comments form for the current post.
 */
function gutenberg_render_block_core_post_comments_form() {
	$post = gutenberg_get_post_from_context();
	if ( ! $post ) {
		return '';
	}
	ob_start();
	comment_form( array(), $post->ID );
	$form = ob_get_clean();

	return $form;
}

/**
 * Registers the `core/post-comments-form` block on the server.
 */
function gutenberg_register_block_core_post_comments_form() {
	register_block_type(
		'core/post-comments-form',
		array(
			'render_callback' => 'gutenberg_render_block_core_post_comments_form',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_post_comments_form', 20 );
