<?php
/**
 * Server-side rendering of the `core/post-comments-count` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-comments-count` block on the server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the filtered post comments count for the current post.
 */
function gutenberg_render_block_core_post_comments_count( $attributes ) {
	$post = gutenberg_get_post_from_context();
	if ( ! $post ) {
		return '';
	}
	$class = 'wp-block-post-comments-count';
	if ( isset( $attributes['className'] ) ) {
		$class .= ' ' . $attributes['className'];
	}
	return sprintf(
		'<span class="%1$s">%2$s</span>',
		esc_attr( $class ),
		get_comments_number( $post )
	);
}

/**
 * Registers the `core/post-comments-count` block on the server.
 */
function gutenberg_register_block_core_post_comments_count() {
	register_block_type(
		'core/post-comments-count',
		array(
			'attributes'      => array(
				'className' => array(
					'type' => 'string',
				),
			),
			'render_callback' => 'gutenberg_render_block_core_post_comments_count',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_post_comments_count', 20 );
