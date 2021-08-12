<?php
/**
 * Server-side rendering of the `core/post-comments-link` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-comments-link` block on the server.
 *
 * @param  array    $attributes Block attributes.
 * @param  string   $content    Block default content.
 * @param  WP_Block $block      Block instance.
 * @return string   Returns the rendered link.
 */
function gutenberg_render_block_core_post_comments_link( $attributes, $content, $block ) {
	if (
		! isset( $block->context['postId'] ) ||
		isset( $block->context['postId'] ) &&
		! comments_open( $block->context['postId'] )
	) {
		return '';
	}

	$align_class_name   = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $align_class_name ) );
	$comments_number    = (int) get_comments_number( $block->context['postId'] );
	$comments_link      = get_comments_link( $block->context['postId'] );
	$post_title         = get_the_title( $block->context['postId'] );
	$comment_text       = '';

	if ( 0 === $comments_number ) {
		$comment_text = sprintf(
			/* translators: %s post title */
			__( 'No comments<span class="screen-reader-text"> on %s</span>' ),
			$post_title
		);
	} else {
		$comment_text = sprintf(
			/* translators: 1: Number of comments, 2: post title */
			_n(
				'%1$s comment<span class="screen-reader-text"> on %2$s</span>',
				'%1$s comments<span class="screen-reader-text"> on %2$s</span>',
				$comments_number
			),
			number_format_i18n( $comments_number ),
			$post_title
		);
	}

	return "<div {$wrapper_attributes}><a href='{$comments_link}'>{$comment_text}</a></div>";
}

/**
 * Registers the `core/post-comments-link` block on the server.
 */
function gutenberg_register_block_core_post_comments_link() {
	register_block_type_from_metadata(
		__DIR__ . '/post-comments-link',
		array(
			'render_callback' => 'gutenberg_render_block_core_post_comments_link',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_post_comments_link', 20 );
