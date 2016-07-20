<div class="post-header">

	<?php if ( has_post_thumbnail() ) : ?>

		<div class="featured-media">
		
			<?php if( is_sticky() ) { ?> <span class="sticky-post"><?php _e('Sticky post', 'hemingway'); ?></span> <?php } ?>
		
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>">
			
				<?php the_post_thumbnail('post-image'); ?>
				
				<?php if ( !empty(get_post(get_post_thumbnail_id())->post_excerpt) ) : ?>
								
					<div class="media-caption-container">
					
						<p class="media-caption"><?php echo get_post(get_post_thumbnail_id())->post_excerpt; ?></p>
						
					</div>
					
				<?php endif; ?>
				
			</a>
					
		</div> <!-- /featured-media -->
			
	<?php endif; ?>
	
    <h2 class="post-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
    
    <div class="post-meta">
	
		<span class="post-date"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_time(get_option('date_format')); ?></a></span>
		
		<span class="date-sep"> / </span>
			
		<span class="post-author"><?php the_author_posts_link(); ?></span>
		
		<span class="date-sep"> / </span>
		
		<?php comments_popup_link( '<span class="comment">' . __( '0 Comments', 'hemingway' ) . '</span>', __( '1 Comment', 'hemingway' ), __( '% Comments', 'hemingway' ) ); ?>
		
		<?php if( is_sticky() && !has_post_thumbnail() ) { ?> 
		
			<span class="date-sep"> / </span>
		
			<?php _e('Sticky', 'hemingway'); ?>
		
		<?php } ?>
		
		<?php if ( current_user_can( 'manage_options' ) ) { ?>
		
			<span class="date-sep"> / </span>
						
			<?php edit_post_link(__('Edit', 'hemingway')); ?>
		
		<?php } ?>
								
	</div>
    
</div> <!-- /post-header -->
									                                    	    
<div class="post-content">
	    		            			            	                                                                                            
		<?php the_content(); ?>
					
		<?php wp_link_pages(); ?>

</div> <!-- /post-content -->
            
<div class="clear"></div>