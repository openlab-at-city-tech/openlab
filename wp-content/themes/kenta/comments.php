<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @see https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Kenta
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

use LottaFramework\Utils;

if ( post_password_required() ) {
	return;
}

$attrs = [
	'class' => 'mx-auto kenta-max-w-content has-global-padding',
];

if ( is_customize_preview() ) {
	$attrs['data-shortcut']          = 'border';
	$attrs['data-shortcut-location'] = 'kenta_content:kenta_content_form';
}
?>
<div <?php Utils::print_attribute_string( $attrs ); ?>>
    <div id="comments" class="kenta-comments-area">
		<?php
		// Check for have_comments().
		if ( have_comments() ): ?>
            <h2 class="comments-title font-bold text-lg mb-gutter">
				<?php
				$comment_count = get_comments_number();
				if ( 1 == $comment_count ) {
					echo esc_html__( 'One Comment', 'kenta' );
				} else {
					/* translators: %s: The count of comments */
					printf( esc_html__( '%s Comments', 'kenta' ), $comment_count );
				}
				?>
            </h2>

			<?php the_comments_navigation(); ?>

            <ol class="comment-list mb-gutter">
				<?php
				wp_list_comments( [
					'style'      => 'ol',
					'short_ping' => true,
				] );
				?>
            </ol><!-- .comment-list -->

			<?php the_comments_navigation();

			// If comments are closed and there are comments, let's leave a little note, shall we?
			if ( ! comments_open() ): ?>
                <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'kenta' ); ?></p>
			<?php
			endif;
		endif;

		comment_form( [
			'class_form' => 'comment-form kenta-form form-default'
		] );
		?>
    </div>
</div><!-- #comments -->
