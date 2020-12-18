<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ePortfolio
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class("twp-single-article"); ?>>
	<header class="entry-header">
		<div class="twp-categories twp-categories-with-line">
			<?php eportfolio_post_categories(); ?>
		</div>
		<?php
			the_title( '<h2 class="entry-title">', '</h2>' );
			?>
			<div class="twp-author-meta">
				<?php
					eportfolio_post_author();
					eportfolio_post_date();
				?>
			</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<?php 
	$post_options = get_post_meta( $post->ID, 'eportfolio-meta-checkbox', true );
	if ( $post_options ) {
	   eportfolio_post_thumbnail();
	} ?>

	<div class="entry-content">
		<?php
		the_content( sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'eportfolio' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		) );

		wp_link_pages( array(
			'before' => '<div class="page-links"><span class="post-page-numbers twp-caption">pages</span>' . esc_html__( '', 'eportfolio' ),
			'after'  => '</div>',
		) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php eportfolio_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
