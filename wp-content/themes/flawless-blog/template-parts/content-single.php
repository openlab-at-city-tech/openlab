<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Flawless Blog
 */

$single_date       = get_theme_mod( 'flawless_blog_enable_single_date', true );
$single_author     = get_theme_mod( 'flawless_blog_enable_single_author', true );
$single_category   = get_theme_mod( 'flawless_blog_enable_single_category', true );
$single_tag        = get_theme_mod( 'flawless_blog_enable_single_tag', true );

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( is_singular() ) : ?>
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->
		<?php
		if ( 'post' === get_post_type() ) :
			setup_postdata( get_post() );
			?>
			<div class="entry-meta">
				<?php
				if ( $single_date === true ) {
					flawless_blog_posted_on();
				}
				if ( $single_author === true ) {
					flawless_blog_posted_by();
				}
				?>
			</div><!-- .entry-meta -->
			<?php
		endif;
		?>
	<?php endif; ?>

	<?php flawless_blog_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'flawless-blog' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			)
		);
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'flawless-blog' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->
	<footer class="entry-footer">
		<?php
		if ( $single_category === true ) {
			flawless_blog_categories_list();
		}
		if ( $single_tag === true ) {
			flawless_blog_entry_footer();
		}
		?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
