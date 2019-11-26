<?php get_header(); ?>

<div class="content section-inner">
											        
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	
		<div class="posts">
	
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
				<div class="content-inner">
									
					<div class="featured-media">
					
						<?php $image_array = wp_get_attachment_image_src( $post->ID, 'full', false ); ?>
					
						<a href="<?php echo esc_url( $image_array[0] ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment">
							<?php echo wp_get_attachment_image( $post->ID, 'post-image' ); ?>
						</a>
					
					</div><!-- .featured-media -->
					
					<div class="post-header">
					
						<h2 class="post-title"><?php echo basename( get_attached_file( $post->ID ) ); ?></h2>
						
						<div class="post-meta">
						
							<span><?php echo __( 'Uploaded', 'lingonberry' ) . ' ' . get_the_time( get_option( 'date_format' ) ); ?></span>
							
							<span class="date-sep">/</span>
						
							<span><?php echo __( 'Width:', 'lingonberry' ) . ' ' . $image_array[1] . ' px'; ?></span>
							
							<span class="date-sep">/</span>
							
							<span><?php echo __( 'Height:', 'lingonberry' ) . ' ' . $image_array[2] . ' px'; ?></span>
						
						</div>
					
					</div><!-- .post-header -->
	
					<?php if ( has_excerpt() ) : ?>
					
						<div class="post-content">
						
							<?php the_excerpt(); ?>
							
						</div><!-- .post-content -->
						
					<?php endif; ?>
											
				</div><!-- .content-inner -->
								
				<div class="post-nav">
				
					<?php

					// Get images in the current attachments gallery, and get the next and previous from it
					$attachments = array_values( get_children( array( 
						'order' 			=> 'ASC', 
						'orderby' 			=> 'menu_order ID',
						'post_mime_type' 	=> 'image',
						'post_parent' 		=> $post->post_parent, 
						'post_status' 		=> 'inherit', 
						'post_type' 		=> 'attachment', 
					) ) );

					foreach ( $attachments as $k => $attachment ) :
						if ( $attachment->ID == $post->ID )
							break;
					endforeach;

					$l = $k - 1;
					$k++;

					if ( isset( $attachments[ $k ] ) ) :
						// Get the URL of the next image attachment
						$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
						$prev_attachment_url = get_attachment_link( $attachments[ $l ]->ID );
					else :
						// ...Or get the URL of the first image attachment
						$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
					endif;
					?>

					<a href="<?php echo esc_url( $prev_attachment_url ); ?>" class="post-nav-older" rel="attachment"><?php _e( '&laquo; Previous<span> attachment</span>', 'lingonberry' ); ?></a>
					<a href="<?php echo esc_url( $next_attachment_url ); ?>" class="post-nav-newer" rel="attachment"><?php _e( 'Next<span> attachment</span> &raquo;', 'lingonberry' ); ?></a>
				
					<div class="clear"></div>
				
				</div><!-- .post-nav -->
				
				<?php comments_template( '', true ); ?>
															                        
		   	<?php endwhile; else: ?>
		
				<p><?php _e( "We couldn't find any posts that matched your query. Please try again.", "lingonberry" ); ?></p>
			
			<?php endif; ?>    
				
		</div><!-- .post -->
		
	</div><!-- .posts -->

</div><!-- .content section-inner -->
		
<?php get_footer(); ?>