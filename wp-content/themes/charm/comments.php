<?php
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>

		<h3 class="comments-title"><?php comments_number(); ?></h3>

		<ul class="comment-list">
			<?php wp_list_comments( array( 'avatar_size' => 80 ) ); ?>
		</ul>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<nav class="comments-pagination" role="navigation">
				<?php paginate_comments_links( array( 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?>
			</nav>
		<?php endif; ?>

		<?php if ( ! comments_open() ) : ?>
			<p class="no-comments"><?php _e( 'Comments are closed.', 'themerain' ); ?></p>
		<?php endif; ?>

	<?php endif; ?>

	<?php comment_form(); ?>
</div>