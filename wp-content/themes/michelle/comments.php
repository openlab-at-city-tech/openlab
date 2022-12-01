<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains
 * both the current comments and the comment form.
 *
 * @link  https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.2.0
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

do_action( 'tha_comments_before' );

?>

<div id="comments" class="comments-area">
	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) :
		?>

		<h2 class="comments-title">
			<?php

			$comment_count = get_comments_number();

			if ( '1' === $comment_count ) {
				printf(
					/* translators: 1: title. */
					esc_html__( 'One thought on &ldquo;%1$s&rdquo;', 'michelle' ),
					'<span>' . get_the_title() . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			} else {
				printf(
					/* translators: 1: comment count number, 2: title. */
					esc_html( _nx( '%1$d thought on &ldquo;%2$s&rdquo;', '%1$d thoughts on &ldquo;%2$s&rdquo;', $comment_count, 'Comments list title.', 'michelle' ) ),
					number_format_i18n( $comment_count ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'<span>' . get_the_title() . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}

			?>
		</h2>

		<?php the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php

			wp_list_comments( array(
				'avatar_size' => 240,
				'style'       => 'ol',
				'short_ping'  => true,
			) );

			?>
		</ol>

		<?php

		the_comments_navigation();

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() ) :
			?>
			<p class="comments-closed no-comments"><?php esc_html_e( 'Comments are closed.', 'michelle' ); ?></p>
			<?php
		endif;

	endif; // Check for have_comments().

	comment_form();

	?>
</div>

<?php

do_action( 'tha_comments_after' );
