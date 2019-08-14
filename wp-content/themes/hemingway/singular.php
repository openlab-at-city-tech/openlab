<?php get_header(); ?>

<div class="wrapper section-inner">

	<div class="content left">
												        
		<?php if ( have_posts() ) : 
			
			while ( have_posts() ) : the_post(); ?>
		
				<div class="posts">
			
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		
						<div class="post-header">

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
									
							<?php endif;
							
							// Legacy output of the videourl post meta field
							$videourl = get_post_meta( $post->ID, 'videourl', true ); 
							
							if ( $videourl != '' ) : ?>

								<div class="featured-media">
								
									<?php if ( strpos( $videourl, '.mp4' ) !== false ) : ?>
					
										<video controls>
											<source src="<?php echo $videourl; ?>" type="video/mp4">
										</video>
																								
									<?php else : 
										
										echo wp_oembed_get( $videourl ); 
											
									endif; ?>
									
								</div><!-- .featured-media -->
							
							<?php endif; ?>

							<?php if ( get_the_title() ) : ?>
							
								<h1 class="post-title">

									<?php if ( is_single() ) : ?>
								
										<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>

									<?php else : ?>

										<?php the_title(); ?>

									<?php endif; ?>
									
								</h1>

							<?php endif; ?>

							<?php if ( is_single() ) : ?>
								
								<div class="post-meta">
								
									<span class="post-date"><a href="<?php the_permalink(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></span>
									
									<span class="date-sep"> / </span>
										
									<span class="post-author"><?php the_author_posts_link(); ?></span>
									
									<span class="date-sep"> / </span>
									
									<?php comments_popup_link( '<span class="comment">' . __( '0 Comments', 'hemingway' ) . '</span>', __( '1 Comment', 'hemingway' ), __( '% Comments', 'hemingway' ) ); ?>
									
									<?php if ( current_user_can( 'manage_options' ) ) : ?>
									
										<span class="date-sep"> / </span>
													
										<?php edit_post_link( __( 'Edit', 'hemingway' ) ); ?>
									
									<?php endif; ?>
															
								</div><!-- .post-meta -->

							<?php endif; ?>
							
						</div><!-- .post-header -->
																										
						<div class="post-content">
						
							<?php the_content(); ?>
							<?php wp_link_pages(); ?>
												
						</div><!-- .post-content -->
									
						<div class="clear"></div>

						<?php if ( is_single() ) : ?>
						
							<div class="post-meta-bottom">
																				
								<p class="post-categories"><span class="category-icon"><span class="front-flap"></span></span> <?php the_category( ', ' ); ?></p>
								
								<?php if ( has_tag() ) : ?>
									<p class="post-tags"><?php the_tags( '', '' ); ?></p>
								<?php endif; ?>
								
								<div class="clear"></div>

								<?php

								$prev_post = get_previous_post();
								$next_post = get_next_post();

								if ( $prev_post || $next_post ) : ?>
														
									<div class="post-nav">
																
										<?php if ( $prev_post ) : ?>
										
											<a class="post-nav-older" href="<?php echo get_permalink( $prev_post->ID ); ?>">
												
												<h5><?php _e( 'Previous post', 'hemingway' ); ?></h5>
												<?php echo get_the_title( $prev_post->ID ); ?>
											
											</a>
									
										<?php endif; ?>
										
										<?php if ( $next_post ) : ?>
											
											<a class="post-nav-newer" href="<?php echo get_permalink( $next_post->ID ); ?>">
											
												<h5><?php _e( 'Next post', 'hemingway' ); ?></h5>
												<?php echo get_the_title( $next_post->ID ); ?>
											
											</a>
									
										<?php endif; ?>
																	
										<div class="clear"></div>
									
									</div><!-- .post-nav -->

								<?php endif; ?>
													
							</div><!-- .post-meta-bottom -->

							<?php
						endif;
						
						comments_template( '', true );
					
					endwhile;
			
				endif; 
				
				?>    
		
			</div><!-- .post -->
			
		</div><!-- .posts -->
	
	</div><!-- .content -->
	
	<?php get_sidebar(); ?>
	
	<div class="clear"></div>
	
</div><!-- .wrapper -->
		
<?php get_footer(); ?>