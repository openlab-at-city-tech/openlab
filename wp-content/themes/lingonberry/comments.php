<?php 

if ( post_password_required() ) {
	return;
}

if ( have_comments() ) : ?>
	
	<a name="comments"></a>

	<div class="comments">
			
		<h2 class="comments-title">
		
			<?php 
			$comment_count = count( $wp_query->comments_by_type['comment'] );
			echo $comment_count . ' ' . _n( 'Comment', 'Comments', $comment_count, 'lingonberry' ); ?>
			
		</h2>

		<ol class="commentlist">
			<?php wp_list_comments( array( 'type' => 'comment', 'callback' => 'lingonberry_comment' ) ); ?>
		</ol>
		
		<?php if ( ! empty( $comments_by_type['pings'] ) ) : ?>
		
			<div class="pingbacks">
			
				<div class="pingbacks-inner">
			
					<h3 class="pingbacks-title">

						<?php 
						$pingback_count = count( $wp_query->comments_by_type['pings'] );
						echo $pingback_count . ' ' . _n( 'Pingback', 'Pingbacks', $pingback_count, 'lingonberry' ); ?>
					
					</h3>
				
					<ol class="pingbacklist">
						<?php wp_list_comments( array( 'type' => 'pings', 'callback' => 'lingonberry_comment' ) ); ?>
					</ol>
					
				</div>
				
			</div>
		
		<?php endif; ?>
			
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		
			<div class="comment-nav-below post-nav" role="navigation">
			
				<h3 class="assistive-text section-heading"><?php _e( 'Comment Navigation', 'lingonberry' ); ?></h3>
				
				<div class="post-nav-older"><?php previous_comments_link( '&laquo; ' . __('Older','lingonberry') . '<span> ' . __('Comments', 'lingonberry') . '</span>'); ?></div>
				
				<div class="post-nav-newer"><?php next_comments_link( __('Newer','lingonberry') . '<span> ' . __('Comments', 'lingonberry') . '</span>  &raquo;' ); ?></div>
				
				<div class="clear"></div>
				
			</div><!-- .comment-nav-below -->
			
		<?php endif; ?>
		
	</div><!-- .comments -->
	
	<?php 
endif;

if ( ! comments_open() && !is_page() ) : ?>

	<p class="nocomments"><?php _e( 'Comments are closed.', 'lingonberry' ); ?></p>
	
<?php endif;

comment_form();

?>