<?php
/**
 * Template Name: Archives
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

get_header(); ?>

<div id="content-container">
	<div id="content" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

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

		<?php comments_template(); ?>

		<?php endwhile; // end of the loop. ?>

	</div><!-- #content -->
</div><!-- #container -->

<?php
get_sidebar();
get_footer();
