<?php if ( post_password_required() )
	return;
?>

	<?php if ( have_comments() ) : ?>
	
		<div class="comments">
		
			<a name="comments"></a>
				
			<h2 class="comments-title">
			
				<?php printf( _n( '%s Comment', '%s Comments', count($wp_query->comments_by_type[comment]), 'hemingway' ), count($wp_query->comments_by_type[comment]) ); ?>
				
			</h2>
	
			<ol class="commentlist">
			    <?php wp_list_comments( array( 'type' => 'comment', 'callback' => 'hemingway_comment' ) ); ?>
			</ol>
			
			<?php if ( ! empty( $comments_by_type['pings'] ) ) : ?>
			
				<div class="pingbacks">
				
					<div class="pingbacks-inner">
				
						<h3 class="pingbacks-title">
                        
                            <?php printf( _n( '%s Pingback', '%s Pingbacks', count($wp_query->comments_by_type[pings]), 'hemingway' ), count($wp_query->comments_by_type[pings]) ); ?>
						
						</h3>
					
						<ol class="pingbacklist">
						    <?php wp_list_comments( array( 'type' => 'pings', 'callback' => 'hemingway_comment' ) ); ?>
						</ol>
						
					</div>
					
				</div>
			
			<?php endif; ?>
				
			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			
				<div class="comment-nav-below" role="navigation">
									
					<div class="post-nav-older"><?php previous_comments_link( __( '&laquo; Older<span> Comments</span>', 'hemingway' ) ); ?></div>
					
					<div class="post-nav-newer"><?php next_comments_link( __( 'Newer<span> Comments</span> &raquo;', 'hemingway' ) ); ?></div>
					
					<div class="clear"></div>
					
				</div> <!-- /comment-nav-below -->
				
			<?php endif; ?>
			
		</div><!-- /comments -->
		
	<?php endif; ?>
	
	<?php if ( ! comments_open() && !is_page() ) : ?>
	
		<p class="nocomments"><?php _e( 'Comments are closed.', 'hemingway' ); ?></p>
		
	<?php endif; ?>
	
	<?php $comments_args = array(
	
		'comment_notes_before' => 
			'<p class="comment-notes">' . __( 'Your email address will not be published.', 'hemingway' ) . '</p>',
	
		'comment_field' => 
			'<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="6" required>' . '</textarea></p>',
		
		'fields' => apply_filters( 'comment_form_default_fields', array(
		
			'author' =>
				'<p class="comment-form-author">' .
				'<input id="author" name="author" type="text" placeholder="' . __('Name','hemingway') . '" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" />' . '<label for="author">Author</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '</p>',
			
			'email' =>
				'<p class="comment-form-email">' . '<input id="email" name="email" type="text" placeholder="' . __('Email','hemingway') . '" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" /><label for="email">Email</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '</p>',
			
			'url' =>
			'<p class="comment-form-url">' . '<input id="url" name="url" type="text" placeholder="' . __('Website','hemingway') . '" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /><label for="url">Website</label></p>')
		),
	);
	
	comment_form($comments_args);
	
	?>