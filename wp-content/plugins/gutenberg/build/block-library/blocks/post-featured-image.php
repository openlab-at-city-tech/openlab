<?php
/**
 * Server-side rendering of the `core/post-featured-image` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-featured-image` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the featured image for the current post.
 */
function gutenberg_render_block_core_post_featured_image( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	return '<p>' . get_the_post_thumbnail( $block->context['postId'] ) . '</p>';
}

/**
 * Registers the `core/post-featured-image` block on the server.
 */
function gutenberg_register_block_core_post_featured_image() {
	register_block_type_from_metadata(
		__DIR__ . '/post-featured-image',
		array(
			'render_callback' => 'gutenberg_render_block_core_post_featured_image',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_post_featured_image', 20 );
