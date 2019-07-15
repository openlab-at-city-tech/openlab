<?php get_header(); ?>

<main id="site-content" role="main">

	<?php if ( is_archive() || is_search() ) : ?>

		<header class="archive-header archive-header-mobile bg-color-black color-darker-gray">

			<div class="section-inner">

				<?php

				// Store the output, since we're outputting the archive header twice (desktop version and mobile version)
				ob_start(); ?>

				<h6 class="subheading"><?php echo koji_get_archive_title_prefix(); ?></h6>

				<div class="archive-header-inner">
				
					<h3 class="archive-title color-white hanging-quotes"><?php the_archive_title(); ?></h3>

					<?php if ( is_search() ) :

						global $wp_query; ?>

						<div class="archive-description">
							<p><?php

							// Translators: %s = the number of results
							printf( _nx( 'Found %s result matching your search.', 'Found %s results matching your search.',$wp_query->found_posts, '%s = number of results', 'koji' ), $wp_query->found_posts ); ?></p>
						</div><!-- .archive-description -->

					<?php elseif ( get_the_archive_description() ) : ?>

						<div class="archive-description">
							<?php the_archive_description(); ?>
						</div><!-- .archive-description -->

					<?php endif; ?>
				
				</div><!-- .archive-header-inner -->

				<?php

				$archive_header_contents = ob_get_clean();

				echo $archive_header_contents;

				?>

			</div><!-- .section-inner -->

		</header><!-- .archive-header -->

	<?php endif; ?>

	<div class="section-inner">

		<div class="posts load-more-target" id="posts" aria-live="polite">

			<div class="grid-sizer"></div>

			<?php if ( is_archive() || is_search() ) : ?>

				<div class="preview archive-header archive-header-desktop">

					<div class="preview-wrapper bg-color-black color-gray">

						<div class="preview-inner">

							<?php echo $archive_header_contents; ?>

						</div><!-- .preview-inner -->

					</div><!-- .preview -->

				</div><!-- .archive-header -->

				<?php
			endif;

			if ( have_posts() ) :

				while ( have_posts() ) : the_post();

					get_template_part( 'preview', get_post_type() );

				endwhile;

			endif;

			?>

		</div><!-- .posts -->

		<?php get_template_part( 'pagination' ); ?>

	</div><!-- .section-inner -->

</main><!-- #site-content -->

<?php get_footer(); ?>
