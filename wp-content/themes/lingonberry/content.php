<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php 
	$post_format 	= get_post_format() ? get_post_format() : 'default'; 
	$post_type 		= get_post_type();
	?>

	<?php if ( $post_type == 'post' && ! is_single() ) : ?>

		<div class="post-bubbles">

			<a href="<?php the_permalink(); ?>" class="format-bubble"></a>
				
			<?php if ( is_sticky() ) : ?>
				<a href="<?php the_permalink(); ?>" class="sticky-bubble"><?php _e( 'Sticky post', 'lingonberry' ); ?></a>
			<?php endif; ?>

		</div><!-- .post-bubbles -->

	<?php endif; ?>

	<div class="content-inner">

		<?php
		$header_hidden_class = ( ! is_single() && in_array( $post_format, array( 'video', 'quote', 'aside', 'chat', 'link' ) ) ) ? ' hidden' : '';
		?>

		<header class="post-header<?php echo $header_hidden_class; ?>">
		
			<?php
			if ( has_post_thumbnail() || $post_format == 'gallery' ) : 
				?>
		
				<figure class="featured-media">

					<?php if ( $post_format == 'gallery' ) : ?>

						<?php lingonberry_flexslider( 'post-image' ); ?>

					<?php else : ?>
				
						<a href="<?php the_permalink(); ?>">
						
							<?php
								
							the_post_thumbnail();

							$caption = get_the_post_thumbnail_caption();
							
							if ( $caption ) : ?>
											
								<figcaption class="media-caption-container">
									<p class="media-caption"><?php echo $caption; ?></p>
								</figcaption>
								
								<?php 
							endif;
							?>
							
						</a>

					<?php endif; ?>
							
				</figure><!-- .featured-media -->
					
				<?php 
			endif;
			
			if ( is_singular() ) :

				the_title( '<h1 class="post-title">', '</h1>' );

			elseif ( get_the_title() ) : 
				?>
			
				<h2 class="post-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

				<?php 
			endif;

			if ( $post_type == 'post' ) {
				lingonberry_meta();
			}

			?>
			
		</header><!-- .post-header -->

		<?php if ( get_the_content() ) : ?>
																						
			<div class="post-content">
			
				<?php the_content(); ?>

				<?php wp_link_pages(); ?>

			</div><!-- .post-content -->

		<?php endif; ?>
		
		<?php if ( is_single() ) : ?>
		
			<div class="post-cat-tags">
						
				<p class="post-categories"><?php _e( 'Categories:', 'lingonberry' ); ?> <?php the_category( ', ' ); ?></p>

				<?php if ( get_the_tags( $post->ID ) ) : ?>
			
					<p class="post-tags"><?php the_tags( __( 'Tags:', 'lingonberry' ) . ' ', ', '); ?></p>

				<?php endif; ?>
			
			</div><!-- .post-cat-tags -->
			
		<?php endif; ?>
			
	</div><!-- .content-inner -->

</article>