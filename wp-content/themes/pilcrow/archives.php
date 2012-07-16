<?php
/**
 * Template Name: Archives
 *
 * @package WordPress
 * @subpackage Pilcrow
 * @since Pilcrow 1.0
 */

get_header(); ?>

		<div id="content-container">
			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<?php if ( is_front_page() ) { ?>
							<h2 class="entry-title"><?php the_title(); ?></h2>
						<?php } else { ?>
							<h1 class="entry-title"><?php the_title(); ?></h1>
						<?php } ?>

						<div class="entry entry-content">
							<h2><?php _e( 'Browse by Month:', 'pilcrow' ); ?></h2>
							<ul>
								<?php wp_get_archives('type=monthly'); ?>
							</ul>

							<h2><?php _e( 'Browse by Category:', 'pilcrow' ); ?></h2>
							<ul>
								<?php wp_list_categories( 'title_li=' ); ?>
							</ul>
						</div><!-- .entry-content -->
					</div><!-- #post-## -->

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>