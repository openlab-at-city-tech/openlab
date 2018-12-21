<?php

/* Template Name: Archive template */

get_header(); ?>

<div class="wrapper section-inner">						

	<div class="content left">
			
		<div class="posts">
	
			<div class="post">
			
				<?php if ( have_posts() ) : 
					
					while ( have_posts() ) : the_post(); ?>
					
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
							
							<div class="archive-box">
						
								<div class="archive-col">
													
									<h3><?php _e( 'Last 30 Posts', 'hemingway' ); ?></h3>
												
									<ul>
										<?php 
										
										$archive_posts = get_posts( array(
											'post_status'		=> 'publish',
											'posts_per_page'	=> 30,
										) );

										foreach ( $archive_posts as $archive_post ) : ?>
											<li>
												<a href="<?php echo get_the_permalink( $archive_post->ID ); ?>">
													<?php echo get_the_title( $archive_post->ID );?> 
													<span>(<?php echo get_the_time( get_option( 'date_format' ), $archive_post->ID ); ?>)</span>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
									
									<h3><?php _e( 'Archives by Categories', 'hemingway' ); ?></h3>
									
									<ul>
										<?php wp_list_categories( 'title_li=' ); ?>
									</ul>
									
									<h3><?php _e( 'Archives by Tags', 'hemingway' ); ?></h3>
									
									<ul>
										<?php 
										
										$tags = get_tags();
										
										if ( $tags ) {
											foreach ( $tags as $tag ) {
												/* Translators: %s = The name of the tag */
												echo '<li><a href="' . get_tag_link( $tag->term_id ) . '" title="' . sprintf( __( "View all posts in %s", 'hemingway' ), $tag->name ) . '" ' . '>' . $tag->name.'</a></li> ';
											}
										}
										
										?>
									</ul>
								
								</div><!-- .archive-col -->
								
								<div class="archive-col">
								
									<h3><?php _e( 'Contributors', 'hemingway' ); ?></h3>
									
									<ul>
										<?php wp_list_authors(); ?> 
									</ul>
									
									<h3><?php _e( 'Archives by Year', 'hemingway' ); ?></h3>
									
									<ul>
										<?php wp_get_archives( 'type=yearly' ); ?>
									</ul>
									
									<h3><?php _e( 'Archives by Month', 'hemingway' ); ?></h3>
									
									<ul>
										<?php wp_get_archives( 'type=monthly' ); ?>
									</ul>
								
									<h3><?php _e( 'Archives by Day', 'hemingway' ); ?></h3>
									
									<ul>
										<?php wp_get_archives( 'type=daily' ); ?>
									</ul>
								
								</div><!-- .archive-col -->
								
								<div class="clear"></div>
						
							</div><!-- .archive-box -->
							
							<?php if ( current_user_can( 'manage_options' ) ) : ?>
																			
								<p><?php edit_post_link( __( 'Edit', 'hemingway' ) ); ?></p>
							
							<?php endif; ?>
																												
						</div><!-- .post-content -->
												
						<div class="clear"></div>
						
						<?php 
						
						comments_template( '', true );
						
					endwhile; 
			
				endif; ?>
	
			</div><!-- .post -->
		
		</div><!-- .posts -->
	
	</div><!-- .content -->
	
	<?php get_sidebar(); ?>

	<div class="clear"></div>
	
</div><!-- .wrapper section-inner -->
								
<?php get_footer(); ?>