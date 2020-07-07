<?php

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
*/
if ( post_password_required() ) {
	return;
}

?>

<div class="comments-container" id="comments">

	<?php if ( $comments ) : ?>

		<div class="comments">
		
			<h3 class="comment-reply-title"><?php _e( 'Comments', 'hamilton' ) ?></h3>
			
			<?php 
			
			wp_list_comments( array( 
				'style' 		=>	'div',
				'avatar_size'	=>	110,
			) );
			
			if ( paginate_comments_links( 'echo=0' ) ) : ?>
			
				<div class="comments-pagination pagination"><?php paginate_comments_links(); ?></div>
			
			<?php endif; ?>
		
		</div><!-- comments -->
	
	<?php endif; ?>

	<?php if ( comments_open() || pings_open() ) : ?>

		<?php comment_form( 'comment_notes_before=&comment_notes_after=' ); ?>

	<?php endif; ?>

</div><!-- .comments-container -->