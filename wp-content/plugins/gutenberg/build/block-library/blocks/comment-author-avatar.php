<?php
/**
 * Server-side rendering of the `core/comment-author-avatar` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/comment-author-avatar` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Return the post comment's avatar.
 */
function gutenberg_render_block_core_comment_author_avatar( $attributes, $content, $block ) {
	if ( ! isset( $block->context['commentId'] ) ) {
		return '';
	}

	$comment = get_comment( $block->context['commentId'] );
	if ( ! $comment ) {
		return '';
	}

	// This is the only way to retreive style and classes on different instances.
	$wrapper_attributes = WP_Block_Supports::get_instance()->apply_block_supports();
	$width              = isset( $attributes['width'] ) ? $attributes['width'] : 96;
	$height             = isset( $attributes['height'] ) ? $attributes['height'] : 96;
	$styles             = isset( $wrapper_attributes['style'] ) ? $wrapper_attributes['style'] : '';
	$classes            = isset( $wrapper_attributes['class'] ) ? $wrapper_attributes['class'] : '';

	/* translators: %s is the Comment Author name */
	$alt = sprintf( __( '%s Avatar' ), $comment->comment_author );

	$avatar_string = get_avatar(
		$comment,
		null,
		'',
		$alt,
		array(
			'height'     => $height,
			'width'      => $width,
			'extra_attr' => sprintf( 'style="%1s"', $styles ),
			'class'      => $classes,
		)
	);
	return $avatar_string;
}

/**
 * Registers the `core/comment-author-avatar` block on the server.
 */
function gutenberg_register_block_core_comment_author_avatar() {
	register_block_type_from_metadata(
		__DIR__ . '/comment-author-avatar',
		array(
			'render_callback' => 'gutenberg_render_block_core_comment_author_avatar',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_comment_author_avatar', 20 );
