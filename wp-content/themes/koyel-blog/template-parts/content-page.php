<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Koyel
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header text-center">
		<?php the_title( '<h1 class="single-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="single-entry-meta text-center">
		<?php koyel_blog_posted_by(); ?><span class="line">/</span><?php  koyel_blog_posted_on();  ?>
	</div>
	<?php if ( has_post_thumbnail () ): ?>
	<div class="single-area-img">
		<?php koyel_post_thumbnail(); ?>
	</div>
	<?php endif; ?>
	<div class="entry-content <?php if ( ! has_post_thumbnail () ): ?>padding-top<?php endif; ?>">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'koyel-blog' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Edit <span class="screen-reader-text">%s</span>', 'koyel-blog' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->