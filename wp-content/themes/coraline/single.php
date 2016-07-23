<?php
/**
 * @package Coraline
 * @since Coraline 1.0
 */

get_header(); ?>

<div id="content-container">
	<div id="content" role="main">

	<?php if ( have_posts() ) while ( have_posts() ) : the_post();

			$format = get_post_format();

			if ( false == $format)
				$format = 'standard'; ?>

		<div id="nav-above" class="navigation">
			<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'coraline' ) . '</span> %title' ); ?></div>
			<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'coraline' ) . '</span>' ); ?></div>
		</div><!-- #nav-above -->

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php if ( 'standard' != $format ) : ?>
				<a class="entry-format" href="<?php echo esc_url( get_post_format_link( get_post_format() ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'All %s posts', 'coraline' ), get_post_format_string( get_post_format() ) ) ); ?>"><?php echo esc_html( get_post_format_string( get_post_format() ) ); ?></a>
			<?php endif; ?>

			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

			<?php if ( 'standard' == $format ) : ?>
				<div class="entry-meta">
					<?php coraline_posted_on(); coraline_posted_by(); ?><span class="comments-link"><span class="meta-sep">|</span> <?php comments_popup_link( __( 'Leave a comment', 'coraline' ), __( '1 Comment', 'coraline' ), __( '% Comments', 'coraline' ) ); ?></span>
					<?php edit_post_link( __( 'Edit', 'coraline' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
				</div><!-- .entry-meta -->
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'coraline' ), 'after' => '</div>' ) ); ?>
			</div><!-- .entry-content -->

			<div class="entry-info">
			<?php if ( 'standard' != $format ) : ?>
				<p class="format-entry-meta">
					<?php coraline_posted_on(); coraline_posted_by(); ?>
					<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'coraline' ), __( '1 Comment', 'coraline' ), __( '% Comments', 'coraline' ) ); ?></span>
				</p>
			<?php endif; ?>
				<?php coraline_posted_in(); ?>
				<?php edit_post_link( __( 'Edit', 'coraline' ), '<span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-info -->
		</div><!-- #post-## -->

		<div id="nav-below" class="navigation">
			<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'coraline' ) . '</span> %title' ); ?></div>
			<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'coraline' ) . '</span>' ); ?></div>
		</div><!-- #nav-below -->

		<?php comments_template(); ?>

	<?php endwhile; // end of the loop. ?>

	</div><!-- #content -->
</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>