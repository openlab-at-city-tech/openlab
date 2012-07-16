<?php
/**
 * Template Name: Full-width, no sidebar
 * @package WordPress
 * @subpackage Coraline
 * @since Coraline 1.0
 */

coraline_set_full_content_width();
get_header(); ?>

		<div id="content-container" class="full-width">
			<div id="content" role="main">

			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'coraline' ), 'after' => '</div>' ) ); ?>
						<?php edit_post_link( __( 'Edit', 'coraline' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-content -->
				</div><!-- #post-## -->

				<?php if ( comments_open() ) comments_template( '', true ); ?>

			<?php endwhile; ?>

			</div><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>