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
    
	</div> <!-- comments -->
  
<?php endif; ?>

<?php if ( comments_open() || pings_open() ) : ?>

	<?php comment_form( 'comment_notes_before=&comment_notes_after=' ); ?>

<?php elseif ( $comments ) : ?>

	<div id="respond">
		
		<p class="closed"><?php _e( 'Comments closed', 'hamilton' ); ?></p>
		
	</div> <!-- #respond -->

<?php endif; ?>