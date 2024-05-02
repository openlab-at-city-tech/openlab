<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package kadence
 */

namespace Kadence;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
kadence()->print_styles( 'kadence-comments' );
?>
<div id="comments" class="comments-area<?php echo ( kadence()->option( 'post_footer_area_boxed' ) ? ' content-bg entry-content-wrap entry' : '' ); ?>">
	<?php
	do_action( 'kadence_before_comments' );
	/**
	 * Kadence Comments hook.
	 *
	 * @hooked Kadence/comments_list - 10
	 * @hooked Kadence/comments_form - 15/5
	 */
	do_action( 'kadence_comments' );

	do_action( 'kadence_after_comments' );

	?>
</div><!-- #comments -->
