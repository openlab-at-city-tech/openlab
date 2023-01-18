<?php
/**
 * Template part for displaying featured posts
 *
 * @package Miniva
 */

global $miniva_featured_thumbnail_size;
?>

<article id="post-<?php the_ID(); ?>">
	<a class="post-thumbnail img-cover" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( $miniva_featured_thumbnail_size ); ?>
		<?php endif; ?>
	</a>
	<header class="entry-header">
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
	</header>
</article>
