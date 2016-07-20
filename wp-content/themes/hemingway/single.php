<?php get_header(); ?>

<div class="wrapper section-inner">

	<div class="content left">
												        
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
			<div class="posts">
		
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
					<div class="post-header">

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
						
						<?php $videourl = get_post_meta($post->ID, 'videourl', true); if ( $videourl != '' ) : ?>

							<div class="featured-media">
							
								<?php if (strpos($videourl,'.mp4') !== false) : ?>
				
									<video controls>
									  <source src="<?php echo $videourl; ?>" type="video/mp4">
									</video>
																							
								<?php else : ?>
									
									<?php 
									
										$embed_code = wp_oembed_get($videourl); 
										
										echo $embed_code;
										
									?>
										
								<?php endif; ?>
								
							</div> <!-- /featured-media -->
						
						<?php endif; ?>
						
					    <h1 class="post-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
					    
					    <div class="post-meta">
						
							<span class="post-date"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_time(get_option('date_format')); ?></a></span>
							
							<span class="date-sep"> / </span>
								
							<span class="post-author"><?php the_author_posts_link(); ?></span>
							
							<span class="date-sep"> / </span>
							
							<?php comments_popup_link( '<span class="comment">' . __( '0 Comments', 'hemingway' ) . '</span>', __( '1 Comment', 'hemingway' ), __( '% Comments', 'hemingway' ) ); ?>
							
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
					
					<div class="post-meta-bottom">
																		
						<p class="post-categories"><span class="category-icon"><span class="front-flap"></span></span> <?php the_category(', '); ?></p>
						
						<?php if( has_tag()) { ?><p class="post-tags"><?php the_tags('', ''); ?></p><?php } ?>
						
						<div class="clear"></div>
												
						<div class="post-nav">
													
							<?php
							$prev_post = get_previous_post();
							if (!empty( $prev_post )): ?>
							
								<a class="post-nav-older" title="<?php _e('Previous post:', 'hemingway'); echo ' ' . get_the_title($prev_post); ?>" href="<?php echo get_permalink( $prev_post->ID ); ?>">
								
								<h5><?php _e('Previous post', 'hemingway'); ?></h5>																
								<?php echo get_the_title($prev_post); ?>
								
								</a>
						
							<?php endif; ?>
							
							<?php
							$next_post = get_next_post();
							if (!empty( $next_post )): ?>
								
								<a class="post-nav-newer" title="<?php _e('Next post:', 'hemingway'); echo ' ' . get_the_title($next_post); ?>" href="<?php echo get_permalink( $next_post->ID ); ?>">
								
								<h5><?php _e('Next post', 'hemingway'); ?></h5>							
								<?php echo get_the_title($next_post); ?>
								
								</a>
						
							<?php endif; ?>
														
							<div class="clear"></div>
						
						</div> <!-- /post-nav -->
											
					</div> <!-- /post-meta-bottom -->
					
					<?php comments_template( '', true ); ?>
												                        
			   	<?php endwhile; else: ?>
			
					<p><?php _e("We couldn't find any posts that matched your query. Please try again.", "hemingway"); ?></p>
				
				<?php endif; ?>    
		
			</div> <!-- /post -->
			
		</div> <!-- /posts -->
	
	</div> <!-- /content -->
	
	<?php get_sidebar(); ?>
	
	<div class="clear"></div>
	
</div> <!-- /wrapper -->
		
<?php get_footer(); ?>