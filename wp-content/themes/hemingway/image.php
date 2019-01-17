<?php get_header(); ?>

<div class="wrapper section-inner">

	<div class="content left">
											        
		<?php if ( have_posts() ) : 
			
			while( have_posts() ) : the_post(); ?>
		
				<div class="posts">
			
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					
						<div class="content-inner">
											
							<div class="featured-media">
							
								<?php $image_array = wp_get_attachment_image_src( $post->ID, 'full', false ); ?>
							
								<a href="<?php echo esc_url( $image_array[0] ); ?>" rel="attachment">
									<?php echo wp_get_attachment_image( $post->ID, 'post-image' ); ?>
								</a>
							
							</div><!-- .featured-media -->
							
							<div class="post-header">
							
								<h2 class="post-title"><?php echo basename( get_attached_file( $post->ID ) ); ?></h2>
								
								<div class="post-meta">
								
									<span><?php echo the_time( get_option( 'date_format' ) ); ?></span>
									
									<span class="date-sep"> / </span>
								
									<span><?php echo $image_array[1] // 1 is the width ?> <span style="text-transform:lowercase;">x</span> <?php echo $image_array[2] . ' px'; // 2 is the height ?></span>
								
								</div>
							
							</div><!-- .post-header -->
			
							<?php if ( ! empty( $post->post_excerpt ) ) : ?>
							
								<div class="post-content">
								
									<?php the_excerpt(); ?>
									
								</div><!-- .post-content -->
								
							<?php endif; ?>
													
						</div><!-- .content-inner -->
						
						<div class="post-meta-bottom">
										
							<div class="post-nav">
							
								<?php
								/**
								 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
								 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
								 */
								$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
								foreach ( $attachments as $k => $attachment ) :
									if ( $attachment->ID == $post->ID )
										break;
								endforeach;
								
								$l = $k - 1;
								$k++;
								
								if ( isset( $attachments[ $k ] ) ) :
									// get the URL of the next image attachment
									$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
									$prev_attachment_url = get_attachment_link( $attachments[ $l ]->ID );
								else :
									// or get the URL of the first image attachment
									$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
								endif;
								?>
								<a href="<?php echo esc_url( $prev_attachment_url ); ?>" class="post-nav-older" rel="attachment"><?php _e('&laquo; Previous<span> attachment</span>', 'hemingway'); ?></a>
								<a href="<?php echo esc_url( $next_attachment_url ); ?>" class="post-nav-newer" rel="attachment"><?php _e('Next<span> attachment</span> &raquo;', 'hemingway'); ?></a>
							
								<div class="clear"></div>
							
							</div><!-- .post-nav -->
						
						</div><!-- .post-meta-bottom -->
						
						<?php 
						
						comments_template( '', true );

					endwhile; 

				endif; 
				
				?>    
					
			</div><!-- .post -->
			
		</div><!-- .posts -->
		
	</div><!-- .content -->
	
	<?php get_sidebar(); ?>
	
	<div class="clear"></div>

</div><!-- .wrapper.section-inner -->
		
<?php get_footer(); ?>