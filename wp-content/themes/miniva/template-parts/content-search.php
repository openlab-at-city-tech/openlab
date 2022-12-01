<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Miniva
 */

?>

<?php do_action( 'miniva_post_before' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'miniva_post_start' ); ?>

	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php
			miniva_posted_on();
			miniva_posted_by();
			?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php do_action( 'miniva_post_middle' ); ?>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->

	<footer class="entry-footer">
		<?php miniva_entry_footer(); ?>
	</footer><!-- .entry-footer -->

	<?php do_action( 'miniva_post_end' ); ?>

</article><!-- #post-<?php the_ID(); ?> -->

<?php do_action( 'miniva_post_after' ); ?>
