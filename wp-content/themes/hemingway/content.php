<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="post-header">

		<?php 

		$post_format = get_post_format();

		// On archive, only output the featured image and title on posts without a post format.
		if ( ( is_singular() || ! $post_format ) ) :
		
			hemingway_the_featured_media( $post );

			if ( get_the_title() ) : 

				$title_elem = is_singular() ? 'h1' : 'h2';
			
				?>
			
				<<?php echo $title_elem; ?> class="post-title entry-title">
					<?php if ( ! is_singular() ) : ?>
						<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
					<?php else : ?>
						<?php the_title(); ?>
					<?php endif; ?>
				</<?php echo $title_elem; ?>>

				<?php 

			endif;

		endif;

		do_action( 'hemingway_before_post_meta', $post->ID );

		$post_type = get_post_type();
		$post_type_outputs_post_meta = hemingway_post_type_has_post_meta_output( $post_type );
		
		if ( $post_type_outputs_post_meta ) : ?>
			
			<div class="post-meta">
			
				<span class="post-date"><a href="<?php the_permalink(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></span>
				
				<span class="date-sep"> / </span>
					
				<span class="post-author"><?php the_author_posts_link(); ?></span>

				<?php if ( comments_open() ) : ?>

					<span class="date-sep"> / </span>
				
					<?php comments_popup_link( '<span class="comment">' . __( '0 Comments', 'hemingway' ) . '</span>', __( '1 Comment', 'hemingway' ), __( '% Comments', 'hemingway' ) ); ?>

				<?php endif; ?>
				
				<?php if ( current_user_can( 'manage_options' ) ) : ?>
				
					<span class="date-sep"> / </span>
								
					<?php edit_post_link( __( 'Edit', 'hemingway' ) ); ?>
				
				<?php endif; ?>
										
			</div><!-- .post-meta -->

			<?php 
		endif; 
		
		do_action( 'hemingway_after_post_meta', $post->ID );
		
		?>
		
	</div><!-- .post-header -->
																					
	<div class="post-content entry-content">
	
		<?php

		if ( is_search() ) {
			the_excerpt();
		} else {
			the_content(); 
		}

		wp_link_pages( array(
			'before'           => '<nav class="post-nav-links"><span class="label">' . __( 'Pages:', 'hemingway' ) . '</span>',
			'after'            => '</nav>',
		) );

		edit_post_link( __( 'Edit', 'hemingway' ), '<p>', '</p>' ); 

		?>
							
	</div><!-- .post-content -->
				
	<?php if ( is_singular() && $post_type_outputs_post_meta ) : ?>
	
		<div class="post-meta-bottom">

			<?php if ( get_the_category() ) : ?>
															
				<p class="post-categories"><span class="category-icon"><span class="front-flap"></span></span> <?php the_category( ', ' ); ?></p>
	
			<?php endif; ?>
			
			<?php if ( has_tag() ) : ?>
				<p class="post-tags"><?php the_tags( '', '' ); ?></p>
			<?php endif; ?>
			
			<?php

			$prev_post = get_previous_post();
			$next_post = get_next_post();

			if ( $prev_post || $next_post ) : ?>
									
				<nav class="post-nav group">
											
					<?php if ( $prev_post ) : ?>
						<a class="post-nav-older" href="<?php the_permalink( $prev_post->ID ); ?>">
							<h5><?php _e( 'Previous post', 'hemingway' ); ?></h5>
							<?php echo get_the_title( $prev_post->ID ); ?>
						</a>
					<?php endif; ?>
					
					<?php if ( $next_post ) : ?>
						<a class="post-nav-newer" href="<?php the_permalink( $next_post->ID ); ?>">
							<h5><?php _e( 'Next post', 'hemingway' ); ?></h5>
							<?php echo get_the_title( $next_post->ID ); ?>
						</a>
					<?php endif; ?>

				</nav><!-- .post-nav -->

			<?php endif; ?>
								
		</div><!-- .post-meta-bottom -->

		<?php
	endif;

	if ( is_singular() ) {
		comments_template( '', true );
	}

	?>

</article><!-- .post -->