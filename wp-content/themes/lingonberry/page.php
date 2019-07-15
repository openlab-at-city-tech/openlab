<?php get_header(); ?>

<div class="content section-inner">						
			
	<div class="posts">

		<div class="post">
		
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		
				<div class="content-inner<?php if ( comments_open() ) echo ' comments-allowed'; ?>">
										
					<div class="post-header">
					
						<?php if ( has_post_thumbnail() ) : ?>
	
							<div class="featured-media">
							
								<?php
							
								the_post_thumbnail( 'post-image' );
			
								$image_caption = get_post( get_post_thumbnail_id() )->post_excerpt;
								
								if ( $image_caption ) : ?>
												
									<div class="media-caption-container">
									
										<p class="media-caption"><?php echo $image_caption; ?></p>
										
									</div>
									
								<?php endif; ?>
										
							</div><!-- .featured-media -->
								
						<?php endif; ?>
												
					    <?php the_title( '<h1 class="post-title">', '</h1>' ); ?>
					    				    
				    </div><!-- .post-header -->
	
					<div class="post-content">

						<?php the_content(); ?>

						<?php wp_link_pages(); ?>

						<?php if ( current_user_can( 'manage_options' ) ) : ?>
							<p><?php edit_post_link( __( 'Edit', 'lingonberry' ) ); ?></p>
						<?php endif; ?>
						
					</div><!-- .post-content -->
					
				</div><!-- .post-inner -->
		
				<?php comments_template( '', true ); ?>
			
			<?php endwhile; else: ?>
	
				<p><?php _e( "We couldn't find any posts that matched your query. Please try again.", "lingonberry" ); ?></p>
		
			<?php endif; ?>

		</div><!-- .post -->
	
	</div><!-- .posts -->

	<div class="clear"></div>
	
</div><!-- .content section-inner -->
								
<?php get_footer(); ?>