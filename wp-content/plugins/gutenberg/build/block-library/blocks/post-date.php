<?php
/**
 * Server-side rendering of the `core/post-date` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-date` block on the server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the filtered post date for the current post wrapped inside "time" tags.
 */
function gutenberg_render_block_core_post_date( $attributes ) {
	$post = gutenberg_get_post_from_context();
	if ( ! $post ) {
		return '';
	}
	return '<time datetime="'
		. get_the_date( 'c', $post ) . '">'
		. get_the_date( isset( $attributes['format'] ) ? $attributes['format'] : '', $post )
		. '</time>';
}

/**
 * Registers the `core/post-date` block on the server.
 */
function gutenberg_register_block_core_post_date() {
	register_block_type(
		'core/post-date',
		array(
			'attributes'      => array(
				'format' => array(
					'type' => 'string',
				),
			),
			'render_callback' => 'gutenberg_render_block_core_post_date',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_post_date', 20 );
