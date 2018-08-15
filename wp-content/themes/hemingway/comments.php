<?php 

if ( post_password_required() ) {
	return;
}

if ( have_comments() ) : ?>

	<div class="comments">
	
		<a name="comments"></a>
			
		<h2 class="comments-title">
		
			<?php 
			$comment_count = count( $wp_query->comments_by_type['comment'] );
			printf( _n( '%s Comment', '%s Comments', $comment_count, 'hemingway' ), absint( $comment_count ) ); 
			?>
			
		</h2>

		<ol class="commentlist">
			<?php wp_list_comments( array( 
				'callback' 	=> 'hemingway_comment',
				'type' 		=> 'comment', 
			) ); ?>
		</ol>
		
		<?php if ( ! empty( $comments_by_type['pings'] ) ) : ?>
		
			<div class="pingbacks">
			
				<div class="pingbacks-inner">
			
					<h3 class="pingbacks-title">
					
						<?php 
						$pingback_count = count( $wp_query->comments_by_type['pings'] );
						printf( _n( '%s Pingback', '%s Pingbacks', $pingback_count, 'hemingway' ), absint( $pingback_count ) ); 
						?>
					
					</h3>
				
					<ol class="pingbacklist">
						<?php 
						wp_list_comments( array( 
							'type' 		=> 'pings', 
							'callback' 	=> 'hemingway_comment' 
						) ); 
						?>
					</ol>
					
				</div>
				
			</div>
		
		<?php endif; ?>
			
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		
			<div class="comment-nav-below" role="navigation">
								
				<div class="post-nav-older"><?php previous_comments_link( __( '&laquo; Older<span> Comments</span>', 'hemingway' ) ); ?></div>
				
				<div class="post-nav-newer"><?php next_comments_link( __( 'Newer<span> Comments</span> &raquo;', 'hemingway' ) ); ?></div>
				
				<div class="clear"></div>
				
			</div><!-- .comment-nav-below -->
			
		<?php endif; ?>
		
	</div><!-- /comments -->
	
	<?php 
endif;

if ( ! comments_open() && !is_page() ) : ?>

	<p class="nocomments"><?php _e( 'Comments are closed.', 'hemingway' ); ?></p>
	
<?php endif;

comment_form();

?>