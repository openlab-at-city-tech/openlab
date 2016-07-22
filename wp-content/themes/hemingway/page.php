<?php get_header(); ?>

<div class="wrapper section-inner">						

	<div class="content left">
	
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
		<div class="posts">
	
			<div class="post">
			
				<?php if ( has_post_thumbnail() ) : ?>
					
					<div class="featured-media">
					
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
														
				<div class="post-header">
											
				    <h1 class="post-title"><?php the_title(); ?></h1>
				    				    
			    </div> <!-- /post-header -->
			   				        			        		                
				<div class="post-content">
							                                        
					<?php the_content(); ?>
					
					<?php if ( current_user_can( 'manage_options' ) ) : ?>
																	
						<p><?php edit_post_link( __('Edit', 'hemingway') ); ?></p>
					
					<?php endif; ?>
														            			                        
				</div> <!-- /post-content -->
								
			</div> <!-- /post -->
			
			<?php if ( comments_open() || get_comments_number() != '' ) : ?>
			
				<?php comments_template( '', true ); ?>
			
			<?php endif; ?>
		
		</div> <!-- /posts -->
		
		<?php endwhile; else: ?>
		
			<p><?php _e("We couldn't find any posts that matched your query. Please try again.", "hemingway"); ?></p>
	
		<?php endif; ?>
	
		<div class="clear"></div>
		
	</div> <!-- /content left -->
	
	<?php get_sidebar(); ?>
	
	<div class="clear"></div>

</div> <!-- /wrapper -->
								
<?php get_footer(); ?>