<?php
/**
 * Server-side rendering of the `core/post-terms` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-terms` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the filtered post terms for the current post wrapped inside "a" tags.
 */
function gutenberg_render_block_core_post_terms( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) || ! isset( $attributes['term'] ) ) {
		return '';
	}

	$post_terms = get_the_terms( $block->context['postId'], $attributes['term'] );
	if ( is_wp_error( $post_terms ) ) {
		return '';
	}
	if ( empty( $post_terms ) ) {
		return '';
	}

	$classes = 'taxonomy-' . $attributes['term'];
	if ( isset( $attributes['textAlign'] ) ) {
		$classes .= ' has-text-align-' . $attributes['textAlign'];
	}

	$terms_links = '';
	foreach ( $post_terms as $term ) {
		$terms_links .= sprintf(
			'<a href="%1$s">%2$s</a> | ',
			get_term_link( $term->term_id ),
			esc_html( $term->name )
		);
	}
	$terms_links        = trim( $terms_links, ' | ' );
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classes ) );

	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$terms_links
	);
}

/**
 * Registers the `core/post-terms` block on the server.
 */
function gutenberg_register_block_core_post_terms() {
	register_block_type_from_metadata(
		__DIR__ . '/post-terms',
		array(
			'render_callback' => 'gutenberg_render_block_core_post_terms',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_post_terms', 20 );
