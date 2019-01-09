<?php

/* Template Name: Full width template */

get_header(); ?>

<div class="wrapper section-inner">						

	<div class="content full-width">
	
		<?php if ( have_posts() ) : 
			
			while ( have_posts() ) : the_post(); ?>
			
				<div class="posts">
			
					<div class="post">
					
						<?php if ( has_post_thumbnail() ) : ?>
							
							<div class="featured-media">
							
								<a href="<?php the_permalink(); ?>" rel="bookmark">
								
									<?php 
									
									the_post_thumbnail( 'post-image' );

									$image_caption = get_post( get_post_thumbnail_id() )->post_excerpt;
									
									if ( $image_caption ) : ?>
													
										<div class="media-caption-container">
										
											<p class="media-caption"><?php echo $image_caption; ?></p>
											
										</div>
										
									<?php endif; ?>
									
								</a>
										
							</div><!-- .featured-media -->
								
						<?php endif; ?>
															
						<div class="post-header">
													
							<?php the_title( '<h1 class="post-title">', '</h1>' ); ?>
												
						</div><!-- .post-header -->
																						
						<div class="post-content">
																			
							<?php the_content(); ?>
							
							<?php if ( current_user_can( 'manage_options' ) ) : ?>
																			
								<p><?php edit_post_link( __( 'Edit', 'hemingway' ) ); ?></p>
							
							<?php endif; ?>
							
							<div class="clear"></div>
																												
						</div><!-- .post-content -->
			
					</div><!-- .post -->
					
					<?php comments_template( '', true ); ?>
				
				</div><!-- .posts -->
			
				<?php 
			endwhile; 
	
		endif; ?>
	
	</div><!-- .content -->
	
</div><!-- .wrapper section-inner -->
								
<?php get_footer(); ?>