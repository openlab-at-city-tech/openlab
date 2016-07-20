<?php
/*
Template Name: Archive template
*/
?>

<?php get_header(); ?>

<div class="wrapper section-inner">						

	<div class="content left">
			
		<div class="posts">
	
			<div class="post">
			
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				
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
												
					    <h2 class="post-title"><?php the_title(); ?></h2>
					    				    
				    </div> <!-- /post-header -->
				   				        			        		                
					<div class="post-content">
								                                        
						<?php the_content(); ?>
						
						<div class="archive-box">
					
							<div class="archive-col">
												
								<h3><?php _e('Last 30 Posts', 'hemingway') ?></h3>
								            
					            <ul>
						            <?php $archive_30 = get_posts('numberposts=30');
						            foreach($archive_30 as $post) : ?>
						                <li>
						                	<a href="<?php the_permalink(); ?>">
						                		<?php the_title();?> 
						                		<span>(<?php the_time(get_option('date_format')); ?>)</span>
						                	</a>
						                </li>
						            <?php endforeach; ?>
					            </ul>
					            
					            <h3><?php _e('Archives by Categories', 'hemingway') ?></h3>
					            
					            <ul>
					                <?php wp_list_categories( 'title_li=', 'hemingway' ); ?>
					            </ul>
					            
					            <h3><?php _e('Archives by Tags', 'hemingway') ?></h3>
					            
					            <ul>
					                <?php $tags = get_tags();
					                
					                if ($tags) {
					                    foreach ($tags as $tag) {
					                 	   echo '<li><a href="' . get_tag_link( $tag->term_id ) . '" title="' . sprintf( __( "View all posts in %s", 'hemingway' ), $tag->name ) . '" ' . '>' . $tag->name.'</a></li> ';
					                    }
					                }
					                
					                wp_reset_query();?>
					            </ul>
				            
				            </div> <!-- /archive-col -->
				            
				            <div class="archive-col">
				            
				            	<h3><?php _e('Contributors', 'hemingway') ?></h3>
				            	
				            	<ul>
				            		<?php wp_list_authors(); ?> 
				            	</ul>
				            	
				            	<h3><?php _e('Archives by Year', 'hemingway') ?></h3>
				            	
				            	<ul>
				            	    <?php wp_get_archives('type=yearly'); ?>
				            	</ul>
				            	
				            	<h3><?php _e('Archives by Month', 'hemingway') ?></h3>
				            	
				            	<ul>
				            	    <?php wp_get_archives('type=monthly'); ?>
				            	</ul>
				            
					            <h3><?php _e('Archives by Day', 'hemingway') ?></h3>
					            
					            <ul>
					                <?php wp_get_archives('type=daily'); ?>
					            </ul>
				            
				            </div> <!-- /archive-col -->
				            
				            <div class="clear"></div>
		            
			            </div> <!-- /archive-box -->
			            
			            <?php if ( current_user_can( 'manage_options' ) ) : ?>
																		
							<p><?php edit_post_link( __('Edit', 'hemingway') ); ?></p>
						
						<?php endif; ?>
															            			                        
					</div> <!-- /post-content -->
											
					<div class="clear"></div>
					
					<?php if ( comments_open() ) : ?>
			
						<?php comments_template( '', true ); ?>
					
					<?php endif; ?>
				
				<?php endwhile; else: ?>
		
					<p><?php _e("We couldn't find any posts that matched your query. Please try again.", "hemingway"); ?></p>
			
				<?php endif; ?>
	
			</div> <!-- /post -->
		
		</div> <!-- /posts -->
	
	</div> <!-- /content -->
	
	<?php get_sidebar(); ?>

	<div class="clear"></div>
	
</div> <!-- /wrapper section-inner -->
								
<?php get_footer(); ?>