<?php
/**
 * Server-side rendering of the `core/post-comments-form` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-comments-form` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the filtered post comments form for the current post.
 */
function gutenberg_render_block_core_post_comments_form( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	ob_start();
	comment_form( array(), $block->context['postId'] );
	$form = ob_get_clean();

	return $form;
}

/**
 * Registers the `core/post-comments-form` block on the server.
 */
function gutenberg_register_block_core_post_comments_form() {
	register_block_type_from_metadata(
		__DIR__ . '/post-comments-form',
		array(
			'render_callback' => 'gutenberg_render_block_core_post_comments_form',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_post_comments_form', 20 );
