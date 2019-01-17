<div id="post-<?php the_ID(); ?>" <?php post_class( 'post-preview' ); ?>>

	<?php 

	$post_format = get_post_format(); 

	if ( in_array( $post_format, array( 'video', 'aside', 'quote' ) ) ) : ?>

		<div class="post-meta">
			
			<span class="post-date"><a href="<?php the_permalink(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></span>
			
			<?php if ( is_sticky() && !has_post_thumbnail() ) : ?> 
				
				<span class="date-sep"> / </span>
			
				<?php _e( 'Sticky', 'hemingway' ); ?>
			
			<?php endif; ?>
			
			<?php edit_post_link( __( 'Edit', 'hemingway' ), '<span class="date-sep"> / </span>' ); ?>
									
		</div><!-- .post-meta -->

	<?php else : ?>

		<div class="post-header">

			<?php if ( has_post_thumbnail() ) : ?>

				<div class="featured-media">
				
					<?php if ( is_sticky() ) : ?><span class="sticky-post"><?php _e( 'Sticky post', 'hemingway' ); ?></span><?php endif; ?>
				
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
			
			<h2 class="post-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			
			<div class="post-meta">
			
				<span class="post-date"><a href="<?php the_permalink(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></span>
				
				<span class="date-sep"> / </span>
					
				<span class="post-author"><?php the_author_posts_link(); ?></span>
				
				<span class="date-sep"> / </span>
				
				<?php comments_popup_link( '<span class="comment">' . __( '0 Comments', 'hemingway' ) . '</span>', __( '1 Comment', 'hemingway' ), __( '% Comments', 'hemingway' ) ); ?>
				
				<?php if ( is_sticky() && !has_post_thumbnail() ) : ?> 
				
					<span class="date-sep"> / </span>
				
					<?php _e('Sticky', 'hemingway'); ?>
				
				<?php endif; ?>
				
				<?php edit_post_link( __( 'Edit', 'hemingway' ), '<span class="date-sep"> / </span>' ); ?>
										
			</div>
			
		</div><!-- .post-header -->

	<?php endif; ?>

	<?php if ( get_the_content() ) : ?>
																					
		<div class="post-content">
			
			<?php the_content(); ?>
						
			<?php wp_link_pages(); ?>

		</div><!-- .post-content -->

	<?php endif; ?>
				
	<div class="clear"></div>

</div><!-- .post -->