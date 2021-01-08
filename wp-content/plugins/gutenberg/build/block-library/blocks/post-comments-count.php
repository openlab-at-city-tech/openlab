<?php
/**
 * Server-side rendering of the `core/post-comments-count` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-comments-count` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the filtered post comments count for the current post.
 */
function gutenberg_render_block_core_post_comments_count( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$class = 'wp-block-post-comments-count';
	if ( isset( $attributes['className'] ) ) {
		$class .= ' ' . $attributes['className'];
	}

	return sprintf(
		'<span class="%1$s">%2$s</span>',
		esc_attr( $class ),
		get_comments_number( $block->context['postId'] )
	);
}

/**
 * Registers the `core/post-comments-count` block on the server.
 */
function gutenberg_register_block_core_post_comments_count() {
	register_block_type_from_metadata(
		__DIR__ . '/post-comments-count',
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
