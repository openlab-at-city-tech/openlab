<?php

if ( post_password_required() ) {
	return;
}

$comments_number = absint( get_comments_number() );

if ( $comments_number ) : 

	// Translators: %s = the number of comments
	$comments_title = sprintf( _nx( '%s Comment', '%s Comments', $comments_number, 'Translators: %s = the number of comments', 'koji' ), $comments_number ); ?>

	<div class="comments" id="comments">

	<div class="comments-header">

		<h3 class="comment-reply-title"><?php echo $comments_title; ?></h3>

		<?php if ( comments_open() ) : ?>

			<a class="leave-comment-link" href="#respond"><?php _e( 'Add Yours &rarr;', 'koji' ); ?></a>

		<?php endif; ?>

	</div><!-- .comments-header -->

	<?php

	wp_list_comments( array(
		'avatar_size'	=> 120,
		'style' 		=> 'div',
	) );

	$comment_pagination = paginate_comments_links( array(
		'echo'	=> false,
	) );

	if ( $comment_pagination ) :

		// If we're only showing the "Next" link, add a class indicating so
		if ( strpos( $comment_pagination, 'prev page-numbers' ) === false ) {
			$pagination_classes = ' only-next';
		} else {
			$pagination_classes = '';
		}
		?>

		<nav class="comments-pagination pagination<?php echo $pagination_classes; ?>">
			<?php echo $comment_pagination; ?>
		</nav>

	<?php endif; ?>

	</div><!-- comments -->

<?php endif; ?>

<?php if ( comments_open() || pings_open() ) :

	comment_form( 'comment_notes_before=&comment_notes_after=' );

else : ?>

	<div class="comment-respond" id="respond">

		<p class="closed"><?php _e( 'Comments are closed.', 'koji' ); ?></p>

	</div><!-- #respond -->

<?php endif; ?>
