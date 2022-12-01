<?php
/**
 * Template part for displaying posts
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
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php
				miniva_posted_on();
				miniva_posted_by();
				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php do_action( 'miniva_post_middle' ); ?>

	<div class="entry-content">
		<?php miniva_the_content(); ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php miniva_entry_footer(); ?>
	</footer><!-- .entry-footer -->

	<?php do_action( 'miniva_post_end' ); ?>

</article><!-- #post-<?php the_ID(); ?> -->

<?php do_action( 'miniva_post_after' ); ?>
