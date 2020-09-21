<article <?php post_class( 'single-container bg-color-white' ); ?> id="post-<?php the_ID(); ?>">

	<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>

		<div class="featured-media">

			<?php the_post_thumbnail(); ?>

		</div><!-- .featured-media -->

	<?php endif; ?>

	<div class="post-inner section-inner">

		<header class="post-header">

			<?php the_title( '<h1 class="post-title">', '</h1>' ); ?>

		</header><!-- .post-header -->

		<div class="entry-content">

			<?php 
			the_content();
			if ( ! is_single() ) edit_post_link();
			wp_link_pages(); 
			?>

		</div><!-- .entry-content -->

		<?php

		if ( is_single() ) :

			// Post meta
			koji_the_post_meta( $post->ID, 'single' );

			// Single pagination
			$next_post = get_next_post();
			$prev_post = get_previous_post();

			if ( $next_post || $prev_post ) :

				$pagination_classes = '';

				if ( ! $next_post ) {
					$pagination_classes = ' only-one only-prev';
				} elseif ( ! $prev_post ) {
					$pagination_classes = ' only-one only-next';
				}

				?>

				<nav class="pagination-single<?php echo $pagination_classes; ?>">

					<?php if ( $prev_post ) : ?>

						<a class="previous-post" href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>">
							<span class="arrow">
								<img aria-hidden="true" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/icons/arrow-left.svg" />
								<span class="screen-reader-text"><?php _e( 'Previous post:', 'koji' ); ?> </span>
							</span>
							<span class="title"><?php echo wp_kses_post( get_the_title( $prev_post->ID ) ); ?></span>
						</a>

					<?php endif; ?>

					<?php if ( $next_post ) : ?>

						<a class="next-post" href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>">
							<span class="arrow">
								<img aria-hidden="true" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/icons/arrow-right.svg" />
								<span class="screen-reader-text"><?php _e( 'Next post:', 'koji' ); ?> </span>
							</span>
							<span class="title"><?php echo wp_kses_post( get_the_title( $next_post->ID ) ); ?></span>
						</a>

					<?php endif; ?>

				</nav><!-- .single-pagination -->

				<?php

			endif;

		endif;

		// If comments are open, or there are at least one comment
		if ( ( comments_open() || get_comments_number() ) && ! post_password_required() ) : ?>

			<div class="comments-wrapper">
				<?php comments_template(); ?>
			</div><!-- .comments-wrapper -->

		<?php endif; ?>

	</div><!-- .post-inner -->

</article>
