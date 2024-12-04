<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Koyel
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php koyel_blog_posted_on(); ?>
		</div><!-- .entry-meta -->
	<?php endif; ?>

	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif; ?>
	</header><!-- .entry-header -->
	<div class="entry-content">
		<?php the_excerpt(); ?>
	</div><!-- .entry-content -->

	<?php if ( has_post_thumbnail () ): ?>
	<div class="single-img">
		<?php koyel_post_thumbnail(); ?>
	</div>
	<?php endif; ?>

	<footer class="entry-footer">
		<?php echo'<a href="'.esc_url ( get_the_permalink( $post->ID ) ).'" class="more-btn">'.esc_html__('Read More','koyel-blog').'<i class="fa fa-angle-right" aria-hidden="true"></i></a>'; ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
